<?php
require_once './config/db.php';
require_once './config/sesioniniciada.php';

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar sesión
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Verificar el rol del usuario y obtener departamento
$rol = $_SESSION['Rol_ID'];
$departamento_id = null;

try {
    // Lógica para seleccionar el departamento
    if ($rol == 1) {
        $departamento_id = $_SESSION['Departamento_ID'];
    } elseif ($rol == 2 || $rol == 3) {
        if (isset($_GET['departamento_id'])) {
            $departamento_id = filter_var($_GET['departamento_id'], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $sql_primer_departamento = "SELECT Departamento_ID FROM departamentos ORDER BY Departamento_ID LIMIT 1";
            $result_primer_departamento = mysqli_query($conexion, $sql_primer_departamento);
            
            if ($result_primer_departamento && mysqli_num_rows($result_primer_departamento) > 0) {
                $row_primer_departamento = mysqli_fetch_assoc($result_primer_departamento);
                $departamento_id = $row_primer_departamento['Departamento_ID'];
            } else {
                throw new Exception("No se encontraron departamentos disponibles.");
            }
        }
    } else {
        throw new Exception("Rol de usuario no autorizado.");
    }

    // Obtener información del departamento
    $sql_departamento = "SELECT Nombre_Departamento, Departamentos 
            FROM departamentos 
            WHERE Departamento_ID = ?";
    $stmt = $conexion->prepare($sql_departamento);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result_departamento = $stmt->get_result();

    if ($result_departamento && $row_departamento = $result_departamento->fetch_assoc()) {
        $nombre_departamento = $row_departamento['Nombre_Departamento'];
        $departamento_nombre = $row_departamento['Departamentos'];
    } else {
        die("No se encontró el departamento especificado.");
    }

    // Define the mapping array with all possible variations
    $departmentMapping = [
        'Estudios Regionales' => [
            'ESTUDIOS REGIONALES',
            'Estudios Regionales',
            'Est. Regionales'
        ],
        'Finanzas' => [
            'FINANZAS',
            'Finanzas',
            'FIN'
        ],
        'Ciencias Sociales' => [
            'CIENCIAS SOCIALES',
            'Ciencias Sociales',
            'CERI/CIENCIAS SOCIALES',
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS ',
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS'
        ],
        'PALE' => [
            'PALE',
            'PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA',
            'ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA'
        ],
        'Economía' => [
            'ECONOMIA',
            'Economía',
            'Economia'
        ],
        'Recursos Humanos' => [
            'RECURSOS HUMANOS',
            'Recursos Humanos',
            'RH',
            'RECURSOS_HUMANOS'
        ],
        'Métodos Cuantitativos' => [
            'METODOS CUANTITATIVOS',
            'Métodos Cuantitativos',
            'Metodos Cuantitativos'
        ],
        'Políticas Públicas' => [
            'POLITICAS PUBLICAS',
            'Políticas Públicas',
            'Politicas Publicas',
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS ',
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS'
        ],
        'Administración' => [
            'ADMINISTRACION',
            'Administración',
            'Administracion',
            'ADMINISTRACION ',
            'ADMINISTRACION  '
        ],
        'Auditoria' => [
            'AUDITORIA',
            'Auditoría',
            'Auditoria',
            'SECRETARIA ADMINISTRATIVA/AUDITORIA'
        ],
        'Mercadotecnia' => [
            'MERCADOTECNIA',
            'Mercadotecnia',
            'MERCADOTECNIA Y NEGOCIOS INTERNACIONALES'
        ],
        'Impuestos' => [
            'IMPUESTOS',
            'Impuestos',
            'IMP'
        ],
        'Sistemas de Información' => [
            'SISTEMAS DE INFORMACION',
            'Sistemas de Información',
            'Sistemas de Informacion'
        ],
        'Turismo' => [
            'TURISMO',
            'Turismo',
            'TURISMO R. Y S.'
        ],
        'Contabilidad' => [
            'CONTABILIDAD',
            'Contabilidad',
            'CONT'
        ],
        'Otros' => [
            'SECRETARIA ADMINISTRATIVA/AUDITORIA',
            'CERI/SECRETARIA ACADEMICA',
            'SECRETARIA ACADEMICA',
            'SECRETARIA ACADEMICA  '
        ]
    ];

    // Function to normalize department name
    function normalizeDepartmentName($dbDepartment) {
        global $departmentMapping;
        
        foreach ($departmentMapping as $standardName => $variations) {
            if (in_array($dbDepartment, $variations)) {
                return $standardName;
            }
        }
        
        return $dbDepartment; // Return original if no mapping found
    }

    // Aquí comienza el HTML
    include './template/header.php';
    include './template/navbar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Profesores - <?php echo htmlspecialchars($departamento_nombre); ?></title>
    <link rel="stylesheet" href="./CSS/modal-profesores.css">
</head>
<body>

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="encabezado-izquierda" style="display: flex; align-items: center;">
            <!-- Barra de búsqueda -->
            <div class="search-bar">
                <div class="search-input-container">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="text" placeholder="Buscar profesor..." id="buscar-todos-profesores" onkeyup="filtrarTodosProfesores()">
                </div>
            </div>
        </div>
        <div class="encabezado-centro">
            <h3>Profesores - <?php echo htmlspecialchars($departamento_nombre); ?></h3>
        </div>
        <div class="encabezado-derecha">
            <div id="list1" class="dropdown-check-list" tabindex="100">
                <span class="anchor">Departamento: </span>
                <ul class="items">
                    <?php
                    // Obtener departamentos únicos de la tabla
                    /*
                    $sql_departamentos = "SELECT DISTINCT Departamento FROM coord_per_prof ORDER BY Departamento";
                    $result_departamentos = mysqli_query($conexion, $sql_departamentos);
                    
                    while($row = mysqli_fetch_assoc($result_departamentos)) {
                        echo "<li><input type='checkbox' value='" . htmlspecialchars($row['Departamento']) . "' />" . 
                            htmlspecialchars($row['Departamento']) . "</li>";
                            
                    }
                    */
                    ?>
                    <li><input type="checkbox" />Administración</li>
                    <li><input type="checkbox" />Auditoria</li>
                    <li><input type="checkbox" />Ciencias Sociales</li>
                    <li><input type="checkbox" />Contabilidad</li>
                    <li><input type="checkbox" />Economía</li>
                    <li><input type="checkbox" />Estudios Regionales</li>
                    <li><input type="checkbox" />Finanzas</li>
                    <li><input type="checkbox" />Impuestos</li>
                    <li><input type="checkbox" />Mercadotecnia</li>
                    <li><input type="checkbox" />Métodos Cuantitativos</li>
                    <li><input type="checkbox" />PALE</li>
                    <li><input type="checkbox" />Políticas Públicas</li>
                    <li><input type="checkbox" />Recursos Humanos</li>
                    <li><input type="checkbox" />Sistemas de Información</li>
                    <li><input type="checkbox" />Turismo</li>
                    <li><input type="checkbox" />Otros</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="contenedor-tabla">
        <div class="contenido-tabla">
            <!-- Tabla de profesores -->
            <div class="profesores-container">
                <table class="profesores-table">
                    <thead>
                        <tr>
                           <!-- <th class="detalle-column">count</th> --> 
                            <th class="detalle-column">Código</th>
                            <th class="detalle-column">Nombre Completo</th>
                            <th class="detalle-column">Categoria Actúal</th>
                            <th class="detalle-column">Departamento</th>
                            <th class="detalle-column">Detalles del Profesor</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                        // Consulta SQL con las variantes del departamento
                        $sql_todos_profesores = "SELECT  Codigo, Nombre_Completo, Categoria_actual, Departamento 
                                            FROM coord_per_prof 
                                            ORDER BY Departamento, Nombre_Completo";
                        
                        $result_todos_profesores = mysqli_query($conexion, $sql_todos_profesores);
                        
                        if ($result_todos_profesores) {
                            $contador = 1;
                            while($row = mysqli_fetch_assoc($result_todos_profesores)) {
                                $departamento_normalizado = normalizeDepartmentName($row['Departamento']);
                                echo "<tr class='tr-info'>";
                                //echo "<td class='detalle-column detalle-column1'>" . htmlspecialchars($contador ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column detalle-column1'>" . htmlspecialchars($row['Codigo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Nombre_Completo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Categoria_actual'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($departamento_normalizado ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column detalle-column2'><button onclick='verDetalleProfesor(" . $row['Codigo'] . ")' class='btn-detalle'>Ver detalle</button></td>";
                                echo "</tr>";
                                $contador = $contador + 1;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No se encontraron profesores</td></tr>";
                            echo "<tr><td colspan='3'>Error en la consulta: " . mysqli_error($conexion) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles -->
<div id="modal-detalle-profesor" class="modal-detalle">
    <div class="modal-content-detalle">
        <div id="detalle-profesor-contenido">
            <span class="close" onclick="cerrarModalDetalle()">&times;</span>
        </div> 
    </div>
</div>

<script src="./JS/profesores/detalle-profesor.js"></script>
<script src="./JS/profesores/filtro-profesores.js"></script>
<script src="./JS/profesores/desplegable-box.js"></script>
<script src="./JS/profesores/filtro-departamentos.js"></script>
<script>
    // Pass the session department to JavaScript
    const sessionDepartment = "<?php echo htmlspecialchars(normalizeDepartmentName($nombre_departamento)); ?>";
    const isPosgrados = "<?php echo ($nombre_departamento === 'Posgrados') ? 'true' : 'false'; ?>";
</script>

<!-- DataTables Scripts -->
 
<?php
    } catch (Exception $e) {
        echo "<div class='error-message'>";
        echo "Error: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }

    include("./template/footer.php");
?>

<script>
$(document).ready(function() {
    $('.profesores-table').DataTable({
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: ['colvis']
    });
});
</script>
</body>
</html>
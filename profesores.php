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
    // Array de mapeo de departamentos
    $departamentos_mapping = [

        'Administración' => [
            'Administracion',
            'ADMINISTRACION',
            'Administración'
        ],
        'PALE' => [
            'ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA',
            'PALE',
            'Programa de Aprendizaje de Lengua Extranjera'
        ],
        'Auditoría' => [
            'Auditoria',
            'AUDITORIA',
            'Auditoría',
            'SECRETARIA ADMINISTRATIVA/AUDITORIA'
        ],
        'Ciencias_Sociales' => [
            'CERI/CIENCIAS SOCIALES',
            'CIENCIAS SOCIALES',
            'Ciencias Sociales', 
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS'
        ],
        'Políticas_Públicas' => [
            'POLITICAS PUBLICAS',
            'Políticas Públicas',
            'Politicas Publicas'
        ],
        'Contabilidad' => [
            'CONTABILIDAD',
            'Contabilidad'
        ],
        'Economía' => [
            'ECONOMIA',
            'Economía',
            'Economia'
        ],
        'Estudios_Regionales' => [
            'ESTUDIOS REGIONALES',
            'Estudios Regionales'
        ],
        'Finanzas' => [
            'FINANZAS',
            'Finanzas'
        ],
        'Impuestos' => [
            'IMPUESTOS',
            'Impuestos'
        ],
        'Mercadotecnia' => [
            'MERCADOTECNIA',
            'Mercadotecnia',
            'MERCADOTECNIA Y NEGOCIOS INTERNACIONALES'
        ],
        'Métodos_Cuantitativos' => [
            'METODOS CUANTITATIVOS',
            'Métodos Cuantitativos',
            'Metodos Cuantitativos'
        ],
        'Recursos_Humanos' => [
            'RECURSOS HUMANOS',
            'Recursos Humanos',
            'RECURSOS_HUMANOS'
        ],
        'Sistemas_de_Información' => [
            'SISTEMAS DE INFORMACION',
            'Sistemas de Información',
            'Sistemas de Informacion'
        ],
        'Turismo' => [
            'TURISMO',
            'Turismo',
            'Turismo R. y S.'
        ],
    ];

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
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result_departamento = $stmt->get_result();

    if (!$result_departamento || $result_departamento->num_rows === 0) {
        throw new Exception("No se encontró el departamento especificado.");
    }

    $row_departamento = $result_departamento->fetch_assoc();
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    $departamento_nombre = $row_departamento['Departamentos'];

    // Obtener las variantes del departamento desde el mapping
    $departamento_variantes = [];
    foreach ($departamentos_mapping as $key => $variants) {
        // Comparar tanto con Nombre_Departamento como con Departamentos
        if ($key === $nombre_departamento || $key === $departamento_nombre) {
            $departamento_variantes = $variants;
            break;
        }
    }

    // Si no se encontraron variantes, usar el nombre original
    if (empty($departamento_variantes)) {
        $departamento_variantes = [$nombre_departamento, $departamento_nombre];
    }

    // Preparar la consulta usando prepared statements
    $placeholders = str_repeat('?,', count($departamento_variantes) - 1) . '?';
    $sql_todos_profesores = "SELECT DISTINCT 
                            Codigo, 
                            Nombre_completo, 
                            Categoria_actual,
                            Departamento
                            FROM coord_per_prof 
                            WHERE Departamento IN ($placeholders)
                            ORDER BY Nombre_completo";
    
    $stmt_profesores = $conexion->prepare($sql_todos_profesores);
    if (!$stmt_profesores) {
        throw new Exception("Error en la preparación de la consulta de profesores: " . $conexion->error);
    }

    // Bind de los parámetros dinámicamente
    $tipos = str_repeat('s', count($departamento_variantes));
    $stmt_profesores->bind_param($tipos, ...$departamento_variantes);
    $stmt_profesores->execute();
    $result_todos_profesores = $stmt_profesores->get_result();

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
        <div class="encabezado-izquierda">
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
                    $sql_departamentos = "SELECT DISTINCT Departamento_ID, Departamentos FROM departamentos ORDER BY Departamentos";
            $result_departamentos = mysqli_query($conexion, $sql_departamentos);
            
            while($row = mysqli_fetch_assoc($result_departamentos)) {
                $checked = ($row['Departamento_ID'] == $departamento_id) ? 'checked' : '';
                echo "<li>
                    <input type='checkbox' name='departamentos[]' 
                           value='" . $row['Departamento_ID'] . "' 
                           " . $checked . "
                           onchange='actualizarTablaProfesores()'/>
                    " . htmlspecialchars($row['Departamentos']) . "
                    </li>";
            }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="contenedor-tabla">
        <div class="contenido-tabla">
            <div class="profesores-container">
                <table class="profesores-table">
                    <thead>
                        <tr>
                            <th class="detalle-column">count</th>
                            <th class="detalle-column">Código</th>
                            <th class="detalle-column">Nombre Completo</th>
                            <th class="detalle-column">Categoria Actual</th>
                            <th class="detalle-column">Departamento</th>
                            <th class="detalle-column">Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_todos_profesores) {
                            $contador = 1;
                            while($row = $result_todos_profesores->fetch_assoc()) {
                                echo "<tr class='tr-info'>";
                                echo "<td class='detalle-column detalle-column1'>" . htmlspecialchars($contador) . "</td>";
                                echo "<td class='detalle-column detalle-column1'>" . htmlspecialchars($row['Codigo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Nombre_completo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Categoria_actual'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Departamento'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column detalle-column2'>
                                        <button onclick='verDetalleProfesor(" . $row['Codigo'] . ", " . $departamento_id . ")' 
                                                class='btn-detalle'>Ver detalle</button>
                                    </td>";
                                echo "</tr>";
                                $contador = $contador + 1;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No se encontraron profesores</td></tr>";
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
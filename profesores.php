<?php
require_once './config/db.php';
require_once './config/sesioniniciada.php';
require_once './functions/profesores/funciones-horas.php';

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
            'Est. Regionales',
            'Estudios_Regionales'
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
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS',
            'Ciencias_Sociales'
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
            'RECURSOS_HUMANOS',
            'Recursos_Humanos'
        ],
        'Métodos Cuantitativos' => [
            'METODOS CUANTITATIVOS',
            'Métodos Cuantitativos',
            'Metodos Cuantitativos',
            'Métodos_Cuantitativos'
        ],
        'Políticas Públicas' => [
            'POLITICAS PUBLICAS',
            'Políticas Públicas',
            'Politicas Publicas',
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS ',
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS',
            'Políticas_Públicas'
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
            'AUDITORIA ',
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
            'Sistemas de Informacion',
            'Sistemas_de_Información'
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

    // Funciones auxiliares para determinar la clase CSS
    function getHorasClass($actual, $esperado) {
        if ($esperado == 0 && $actual == 0) return 'horas-cero';
        if ($actual < $esperado) return 'horas-faltantes';
        if ($actual == $esperado) return 'horas-correctas';
        return 'horas-excedidas';
    }

    function getSumaHorasSegura($codigo_profesor, $conexion) {
        if ($codigo_profesor === null) {
            return [0, 0, 0, 0, 0, 'Sin datos', 'Sin datos', 'Sin datos'];
        }
        
        $departamentos = mysqli_query($conexion, "SELECT Nombre_Departamento, Departamentos FROM departamentos");
        $suma_cargo_plaza = 0;
        $suma_horas_definitivas = 0;
        $suma_horas_temporales = 0;
        $horas_por_depto_cargo = [];
        $horas_por_depto_def = [];
        $horas_por_depto_temp = [];
        
        while ($dept = mysqli_fetch_assoc($departamentos)) {
            $tabla = "data_" . $dept['Nombre_Departamento'];
            $query = "SELECT HORAS, TIPO_CONTRATO FROM $tabla WHERE CODIGO_PROFESOR = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("s", $codigo_profesor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $suma_dept_cargo = 0;
            $suma_dept_def = 0;
            $suma_dept_temp = 0;
            
            while($row = $result->fetch_assoc()) {
                // Add null check before trim()
                $tipo_contrato = isset($row['TIPO_CONTRATO']) ? strtolower(trim($row['TIPO_CONTRATO'])) : '';
                $horas = !empty($row['HORAS']) ? intval($row['HORAS']) : 2;
                
                if ($tipo_contrato === 'cargo a plaza') {
                    $suma_dept_cargo += $horas;
                    $suma_cargo_plaza += $horas;
                } elseif ($tipo_contrato === 'horas definitivas') {
                    $suma_dept_def += $horas;
                    $suma_horas_definitivas += $horas;
                } elseif ($tipo_contrato === 'asignatura') {
                    $suma_dept_temp += $horas;
                    $suma_horas_temporales += $horas;
                }
            }
            
            // Guardar las horas por departamento si hay alguna
            if ($suma_dept_cargo > 0) {
                $horas_por_depto_cargo[] = $dept['Departamentos'] . ": " . $suma_dept_cargo;
            }
            if ($suma_dept_def > 0) {
                $horas_por_depto_def[] = $dept['Departamentos'] . ": " . $suma_dept_def;
            }
            if ($suma_dept_temp > 0) {
                $horas_por_depto_temp[] = $dept['Departamentos'] . ": " . $suma_dept_temp;
            }
            
            $stmt->close();
        }
        
        // Convertir arrays a strings
        $horas_cargo_str = !empty($horas_por_depto_cargo) ? implode("\n", $horas_por_depto_cargo) : 'Sin secciones registradas';
        $horas_def_str = !empty($horas_por_depto_def) ? implode("\n", $horas_por_depto_def) : 'Sin secciones registradas';
        $horas_temp_str = !empty($horas_por_depto_temp) ? implode("\n", $horas_por_depto_temp) : 'Sin secciones registradas';
        
        // Consultar horas frente a grupo y definitivas de la base de datos
        $query_horas = "SELECT Horas_frente_grupo, Horas_definitivas FROM coord_per_prof WHERE Codigo = ?";
        $stmt = $conexion->prepare($query_horas);
        $stmt->bind_param("s", $codigo_profesor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $horas_frente_grupo = $row ? intval($row['Horas_frente_grupo']) : 0;
        $horas_definitivasDB = $row ? intval($row['Horas_definitivas']) : 0;
        
        return [
            $suma_cargo_plaza,
            $suma_horas_definitivas,
            $suma_horas_temporales,
            $horas_frente_grupo,
            $horas_definitivasDB,
            $horas_cargo_str,
            $horas_def_str,
            $horas_temp_str
        ];
    }

    // Función para formatear el contenido del tooltip
    function formatearHorasDepartamento($horasString) {
        if (empty($horasString) || $horasString === 'Sin secciones registradas') {
            return 'Sin horas registradas';
        }
        return str_replace("\n", "<br>", htmlspecialchars($horasString));
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
                            <th class="detalle-column col-codigo th-L">Código</th>
                            <th class="detalle-column col-nombre">Nombre Completo</th>
                            <th class="detalle-column col-categoria">Categoria Actúal</th>
                            <th class="detalle-column col-depto">Departamento</th>
                            <th class="detalle-column col-horas-f">Horas frente a grupo</th>
                            <th class="detalle-column col-horas-d">Horas Definitivas</th>
                            <th class="detalle-column col-horas-t">Horas temporales</th>
                            <th class="detalle-column col-detalle th-R">Detalles del Profesor</th>
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
                                $codigo_profesor = $row['Codigo'];
                                
                                // Obtener todas las horas
                                list($suma_cargo_plaza, $suma_horas_definitivas, $suma_horas_temporales, 
                                    $horas_frente_grupo, $horas_definitivasDB, $horas_por_depto_cargo,
                                    $horas_por_depto_def, $horas_por_depto_temp) = 
                                getSumaHorasSegura($codigo_profesor, $conexion);

                                echo "<tr class='tr-info'>";
                                echo "<td class='detalle-column detalle-column1 col-codigo'>" . htmlspecialchars($row['Codigo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column col-nombre'>" . htmlspecialchars($row['Nombre_Completo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column col-categoria'>" . htmlspecialchars($row['Categoria_actual'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column col-depto'>" . htmlspecialchars($departamento_normalizado ?? 'Sin datos') . "</td>";
                                
                                // Horas frente a grupo con tooltip
                                echo "<td class='detalle-column col-horas-f'>";
                                echo "<div class='tooltip'>";
                                echo "<span class='" . getHorasClass($suma_cargo_plaza, $horas_frente_grupo) . "'>" . 
                                    $suma_cargo_plaza . "/" . $horas_frente_grupo . "</span>";
                                echo "<span class='tooltiptext'>" . formatearHorasDepartamento($horas_por_depto_cargo) . "</span>";
                                echo "</div></td>";
                                
                                // Horas definitivas con tooltip
                                echo "<td class='detalle-column col-horas-d'>";
                                echo "<div class='tooltip'>";
                                echo "<span class='" . getHorasClass($suma_horas_definitivas, $horas_definitivasDB) . "'>" . 
                                    $suma_horas_definitivas . "/" . $horas_definitivasDB . "</span>";
                                echo "<span class='tooltiptext'>" . formatearHorasDepartamento($horas_por_depto_def) . "</span>";
                                echo "</div></td>";
                                
                                // Horas temporales con tooltip
                                echo "<td class='detalle-column col-horas-t'>";
                                echo "<div class='tooltip'>";
                                echo "<span class='horas-temporales dept-otros'>" . $suma_horas_temporales . "</span>";
                                echo "<span class='tooltiptext'>" . formatearHorasDepartamento($horas_por_depto_temp) . "</span>";
                                echo "</div></td>";
                                
                                echo "<td class='detalle-column detalle-column2 col-detalle'><button onclick='verDetalleProfesor(" . $row['Codigo'] . ")' class='btn-detalle'>Ver detalle</button></td>";
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
    const sessionDepartment = "<?php 
        $normalized_dept = str_replace('_', ' ', normalizeDepartmentName($nombre_departamento));
        echo htmlspecialchars($normalized_dept, ENT_QUOTES); 
    ?>";
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
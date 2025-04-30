<?php
require_once './config/db.php';
require_once './template/header.php';
require_once './template/navbar.php';
require_once './config/sesiones.php';
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
    if ($rol == 1 || $rol == 4) {
        $departamento_id = $_SESSION['Departamento_ID'];
    } elseif ($rol == 2 || $rol == 3 || $rol == 0) {
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

    // Añadir esta condición para el título según el rol
    $titulo_departamento = ($rol == 0) ? "Administrador" : $departamento_nombre;

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
        'Auditoría' => [
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
    function normalizeDepartmentName($dbDepartment)
    {
        global $departmentMapping;

        foreach ($departmentMapping as $standardName => $variations) {
            if (in_array($dbDepartment, $variations)) {
                return $standardName;
            }
        }

        return $dbDepartment; // Return original if no mapping found
    }

    // Funciones auxiliares para determinar la clase CSS
    function getHorasClass($actual, $esperado)
    {
        if ($esperado == 0 && $actual == 0) return 'horas-cero';
        if ($actual < $esperado) return 'horas-faltantes';
        if ($actual == $esperado) return 'horas-correctas';
        return 'horas-excedidas';
    }

    // Función para formatear el contenido del tooltip
    function formatearHorasDepartamento($horasString)
    {
        if (empty($horasString) || $horasString === 'Sin secciones registradas') {
            return 'Sin horas registradas';
        }
        return str_replace("\n", "<br>", htmlspecialchars($horasString));
    }

?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Profesores - <?php echo htmlspecialchars($titulo_departamento); ?></title>
        <link rel="stylesheet" href="./CSS/profesores/modal-profesores.css?v=<?php echo filemtime('./CSS/profesores/modal-profesores.css'); ?>">
        <link rel="stylesheet" href="./CSS/profesores/detalle-profesor.css?v=<?php echo filemtime('./CSS/profesores/detalle-profesor.css'); ?>">
        <!-- Script para mostrar el loader inmediatamente -->
        <script>
            // Esta función se ejecuta inmediatamente, antes de que se cargue el resto del contenido
            (function() {
                document.write('<div id="loading-overlay"><div class="loading-content"><div class="spinner"></div><p>Cargando profesores...</p></div></div>');
            })();

            // Desaparece la tabla en lo que cargan todos los datos:
            window.addEventListener("load", function() {
                document.getElementById("loading-overlay").style.display = "none"; // Oculta el loading
                document.querySelector(".profesores-container").style.display = "block"; // Muestra el contenido
            });
        </script>
    </head>

    <body>

        <div class="cuadro-principal">
            <div class="encabezado">
                <div class="encabezado-izquierda" style="display: flex; align-items: center;">
                    <!-- Barra de búsqueda -->
                    <div class="search-bar">
                        <div class="search-input-container">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="text" placeholder="Buscar..." id="buscar-todos-profesores" onkeyup="filtrarTodosProfesores()">
                        </div>
                    </div>
                </div>
                <div class="encabezado-centro">
                    <h3>Profesores - <?php echo htmlspecialchars($titulo_departamento); ?></h3>
                </div>
                <div class="encabezado-derecha">
                    <div id="list1" class="dropdown-check-list" tabindex="100">
                        <span class="anchor">Departamento: </span>
                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                        <ul class="items">
                            <li><input type="checkbox" />Administración</li>
                            <li><input type="checkbox" />Auditoría</li>
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
                                    <!-- Los elementos tienen id's para modificar el atributo "display" -->
                                    <th class="detalle-column col-codigo th-L">Código</th>
                                    <th class="detalle-column col-nombre">Nombre Completo</th>
                                    <th class="detalle-column col-categoria" id="title-categoria">Categoría Actual</th>
                                    <th class="detalle-column col-depto">Departamento</th>
                                    <th class="detalle-column col-horas-f" id="title-horas-f">Horas frente a grupo</th>
                                    <th class="detalle-column col-horas-d" id="title-horas-d">Horas Definitivas</th>
                                    <th class="detalle-column col-horas-t" id="title-horas-t">Horas temporales</th>
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

                                if ($result_todos_profesores && mysqli_num_rows($result_todos_profesores) > 0) {
                                    $contador = 1;
                                    while ($row = mysqli_fetch_assoc($result_todos_profesores)) {
                                        $departamento_normalizado = normalizeDepartmentName($row['Departamento']);
                                        $codigo_profesor = $row['Codigo'];

                                        // Obtener todas las horas
                                        list(
                                            $suma_cargo_plaza,
                                            $suma_horas_definitivas,
                                            $suma_horas_temporales,
                                            $horas_frente_grupo,
                                            $horas_definitivasDB,
                                            $horas_por_depto_cargo,
                                            $horas_por_depto_def,
                                            $horas_por_depto_temp
                                        ) =
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
                                    echo "<tr id='no-data-row'><td colspan='8'>No hay información disponible</td></tr>";
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

        <script src="./JS/profesores/detalle-profesor.js?v=<?php echo filemtime('./JS/profesores/detalle-profesor.js'); ?>"></script>
        <script src="./JS/profesores/filtro-profesores.js?v=<?php echo filemtime('./JS/profesores/filtro-profesores.js'); ?>"></script>
        <script src="./JS/profesores/desplegable-box.js?v=<?php echo filemtime('./JS/profesores/desplegable-box.js'); ?>"></script>
        <script src="./JS/profesores/filtro-departamentos.js?v=<?php echo filemtime('./JS/profesores/filtro-departamentos.js'); ?>"></script>
        <script src="./JS/profesores/carga-profesores.js?v=<?php echo filemtime('./JS/profesores/carga-profesores.js'); ?>"></script>
        <script src="./JS/profesores/responsividad-tabla-profesores.js?v=<?php echo filemtime('./JS/profesores/responsividad-tabla-profesores.js'); ?>"></script>
        <script>
            // Pass the session department to JavaScript
            const sessionDepartment = "<?php
                                        $normalized_dept = str_replace('_', ' ', normalizeDepartmentName($nombre_departamento));
                                        echo htmlspecialchars($normalized_dept, ENT_QUOTES);
                                        ?>";
            const isPosgrados = "<?php echo ($nombre_departamento === 'Posgrados') ? 'true' : 'false'; ?>";
            const userRol = "<?php echo $_SESSION['Rol_ID']; ?>";
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
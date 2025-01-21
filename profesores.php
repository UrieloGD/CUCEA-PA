<?php
require_once './config/db.php';
require_once './config/sesioniniciada.php';
?>

<?php include './template/header.php' ?>
<?php include './template/navbar.php' ?>

<?php
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
// Verificar el rol del usuario
$rol = $_SESSION['Rol_ID'];
$departamento_id = null;

// Lógica para seleccionar el departamento
if ($rol == 1) {
    // Para jefes de departamento, usar su departamento asignado
    $departamento_id = $_SESSION['Departamento_ID'];
} elseif ($rol == 2 || $rol == 3) {
    // Para roles 2 y 3, permitir selección de departamento
    if (isset($_GET['departamento_id'])) {
        // Si se proporciona un departamento_id específico
        $departamento_id = (int)$_GET['departamento_id'];
    } else {
        // Si no se proporciona, seleccionar el primer departamento
        $sql_primer_departamento = "SELECT Departamento_ID FROM departamentos ORDER BY Departamento_ID LIMIT 1";
        $result_primer_departamento = mysqli_query($conexion, $sql_primer_departamento);
        
        if ($result_primer_departamento && mysqli_num_rows($result_primer_departamento) > 0) {
            $row_primer_departamento = mysqli_fetch_assoc($result_primer_departamento);
            $departamento_id = $row_primer_departamento['Departamento_ID'];
        } else {
            die("No se encontraron departamentos disponibles.");
        }
    }
} else {
    die("Rol de usuario no autorizado.");
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
?>

<!-- Modal para listar todos los profesores del departamento -->
<title>Profesores - <?php echo $departamento_nombre; ?></title>
<link rel="stylesheet" href="./CSS/modal-profesores.css">

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
        </div>
    </div>
    <div class="contenedor-tabla">
        <div class="contenido-tabla">
            <!-- Tabla de profesores -->
            <div class="profesores-container">
                <table class="profesores-table">
                    <thead>
                        <tr>
                            <th class="detalle-column">Código</th>
                            <th class="detalle-column">Nombre Completo</th>
                            <th class="detalle-column">Categoria Actúal</th>
                            <th class="detalle-column">Detalles del Profesor</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php
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
                                'Ciencias Sociales'
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
                            'Posgrados' => [
                                'POSGRADOS',
                                'Posgrados'
                            ]
                        ];

                        // Encontrar todas las variantes del departamento actual
                        $departamento_variantes = [];
                        foreach ($departamentos_mapping as $key => $variants) {
                            if ($key === $nombre_departamento) {
                                $departamento_variantes = $variants;
                                break;
                            }
                        }

                        // Crear la condición WHERE para la consulta SQL
                        $where_conditions = [];
                        foreach ($departamento_variantes as $variante) {
                            $where_conditions[] = "Departamento = '" . mysqli_real_escape_string($conexion, $variante) . "'";
                        }
                        $where_clause = count($where_conditions) > 0 ? implode(' OR ', $where_conditions) : "1=0";

                        // Consulta SQL con las variantes del departamento
                        $sql_todos_profesores = "SELECT DISTINCT Codigo, Nombre_Completo, Categoria_actual 
                                            FROM coord_per_prof 
                                            WHERE $where_clause
                                            ORDER BY Nombre_Completo";
                        
                        $result_todos_profesores = mysqli_query($conexion, $sql_todos_profesores);
                        
                        if ($result_todos_profesores) {
                            while($row = mysqli_fetch_assoc($result_todos_profesores)) {
                                echo "<tr class='tr-info'>";
                                echo "<td class='detalle-column detalle-column1'>" . htmlspecialchars($row['Codigo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Nombre_Completo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Categoria_actual'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column detalle-column2'><button onclick='verDetalleProfesor(" . $row['Codigo'] . ")' class='btn-detalle'>Ver detalle</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Error en la consulta: " . mysqli_error($conexion) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar información detallada del profesor -->
<div id="modal-detalle-profesor" class="modal-detalle">
    <div class="modal-content-detalle">
        <!--<h2>Detalle del Profesor</h2>-->
        <div id="detalle-profesor-contenido">
            <span class="close" onclick="cerrarModalDetalle()">&times;</span>
            <!--El contenido se cargará dinámicamente -->
        </div> 
    </div>
</div>

<!-- Scripts de la librería DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js"></script>
<script src="https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.js"></script>
<script src="https://cdn.datatables.net/colreorder/2.0.4/js/colReorder.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.2/js/dataTables.fixedColumns.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.2/js/fixedColumns.dataTables.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>

<script src="./JS/profesores/detalle-profesor.js"></script>
<script src="./JS/profesores/filtro-profesores.js"></script>

<?php include("./template/footer.php"); ?>
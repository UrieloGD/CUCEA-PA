<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verify user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include header and navbar
include './template/header.php';
include './template/navbar.php';

// Get department ID based on user role
$rol = $_SESSION['Rol_ID'];
$usuario_id = $_SESSION['Codigo'] ?? 0;

if ($rol == 0) { // Admin
    $departamento_id = isset($_GET['Departamento_ID']) ? (int)$_GET['Departamento_ID'] : 1;
} elseif ($rol == 1 || $rol == 4) { // Department heads
    $departamento_id = $_SESSION['Departamento_ID'];
} elseif ($rol == 2 || $rol == 3) { // Other roles
    if (isset($_GET['Departamento_ID'])) {
        $departamento_id = (int)$_GET['Departamento_ID'];
    } else {
        // Select first department if none specified
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

// Get department information
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();

if (!$result_departamento || $result_departamento->num_rows == 0) {
    die("No se encontró información del departamento.");
}

$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento_nombre = $row_departamento['Departamentos'];

// Prepare table name for the department
$tabla_departamento = "data_" . $nombre_departamento;

// Get data from the department's table
$sql = "SELECT * FROM $tabla_departamento WHERE Departamento_ID = ? AND PAPELERA = 'activo'";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta de datos: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

// Convert data to JSON format for Tabulator
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Close the database connection
mysqli_close($conexion);
?>

<title>Data - <?php echo $departamento_nombre; ?></title>

<?php include './functions/basesdedatos/modal-descargar-excel/modal-descargar-excel.php'; ?>

<!-- Incluir Tabulator CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/tabulator-tables@5.5.2/dist/css/tabulator.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<!-- Tema BULMA para Tabulator-->
<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator_bulma.min.css" rel="stylesheet">

<!-- Custom CSS for the table -->
<link rel="stylesheet" href="./CSS/basesdedatos/basesdedatos.css?v=<?php echo filemtime('./CSS/basesdedatos/basesdedatos.css'); ?>" />

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="encabezado-izquierda" style="display: flex; align-items: center;">
            <!-- <div class="barra-buscador" id="barra-buscador">
                <div class="icono-buscador" id="icono-buscador">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
                <input type="text" id="input-buscador" placeholder="Buscar...">
            </div> -->
        </div>
        <div class="encabezado-centro">
            <h3>Data - <?php echo $departamento_nombre; ?></h3>
        </div>
        <div class="encabezado-derecha">
            <div class="iconos-container">
                <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()" data-tooltip="Guardar cambios">
                    <i class="fa fa-save" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()" data-tooltip="Deshacer cambios">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-papelera" onclick="mostrarModalRegistrosEliminados()" data-tooltip="Ver registros eliminados">
                    <i class="fa fa-trash-restore" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-visibilidad" data-tooltip="Mostrar/ocultar columnas">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-filtro" data-tooltip="Mostrar/ocultar filtros">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()" data-tooltip="Añadir nuevo registro">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <!-- <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div> -->
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()" data-tooltip="Eliminar registros seleccionados">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarDescargarExcel()" data-tooltip="Descargar Excel">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Inputs ocultos para pasar datos a JavaScript -->
    <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>" data-user-role="<?php echo $rol; ?>">
    <input type="hidden" id="user-role" value="<?php echo $rol; ?>">
    <input type="hidden" id="table-data" value="<?php echo htmlspecialchars(json_encode($data)); ?>">

    <div id="tabla-datos"></div>
</div>

<script>
    // Inicializar la tabla cuando el DOM esté listo
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener los datos desde el input oculto
        const tableDataElement = document.getElementById('table-data');
        const tableData = JSON.parse(tableDataElement.value);

        // Inicializar Tabulator
        const table = initializeTabulator(tableData);

        // Configurar eventos de los iconos
        setupTableEvents(table);
    });
</script>

<!-- Archivos JavaScript -->
<script src="./JS/basesdedatos/descargar-data-excel.js?v=<?php echo time(); ?>"></script>
<script src="./JS/basesdedatos/tabulator-edit-manager.js?v=<?php echo time(); ?>"></script>
<script src="./JS/basesdedatos/tabulator-config.js?v=<?php echo time(); ?>"></script>

<?php include("./template/footer.php"); ?>
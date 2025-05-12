<?php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 0) {
    header("Location: home.php");
    exit();
}
?>

<?php include './template/header.php' ?>
<?php include './template/navbar.php' ?>
<?php
require_once './config/db.php';


// Obtener el total de horas asignadas
$tabla_departamento = "coord_per_prof";

function getTotalAssignedHours($conexion, $codigo_profesor)
{
    $tablas_departamentos = [
        'data_administración',
        'data_auditoría',
        'data_ciencias_sociales',
        'data_contabilidad',
        'data_economía',
        'data_estudios_regionales',
        'data_finanzas',
        'data_impuestos',
        'data_mercadotecnia',
        'data_métodos_cuantitativos',
        'data_pale',
        'data_políticas_públicas',
        'data_posgrados',
        'data_recursos_humanos',
        'data_sistemas_de_información',
        'data_turismo'
    ];

    $total_horas = 0;

    foreach ($tablas_departamentos as $tabla) {
        $sql = "SELECT SUM(CAST(HORAS AS UNSIGNED)) AS horas 
                FROM $tabla 
                WHERE CODIGO_PROFESOR = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $codigo_profesor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_horas += $row['horas'] ? intval($row['horas']) : 0;
        $stmt->close();
    }

    return $total_horas;
}

// Consulta SQL para obtener solo registros activos
$sql = "SELECT * FROM $tabla_departamento WHERE Papelera = 'activo'";
$result = mysqli_query($conexion, $sql);

// Función para convertir fechas de Excel a MySQL
function convertExcelDate($value)
{
    if (!is_numeric($value)) {
        return $value;
    }
    $unix_date = ($value - 25569) * 86400;
    return date("Y-m-d", $unix_date);
}

// Función para convertir fechas de MySQL a formato de visualización
function formatDateForDisplay($mysqlDate)
{
    if (!$mysqlDate || $mysqlDate == '0000-00-00') {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $mysqlDate);
    return $date ? $date->format('d/m/Y') : '';
}

// Función para implementar el soft delete
function softDeleteRegistros($conexion, $ids)
{
    $ids = array_map('intval', $ids);
    $ids_str = implode(',', $ids);

    $sql = "UPDATE coord_per_prof 
            SET Papelera = 'inactivo', 
                Fecha_Modificacion = CURRENT_TIMESTAMP 
            WHERE ID IN ($ids_str)";

    return mysqli_query($conexion, $sql);
}

// Función para restaurar registros
// function restaurarRegistros($conexion, $ids) {
//     $ids = array_map('intval', $ids);
//     $ids_str = implode(',', $ids);

//     $sql = "UPDATE coord_per_prof 
//             SET Papelera = 'activo', 
//                 Fecha_Modificacion = CURRENT_TIMESTAMP 
//             WHERE ID IN ($ids_str)";

//     return mysqli_query($conexion, $sql);
// }

?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<title>Coordinación de Personal - Plantilla Académica</title>

<!-- Script de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Mostrar el loader inmediatamente
    window.addEventListener('load', function() {
        Swal.close(); // Cerrar el loader cuando todo esté cargado
    });

    // Mostrar Sweet Alert inmediatamente
    Swal.fire({
        title: 'Cargando datos...',
        html: 'Por favor espere mientras se procesan los datos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
</script>

<!-- Tabulator CSS Plugins -->
<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator_bulma.min.css" rel="stylesheet">

<!-- Tabulator JS -->
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>

<!-- CSS base -->
<link rel="stylesheet" href="./CSS/coord-pers/basesdedatos-Coord.css?v=<?php echo filemtime('./CSS/coord-pers/basesdedatos-Coord.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/coord-papelera.css?v=<?php echo filemtime('./CSS/coord-pers/coord-papelera.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/modal-anadir-registro.css?v=<?php echo filemtime('./CSS/coord-pers/modal-anadir-registro.css'); ?>">


<div class="cuadro-principal">
    <div class="cuadro-scroll">
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
            <h3>Plantilla Académica - Coordinación de Personal</h3>
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

    <?php
    // Verificar rol antes de mostrar la tabla editable
    if ($_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 0) {
        // Deshabilitar edición o mostrar mensaje
        $tabla_editable = false;
    }
    ?>

    <!-- <div class="Tabla datatable-container">
        <div class="table-container"> -->
            <div id="tabla-datos-tabulator"></div> <!-- Solo este elemento es necesario para Tabulator -->
        </div>
    </div>

    <?php include './functions/coord-personal-plantilla/modal-descargar-excel/modal-descargar-excel.php'; ?>
    <?php include './functions/coord-personal-plantilla/modal-anadir-registro/modal-anadir-registro.php'; ?>
    <?php include './functions/coord-personal-plantilla/registros-eliminados/modal-registros-eliminados-cp.php'; ?>


    <!-- Linea que valida el rol id del usuario para mandarlo a JS -->
    <input type="hidden" id="user-role" value="<?php echo $_SESSION['Rol_ID']; ?>">

    <!-- jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personalizados-->
    <!-- <script src="./JS/plantilla-CoordPers/tabla-editable-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/tabla-editable-coord.js'); ?>"></script> -->
    <script src="./JS/plantilla-CoordPers/eliminar-registro-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/eliminar-registro-coord.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/modal-eliminados-coord-pers.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/modal-eliminados-coord-pers.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/anadir-profesor.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/anadir-profesor.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/descargar-data-excel-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/descargar-data-excel-coord.js'); ?>"></script>
    <script src="./JS/plantilla-CoordPers/inicializar-tablas-cp.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/inicializar-tablas-cp.js'); ?>"></script>

    <!-- Script para cambiar el encabezado por responsividad -->
    <script>
        window.addEventListener("resize", function() {
            var tituloContainer = document.querySelector(".encabezado-centro");
            if (window.innerWidth <= 768) {
                tituloContainer.innerHTML = "<h3>Plantilla Académica (C.P)</h3>";
            } else {
                tituloContainer.innerHTML = "<h3>Plantilla Académica - Coordinación de Personal</h3>";
            }
        });
    </script>

    <?php include("./template/footer.php"); ?>
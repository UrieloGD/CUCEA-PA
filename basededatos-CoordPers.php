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

<!-- CSS base -->
<link rel="stylesheet" href="./CSS/coord-pers/basesdedatos-Coord.css?v=<?php echo filemtime('./CSS/coord-pers/basesdedatos-Coord.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/coord-papelera.css?v=<?php echo filemtime('./CSS/coord-pers/coord-papelera.css'); ?>">
<link rel="stylesheet" href="./CSS/coord-pers/modal-anadir-registro.css?v=<?php echo filemtime('./CSS/coord-pers/modal-anadir-registro.css'); ?>">

<!-- DataTables CSS Core -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">

<!-- DataTables CSS Plugins -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">

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

    <div class="Tabla datatable-container">
        <div class="table-container">
            <div class="custom-search-container"></div>
            <table id="tabla-datos" class="display">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID <span class="filter-icon" data-column="1"><i class="fas fa-filter"></i></span></th>
                        <th>DATOS <span class="filter-icon" data-column="2"><i class="fas fa-filter"></i></span></th>
                        <th>CODIGO <span class="filter-icon" data-column="3"><i class="fas fa-filter"></i></span></th>
                        <th>PATERNO <span class="filter-icon" data-column="4"><i class="fas fa-filter"></i></span></th>
                        <th>MATERNO <span class="filter-icon" data-column="5"><i class="fas fa-filter"></i></span></th>
                        <th>NOMBRES <span class="filter-icon" data-column="6"><i class="fas fa-filter"></i></span></th>
                        <th>NOMBRE COMPLETO <span class="filter-icon" data-column="7"><i class="fas fa-filter"></i></span></th>   
                        <th>DEPARTAMENTO <span class="filter-icon" data-column="9"><i class="fas fa-filter"></i></span></th>
                        <th>CATEGORIA ACTUAL <span class="filter-icon" data-column="10"><i class="fas fa-filter"></i></span></th>
                        <th>CATEGORIA ACTUAL <span class="filter-icon" data-column="11"><i class="fas fa-filter"></i></span></th>
                        <th>HORAS FRENTE A GRUPO <span class="filter-icon" data-column="12"><i class="fas fa-filter"></i></span></th>
                        <th>DIVISION <span class="filter-icon" data-column="13"><i class="fas fa-filter"></i></span></th>
                        <th>TIPO DE PLAZA <span class="filter-icon" data-column="14"><i class="fas fa-filter"></i></span></th>
                        <th>CAT.ACT. <span class="filter-icon" data-column="15"><i class="fas fa-filter"></i></span></th>
                        <th>CARGA HORARIA <span class="filter-icon" data-column="16"><i class="fas fa-filter"></i></span></th>
                        <th>HORAS DEFINITIVAS <span class="filter-icon" data-column="17"><i class="fas fa-filter"></i></span></th>
                        <th>UDG VIRTUAL CIT OTRO CENTRO<span class="filter-icon" data-column="18"><i class= "fas fa-filter"></i></span></th>
                        <th>HORARIO <span class="filter-icon" data-column="19"><i class="fas fa-filter"></i></span></th>
                        <th>TURNO <span class="filter-icon" data-column="20"><i class="fas fa-filter"></i></span></th>
                        <th>INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION <span class="filter-icon" data-column="21"><i class="fas fa-filter"></i></span></th>
                        <th>S.N.I. <span class="filter-icon" data-column="22"><i class="fas fa-filter"></i></span></th>
                        <th>SNI DESDE <span class="filter-icon" data-column="23"><i class="fas fa-filter"></i></span></th>
                        <th>CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR <span class="filter-icon" data-column="24"><i class="fas fa-filter"></i></span></th>
                        <th>TELEFONO PARTICULAR <span class="filter-icon" data-column="25"><i class="fas fa-filter"></i></span></th>
                        <th>TELEFONO OFICINA O CELULAR <span class="filter-icon" data-column="26"><i class="fas fa-filter"></i></span></th>
                        <th>DOMICILIO <span class="filter-icon" data-column="27"><i class="fas fa-filter"></i></span></th>
                        <th>COLONIA <span class="filter-icon" data-column="28"><i class="fas fa-filter"></i></span></th>
                        <th>C.P. <span class="filter-icon" data-column="29"><i class="fas fa-filter"></i></span></th>
                        <th>CIUDAD <span class="filter-icon" data-column="30"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO <span class="filter-icon" data-column="31"><i class="fas fa-filter"></i></span></th>
                        <th>NO. AFIL. I.M.S.S. <span class="filter-icon" data-column="32"><i class="fas fa-filter"></i></span></th>
                        <th>C.U.R.P. <span class="filter-icon" data-column="33"><i class="fas fa-filter"></i></span></th>
                        <th>RFC <span class="filter-icon" data-column="34"><i class="fas fa-filter"></i></span></th>
                        <th>LUGAR DE NACIMIENTO <span class="filter-icon" data-column="35"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO CIVIL <span class="filter-icon" data-column="36"><i class="fas fa-filter"></i></span></th>
                        <th>TIPO DE SANGRE <span class="filter-icon" data-column="37"><i class="fas fa-filter"></i></span></th>
                        <th>FECHA NAC. <span class="filter-icon" data-column="38"><i class="fas fa-filter"></i></span></th>
                        <th>EDAD <span class="filter-icon" data-column="39"><i class="fas fa-filter"></i></span></th>
                        <th>NACIONALIDAD <span class="filter-icon" data-column="40"><i class="fas fa-filter"></i></span></th>
                        <th>CORREO ELECTRONICO <span class="filter-icon" data-column="41"><i class="fas fa-filter"></i></span></th>
                        <th>CORREOS OFICIALES <span class="filter-icon" data-column="42"><i class="fas fa-filter"></i></span></th>
                        <th>ULTIMO GRADO <span class="filter-icon" data-column="43"><i class="fas fa-filter"></i></span></th>
                        <th>PROGRAMA <span class="filter-icon" data-column="44"><i class="fas fa-filter"></i></span></th>
                        <th>NIVEL <span class="filter-icon" data-column="45"><i class="fas fa-filter"></i></span></th>
                        <th>INSTITUCION <span class="filter-icon" data-column="46"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO/PAIS <span class="filter-icon" data-column="47"><i class="fas fa-filter"></i></span></th>
                        <th>AÑO <span class="filter-icon" data-column="48"><i class="fas fa-filter"></i></span></th>
                        <th>GDO EXP <span class="filter-icon" data-column="49"><i class="fas fa-filter"></i></span></th>
                        <th>OTRO GRADO <span class="filter-icon" data-column="50"><i class="fas fa-filter"></i></span></th>
                        <th>PROGRAMA <span class="filter-icon" data-column="51"><i class="fas fa-filter"></i></span></th>
                        <th>NIVEL <span class="filter-icon" data-column="52"><i class="fas fa-filter"></i></span></th>
                        <th>INSTITUCION <span class="filter-icon" data-column="53"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO/PAIS <span class="filter-icon" data-column="54"><i class="fas fa-filter"></i></span></th>
                        <th>AÑO <span class="filter-icon" data-column="55"><i class="fas fa-filter"></i></span></th>
                        <th>GDO EXP <span class="filter-icon" data-column="56"><i class="fas fa-filter"></i></span></th>
                        <th>OTRO GRADO <span class="filter-icon" data-column="57"><i class="fas fa-filter"></i></span></th>
                        <th>PROGRAMA <span class="filter-icon" data-column="58"><i class="fas fa-filter"></i></span></th>
                        <th>NIVEL <span class="filter-icon" data-column="59"><i class="fas fa-filter"></i></span></th>
                        <th>INSTITUCION <span class="filter-icon" data-column="60"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO/PAIS <span class="filter-icon" data-column="61"><i class="fas fa-filter"></i></span></th>
                        <th>AÑO <span class="filter-icon" data-column="62"><i class="fas fa-filter"></i></span></th>
                        <th>GDO EXP <span class="filter-icon" data-column="63"><i class="fas fa-filter"></i></span></th>
                        <th>PROESDE 24-25 <span class="filter-icon" data-column="64"><i class="fas fa-filter"></i></span></th>
                        <th>A PARTIR DE <span class="filter-icon" data-column="65"><i class="fas fa-filter"></i></span></th>
                        <th>FECHA DE INGRESO <span class="filter-icon" data-column="66"><i class="fas fa-filter"></i></span></th>
                        <th>ANTIGÜEDAD <span class="filter-icon" data-column="67"><i class="fas fa-filter"></i></span></th>
                        <th class="estado-column">ESTADO <span class="filter-icon" data-column="68"><i class="fas fa-filter"></i></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr data-id='" . htmlspecialchars($row["ID"] ?? '') . "'>";
                            echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . htmlspecialchars($row["ID"] ?? '') . "'></td>";
                            echo "<td>" . htmlspecialchars($row["ID"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Datos"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Codigo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Paterno"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Materno"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Nombres"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Nombre_completo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Departamento"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Categoria_actual"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Categoria_actual_dos"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Horas_frente_grupo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Division"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Tipo_plaza"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Cat_act"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Carga_horaria"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Horas_definitivas"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Udg_virtual_CIT"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Horario"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Turno"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Investigacion_nombramiento_cambio_funcion"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["SNI"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["SNI_desde"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Cambio_dedicacion"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Telefono_particular"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Telefono_oficina"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Domicilio"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Colonia"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["CP"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Ciudad"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Estado"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["No_imss"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["CURP"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["RFC"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Lugar_nacimiento"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Estado_civil"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Tipo_sangre"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Fecha_nacimiento"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Edad"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Nacionalidad"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Correo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Correos_oficiales"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Ultimo_grado"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Programa"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Nivel"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Institucion"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Estado_pais"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Año"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Gdo_exp"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_grado"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_programa"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_nivel"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_institucion"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_estado_pais"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_año"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_gdo_exp"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_grado_alternativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_programa_alternativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_nivel_altenrativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_institucion_alternativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_estado_pais_alternativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_año_alternativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Otro_gdo_exp_alternativo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Proesde_24_25"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["A_partir_de"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Fecha_ingreso"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Antiguedad"] ?? '') . "</td>";

                            $total_assigned_hours = getTotalAssignedHours($conexion, $row['Codigo']);
                            $carga_horaria = intval($row['Carga_horaria']);
                            $comparison = $total_assigned_hours . "/" . $carga_horaria;

                            if ($total_assigned_hours > $carga_horaria) {
                                $estado_class = "estado-excedido";
                            } elseif ($total_assigned_hours < $carga_horaria) {
                                $estado_class = "estado-pendiente";
                            } else {
                                $estado_class = "estado-completo";
                            }

                            echo "<td class='estado-cell'><span class='estado-indicator " . $estado_class . "'>" . htmlspecialchars($comparison) . "</span></td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='70'>No hay datos disponibles</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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

    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    <!-- DataTables Plugins-->
    <script src="https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js"></script>
    <script src="https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>

    <!-- Scripts personalizados-->
    <script src="./JS/plantilla-CoordPers/tabla-editable-coord.js?v=<?php echo filemtime('./JS/plantilla-CoordPers/tabla-editable-coord.js'); ?>"></script>
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
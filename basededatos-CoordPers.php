<?php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3) {
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

// Consulta SQL para obtener todos los registros de Coord_Per_Prof
$sql = "SELECT * FROM $tabla_departamento";
$result = mysqli_query($conexion, $sql);

function convertExcelDate($value)
{
    if (!is_numeric($value)) {
        return $value;
    }
    $unix_date = ($value - 25569) * 86400;
    return date("Y-m-d", $unix_date);
}

function formatDateForDisplay($mysqlDate)
{
    if (!$mysqlDate || $mysqlDate == '0000-00-00') {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $mysqlDate);
    return $date ? $date->format('d/m/Y') : '';
}
?>

<!-- CSS de la librería DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.2/css/fixedColumns.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar Sweet Alert al iniciar
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
    });
</script>

<title>Coordinación de Personal - Plantilla Académica</title>
<link rel="stylesheet" href="./CSS/basesdedatos-Coord.css">

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

                <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()">
                    <i class="fa fa-save" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-visibilidad">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-filtro">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <!-- <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div> -->
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarDescargarExcel()">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="Tabla datatable-container">
        <div class="table-container">
            <div class="custom-search-container"></div>
            <table id="tabla-datos" class="display">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID <span class="filter-icon" data-column="1"><i class="fas fa-filter"></i></span></th>
                        <th>CODIGO <span class="filter-icon" data-column="2"><i class="fas fa-filter"></i></span></th>
                        <th>PATERNO <span class="filter-icon" data-column="3"><i class="fas fa-filter"></i></span></th>
                        <th>MATERNO <span class="filter-icon" data-column="4"><i class="fas fa-filter"></i></span></th>
                        <th>NOMBRES <span class="filter-icon" data-column="5"><i class="fas fa-filter"></i></span></th>
                        <th>NOMBRE COMPLETO <span class="filter-icon" data-column="6"><i class="fas fa-filter"></i></span></th>
                        <th>SEXO <span class="filter-icon" data-column="7"><i class="fas fa-filter"></i></span></th>
                        <th>DEPARTAMENTO <span class="filter-icon" data-column="8"><i class="fas fa-filter"></i></span></th>
                        <th>CATEGORIA ACTUAL <span class="filter-icon" data-column="9"><i class="fas fa-filter"></i></span></th>
                        <th>CATEGORIA ACTUAL <span class="filter-icon" data-column="10"><i class="fas fa-filter"></i></span></th>
                        <th>HORAS FRENTE A GRUPO <span class="filter-icon" data-column="11"><i class="fas fa-filter"></i></span></th>
                        <th>DIVISION <span class="filter-icon" data-column="12"><i class="fas fa-filter"></i></span></th>
                        <th>TIPO DE PLAZA <span class="filter-icon" data-column="13"><i class="fas fa-filter"></i></span></th>
                        <th>CAT.ACT. <span class="filter-icon" data-column="14"><i class="fas fa-filter"></i></span></th>
                        <th>CARGA HORARIA <span class="filter-icon" data-column="15"><i class="fas fa-filter"></i></span></th>
                        <th>HORAS DEFINITIVAS <span class="filter-icon" data-column="16"><i class="fas fa-filter"></i></span></th>
                        <th>HORARIO <span class="filter-icon" data-column="17"><i class="fas fa-filter"></i></span></th>
                        <th>TURNO <span class="filter-icon" data-column="18"><i class="fas fa-filter"></i></span></th>
                        <th>INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION <span class="filter-icon" data-column="19"><i class="fas fa-filter"></i></span></th>
                        <th>S.N.I. <span class="filter-icon" data-column="20"><i class="fas fa-filter"></i></span></th>
                        <th>SNI DESDE <span class="filter-icon" data-column="21"><i class="fas fa-filter"></i></span></th>
                        <th>CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR <span class="filter-icon" data-column="22"><i class="fas fa-filter"></i></span></th>
                        <th>INICIO <span class="filter-icon" data-column="23"><i class="fas fa-filter"></i></span></th>
                        <th>FIN <span class="filter-icon" data-column="24"><i class="fas fa-filter"></i></span></th>
                        <th>2024A <span class="filter-icon" data-column="25"><i class="fas fa-filter"></i></span></th>
                        <th>TELEFONO PARTICULAR <span class="filter-icon" data-column="26"><i class="fas fa-filter"></i></span></th>
                        <th>TELEFONO OFICINA O CELULAR <span class="filter-icon" data-column="27"><i class="fas fa-filter"></i></span></th>
                        <th>DOMICILIO <span class="filter-icon" data-column="28"><i class="fas fa-filter"></i></span></th>
                        <th>COLONIA <span class="filter-icon" data-column="29"><i class="fas fa-filter"></i></span></th>
                        <th>C.P. <span class="filter-icon" data-column="30"><i class="fas fa-filter"></i></span></th>
                        <th>CIUDAD <span class="filter-icon" data-column="31"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO <span class="filter-icon" data-column="32"><i class="fas fa-filter"></i></span></th>
                        <th>NO. AFIL. I.M.S.S. <span class="filter-icon" data-column="33"><i class="fas fa-filter"></i></span></th>
                        <th>C.U.R.P. <span class="filter-icon" data-column="34"><i class="fas fa-filter"></i></span></th>
                        <th>RFC <span class="filter-icon" data-column="35"><i class="fas fa-filter"></i></span></th>
                        <th>LUGAR DE NACIMIENTO <span class="filter-icon" data-column="36"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO CIVIL <span class="filter-icon" data-column="37"><i class="fas fa-filter"></i></span></th>
                        <th>TIPO DE SANGRE <span class="filter-icon" data-column="38"><i class="fas fa-filter"></i></span></th>
                        <th>FECHA NAC. <span class="filter-icon" data-column="39"><i class="fas fa-filter"></i></span></th>
                        <th>EDAD <span class="filter-icon" data-column="40"><i class="fas fa-filter"></i></span></th>
                        <th>NACIONALIDAD <span class="filter-icon" data-column="41"><i class="fas fa-filter"></i></span></th>
                        <th>CORREO ELECTRONICO <span class="filter-icon" data-column="42"><i class="fas fa-filter"></i></span></th>
                        <th>CORREOS OFICIALES <span class="filter-icon" data-column="43"><i class="fas fa-filter"></i></span></th>
                        <th>ULTIMO GRADO <span class="filter-icon" data-column="44"><i class="fas fa-filter"></i></span></th>
                        <th>PROGRAMA <span class="filter-icon" data-column="45"><i class="fas fa-filter"></i></span></th>
                        <th>NIVEL <span class="filter-icon" data-column="46"><i class="fas fa-filter"></i></span></th>
                        <th>INSTITUCION <span class="filter-icon" data-column="47"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO/PAIS <span class="filter-icon" data-column="48"><i class="fas fa-filter"></i></span></th>
                        <th>AÑO <span class="filter-icon" data-column="49"><i class="fas fa-filter"></i></span></th>
                        <th>GDO EXP <span class="filter-icon" data-column="50"><i class="fas fa-filter"></i></span></th>
                        <th>OTRO GRADO <span class="filter-icon" data-column="51"><i class="fas fa-filter"></i></span></th>
                        <th>PROGRAMA <span class="filter-icon" data-column="52"><i class="fas fa-filter"></i></span></th>
                        <th>NIVEL <span class="filter-icon" data-column="53"><i class="fas fa-filter"></i></span></th>
                        <th>INSTITUCION <span class="filter-icon" data-column="54"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO/PAIS <span class="filter-icon" data-column="55"><i class="fas fa-filter"></i></span></th>
                        <th>AÑO <span class="filter-icon" data-column="56"><i class="fas fa-filter"></i></span></th>
                        <th>GDO EXP <span class="filter-icon" data-column="57"><i class="fas fa-filter"></i></span></th>
                        <th>OTRO GRADO <span class="filter-icon" data-column="58"><i class="fas fa-filter"></i></span></th>
                        <th>PROGRAMA <span class="filter-icon" data-column="59"><i class="fas fa-filter"></i></span></th>
                        <th>NIVEL <span class="filter-icon" data-column="60"><i class="fas fa-filter"></i></span></th>
                        <th>INSTITUCION <span class="filter-icon" data-column="61"><i class="fas fa-filter"></i></span></th>
                        <th>ESTADO/PAIS <span class="filter-icon" data-column="62"><i class="fas fa-filter"></i></span></th>
                        <th>AÑO <span class="filter-icon" data-column="63"><i class="fas fa-filter"></i></span></th>
                        <th>GDO EXP <span class="filter-icon" data-column="64"><i class="fas fa-filter"></i></span></th>
                        <th>PROESDE 24-25 <span class="filter-icon" data-column="65"><i class="fas fa-filter"></i></span></th>
                        <th>A PARTIR DE <span class="filter-icon" data-column="66"><i class="fas fa-filter"></i></span></th>
                        <th>FECHA DE INGRESO <span class="filter-icon" data-column="67"><i class="fas fa-filter"></i></span></th>
                        <th>ANTIGÜEDAD <span class="filter-icon" data-column="68"><i class="fas fa-filter"></i></span></th>
                        <th class="estado-column">ESTADO <span class="filter-icon" data-column="69"><i class="fas fa-filter"></i></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr data-id='" . htmlspecialchars($row["ID"] ?? '') . "'>";
                            echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . htmlspecialchars($row["ID"] ?? '') . "'></td>";
                            echo "<td>" . htmlspecialchars($row["ID"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Codigo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Paterno"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Materno"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Nombres"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Nombre_completo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Sexo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Departamento"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Categoria_actual"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Categoria_actual_dos"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Horas_frente_grupo"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Division"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Tipo_plaza"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Cat_act"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Carga_horaria"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Horas_definitivas"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Horario"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Turno"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Investigacion_nombramiento_cambio_funcion"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["SNI"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["SNI_desde"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Cambio_dedicacion"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Inicio"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["Fin"] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row["2024A"] ?? '') . "</td>";
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
                        echo "<tr><td colspan='71'>No hay datos disponibles</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para añadir registros -->
    <div id="modal-añadir" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Registrar nuevo profesor</h2>
            <hr style="border: 1px solid #0071b0; width: 99%;">
            <form id="form-añadir-registro">
                <div class="form-container">
                    <div class="form-section">
                        <div class="form-movil">
                            <h3>Datos</h3>

                            <input type="number" id="codigo" name="codigo" placeholder="Código">
                            <input type="text" id="paterno" name="paterno" placeholder="Paterno">
                            <input type="text" id="materno" name="materno" placeholder="Materno">
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre">

                            <input type="text" id="completo" name="completo" placeholder="Nombre Completo">
                            <input type="text" id="sexo" name="sexo" placeholder="Sexo">

                            <input type="text" id="departamento" name="departamento" placeholder="Departamento">
                            <input type="text" id="categoria_actual" name="categoria_actual" placeholder="Categoría Actual">
                            <input type="text" id="categoria_actual_dos" name="categoria_actual_dos" placeholder="Categoría Actual">

                            <input type="text" id="horas_frente_grupo" name="horas_frente_grupo" min="0" placeholder="Horas Frente a Grupo">
                            <input type="text" id="division" name="division" placeholder="División">

                            <input type="text" id="tipo_plaza" name="tipo_plaza" placeholder="Tipo de Plaza">
                            <input type="text" id="cat_act" name="cat_act" placeholder="CAT_ACT">

                            <input type="text" id="carga_horaria" name="carga_horaria" placeholder="Carga Horaria">
                            <input type="text" id="horas_definitivas" name="horas_definitivas" min="0" placeholder="Horas Definitivas ">
                            <input type="text" id="horario" name="horario" placeholder="Horario">
                            <input type="text" id="turno" name="turno" placeholder="Turno">

                            <input type="text" id="investigacion" name="investsigacion" placeholder="Investigación / Nombramiento / Cambio de función">

                            <input type="text" id="sni" name="sni" placeholder="S.N.I">
                            <input type="text" id="sni_desde" name="sni_desde" placeholder="SNI Desde">

                            <input type="text" id="cambio_dediacion" name="cambio_dediacion" placeholder="Cambio de Dedicación">
                            <input type="text" id="inicio" name="inicio" placeholder="Inicio">
                            <input type="text" id="fin" name="fin" placeholder="Fin">
                            <input type="text" id="a_2024" name="a_2024" placeholder="2024A">

                            <input type="text" id="telefono_particular" name="telefono_particular" placeholder="Telefono Particular">
                            <input type="text" id="telefono_oficina" name="telefono_oficina" placeholder="Telefono Oficina o Celuar">

                            <input type="text" id="domicilio" name="domicilio" placeholder="Domicilio">
                            <input type="text" id="colonia" name="colonia" placeholder="Colonia">
                            <input type="text" id="cp" name="cp" min="0" placeholder="C.P">

                            <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad">
                            <input type="text" id="estado" name="estado" placeholder="Estado">

                            <input type="text" id="no_imss" name="no_imss" placeholder="NO. AFIL. I.M.S.S.">
                            <input type="text" id="curp" name="curp" placeholder="C.U.R.P">
                            <input type="text" id="rfc" name="rfc" placeholder="RFC">

                            <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" placeholder="Lugar de Nacimiento">
                            <input type="text" id="estado_civil" name="estado_civil" placeholder="Estado Civil">
                            <input type="text" id="tipo_sangre" name="tipo_sangre" placeholder="Tipo de Sangre">

                            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="Fecha de Nacimiento">
                            <input type="text" id="edad" name="edad" min="0" placeholder="Edad">
                            <input type="text" id="nacionalidad" name="nacionalidad" placeholder="Nacionalidad">

                            <input type="email" id="correo" name="correo" placeholder="Correo Electrónico">
                            <input type="email" id="correos_oficiales" name="correos_oficiales" placeholder="Correos Oficiales">

                            <input type="text" id="ultimo_grado" name="ultimo_grado" placeholder="Último grado">
                            <input type="text" id="programa" name="programa" placeholder="Programa">
                            <input type="text" id="nivel" name="nivel" placeholder="Nivel">
                            <input type="text" id="institucion" name="institucion" placeholder="Institución">

                            <input type="text" id="estado_pais" name="estado_pais" placeholder="Estado/País">
                            <input type="text" id="año" name="año" min="0" placeholder="Año">
                            <input type="text" id="gdo_exp" name="gdo_exp" placeholder="Gdo_Exp">

                            <input type="text" id="otro_grado" name="otro_grado" placeholder="Último grado">
                            <input type="text" id="otro_programa" name="otro_programa" placeholder="Programa">
                            <input type="text" id="otro_nivel" name="otro_nivel" placeholder="Nivel">
                            <input type="text" id="otro_institucion" name="otro_institucion" placeholder="Institución">

                            <input type="text" id="otro_estado_pais" name="otro_estado_pais" placeholder="Estado/País">
                            <input type="text" id="otro_año" name="otro_año" min="0" placeholder="Año">
                            <input type="text" id="otro_gdo_exp" name="otro_gdo_exp" placeholder="Gdo_Exp">

                            <input type="text" id="otro_grado_alternativo" name="otro_grado_alternativo" placeholder="Último grado">
                            <input type="text" id="otro_programa_alternativo" name="otro_programa_alternativo" placeholder="Programa">
                            <input type="text" id="otro_nivel_alternativo" name="otro_nivel_alternativo" placeholder="Nivel">
                            <input type="text" id="otro_institucion_alternativo" name="otro_institucion_alternativo" placeholder="Institución">

                            <input type="text" id="otro_estado_pais_alternativo" name="otro_estado_pais_alternativo" placeholder="Estado/País">
                            <input type="text" id="otro_año_alternativo" name="otro_año_alternativo" min="0" placeholder="Año">
                            <input type="text" id="otro_gdo_exp_alternativo" name="otro_gdo_exp_alternativo" placeholder="Gdo_Exp">

                            <input type="text" id="proesde" name="proesde" placeholder="Proesde 24-25">
                            <input type="text" id="a_partir_de" name="a_partir_de" placeholder="A Partir De">
                            <input type="text" id="fecha_ingreso" name="fecha_ingreso" placeholder="Fecha de Ingreso">
                            <input type="text" id="Antiguedad" name="Antiguedad" placeholder="Antigüedad">

                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="añadirRegistro()">Guardar</button>
                    <button type="button" onclick="cerrarFormularioAñadir()">Descartar</button>
                </div>
            </form>
        </div>
    </div>

    <?php include './functions/coord-personal-plantilla/modal-descargar-excel/modal-descargar-excel.php'; ?>

    <!-- Scripts de la librería DataTables-->
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

    <script src="./JS/plantilla-CoordPers/tabla-editable-coord.js"></script>
    <script src="./JS/plantilla-CoordPers/eliminar-registro-coord.js"></script>
    <script src="./JS/plantilla-CoordPers/anadir-profesor.js"></script>
    <script src="./JS/plantilla-CoordPers/descargar-data-excel-coord.js"></script>
    <script src="./JS/basesdedatos/inicializar-tablas.js"></script>

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
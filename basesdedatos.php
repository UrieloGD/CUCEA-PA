<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir los archivos
require_once './config/db.php';
require_once './config/sesioniniciada.php';
?>

<?php include './template/header.php' ?>
<?php include './template/navbar.php' ?>
<?php
//include './config/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Verificar el rol del usuario
$rol = $_SESSION['Rol_ID'];

// Lógica para seleccionar el departamento
if ($rol == 1) {
    // Para jefes de departamento, usar su departamento asignado
    $departamento_id = $_SESSION['Departamento_ID'];
} elseif ($rol == 2 || $rol == 3) {
    // Para roles 2 y 3, permitir selección de departamento
    if (isset($_GET['Departamento_ID'])) {  // Cambiado a Departamento_ID con mayúsculas
        // Si se proporciona un Departamento_ID específico
        $departamento_id = (int)$_GET['Departamento_ID'];
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

// Consulta para obtener información del departamento
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();

if (!$result_departamento) {
    die("Query failed: " . $conexion->error);
}

if (!$conexion) {
    die("Connection failed: " . mysqli_connect_error());
}

// Función para verificar choques (añadir al inicio del archivo, antes de generar la tabla)
function verificarChoques($registro_actual, $departamentos, $conexion)
{
    $choques = [];
    $departamento_actual = $registro_actual['Departamento'];

    foreach ($departamentos as $nombre_dep => $registros) {
        if ($nombre_dep == $departamento_actual) continue;

        foreach ($registros as $registro) {
            $choque_horario = (
                ($registro_actual['HORA_INICIAL'] >= $registro['HORA_INICIAL'] &&
                    $registro_actual['HORA_INICIAL'] < $registro['HORA_FINAL']) ||
                ($registro_actual['HORA_FINAL'] > $registro['HORA_INICIAL'] &&
                    $registro_actual['HORA_FINAL'] <= $registro['HORA_FINAL'])
            );

            $dias_semana = ['L', 'M', 'I', 'J', 'V', 'S', 'D'];
            $dias_choque = false;

            foreach ($dias_semana as $dia) {
                if (
                    !empty($registro_actual[$dia]) && !empty($registro[$dia]) &&
                    $registro_actual[$dia] == $registro[$dia]
                ) {
                    $dias_choque = true;
                    break;
                }
            }

            if (
                $registro['MODULO'] == $registro_actual['MODULO'] &&
                $registro['AULA'] == $registro_actual['AULA'] &&
                $choque_horario &&
                $dias_choque
            ) {
                // Buscar el timestamp de subida más antiguo
                $sql_timestamp = "SELECT d.Nombre_Departamento 
                                  FROM plantilla_dep pd
                                  JOIN departamentos d ON pd.Departamento_ID = d.Departamento_ID
                                  WHERE d.Nombre_Departamento IN ('$departamento_actual', '$nombre_dep')
                                  ORDER BY pd.Fecha_Subida_Dep ASC
                                  LIMIT 1";

                $result_timestamp = mysqli_query($conexion, $sql_timestamp);
                $primer_departamento = mysqli_fetch_assoc($result_timestamp);

                $choques[] = [
                    'Departamento' => $nombre_dep,
                    'ID_Choque' => $registro['ID_Plantilla'],
                    'Primer_Departamento' => $primer_departamento['Nombre_Departamento']
                ];
            }
        }
    }

    return $choques;
}

// Antes de generar la tabla, cargar datos de todos los departamentos
$departamentos_query = "SELECT Departamento_ID, Nombre_Departamento FROM departamentos";
$departamentos_result = mysqli_query($conexion, $departamentos_query);
$departamentos = [];

while ($dep = mysqli_fetch_assoc($departamentos_result)) {
    $tabla_dep = "data_" . str_replace(' ', '_', $dep['Nombre_Departamento']);
    $query = "SELECT 
        ID_Plantilla, 
        MODULO, 
        HORA_INICIAL, 
        HORA_FINAL, 
        AULA,
        L, M, I, J, V, S, D,
        '$dep[Nombre_Departamento]' as Departamento
    FROM $tabla_dep 
    WHERE MODULO IS NOT NULL 
      AND HORA_INICIAL IS NOT NULL 
      AND HORA_FINAL IS NOT NULL 
      AND AULA IS NOT NULL";

    $result_dep = mysqli_query($conexion, $query);
    $departamentos[$dep['Nombre_Departamento']] = mysqli_fetch_all($result_dep, MYSQLI_ASSOC);
}

$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento_nombre = $row_departamento['Departamentos'];

$tabla_departamento = "data_" . $nombre_departamento;

$sql = "SELECT * FROM $tabla_departamento WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta de datos: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<title>Data - <?php echo $departamento_nombre; ?></title>
<link rel="stylesheet" href="./CSS/basesdedatos.css">

<!-- CSS de la librería DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.2/css/fixedColumns.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.css">

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="encabezado-izquierda" style="display: flex; align-items: center;">
            <!--<div class="barra-buscador" id="barra-buscador">
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
                <?php if ($rol == 1): ?>
                    <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()">
                        <i class="fa fa-save" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()">
                        <i class="fa fa-undo" aria-hidden="true"></i>
                    </div>
                <?php endif; ?>
                <!-- <div class="icono-buscador" id="icono-todos-profesores" onclick="mostrarModalTodosProfesores()">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div> -->
                <div class="icono-buscador" id="icono-visibilidad">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-filtro">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </div>
                <?php if ($rol == 1): ?>
                    <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                        <i class="fa fa-add" aria-hidden="true"></i>
                    </div>
                    <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </div>
                <?php endif; ?>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarPopupColumnas()">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    <div id="popup-columnas" class="column-selector">
        <h3>Selecciona las columnas a descargar</h3>
        <div id="opciones-columnas"></div>
        <div class="fila-botones">
            <button onclick="descargarExcelSeleccionado()">Descargar seleccion</button>
            <?php if ($_SESSION['Rol_ID'] == 2): ?>
                <button class="btn-cotejo" onclick="descargarExcelCotejado()">Descargar cotejo</button>
            <?php endif; ?>
            <!-- <button onclick="cerrarPopupColumnas()">Cancelar</button> -->
        </div>
    </div>

    <?php
    // Verificar rol antes de mostrar la tabla editable
    if ($_SESSION['Rol_ID'] != 1) {
        // Deshabilitar edición o mostrar mensaje
        $tabla_editable = false;
    }
    ?>

    <div class="datatable-container">
        <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>">
        <table id="tabla-datos" class="display">
            <thead>
                <tr>
                    <th></th>
                    <th>ID <span class="filter-icon" data-column="1"><i class="fas fa-filter"></i></span></th>
                    <th>CICLO <span class="filter-icon" data-column="2"><i class="fas fa-filter"></i></span></th>
                    <th>CRN <span class="filter-icon" data-column="3"><i class="fas fa-filter"></i></span></th>
                    <th>MATERIA <span class="filter-icon" data-column="4"><i class="fas fa-filter"></i></span></th>
                    <th>CVE MATERIA <span class="filter-icon" data-column="5"><i class="fas fa-filter"></i></span></th>
                    <th>SECCIÓN <span class="filter-icon" data-column="6"><i class="fas fa-filter"></i></span></th>
                    <th>NIVEL <span class="filter-icon" data-column="7"><i class="fas fa-filter"></i></span></th>
                    <th>NIVEL TIPO <span class="filter-icon" data-column="8"><i class="fas fa-filter"></i></span></th>
                    <th>TIPO <span class="filter-icon" data-column="9"><i class="fas fa-filter"></i></span></th>
                    <th>C. MIN <span class="filter-icon" data-column="10"><i class="fas fa-filter"></i></span></th>
                    <th>H. TOTALES <span class="filter-icon" data-column="11"><i class="fas fa-filter"></i></span></th>
                    <th>STATUS <span class="filter-icon" data-column="12"><i class="fas fa-filter"></i></span></th>
                    <th>TIPO CONTRATO <span class="filter-icon" data-column="13"><i class="fas fa-filter"></i></span></th>
                    <th>CÓDIGO <span class="filter-icon" data-column="14"><i class="fas fa-filter"></i></span></th>
                    <th>NOMBRE PROFESOR <span class="filter-icon" data-column="15"><i class="fas fa-filter"></i></span></th>
                    <th>CATEGORIA <span class="filter-icon" data-column="16"><i class="fas fa-filter"></i></span></th>
                    <th>DESCARGA <span class="filter-icon" data-column="17"><i class="fas fa-filter"></i></span></th>
                    <th>CÓDIGO DESCARGA <span class="filter-icon" data-column="18"><i class="fas fa-filter"></i></span></th>
                    <th>NOMBRE DESCARGA <span class="filter-icon" data-column="19"><i class="fas fa-filter"></i></span></th>
                    <th>NOMBRE DEFINITIVO <span class="filter-icon" data-column="20"><i class="fas fa-filter"></i></span></th>
                    <th>TITULAR <span class="filter-icon" data-column="21"><i class="fas fa-filter"></i></span></th>
                    <th>HORAS <span class="filter-icon" data-column="22"><i class="fas fa-filter"></i></span></th>
                    <th>CÓDIGO DEPENDENCIA <span class="filter-icon" data-column="23"><i class="fas fa-filter"></i></span></th>
                    <th>L <span class="filter-icon" data-column="24"><i class="fas fa-filter"></i></span></th>
                    <th>M <span class="filter-icon" data-column="25"><i class="fas fa-filter"></i></span></th>
                    <th>I <span class="filter-icon" data-column="26"><i class="fas fa-filter"></i></span></th>
                    <th>J <span class="filter-icon" data-column="27"><i class="fas fa-filter"></i></span></th>
                    <th>V <span class="filter-icon" data-column="28"><i class="fas fa-filter"></i></span></th>
                    <th>S <span class="filter-icon" data-column="29"><i class="fas fa-filter"></i></span></th>
                    <th>D <span class="filter-icon" data-column="30"><i class="fas fa-filter"></i></span></th>
                    <th>DÍA PRESENCIAL <span class="filter-icon" data-column="31"><i class="fas fa-filter"></i></span></th>
                    <th>DÍA VIRTUAL <span class="filter-icon" data-column="32"><i class="fas fa-filter"></i></span></th>
                    <th>MODALIDAD <span class="filter-icon" data-column="33"><i class="fas fa-filter"></i></span></th>
                    <th>FECHA INICIAL <span class="filter-icon" data-column="34"><i class="fas fa-filter"></i></span></th>
                    <th>FECHA FINAL <span class="filter-icon" data-column="35"><i class="fas fa-filter"></i></span></th>
                    <th>HORA INICIAL <span class="filter-icon" data-column="36"><i class="fas fa-filter"></i></span></th>
                    <th>HORA FINAL <span class="filter-icon" data-column="37"><i class="fas fa-filter"></i></span></th>
                    <th>MÓDULO <span class="filter-icon" data-column="38"><i class="fas fa-filter"></i></span></th>
                    <th>AULA <span class="filter-icon" data-column="39"><i class="fas fa-filter"></i></span></th>
                    <th>CUPO <span class="filter-icon" data-column="40"><i class="fas fa-filter"></i></span></th>
                    <th>OBSERVACIONES <span class="filter-icon" data-column="41"><i class="fas fa-filter"></i></span></th>
                    <th>EXTRAORDINARIO <span class="filter-icon" data-column="42"><i class="fas fa-filter"></i></span></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $row['Departamento'] = $nombre_departamento;
                        $choques = verificarChoques($row, $departamentos, $conexion);

                        echo "<tr data-choques='" . htmlspecialchars(json_encode($choques)) . "' class='" .
                            (!empty($choques) ? 'tiene-choques' : '') . "'>";
                        echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . ($row["ID_Plantilla"] ?? '') . "'></td>";
                        echo "<td>" . htmlspecialchars($row["ID_Plantilla"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CICLO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CRN"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["MATERIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CVE_MATERIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["SECCION"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NIVEL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NIVEL_TIPO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["TIPO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["C_MIN"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["H_TOTALES"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["ESTATUS"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["TIPO_CONTRATO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CODIGO_PROFESOR"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NOMBRE_PROFESOR"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CATEGORIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["DESCARGA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CODIGO_DESCARGA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NOMBRE_DESCARGA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NOMBRE_DEFINITIVO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["TITULAR"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["HORAS"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CODIGO_DEPENDENCIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["L"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["M"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["I"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["J"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["V"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["S"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["D"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["DIA_PRESENCIAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["DIA_VIRTUAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["MODALIDAD"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["FECHA_INICIAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["FECHA_FINAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["HORA_INICIAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["HORA_FINAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["MODULO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["AULA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CUPO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["OBSERVACIONES"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["EXAMEN_EXTRAORDINARIO"] ?? '') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='43'>No hay datos disponibles</td></tr>";
                }
                mysqli_close($conexion);
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include './functions/basesdedatos/modal-añadir-registro/modal-añadir-registro.php'; ?>

<!-- Linea que valida el rol id del usuario para mandarlo a JS -->
<input type="hidden" id="user-role" value="<?php echo $_SESSION['Rol_ID']; ?>">

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

<!-- fa fa icons para firefox y safari -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script src="./JS/basesdedatos/tabla-editable.js"></script>
<script src="./JS/basesdedatos/eliminar-registro.js"></script>
<script src="./JS/basesdedatos/añadir-registro.js"></script>
<script src="./JS/basesdedatos/descargar-data-excel.js"></script>
<script src="./JS/basesdedatos/inicializar-tablas.js"></script>

<?php include("./template/footer.php"); ?>
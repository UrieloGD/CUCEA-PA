<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar que los archivos existan
$required_files = [
    './config/sesioniniciada.php',
    './config/db.php',
    './template/header.php',
    './template/navbar.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: No se encuentra el archivo $file");
    }
}

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
    if (isset($_GET['departamento_id'])) {
        // Si se proporciona un departamento_id específico
        $departamento_id = (int)$_GET['departamento_id'];
    } else {
        // Si no se proporciona, seleccionar el primer departamento
        $sql_primer_departamento = "SELECT Departamento_ID FROM Departamentos ORDER BY Departamento_ID LIMIT 1";
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
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM Departamentos WHERE Departamento_ID = ?";
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
function verificarChoques($registro_actual, $departamentos, $conexion) {
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
                if (!empty($registro_actual[$dia]) && !empty($registro[$dia]) && 
                    $registro_actual[$dia] == $registro[$dia]) {
                    $dias_choque = true;
                    break;
                }
            }

            if ($registro['MODULO'] == $registro_actual['MODULO'] &&
                $registro['AULA'] == $registro_actual['AULA'] &&
                $choque_horario && 
                $dias_choque
            ) {
                // Buscar el timestamp de subida más antiguo
                $sql_timestamp = "SELECT d.Nombre_Departamento 
                                  FROM Plantilla_Dep pd
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
            <?php if($rol == 1): ?>
                <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()">
                    <i class="fa fa-save" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </div>
                <?php endif;?>
                <div class="icono-buscador" id="icono-todos-profesores" onclick="mostrarModalTodosProfesores()">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-visibilidad">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <?php if($rol == 1): ?>
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <?php endif;?>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarPopupColumnas()">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    <div id="popup-columnas">
        <h3>Selecciona las columnas a descargar</h3>
        <hr style="border: 1px solid #0071b0; width: 99%;"></hr>
        <div id="opciones-columnas"></div>
        <button onclick="descargarExcelSeleccionado()">Descargar</button>
        <?php if($_SESSION['Rol_ID'] == 2): ?>
        <button onclick="descargarExcelCotejado()">Descargar cotejo</button>
        <?php endif; ?>
        <button onclick="cerrarPopupColumnas()">Cancelar</button>
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
                    <th>ID <span class="filter-icon" data-column="1">&#9662;</span></th>
                    <th>CICLO <span class="filter-icon" data-column="2">&#9662;</span></th>
                    <th>CRN <span class="filter-icon" data-column="3">&#9662;</span></th>
                    <th>MATERIA <span class="filter-icon" data-column="4">&#9662;</span></th>
                    <th>CVE MATERIA <span class="filter-icon" data-column="5">&#9662;</span></th>
                    <th>SECCIÓN <span class="filter-icon" data-column="6">&#9662;</span></th>
                    <th>NIVEL <span class="filter-icon" data-column="7">&#9662;</span></th>
                    <th>NIVEL TIPO <span class="filter-icon" data-column="8">&#9662;</span></th>
                    <th>TIPO <span class="filter-icon" data-column="9">&#9662;</span></th>
                    <th>C. MIN <span class="filter-icon" data-column="10">&#9662;</span></th>
                    <th>H. TOTALES <span class="filter-icon" data-column="11">&#9662;</span></th>
                    <th>STATUS <span class="filter-icon" data-column="12">&#9662;</span></th>
                    <th>TIPO CONTRATO <span class="filter-icon" data-column="13">&#9662;</span></th>
                    <th>CÓDIGO <span class="filter-icon" data-column="14">&#9662;</span></th>
                    <th>NOMBRE PROFESOR <span class="filter-icon" data-column="15">&#9662;</span></th>
                    <th>CATEGORIA <span class="filter-icon" data-column="16">&#9662;</span></th>
                    <th>DESCARGA <span class="filter-icon" data-column="17">&#9662;</span></th>
                    <th>CÓDIGO DESCARGA <span class="filter-icon" data-column="18">&#9662;</span></th>
                    <th>NOMBRE DESCARGA <span class="filter-icon" data-column="19">&#9662;</span></th>
                    <th>NOMBRE DEFINITIVO <span class="filter-icon" data-column="20">&#9662;</span></th>
                    <th>TITULAR <span class="filter-icon" data-column="21">&#9662;</span></th>
                    <th>HORAS <span class="filter-icon" data-column="22">&#9662;</span></th>
                    <th>CÓDIGO DEPENDENCIA <span class="filter-icon" data-column="23">&#9662;</span></th>
                    <th>L <span class="filter-icon" data-column="24">&#9662;</span></th>
                    <th>M <span class="filter-icon" data-column="25">&#9662;</span></th>
                    <th>I <span class="filter-icon" data-column="26">&#9662;</span></th>
                    <th>J <span class="filter-icon" data-column="27">&#9662;</span></th>
                    <th>V <span class="filter-icon" data-column="28">&#9662;</span></th>
                    <th>S <span class="filter-icon" data-column="29">&#9662;</span></th>
                    <th>D <span class="filter-icon" data-column="30">&#9662;</span></th>
                    <th>DÍA PRESENCIAL <span class="filter-icon" data-column="31">&#9662;</span></th>
                    <th>DÍA VIRTUAL <span class="filter-icon" data-column="32">&#9662;</span></th>
                    <th>MODALIDAD <span class="filter-icon" data-column="33">&#9662;</span></th>
                    <th>FECHA INICIAL <span class="filter-icon" data-column="34">&#9662;</span></th>
                    <th>FECHA FINAL <span class="filter-icon" data-column="35">&#9662;</span></th>
                    <th>HORA INICIAL <span class="filter-icon" data-column="36">&#9662;</span></th>
                    <th>HORA FINAL <span class="filter-icon" data-column="37">&#9662;</span></th>
                    <th>MÓDULO <span class="filter-icon" data-column="38">&#9662;</span></th>
                    <th>AULA <span class="filter-icon" data-column="39">&#9662;</span></th>
                    <th>CUPO <span class="filter-icon" data-column="40">&#9662;</span></th>
                    <th>OBSERVACIONES <span class="filter-icon" data-column="41">&#9662;</span></th>
                    <th>EXTRAORDINARIO <span class="filter-icon" data-column="42">&#9662;</span></th>
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

<!-- Modal para añadir registros -->
<div id="modal-añadir" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Añadir nuevo registro</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-añadir-registro">
            <div class="form-container">
                <div class="form-section">
                    <h3>Materia</h3>
                    <div class="form-row">
                        <input type="text" id="ciclo" name="ciclo" placeholder="Ciclo">
                        <input type="text" id="crn" name="crn" placeholder="CRN">
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="CVE Materia">
                    </div>
                    <div class="form-row">
                        <input type="text" id="materia" name="materia" placeholder="Materia" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nivel" name="nivel" placeholder="Nivel">
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo" name="tipo" placeholder="Tipo">
                        <input type="text" id="nivel_tipo" name="nivel_tipo" placeholder="Nivel tipo">
                        <input type="text" id="seccion" name="seccion" placeholder="Sección">
                    </div>
                    <div class="form-row">
                        <input type="text" id="c_min" name="c_min" placeholder="C. Min" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="h_totales" name="h_totales" placeholder="Horas totales" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="estatus" name="estatus" placeholder="Status">
                    </div>
                    <div class="form-row weekdays">
                        <input type="text" id="l" name="l" placeholder="L" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'L') this.value = '';">
                        <input type="text" id="m" name="m" placeholder="M" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'M') this.value = '';">
                        <input type="text" id="i" name="i" placeholder="I" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'I') this.value = '';">
                        <input type="text" id="j" name="j" placeholder="J" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'J') this.value = '';">
                        <input type="text" id="v" name="v" placeholder="V" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'V') this.value = '';">
                        <input type="text" id="s" name="s" placeholder="S" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'S') this.value = '';">
                        <input type="text" id="d" name="d" placeholder="D" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'D') this.value = '';">
                    </div>
                    <div class="form-row">
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="Día presencial">
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="Día virtual">
                        <input type="text" id="modalidad" name="modalidad" placeholder="Modalidad">
                    </div>
                    <div class="form-row">
                        <input type="text" id="fecha_inicial" name="fecha_inicial" placeholder="Fecha inicial">
                        <input type="text" id="fecha_final" name="fecha_final" placeholder="Fecha final">
                    </div>
                    <div class="form-row">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="Hora inicial" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="hora_final" name="hora_final" placeholder="Hora final" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row">
                        <input type="text" id="modulo" name="modulo" placeholder="Módulo">
                        <input type="text" id="aula" name="aula" placeholder="Aula">
                    </div>
                    <div class="form-row">
                        <input type="text" id="cupo" name="cupo" placeholder="Cupo" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="examen_extraordinario" name="examen_extraordinario" placeholder="Examen extraordinario">
                    </div>
                    <div class="form-row">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Observaciones" class="full-width">
                    </div>
                </div>
            <div class="form-movil">
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="Código profesor" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="Nombre completo del profesor" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo_contrato" name="tipo_contrato" placeholder="Tipo contrato">
                        <input type="text" id="categoria" name="categoria" placeholder="Categoría">
                    </div>
                    <div class="form-row">
                        <input type="text" id="descarga" name="descarga" placeholder="Descarga" class="full-width">
                        <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="Código descarga" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="Nombre descarga" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="Nombre definitivo" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas_totales" name="horas" placeholder="Horas totales" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="titular" name="titular" placeholder="Titular">
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas" name="horas" placeholder="Horas" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="codigo_dependencia" name="codigo_dependencia" placeholder="Código dependencia">
                    </div>
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

<!-- Modal para listar todos los profesores del departamento -->
<div id="modal-todos-profesores" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalTodosProfesores()">&times;</span>
        
        <!-- Barra de búsqueda -->
        <div class="search-bar">
            <div class="search-input-container">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input type="text" placeholder="Buscar profesor..." id="buscar-todos-profesores" onkeyup="filtrarTodosProfesores()">
            </div>
        </div>

        <!-- Tabla de profesores -->
        <div class="profesores-container">
            <h2>Todos los Profesores - <?php echo $departamento_nombre; ?></h2>
            <table class="profesores-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre Completo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="lista-todos-profesores">
                <?php
                include './config/db.php';
                
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
                $sql_todos_profesores = "SELECT DISTINCT Codigo, Nombre_Completo 
                                       FROM Coord_Per_Prof 
                                       WHERE $where_clause
                                       ORDER BY Nombre_Completo";
                
                $result_todos_profesores = mysqli_query($conexion, $sql_todos_profesores);
                
                if ($result_todos_profesores) {
                    while($row = mysqli_fetch_assoc($result_todos_profesores)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Codigo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Nombre_Completo']) . "</td>";
                        echo "<td><button onclick='verDetalleProfesor(" . $row['Codigo'] . ")' class='btn-detalle'>Ver detalle</button></td>";
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

<!-- Modal para visualizar información detallada del profesor -->
<div id="modal-detalle-profesor" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalDetalle()">&times;</span>
        <!--<h2>Detalle del Profesor</h2>-->
        <div id="detalle-profesor-contenido">
            <!--El contenido se cargará dinámicamente -->
        </div> 
    </div>
</div>

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

<script src="./JS/basesdedatos/tabla-editable.js"></script>
<script src="./JS/basesdedatos/eliminar-registro.js"></script>
<script src="./JS/basesdedatos/editar-registros.js"></script>
<script src="./JS/basesdedatos/añadir-registro.js"></script>
<script src="./JS/basesdedatos/descargar-data-excel.js"></script>
<script src="./JS/basesdedatos/inicializar-tablas.js"></script>
<script src="./JS/basesdedatos/visualizar-profesores.js"></script>
<script src="./JS/basesdedatos/detalle-profesor.js"></script>

<?php include("./template/footer.php"); ?>
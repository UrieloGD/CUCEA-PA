<?php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 1 && $_SESSION['Rol_ID'] != 0 && $_SESSION['Rol_ID'] != 4) {
    header("Location: home.php");
    exit();
}
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar que los archivos existan
$required_files = [
    './config/sesiones.php',
    './config/db.php',
    './template/header.php',
    './template/navbar.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: No se encuentra el archivo $file");
    }
}

// Incluir los archivos
require_once './config/db.php';
require_once './config/sesiones.php';
?>

<?php

function obtenerDepartamentoId($usuario_id)
{
    global $conexion;
    $sql = "SELECT Departamento_ID FROM usuarios_departamentos WHERE Usuario_ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $departamento_id = $row['Departamento_ID'];
    } else {
        $departamento_id = null;
    }

    $stmt->close();
    return $departamento_id;
}

$codigo_usuario =  $_SESSION['Codigo'];
$sql_fecha_limite = "SELECT Fecha_Limite FROM fechas_limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$row_fecha_limite = mysqli_fetch_assoc($result_fecha_limite);
$fecha_limite = $row_fecha_limite ? $row_fecha_limite['Fecha_Limite'] : "2024-12-25 23:50";

$departamento_id = null;
if (isset($_SESSION['Codigo'])) {
    $departamento_id = obtenerDepartamentoId($_SESSION['Codigo']);
}

if ($departamento_id === null) {
    error_log("No se encontró Departamento_ID para el usuario: " . $_SESSION['Codigo']);
}

?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Plantilla</title>
<link rel="stylesheet" href="./CSS/plantilla.css?v=<?php echo filemtime('./CSS/plantilla.css'); ?>" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="cuadro-scroll">
    <!--Pestañas-->
    <div class="tab-container">
        <div class="tab-buttons">
            <button class="tab-button active">Descargar plantilla</button>
            <button class="tab-button">Subir plantilla</button>
        </div>
        <div class="tab-content">
            <div class="tab-pane active">
                <div class="info-descarga">
                    <p>En este apartado podrás descargar tu plantilla de Excel para realizar tu Programación Académica.</p>
                </div>
                <div class="icono-descarga">
                    <a href="#" onclick="descargarArchivo(<?php echo json_encode($departamento_id); ?>)">
                        <img src="./Img/Icons/icono-descarga-plantilla.png" alt="imagen de edificios de CUCEA" />
                    </a>
                </div>
                <div class="div-boton-descargar">
                    <button class="boton-descargar" role="button" onclick="descargarArchivo(<?php echo json_encode($departamento_id); ?>)">Descargar</button>
                </div>
                <div class="info-descarga">
                    <p>Si necesitas ayuda, puedes consultar la Guía de Programación Académica haciendo clic <a href="./guiaPA.php">aquí.</a></p>
                </div>
            </div>
        </div>

        <?php
        $justificacion_enviada = false;
        if ($departamento_id) {
            $sql_justificacion = "SELECT Justificacion_Enviada FROM justificaciones
        WHERE Departamento_ID = ? AND Codigo_Usuario = ?
        ORDER BY Fecha_Justificacion DESC LIMIT 1";

            $stmt = $conexion->prepare($sql_justificacion);
            $stmt->bind_param("is", $departamento_id, $codigo_usuario);
            $stmt->execute();
            $result_justificacion = $stmt->get_result();

            if ($result_justificacion->num_rows > 0) {
                $row_justificacion = $result_justificacion->fetch_assoc();
                $justificacion_enviada = $row_justificacion['Justificacion_Enviada'] == 1;
            }
            $stmt->close();
        }
        ?>

        <div class="tab-pane">
            <?php
            $fecha_actual = date("Y-m-d H:i:s");
            $fecha_limite_pasada = strtotime($fecha_actual) > strtotime($fecha_limite);

            if ($fecha_limite_pasada && !$justificacion_enviada) {
            ?>
                <div class="justification-container">
                    <div class="access-restricted">
                        <div class="icon-circle">
                            <img src="./Img/Icons/icono-entrega-tardia.png" alt="Access Restricted" />
                        </div>
                        <h2>Acceso restringido</h2>
                        <p>La fecha límite para subir tu plantilla fue el día <?php echo date('d/m/Y', strtotime($fecha_limite)); ?></p>
                        <p>No subir tus actividades a tiempo puede tener graves consecuencias, tales como:</p>
                        <ul>
                            <li>Atrasar otras tareas.</li>
                            <li>Cargar de trabajo a otras personas o áreas.</li>
                            <li>Perjudicar la agenda de los alumnos.</li>
                        </ul>
                        <p>Si deseas subir la plantilla, justifica por qué no subiste la plantilla a tiempo.</p>
                    </div>
                    <form id="formulario-justificacion" method="post" action="./functions/plantilla/guardar_justificacion.php">
                        <textarea name="justificacion" placeholder="Escribe tu justificación aquí..." rows="5" required></textarea>
                        <div id="char-count">0 / 60 caracteres</div>
                        <input type="hidden" name="departamento_id" value="<?php echo $departamento_id; ?>">
                        <input type="hidden" name="codigo_usuario" value="<?php echo $codigo_usuario; ?>">
                        <button type="submit" class="boton-continuar disabled" disabled>Continuar</button>
                    </form>
                </div>
            <?php
            } else {
            ?>
                <div class="info-subida">
                    <p>Sube aquí tu plantilla (Excel) de Programación Académica</b></p>
                </div>
                <!-- <div class="info-subida">
                    <p>Recuerda que la fecha límite para subir tu plantilla de Programación académica es <b><?php echo date('d/m/Y', strtotime($fecha_limite)); ?></b></p>
                </div> -->
                <?php if ($fecha_limite_pasada && $justificacion_enviada) { ?>
                    <div class="container-precaucion">
                        <h3>Estás subiendo tu plantilla después de la fecha límite. Tu justificación ha sido recibida.</h3>
                    </div>
                <?php } ?>
                <form id="formulario-subida" enctype="multipart/form-data" class="upload-form">
                    <div class="container-inf">
                        <div class="drop-area">
                            <p>Arrastra tu archivo a subir aquí</p>
                            <p>o</p>
                            <button type="button" class="boton-seleccionar-archivo" role="button">
                                Selecciona archivo
                            </button>
                            <input type="file" name="file" id="input-file" accept=".xlsx,.xls" hidden>
                        </div>
                        <div id="preview"></div>
                        <div class="container-peso">
                            <h3>Tamaño máximo de archivo permitido: 2MB</h3>
                        </div>
                        <button type="submit" class="boton-descargar" role="button">Guardar</button>
                    </div>
                </form>
            <?php
            }
            ?>
        </div>
    </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./JS/plantilla/guardarJustifiicacion.js?v=<?php echo filemtime('./JS/plantilla/guardarJustifiicacion.js'); ?>"></script>
<script src="./JS/plantilla/descargarPlantilla.js?v=<?php echo filemtime('./JS/plantilla/descargarPlantilla.js'); ?>"></script>
<script src="./JS/plantilla/drag&drop.js?v=<?php echo filemtime('./JS/plantilla/drag&drop.js'); ?>"></script>
<script src="./JS/plantilla/pestañasPlantilla.js?v=<?php echo filemtime('./JS/plantilla/pestañasPlantilla.js'); ?>"></script>

<?php include './template/footer.php' ?>
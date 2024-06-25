<?php
session_start(); // Inicia la sesión para acceder a los datos del usuario logeado

function obtenerDepartamentoId($usuario_id)
{
    // Conectar a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "pa";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        return null; // Manejar la conexión fallida
    }

    // Obtener el ID del departamento del usuario logeado
    $sql = "SELECT Departamento_ID FROM Usuarios_Departamentos WHERE Usuario_ID = '$usuario_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $departamento_id = $row['Departamento_ID'];
    } else {
        $departamento_id = null; // No se encontró el departamento del usuario
    }

    // Cerrar la conexión a la base de datos
    $conn->close();

    return $departamento_id;
}

include './config/db.php';

// Obtener la última fecha límite de la base de datos
$sql_fecha_limite = "SELECT Fecha_Limite FROM Fechas_Limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$row_fecha_limite = mysqli_fetch_assoc($result_fecha_limite);
$fecha_limite = $row_fecha_limite ? $row_fecha_limite['Fecha_Limite'] : "2024-10-01 23:50";

$departamento_id = null;
if (isset($_SESSION['Codigo'])) {
    $Codigo = $_SESSION['Codigo'];
    $departamento_id = obtenerDepartamentoId($Codigo);
}

$fecha_actual = date("Y-m-d H:i:s");

if ($fecha_actual > $fecha_limite) {
    // Verificar si ya se ha enviado una justificación pendiente o aprobada
    $sql_justificacion = "SELECT * FROM Justificaciones 
                          WHERE Usuario_ID = '$usuario_id' 
                          AND Departamento_ID = '$departamento_id'
                          AND Fecha_Limite_Superada = '$fecha_limite'
                          AND (Estado = 'Pendiente' OR Estado = 'Aprobada')";
    $result_justificacion = mysqli_query($conexion, $sql_justificacion);
    
    if (mysqli_num_rows($result_justificacion) == 0) {
        // No hay justificación pendiente o aprobada, mostrar el modal
        include 'justificacion.php';
        exit(); // Detener la ejecución del resto del script
    }
}

?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Plantilla</title>
<link rel="stylesheet" href="./CSS/plantilla.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestañas-->
    <div class="tab-container">
        <div class="tab-buttons">
            <button class="tab-button active">Descargar plantilla</button>
            <button class="tab-button">Subir plantilla</button>
        </div>
        <div class="tab-content">
            <!--Ventana de descaga de plantilla-->
            <div class="tab-pane active">
                <div class="info-descarga">
                    <p>En este apartado podrás descargar tu plantilla de Excel para realizar tu Programación Académica.</p>
                </div>
                <!--Elementos de descarga-->
                <div class="icono-descarga">
                    <a href="#" onclick="descargarArchivo(<?php echo json_encode($departamento_id); ?>)">
                        <img src="./Img/Icons/icono-descarga-plantilla.png" alt="imagen de edificios de CUCEA" />
                    </a>
                </div>
                <div class="div-boton-descargar">
                    <button class="boton-descargar" role="button" onclick="descargarArchivo(<?php echo json_encode($departamento_id); ?>)">Descargar</button>
                </div>
                <div class="info-descarga">
                    <p>Si necesitas ayuda, puedes consultar la Guía de Programación Académica haciendo clic <a href="./guia.php">aquí.</a></p>
                </div>
            </div>
        </div>
        <!--Ventana de subida de plantilla-->
        <div class="tab-pane">
            <?php
            $fecha_actual = date("Y-m-d H:i:s");
            $fecha_limite_pasada = strtotime($fecha_actual) > strtotime($fecha_limite);

            if ($fecha_limite_pasada) {
                // Mostrar formulario de justificación
            ?>
                <div class="info-subida">
                    <p>La fecha límite para subir tu plantilla ha pasado. Por favor, proporciona una justificación:</p>
                </div>
                <form id="formulario-justificacion" method="post" action="guardar_justificacion.php">
                    <textarea name="justificacion" rows="5" cols="50" required></textarea>
                    <input type="hidden" name="departamento_id" value="<?php echo $departamento_id; ?>">
                    <!-- <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario_id']; ?>"> -->
                    <button type="submit" class="boton-descargar" role="button">Enviar Justificación</button>
                </form>
            <?php
            } else {
                // Mostrar formulario de subida de plantilla original
            ?>
                <div class="info-subida">
                    <p>Recuerda que la fecha límite para subir tu plantilla de Programación académica es
                        <b><?php echo date('d/m/Y H:i', strtotime($fecha_limite)); ?></b>
                    </p>
                </div>
                <form id="formulario-subida" enctype="multipart/form-data">
                    <div class="container-inf">
                        <div class="drop-area">
                            <p>Arrastra tus archivos a subir aquí</p>
                            <p>o</p>
                            <button type="button" class="boton-seleccionar-archivo" role="button" id="seleccionar-archivo-btn">Selecciona archivo</button>
                            <input type="file" name="file" id="input-file" hidden>
                        </div>
                        <div id="preview"></div>
                        <div id="mensaje"></div>
                        <div class="container-peso">
                            <h3>Tamaño máximo de archivo permitido: 2MB</h3>
                        </div>
                        <button type="submit" class="boton-descargar" role="button" id="guardar-btn">Guardar</button>
                    </div>
                </form>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</div>
<script src="./JS/descargar.js"></script>
<script src="./JS/drag.js"></script>
<script src="./JS/pestañas-plantilla.js"></script>
<script src="./JS/Ajax.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include './template/footer.php' ?>
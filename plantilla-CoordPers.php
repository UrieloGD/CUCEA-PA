<?php
session_start();
include './config/db.php';

$codigo_usuario = $_SESSION['Codigo'];
$sql_fecha_limite = "SELECT Fecha_Limite FROM fechas_limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$row_fecha_limite = mysqli_fetch_assoc($result_fecha_limite);
$fecha_limite = $row_fecha_limite ? $row_fecha_limite['Fecha_Limite'] : "2024-10-01 23:50";

?>

<?php
// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 0) {
    header("Location: home.php");
    exit();
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Plantilla</title>
<link rel="stylesheet" href="./CSS/plantilla.css?=v1.0" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="info-subida">
        <!-- <p>Recuerda que la fecha límite para subir tu plantilla de Programación académica es <b><?php echo date('d/m/Y', strtotime($fecha_limite)); ?></b></p> -->
    </div>
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Plantilla - Coordinación de Personal</h3>
        </div>
    </div>
    <form id="formulario-subida" enctype="multipart/form-data">
        <div class="container-inf">
            <div class="drop-area">
                <p>Arrastra tu archivo a subir aquí</p>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./JS/plantilla-CoordPers/drag&drop.js"></script>

<?php include './template/footer.php' ?>
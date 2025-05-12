<?php
session_start();
include './config/db.php';
include './template/header.php';
include './template/navbar.php';
require_once './functions/home/eventos-home.php';
?>

<title>Mantenimiento</title>
<link rel="stylesheet" href="./CSS/errores/404.css" />

<div class="cuadro-principal">
    <div class="container-error">
        <div class="img-error">
            <img src="./Img/img-errores/mantenimiento.png" alt="Mantenimiento">
        </div>
        <div class="text-error">
            <p>
                Página en mantenimiento<br>
                Lamentamos las molestias
            </p>
        </div>
    </div>
    <div class="container-btn">
        <button class="boton-inicio">Regresar al inicio</button>
    </div>
</div>

<?php include './template/footer.php' ?>
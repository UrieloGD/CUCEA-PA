<?php
session_start();
include './config/db.php';
include './template/header.php';
include './template/navbar.php';
require_once './functions/home/eventos-home.php';
?>

<title>Error 404!</title>
<link rel="stylesheet" href="./CSS/errores/404.css" />

<div class="cuadro-principal">
    <div class="container-error">
        <div class="img-error">
            <div>
                <img src="./Img/img-errores/404.png" alt="Error 404" class="shake-vertical">
            </div>
            <div>
                <img src="./Img/img-errores/404-sombra.png" alt="Error 404" class="scale-up-hor-center">
            </div>
        </div>
        <div class="text-error">
            <p>
                Error 404!<br>
                Página no encontrada
            </p>
        </div>
    </div>
    <div class="container-btn">
        <button class="boton-inicio"><a href="home.php">Regresar al inicio</a></button>
    </div>
</div>

<?php include './template/footer.php' ?>
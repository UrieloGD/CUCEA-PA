<?php
session_start();
include './config/db.php';
include './template/header.php';
include './template/navbar.php';
require_once './functions/home/eventos-home.php';
?>

<title>Error 500!</title>
<link rel="stylesheet" href="./CSS/errores/404.css" />

<div class="cuadro-principal">
    <div class="container-error">
        <div class="img-error">
            <img src="./Img/img-errores/500.png" alt="Error 500">
        </div>
        <div class="text-error">
            <p>
                Error 500!<br>
                Error en el servidor
            </p>
        </div>
    </div>
    <div class="container-btn">
        <button class="boton-inicio">Regresar al inicio</button>
    </div>
</div>

<?php include './template/footer.php' ?>
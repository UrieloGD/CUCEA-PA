<?php
// Establecer el código de respuesta HTTP 500
http_response_code(500);

// Evitar que se indexe esta página en buscadores
header('X-Robots-Tag: noindex, nofollow');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include './config/db.php';
include './template/header.php';
include './template/navbar.php';
require_once './functions/home/eventos-home.php';
?>

<title>Error 500!</title>
<link rel="stylesheet" href="./CSS/errores/500.css" />

<div class="cuadro-principal">
    <div class="container-error">
        <div class="img-error">
            <div>
                <img src="./Img/img-errores/500.png" alt="Error 500" class="shake-vertical">
            </div>
            <div>
                <img src="./Img/img-errores/500-sombra.png" alt="Error 500" class="scale-up-hor-center">
            </div>
        </div>
        <div class="text-error">
            <p>
                Error 500!<br>
                Error en el servidor
            </p>
        </div>
    </div>
    <div class="container-btn">
        <button class="boton-inicio"><a href="home.php">Regresar al inicio</a></button>
    </div>
</div>

<?php include './template/footer.php' ?>
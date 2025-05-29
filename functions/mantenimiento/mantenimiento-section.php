<?php
// Página de mantenimiento para secciones
// Esta página se muestra cuando una sección está en mantenimiento

// Si no están definidas, establece valores por defecto
if (!isset($section)) $section = 'esta sección';
if (!isset($message)) $message = 'Esta sección se encuentra temporalmente en mantenimiento. disculpe las molestias.';

?>

<title>Mantenimiento en curso</title>
<link rel="stylesheet" href="./CSS/errores/404.css?v=<?php echo filemtime('./CSS/errores/404.css'); ?>" />
<link rel="stylesheet" href="./CSS/errores/404.css" />

<div class="cuadro-principal">
    <div class="container-error">
        <div class="img-error">
            <div>
                <img src="./Img/img-mantenimiento/mantenimiento.png" alt="Mantenimiento" class="shake-vertical">
            </div>
            <div>
                <img src="./Img/img-mantenimiento/mantenimiento-sombra.png" alt="Mantenimiento" class="scale-up-hor-center">
            </div>
        </div>
        <div class="text-error">
            <p>
                Página en mantenimiento<br>
                Lamentamos las molestias
            </p>
        </div>
    </div>
    <div class="container-btn">
        <button class="boton-inicio"><a href="home.php">Regresar al inicio</a></button>
    </div>
</div>

<?php include './template/footer.php'?>
<!--header -->
<?php include './template/header.php' ?>

<!-- navbar -->
<?php include './template/navbar.php' ?>

<title>Descarga Plantilla</title>
<link rel="stylesheet" href="./CSS/plantilla.css" />


<hr style="margin-left: 10vw; margin-right: 2vw" />
<!-- Fin del  header-->
<!--Cuadro principal del home-->
<div class="home">
    <!--Cuadro de bienvenida-->
    <div class="bienvenida">
        <h2>Descarga de Plantilla</h2>
    </div>
    <div class="bienvenida">
        <p>En este apartado podrás descargar la plantilla de Excel</p>
    </div>

    <!--Elementos de descarga-->
    <div class="cuadros-nav">
        <div class="cuadro-ind">
            <a href="#">
                <img src="./Icons/Descarga.png" alt="imagen de edificios de CUCEA" />
            </a>
        </div>
    </div>
    <div class="cuadros-nav">
        <div class="cuadro-ind">
            <a href="#"><button class="button-65" role="button">Descargar</button></a>
        </div>
    </div>

    <!-- Bloque inferior -->

    <div class="hr"></div>

    <!--Cuadro de subida-->
    <div class="bienvenida">
        <h2>Selecciona el documento que deseas subir</h2>
    </div>
    <div class="bienvenida">
        <p style="text-decoration: underline;">Recuerda que la fecha limite para subir la plantilla es 10 de
            noviembre de 2024</p>
    </div>

    <div class="container-peso">
        <h3>Tamaño máximo de archivo permitido: 2MB</h3>
    </div>

    <div class="container-inf">
        <div class="container-inf-int">
            <!--Elementos de subida-->
            <p>Arrastra tus archivos a subir aquí</p>
            <p>o</p>
            <a href="#"><button class="button-66" role="button">Selecciona archivo</button></a>
        </div>
        <a href="#"><button class="button-65" role="button">Guardar</button></a>
    </div>

    <div class="container-inf-dos">
        <a href="#"><button class="button-65" role="button">Entrega tardía</button></a>
    </div>

</div>
</div>

<?php include './template/footer.php' ?>
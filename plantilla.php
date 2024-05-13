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
                    <a href="#" onclick="descargarArchivo()">
                        <img src="./Img/Icons/icono-descarga-plantilla.png" alt="imagen de edificios de CUCEA" />
                    </a>
                </div>
                <div class="div-boton-descargar">
                    <a href="#" onclick="descargarArchivo()">
                        <button class="boton-descargar" role="button">Descargar</button>
                    </a>
                </div>
                <div class="info-descarga">
                    <p>Si necesitas ayuda, puedes consultar la Guía de Programación Académica haciendo clic <a href="#">aquí.</a></p>
                </div>
            </div>
        </div>
        <!--Ventana de subida de plantilla-->
        <div class="tab-pane">
            <div class="info-subida">
                <p>Recuerda que la fecha límite para subir tu plantilla de Programación académica es
                    <!-- Aqui se incluirá la fecha seleccionada por el Admin -->
                    <b>10 de noviembre de 2024</b>
                </p>
            </div>
            <div class="container-inf">
                <!--Elementos de subida de archivo-->
                <div class="drop-area">
                    <p>Arrastra tus archivos a subir aquí</p>
                    <p>o</p>
                    <button class="boton-seleccionar-archivo" role="button">Selecciona archivo</button>
                    <input type="file" name="" id="input-file" hidden>
                </div>

                <div id="preview"></div>
                <div id="mensaje"></div>

                <div class="container-peso">
                    <h3>Tamaño máximo de archivo permitido: 2MB</h3>
                </div>

                <button class="boton-descargar" role="button" id="guardar-btn" onclick="uploadFiles()">Guardar</button>

            </div>
            <!-- <div class="container-inf-dos">
                <a href="#"><button class="boton-descargar" role="button">Entrega tardía</button></a>
            </div> -->
        </div>
    </div>
</div>
</div>
<script src="./JS/descargar.js"></script>
<script src="./JS/drag.js"></script>
<script src="./JS/pestañas-plantilla.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include './template/footer.php' ?>
<?php
//personal-solicitud-cambios.php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3 and $_SESSION['Rol_ID'] != 1) {
    header("Location: home.php");
    exit();
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Solicitudes de modificaciones</title>
<link rel="stylesheet" href="./CSS//personal-solicitud-cambios/personal-solicitud-cambios.css">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-baja.css">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-propuesta.css">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-baja-propuesta.css">

<!--Cuadro principal del home -->
<div class="cuadro-principal">
    <div div class="encabezado">
        <div class="titulo-bd">
            <h3>Solicitudes de modificaciones</h3>
        </div>
    </div>

    <!-- Contenedor principal de todo el contenido de los contenedores. -->
    <div class="solicitud-contenedor-principal">
        <!-- Contenedor principal de cada solicitud. -->
        <!--------------------------------------->
        <div class="info-sup" onclick="mostrarInformacion('contenedor-informacion-1', this.querySelector('.icono-despliegue i'))">
            <!-- DPTO Y TIPO DE SOLICITUD. -->
            <div class="color-en_revision" style="position: absolute; left: 0px;"></div>
            <div class="nombre-dpto-solicitud">
                <span>Tipo de solicitud:</span><span class="nombre-solicitud" id="solicitud-baja-propuesta">Solicitud de baja-propuesta</span>
            </div>
            <div class="nombre-dpto-solicitud">
                <span>Departamento:</span><span class="nombre-departamento" id="departamento">Departamento de contaduria</span>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <!-- HORA, FECHA Y ESTADO. -->
            <div class="fecha-hora-status">
                <span class="fecha-solicitud" type="date" id="campo-fecha"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 2024-01-01 </span>
                <span class="hora-solicitud" type="time" id="campo-hora"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 00:00 </span>
                <div class="circulo-en_revision">
                    <i class="fa fa-circle" aria-hidden="true"><span class="estado-solicitud" type="text" id="en-revision" style="margin-left: 10px;">En revisión</span></i>
                </div>
            </div>

            <!-- Icono al extremo derecho, para el despliegue de ventana inferior -->
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>

        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-1">
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                    </p>
                    <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                    </p>
                    <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="folio">Folio de solicitud:
                    <p id="info-folio">54440216</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor actual</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno">Castanedo</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno">Escobedo</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="motivo">Motivo:
                    <p id="info-motivo">Jubilacion del profesor</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor propuesto</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno-propuesto">Gomez</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno-propuesto">Alcantara</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo-propuesto">123456789</p>
                    </p>
                </div>
            </div>
            <div class="contenedor-botones">
                <a href="./personal-solicitud-cambios.php">
                    <button class="boton-pdf" id="boton-pdf" style="margin-top: 10px; margin-bottom: 5px;">
                        <i class="fa fa-file-text" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-descargar"></i>Descargar</button></a>
            </div>
        </div>

        <!-- Contenedor principal de cada solicitud. -->
        <!--------------------------------------->
        <div class="info-sup" onclick="mostrarInformacion('contenedor-informacion-2', this.querySelector('.icono-despliegue i'))">
            <!-- DPTO Y TIPO DE SOLICITUD. -->
            <div class="color-aprobado" style="position: absolute; left: 0px;"></div>
            <div class="nombre-dpto-solicitud">
                <span>Tipo de solicitud:</span><span class="nombre-solicitud" id="solicitud-baja">Solicitud de baja</span>
            </div>
            <div class="nombre-dpto-solicitud">
                <span>Departamento:</span><span class="nombre-departamento" id="departamento">Departamento de sistemas de la informacion</span>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <!-- HORA, FECHA Y ESTADO. -->
            <div class="fecha-hora-status">
                <span class="fecha-solicitud" type="date" id="campo-fecha"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 2024-01-01 </span>
                <span class="hora-solicitud" type="time" id="campo-hora"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 00:00 </span>
                <div class="circulo-aprobado">
                    <i class="fa fa-circle" aria-hidden="true"><span class="estado-solicitud" type="text" id="aprobado" style="margin-left: 10px;">Aprobada</span></i>
                </div>
            </div>

            <!-- Icono al extremo derecho, para el despliegue de ventana inferior -->
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>

        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-2">
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                    </p>
                    <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                    </p>
                    <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="folio">Folio de solicitud:
                    <p id="info-folio">54440216</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor actual</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno">Castanedo</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno">Escobedo</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="motivo">Motivo:
                    <p id="info-motivo">Jubilacion del profesor</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor propuesto</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno-propuesto">Gomez</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno-propuesto">Alcantara</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo-propuesto">123456789</p>
                    </p>
                </div>
            </div>
            <div class="contenedor-botones">
                <a href="./personal-solicitud-cambios.php">
                    <button class="boton-pdf" id="boton-pdf" style="margin-top: 10px; margin-bottom: 5px;">
                        <i class="fa fa-file-text" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-descargar"></i>Descargar</button></a>
            </div>
        </div>

        <!-- Contenedor principal de cada solicitud. -->
        <!--------------------------------------->
        <div class="info-sup" onclick="mostrarInformacion('contenedor-informacion-3', this.querySelector('.icono-despliegue i'))">
            <!-- DPTO Y TIPO DE SOLICITUD. -->
            <div class="color-rechazado" style="position: absolute; left: 0px;"></div>
            <div class="nombre-dpto-solicitud">
                <span>Tipo de solicitud:</span><span class="nombre-solicitud" id="solicitud-propuesta">Solicitud de propuesta</span>
            </div>
            <div class="nombre-dpto-solicitud">
                <span>Departamento:</span><span class="nombre-departamento" id="departamento">Departamento de recursos humanos</span>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <!-- HORA, FECHA Y ESTADO. -->
            <div class="fecha-hora-status">
                <span class="fecha-solicitud" type="date" id="campo-fecha"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 2024-01-01 </span>
                <span class="hora-solicitud" type="time" id="campo-hora"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 00:00 </span>
                <div class="circulo-rechazado">
                    <i class="fa fa-circle" aria-hidden="true"><span class="estado-solicitud" type="text" id="rechazado" style="margin-left: 10px;">Rechazado</span></i>
                </div>
            </div>

            <!-- Icono al extremo derecho, para el despliegue de ventana inferior -->
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>

        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-3">
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                    </p>
                    <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                    </p>
                    <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="folio">Folio de solicitud:
                    <p id="info-folio">54440216</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor actual</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno">Castanedo</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno">Escobedo</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="motivo">Motivo:
                    <p id="info-motivo">Jubilacion del profesor</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor propuesto</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno-propuesto">Gomez</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno-propuesto">Alcantara</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo-propuesto">123456789</p>
                    </p>
                </div>
            </div>
            <div class="contenedor-botones">
                <a href="./personal-solicitud-cambios.php">
                    <button class="boton-pdf" id="boton-pdf" style="margin-top: 10px; margin-bottom: 5px;">
                        <i class="fa fa-file-text" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-descargar"></i>Descargar</button></a>
            </div>
        </div>

        <!-- Contenedor principal de cada solicitud. -->
        <!--------------------------------------->
        <div class="info-sup" onclick="mostrarInformacion('contenedor-informacion-4', this.querySelector('.icono-despliegue i'))">
            <!-- DPTO Y TIPO DE SOLICITUD. -->
            <div class="color-pendiente" style="position: absolute; left: 0px;"></div>
            <div class="nombre-dpto-solicitud">
                <span>Tipo de solicitud:</span><span class="nombre-solicitud" id="solicitud-baja-propuesta">Solicitud de baja-propuesta</span>
            </div>
            <div class="nombre-dpto-solicitud">
                <span>Departamento:</span><span class="nombre-departamento" id="departamento">Departamento de mercadotecnia y negocios internacionales</span>
            </div>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <!-- HORA, FECHA Y ESTADO. -->
            <div class="fecha-hora-status">
                <span class="fecha-solicitud" type="date" id="campo-fecha"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 2024-01-01 </span>
                <span class="hora-solicitud" type="time" id="campo-hora"> <span style="margin-left: 0; font-weight: 300;">Fecha:</span> 00:00 </span>
                <div class="circulo-pendiente">
                    <i class="fa fa-circle" aria-hidden="true"><span class="estado-solicitud" type="text" id="pendiente" style="margin-left: 10px;">Pendiente</span></i>
                </div>
            </div>

            <!-- Icono al extremo derecho, para el despliegue de ventana inferior -->
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>

        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-4">
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                    </p>
                    <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                    </p>
                    <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="folio">Folio de solicitud:
                    <p id="info-folio">54440216</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor actual</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno">Castanedo</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno">Escobedo</p>
                    </p>
                </div>
                <div class="contenedor-centro">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="motivo">Motivo:
                    <p id="info-motivo">Jubilacion del profesor</p>
                    </p>
                </div>
            </div>
            <div class="titulo-info">
                <p>Profesor propuesto</p>
            </div>
            <div class="info">
                <div class="contenedor-izquierdo">
                    <p class="paterno">Apellido paterno:
                    <p id="info-paterno-propuesto">Gomez</p>
                    </p>
                    <p class="materno">Apellido materno:
                    <p id="info-materno-propuesto">Alcantara</p>
                    </p>
                </div>
                <div class="contenedor-derecho">
                    <p class="nombres">Nombre(s):
                    <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
                    </p>
                    <p class="codigo">Código:
                    <p id="info-codigo-propuesto">123456789</p>
                    </p>
                </div>
            </div>
            <div class="contenedor-botones">
                <a href="./personal-solicitud-cambios.php">
                    <button class="boton-pdf" id="boton-pdf" style="margin-top: 10px; margin-bottom: 5px;">
                        <i class="fa fa-file-text" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-descargar"></i>Descargar</button></a>
            </div>
        </div>


        <!-- Boton de nueva solicitud -->
        <div class="container-boton-nueva-solicitud">
            <button class="boton-nueva-solicitud" id="nueva-solicitud-btn">Nueva solicitud</button>
            <ul class="lista-opciones" id="lista-opciones">
                <li>Solicitud de baja</li>
                <li>Solicitud de propuesta</li>
                <li>Solicitud de baja-propuesta</li>
            </ul>
        </div>
    </div>
    
            <!-- Botones finales -->
            <div class="contenedor-botones">
                <a href="./personal-solicitud-cambios.php"><button class="boton-guardar" id="boton-cancelar"><i class="fa fa-check-circle" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-guardar"></i>Guardar</button></a>
                <a href="./personal-solicitud-cambios.php"><button class="boton-pdf" id="boton-pdf"><i class="fa fa-file-text" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-descargar"></i>Guardar y descargar</button></a>
                <a href="./personal-solicitud-cambios.php"><button class="boton-cancelar" id="boton-cancelar" style="background-color: #a7b3b9;"><i class="fa fa-times-circle" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-cancelar"></i>Cancelar</button></a>
            </div>
        </div>
    </div>

    <!-- Modal Solicitudes Baja -->
    <?php include './functions/personal-solicitud-cambios/modales/modal-baja.php' ?>
    <?php include './functions/personal-solicitud-cambios/modales/modal-propuesta.php' ?>
    <?php include './functions/personal-solicitud-cambios/modales/modal-baja-propuesta.php' ?>

    <!-- No funciona este script DOM si lo colocamos en el personal-solicitud-cambios.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('nueva-solicitud-btn');
                const lista = document.getElementById('lista-opciones');
                const modales = {
                    'Solicitud de baja': document.getElementById('solicitud-modal-baja-academica'),
                    'Solicitud de propuesta': document.getElementById('solicitud-modal-propuesta-academica'),
                    'Solicitud de baja-propuesta': document.getElementById('solicitud-modal-baja-propuesta')
                };

                // Asegurarse de que los modales estén ocultos al inicio
                Object.values(modales).forEach(modal => {
                    if (modal) modal.style.display = 'none';
                });

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    lista.classList.toggle('show');
                });

                lista.addEventListener('click', function(e) {
                    const opcionSeleccionada = e.target.innerText;
                    if (modales[opcionSeleccionada]) {
                        lista.classList.remove('show');
                        openModal(modales[opcionSeleccionada]);
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!btn.contains(e.target) && !lista.contains(e.target)) {
                        lista.classList.remove('show');
                    }
                });

                // Función para abrir el modal
                function openModal(modal) {
                    if (!modal) return; // Verificar que el modal existe
                    
                    modal.style.display = 'block';
                    
                    const closeButton = modal.querySelector('.close-button');
                    const modalContent = modal.querySelector('.modal-content-propuesta') || modal.querySelector('.modal-content-baja');
                    
                    if (closeButton) { // Verificar que existe el botón antes de agregar el evento
                        closeButton.addEventListener('click', function() {
                            modal.style.display = 'none';
                        });
                    }

                    if (modalContent) { // Verificar que existe el contenido antes de agregar el evento
                        modalContent.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }

                    // Agregar el evento de clic fuera una sola vez
                    const clickOutside = function(e) {
                        if (e.target === modal) {
                            modal.style.display = 'none';
                            window.removeEventListener('click', clickOutside); // Remover el evento después de usarlo
                        }
                    };
                    window.addEventListener('click', clickOutside);
                }
            });
    </script>
    <script src="./JS/personal-solicitud-cambios/personal-solicitud-cambios.js"></script>
    <script src="./JS/personal-solicitud-cambios/modal-baja.js"></script>
    <script src="./JS/personal-solicitud-cambios/modal-propuesta.js"></script>
    <script src="./JS/personal-solicitud-cambios/modal-baja-propuesta.js"></script>
    <script src="./JS/personal-solicitud-cambios/nueva-solicitud.js"></script>

<?php include("./template/footer.php"); ?>
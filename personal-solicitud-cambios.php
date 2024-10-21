<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Solicitudes de modificaciones</title>
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios.css?=v1.0">

<!--Cuadro principal del home -->
<div class="cuadro-principal">
    <div div class="encabezado">
        <div class="titulo-bd">
            <h3>Solicitudes de modificaciones</h3>
        </div>
    </div>
    <!-- Contenedor principal de cada solicitud. -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-1', this.querySelector('.icono-despliegue i'))">
        <div class="color-en_revision"></div> <!-- Color de estado de solicitud al extremo izquierdo -->
        <div class="texto-superior"> <!-- Contenedor del titulo de solicitud -->
            <p class="nombre-solicitud">
                <input type="text" id="solicitud" value="Solicitud de baja">
            </p>
        </div>
        <div class="texto-medio"> <!-- Contenedor del nombre del departamento -->
            <p class="nombre-departamento"> 
                <input type="text" id="departamento" value="Departamento de contaduria.">
            </p>
        </div>
        <ul class="texto-inferior"> <!-- Informacion en la parte inferior de la solicitud: Fecha y hora de solicitud -->
            <li class="fecha-solicitud">
                <input type="date" id="campo-fecha" value="2024-01-01" />
            </li>
            <li class="hora-solicitud">
                <input type="time" id="campo-hora" value="00:00" />
            </li>
            <li class="circulo-en_revision"> • </li>
            <li class="estado-solicitud">
                <input type="text" id="en-revision" value="En revisión" />
            </li>
        </ul>
        <!-- Icono al extremo derecho, para el despliegue de ventana inferior -->
        <div class="icono-despliegue">
            <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
        </div>
    </div>
    <!-- Contenedor que se desplegara. -->
    <div class="contenedor-informacion" id="contenedor-informacion-1">
        <div class="info">
            <p class="CRN">CRN:</p>
            <p id="info-CRN">154875</p>
            <p class="materia">Materia:</p>
            <p id="info-materia">Competividad de la actividad gastronomica</p>
            <p class="clave">Clave:</p>
            <p id="info-clave">IC366</p>
            <p class="SEC">SEC:</p>
            <p id="info-SEC">C02</p>
            <p class="folio">Folio de solicitud:</p>
            <p id="info-folio">54440216</p>
        </div>
        <div class="titulo-info">
            <p>Profesor actual</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno">Castanedo</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno">Escobedo</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres">Rafael Eduardo Alfonso</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo">215195673</p>
            <p class="motivo">Motivo:</p>
            <p id="info-motivo">Jubilacion del profesor</p>
        </div>
        <div class="titulo-info">
            <p>Profesor propuesto</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno-propuesto">Gomez</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno-propuesto">Alcantara</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo-propuesto">123456789</p>
        </div>
    </div>

    <!-- Prueba para solicitudes rechazadas -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-2', this.querySelector('.icono-despliegue i'))">
        <div class="color-rechazado"></div>
        <div class="texto-superior">
            <p class="nombre-solicitud">
                <input type="text" id="solicitud" value="Solicitud de propuesta">
            </p>
        </div>
        <div class="texto-medio">
            <p class="nombre-departamento"> 
                <input type="text" id="departamento" value="Departamento de negocios internacionales.">
            </p>
        </div>
        <ul class="texto-inferior">
            <li class="fecha-solicitud">
                <input type="date" id="campo-fecha" value="2024-01-01" />
            </li>
            <li class="hora-solicitud">
                <input type="time" id="campo-hora" value="00:00" />
            </li>
            <li class="circulo-rechazado"> • </li>
            <li class="estado-solicitud">
                <input type="text" id="rechazado" value="Rechazado" />
            </li>
        </ul>
        <div class="icono-despliegue">
            <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
        </div>
    </div>
    <!-- Contenedor que se desplegara. -->
    <div class="contenedor-informacion" id="contenedor-informacion-2">
    <div class="info">
            <p class="CRN">CRN:</p>
            <p id="info-CRN">154875</p>
            <p class="materia">Materia:</p>
            <p id="info-materia">Competividad de la actividad gastronomica</p>
            <p class="clave">Clave:</p>
            <p id="info-clave">IC366</p>
            <p class="SEC">SEC:</p>
            <p id="info-SEC">C02</p>
            <p class="folio">Folio de solicitud:</p>
            <p id="info-folio">54440216</p>
        </div>
        <div class="titulo-info">
            <p>Profesor actual</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno">Castanedo</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno">Escobedo</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres">Rafael Eduardo Alfonso</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo">215195673</p>
            <p class="motivo">Motivo:</p>
            <p id="info-motivo">Jubilacion del profesor</p>
        </div>
        <div class="titulo-info">
            <p>Profesor propuesto</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno-propuesto">Gomez</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno-propuesto">Alcantara</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo-propuesto">123456789</p>
        </div>
    </div>

    <!-- Prueba para solicitudes aprobadas -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-3', this.querySelector('.icono-despliegue i'))">
        <div class="color-aprobado"></div>
        <div class="texto-superior">
            <p class="nombre-solicitud">
                <input type="text" id="solicitud" value="Solicitud de baja-propuesta">
            </p>
        </div>
        <div class="texto-medio">
            <p class="nombre-departamento"> 
                <input type="text" id="departamento" value="Departamento de administracion.">
            </p>
        </div>
        <ul class="texto-inferior">
            <li class="fecha-solicitud">
                <input type="date" id="campo-fecha" value="2024-01-01" />
            </li>
            <li class="hora-solicitud">
                <input type="time" id="campo-hora" value="00:00" />
            </li>
            <li class="circulo-aprobado"> • </li>
            <li class="estado-solicitud">
                <input type="text" id="aprobado" value="Aprobado" />
            </li>
        </ul>
        <div class="icono-despliegue">
            <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
        </div>
    </div>
    <!-- Contenedor que se desplegara. -->
    <div class="contenedor-informacion" id="contenedor-informacion-3">
    <div class="info">
            <p class="CRN">CRN:</p>
            <p id="info-CRN">154875</p>
            <p class="materia">Materia:</p>
            <p id="info-materia">Competividad de la actividad gastronomica</p>
            <p class="clave">Clave:</p>
            <p id="info-clave">IC366</p>
            <p class="SEC">SEC:</p>
            <p id="info-SEC">C02</p>
            <p class="folio">Folio de solicitud:</p>
            <p id="info-folio">54440216</p>
        </div>
        <div class="titulo-info">
            <p>Profesor actual</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno">Castanedo</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno">Escobedo</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres">Rafael Eduardo Alfonso</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo">215195673</p>
            <p class="motivo">Motivo:</p>
            <p id="info-motivo">Jubilacion del profesor</p>
        </div>
        <div class="titulo-info">
            <p>Profesor propuesto</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno-propuesto">Gomez</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno-propuesto">Alcantara</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo-propuesto">123456789</p>
        </div>
    </div>

    <!-- Prueba para solicitudes pendientes -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-4', this.querySelector('.icono-despliegue i'))">
        <div class="color-pendiente"></div>
        <div class="texto-superior">
            <p class="nombre-solicitud">
                <input type="text" id="solicitud" value="Solicitud de baja-propuesta">
            </p>
        </div>
        <div class="texto-medio">
            <p class="nombre-departamento"> 
                <input type="text" id="departamento" value="Departamento de sistemas de la informacion.">
            </p>
        </div>
        <ul class="texto-inferior">
            <li class="fecha-solicitud">
                <input type="date" id="campo-fecha" value="2024-01-01" />
            </li>
            <li class="hora-solicitud">
                <input type="time" id="campo-hora" value="00:00" />
            </li>
            <li class="circulo-pendiente"> • </li>
            <li class="estado-solicitud">
                <input type="text" id="pendiente" value="Pendiente" />
            </li>
        </ul>
        <div class="icono-despliegue">
            <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
        </div>
    </div>
    <!-- Contenedor que se desplegara. -->
    <div class="contenedor-informacion" id="contenedor-informacion-4">
    <div class="info">
            <p class="CRN">CRN:</p>
            <p id="info-CRN">154875</p>
            <p class="materia">Materia:</p>
            <p id="info-materia">Competividad de la actividad gastronomica</p>
            <p class="clave">Clave:</p>
            <p id="info-clave">IC366</p>
            <p class="SEC">SEC:</p>
            <p id="info-SEC">C02</p>
            <p class="folio">Folio de solicitud:</p>
            <p id="info-folio">54440216</p>
        </div>
        <div class="titulo-info">
            <p>Profesor actual</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno">Castanedo</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno">Escobedo</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres">Rafael Eduardo Alfonso</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo">215195673</p>
            <p class="motivo">Motivo:</p>
            <p id="info-motivo">Jubilacion del profesor</p>
        </div>
        <div class="titulo-info">
            <p>Profesor propuesto</p>
        </div>
        <div class="info">
            <p class="paterno">Apellido paterno:</p>
            <p id="info-paterno-propuesto">Gomez</p>
            <p class="materno">Apellido materno:</p>
            <p id="info-materno-propuesto">Alcantara</p>
            <p class="nombres">Nombre(s):</p>
            <p id="info-nombres-propuesto">Gustavo Arnulfo Alcaraz</p>
            <p class="codigo">Código:</p>
            <p id="info-codigo-propuesto">123456789</p>
        </div>
    </div>

    <!-- Boton de nueva solicitud -->
    <div class="container-boton-nueva-solicitud">
        <button class="boton-nueva-solicitud" id="nueva-solicitud-btn">Nueva solicitud</button>
        <ul class="lista-opciones" id="lista-opciones">
            <li><a href="./solicitud-baja.php">Solicitud de baja</a></li>
            <li><a href="./solicitud-propuesta.php">Solicitud de propuesta</a></li>
            <li><a href="./solicitud-baja-propuesta.php">Solicitud de baja-propuesta</a></li>
        </ul>
    </div>
</div>

<!-- No funciona este script DOM si lo colocamos en el personal-solicitud-cambios.js -->
<script>
document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('nueva-solicitud-btn');
        const lista = document.getElementById('lista-opciones');

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            lista.classList.toggle('show');
            console.log('Botón clickeado, toggle aplicado');
        });

        lista.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                console.log('Enlace clickeado:', e.target.href);
                // Si quieres prevenir la navegación y manejarlo tú mismo, descomenta la siguiente línea:
                // e.preventDefault();
            }
        });

        document.addEventListener('click', function(e) {
            if (!btn.contains(e.target) && !lista.contains(e.target)) {
                lista.classList.remove('show');
                console.log('Clic fuera del menú, menú cerrado');
            }
        });
    });
</script>
<!-- Script para las funciones del despliegue de contenedor hacia abajo al hacer click -->
<script src="./JS/personal-solicitud-cambios/personal-solicitud-cambios.js"></script>
<script src="./JS/pestañas-plantilla.js"></>

<?php include ("./template/footer.php"); ?>
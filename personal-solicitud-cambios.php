<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Solicitudes de modificaciones</title>
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios.css">

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

<!-- Modal para las solicitudes de baja-propuesta -->
<div id="solicitud-modal-baja-propuesta" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button" id="boton-cancelar">&times;</span>
        <h2 class="titulo-modal">Solicitud de baja-propuesta</h2>

        <!-- Encabezado para materia -->
        <h3 class="titulo-materia" style="margin-top: 60px; position: absolute;">Datos de la materia</h3>

        <!-- Campos relacionados a la materia -->
        <div class="campos-materia">
            <div class="borde-CRN">
                <p>CRN</p>
                <input class="texto-CRN" id="texto-CRN" type="text" maxlength="6">
            </div>
            <div class="borde-materia">
                <p>Materia</p>
                <input class="texto-materia" id="texto-materia" type="text"> 
            </div>
            <div class="borde-clave">
                <p>Clave</p>
                <input class="texto-clave" id="texto-clave" type="text" maxlength="5">
            </div>
            <div class="borde-SEC">
                <p>SEC</p>
                <input class="texto-SEC" id="texto-SEC" type="text" maxlength="3">
            </div>
            <div class="borde-folio">
                <p>Folio de solicitud</p>
                <input class="texto-folio" id="texto-folio" type="text" maxlength="10">
            </div>
        </div>

        <!-- Encabezado para profesor actual -->
        <h3 class="titulo-profesor">Profesor actual</h3>

        <!-- Campos relacionados al profesor actual -->
        <div class="campos-profesor">
            <div class="borde-apellido-paterno">
                <p>Apellido paterno</p>
                <input class="texto-apellido-paterno" id="texto-apellido-paterno" type="text" maxlength="50">
            </div>
            <div class="borde-apellido-materno">
                <p>Apellido materno</p>
                <input class="texto-apellido-materno" id="texto-apellido-materno" type="text" maxlength="50"> 
            </div>
            <div class="borde-nombres">
                <p>Nombre(s)</p>
                <input class="texto-nombres" id="texto-nombres" type="text" maxlength="50"> 
            </div>
            <div class="borde-codigo">
                <p>Código</p>
                <input class="texto-codigo" id="texto-codigo" type="text" maxlength="10">  
            </div>
        </div>

        <!-- Campos relacionados al motivo de la baja -->
        <div class="campos-motivos">
            <div class="borde-motivo">
                <p>Motivo</p> 
                <select name="texto-motivo" class="texto-motivo" id="texto-motivo">
                    <option value="" disabled selected>Seleccione el motivo de baja</option>
                    <option value="no-asistir">No asiste a clases con regularidad</option>
                    <option value="no-respetar">No respeta a los alumnos de la clase</option>
                    <option value="no-vive">Fallecimiento del profesor</option>
                    <option value="no-trabaja">Jubilacion del profesor</option>
                </select>
            </div>
            <div class="borde-otro">
                <p>Otro</p>
                <input class="texto-otro" id="texto-otro" type="text" maxlength="120"> 
            </div>
        </div>

        <!-- Encabezado para profesor propuesto -->
        <h3 class="titulo-profesor">Profesor propuesto</h3>
        
        <!-- Campos relacionados al profesor propuesto (Mismos campos y estilos de profesor CSS) 
        a excepcion de los ID's -->
        <div class="campos-profesor">
            <div class="borde-apellido-paterno">
                <p>Apellido paterno</p>
                <input class="texto-apellido-paterno" id="texto-apellido-paterno_propuesto" type="text" maxlength="50">
            </div>
            <div class="borde-apellido-materno">
                <p>Apellido materno</p>
                <input class="texto-apellido-materno" id="texto-apellido-materno_propuesto" type="text" maxlength="50"> 
            </div>
            <div class="borde-nombres">
                <p>Nombre(s)</p>
                <input class="texto-nombres" id="texto-nombres_propuesto" type="text" maxlength="50">  
            </div>
            <div class="borde-codigo">
                <p>Código</p>
                <input class="texto-codigo" id="texto-codigo_propuesto" type="text" maxlength="10">  
            </div>
        </div>
        <div class="campos-profesor">
            <div class="borde-movimiento">
                <p>Movimiento</p> 
                <select name="texto-movimiento" class="texto-movimiento" id="texto-movimiento">
                    <option value="" disabled selected>Seleccione el movimiento</option>
                    <option value="">Movimiento no especificado</option>
                    <option value="">Movimiento #1</option>
                    <option value="">Movimiento #2</option>
                    <option value="">Movimiento #3</option>
                </select>
            </div>
            <div class="borde-contrato" id="borde-margin">
                <p>Contrato</p> 
                <select name="texto-contrato" class="texto-contrato" id="texto-contrato">
                    <option value="" disabled selected>Seleccione tipo de contrato</option>
                    <option value="">Tiempo indefinido</option>
                    <option value="">Tiempo definido</option>
                    <option value="">Contrato temporal</option>
                    <option value="">Sustitucion</option>
                </select>
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

<!-- Modal para las solicitudes de propuesta académica -->
<div id="solicitud-modal-propuesta-academica" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button" id="boton-cancelar">&times;</span>
        <h2 class="titulo-modal">Solicitud de propuesta académica</h2>

        <!-- Encabezado para materia -->
        <h3 class="titulo-materia" style="margin-top: 60px; position: absolute;">Datos de la materia</h3>

        <!-- Campos relacionados a la materia -->
        <div class="campos-materia">
            <div class="borde-CRN">
                <p>CRN</p>
                <input class="texto-CRN" id="texto-CRN" type="text">
            </div>
            <div class="borde-materia">
                <p>Materia</p>
                <input class="texto-materia" id="texto-materia" type="text">
            </div>
            <div class="borde-clave">
                <p>Clave</p>
                <input class="texto-clave" id="texto-clave" type="text"> 
            </div>
            <div class="borde-SEC">
                <p>SEC</p>
                <input class="texto-SEC" id="texto-SEC" type="text">  
            </div>
            <div class="borde-folio">
                <p>Folio de solicitud</p>
                <input class="texto-folio" id="texto-folio" type="text"> 
            </div>
        </div>

        <!-- Encabezado para profesor -->
        <h3 class="titulo-profesor">Datos de profesor</h3>

        <!-- Campos relacionados al profesor propuesto -->
        <div class="campos-profesor">
            <div class="borde-apellido-paterno">
                <p>Apellido paterno</p>
                <input class="texto-apellido-paterno" id="texto-apellido-paterno" type="text">
            </div>
            <div class="borde-apellido-materno">
                <p>Apellido materno</p>
                <input class="texto-apellido-materno" id="texto-apellido-materno" type="text"> 
            </div>
            <div class="borde-nombres">
                <p>Nombre(s)</p>
                <input class="texto-nombres" id="texto-nombres" type="text"> 
            </div>
            <div class="borde-codigo">
                <p>Código</p>
                <input class="texto-codigo" id="texto-codigo" type="text">  
            </div>
        </div>

        <!-- Se usa la misma estructura de campos-motivos para tipo de contrato -->
        <div class="campos-motivos" id="borde-margin">
            <div class="borde-movimiento">
                <p>Movimiento</p> 
                <select name="texto-movimiento" class="texto-movimiento" id="texto-movimiento">
                    <option value="" disabled selected>Seleccione el movimiento</option>
                    <option value="">Movimiento no especificado</option>
                    <option value="">Movimiento #1</option>
                    <option value="">Movimiento #2</option>
                    <option value="">Movimiento #3</option>
                </select>
            </div>
            <div class="borde-contrato">
                <p>Contrato</p> 
                <select name="texto-contrato" class="texto-contrato" id="texto-contrato">
                    <option value="" disabled selected>Seleccione tipo de contrato</option>
                    <option value="">Tiempo indefinido</option>
                    <option value="">Tiempo definido</option>
                    <option value="">Contrato temporal</option>
                    <option value="">Sustitucion</option>
                </select>
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

<!-- Modal para las solicitudes de baja académica -->
<div id="solicitud-modal-baja-academica" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button" id="boton-cancelar">&times;</span>
        <h2 class="titulo-modal">Solicitud de baja académica</h2>
        
        <!-- Encabezado para materia -->
        <h3 class="titulo-materia" style="margin-top: 60px; position: absolute;">Datos de la materia</h3>

        <!-- Campos relacionados a la materia -->
        <div class="campos-materia">
            <div class="borde-CRN">
                <p>CRN</p>
                <input class="texto-CRN" id="texto-CRN" type="text" maxlength="6">
            </div>
            <div class="borde-materia">
                <p>Materia</p>
                <input class="texto-materia" id="texto-materia" type="text"> 
            </div>
            <div class="borde-clave">
                <p>Clave</p>
                <input class="texto-clave" id="texto-clave" type="text"> 
            </div>
            <div class="borde-SEC">
                <p>SEC</p>
                <input class="texto-SEC" id="texto-SEC" type="text">  
            </div>
            <div class="borde-folio">
                <p>Folio de solicitud</p>
                <input class="texto-folio" id="texto-folio" type="text"> 
            </div>
        </div>

        <!-- Encabezado para profesor -->
        <h3 class="titulo-profesor">Datos del profesor</h3>

        <!-- Campos relacionados al profesor actual -->
        <div class="campos-profesor">
            <div class="borde-apellido-paterno">
                <p>Apellido paterno</p>
                <input class="texto-apellido-paterno" id="texto-apellido-paterno" type="text">
            </div>
            <div class="borde-apellido-materno">
                <p>Apellido materno</p>
                <input class="texto-apellido-materno" id="texto-apellido-materno" type="text"> 
            </div>
            <div class="borde-nombres">
                <p>Nombre(s)</p>
                <input class="texto-nombres" id="texto-nombres" type="text"> 
            </div>
            <div class="borde-codigo">
                <p>Código</p>
                <input class="texto-codigo" id="texto-codigo" type="text">  
            </div>
        </div>

        <!-- Campos relacionados al motivo de la baja -->
        <div class="campos-motivos">
            <div class="borde-motivo">
                <p>Motivo</p> 
                <select name="texto-motivo" class="texto-motivo" id="texto-motivo">
                    <option value="" disabled selected>Seleccione el motivo de baja</option>
                    <option value="no-asistir">No asiste a clases con regularidad</option>
                    <option value="no-respetar">No respeta a los alumnos de la clase</option>
                    <option value="no-vive">Fallecimiento del profesor</option>
                    <option value="no-trabaja">Jubilacion del profesor</option>
                </select>
            </div>
            <div class="borde-otro" id="borde-margin">
                <p>Otro</p>
                <input class="texto-otro" id="texto-otro" type="text"> 
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

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        lista.classList.toggle('show');
    });

    lista.addEventListener('click', function(e) {
        const opcionSeleccionada = e.target.innerText;
        
        if (opcionSeleccionada in modales) {
            // Cerrar la lista y abrir el modal correspondiente
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
        modal.style.display = 'block';
        const closeButton = modal.querySelector('.close-button');
        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Evitar que el modal se cierre al hacer clic en su contenido
        const modalContent = modal.querySelector('.modal-content');
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation(); // Detener la propagación del clic
        });

        // Cerrar el modal si se hace clic fuera del contenido del modal
        window.addEventListener('click', function(e) {
            if (e.target === modal) { // Verificamos si el clic fue fuera del contenido
                modal.style.display = 'none';
            }
        });
    }

// Limitaciones de texto y numeros en campos del modal
    document.querySelectorAll(".texto-CRN").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
        });
    });
    document.querySelectorAll(".texto-materia").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });
    document.querySelectorAll(".texto-clave").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 5);
        });
    });
    document.querySelectorAll(".texto-SEC").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 3);
        });
    });
    document.querySelectorAll(".texto-folio").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 10);
        });
    });
    document.querySelectorAll(".texto-apellido-paterno").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 50);
        });
    });
    document.querySelectorAll(".texto-apellido-materno").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 50);
        });
    });
    document.querySelectorAll(".texto-nombres").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 50);
        });
    });
    document.querySelectorAll(".texto-codigo").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
        });
    });
    document.querySelectorAll(".texto-otro").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 120);
        });
    });
    document.querySelectorAll(".texto-apellido-paterno_propuesto").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 50);
        });
    });
    document.querySelectorAll(".texto-apellido-materno_propuesto").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 50);
        });
    });
    document.querySelectorAll(".texto-nombres_propuesto").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 50);
        });
    });
    document.querySelectorAll(".texto-codigo_propuesto").forEach(input => {
        input.addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
        });
    });
});
</script>

<!-- Estilos del modal (No funcionan en el .css) -->
<style>
.modal {
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.7);
}

/* Contenido del modal */
.modal-content {
  position: fixed; 
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%); 
  display: flex;
  flex-wrap: wrap;
  flex: 1;
  background-color: #fff; 
  padding: 20px; 
  border: 1px solid #888; 
  width: 80%;
  max-width: 1600px; 
  border-radius: 10px;
  margin: 0 auto;
  overflow-y: auto;
  max-height: 90vh;
}

.titulo-modal {
  margin-left: 1.1vw;
}

/* Botón de cerrar */
.close-button {
  color: #aaa;
  position: absolute;
  right: 30;
  font-size: 38px; 
  font-weight: bold;
}

.close-button:hover,
.close-button:focus {
  color: black; 
  text-decoration: none; 
  cursor: pointer; 
}

/* Contenedor para los campos de la materia */
.campos-materia,
.campos-profesor,
.campos-motivos {
  display: inline-flex;
  flex-wrap: wrap;
  width: 76vw;
  margin-left: 55px;
  height: fit-content;
}
.campos-materia {
  margin-top: 15px;
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
}
.campos-profesor,
.campos-motivos {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
}

/* Estilos del texto-titulo del campo */
.campos-materia p,
.campos-profesor p,
.campos-motivos p {
  position: relative;
  bottom: 43px;
}

.titulo-profesor,
.titulo-materia {
  display: inline-flex;
  margin-left: 1.1vw;
  margin-bottom: 0;
  color: rgb(148, 144, 144);
}

/* Estilos de los bordes de todos los campos de la pagina */
.borde-CRN,
.borde-materia,
.borde-clave,
.borde-SEC,
.borde-folio,
.borde-apellido-paterno,
.borde-apellido-materno,
.borde-nombres,
.borde-codigo,
.borde-motivo,
.borde-otro,
.borde-contrato,
.borde-movimiento {
  position: relative;
  right: 33px;
  margin-left: 1.1vw;
  margin-top: 40px;
  height: 50px;
}

/* Estilos de los bordes especificos */
.borde-CRN,
.borde-clave,
.borde-SEC {
  width: 25%;
}
.borde-materia {
  width: 100%;
}
.borde-folio {
  width: 100%;
}
.borde-apellido-paterno,
.borde-apellido-materno,
.borde-codigo,
.borde-otro {
  width: 68%;
}
.borde-movimiento {
  width: 45.2%;
}
.borde-nombres {
  width: 100%;
}
.borde-motivo,
.borde-contrato {
  width: 55.6%;
}
#borde-margin {
  margin-bottom: 50px;
}

/* Estilos de todos los input type text para escribir */
.texto-CRN,
.texto-materia,
.texto-clave,
.texto-SEC,
.texto-folio,
.texto-apellido-paterno,
.texto-apellido-materno,
.texto-nombres,
.texto-codigo,
.texto-motivo,
.texto-otro,
.texto-movimiento,
.texto-contrato {
  position: relative;
  align-self: center;
  bottom: 50px;
  left: 10px;
  font-size: 0.9rem;
  outline: none;
  border-style: solid;
  border-radius: 10px;
  border-width: 2px;
  border-color: #E7E9F2;
  padding: 10px;
}
/* Estilos especificos de los input */
.texto-CRN,
.texto-clave,
.texto-SEC {
  width: 100%;
}
.texto-materia {
  width: 100%;
}
.texto-folio {
  width: 100%;
}
.texto-apellido-materno,
.texto-apellido-paterno,
.texto-codigo,
.texto-movimiento {
  width: 100%;
}
.texto-nombres {
  width: 100%;
}
.texto-motivo,
.texto-contrato {
  outline: none;
  border-style: solid;
  width: 100%;
}
.texto-otro {
  width: 100%;
}

/* Estilos de botones del final (Cancelar o guardar) */
.contenedor-botones {
  display: flex; 
  justify-content: center;
  margin: 0 auto;
  width: 100%;
  margin-bottom: 10px;
  height: 50px;
}
.contenedor-botones button {
  margin: 0 7px; 
  border-radius: 10px;
  border-style: none;
  padding: 1vh 2.5vw 1vh;
  font-size: 1rem;
  font-weight: bold;
  color: white;
  background-color: #0071b0;
  border-color: #0071b0;
  cursor: pointer;
  box-shadow: 0px 2px 2px rgb(185, 174, 174);
}

@media screen and (max-width: 992px) {
.campos-profesor,
.campos-motivos {
    flex-wrap: nowrap;
    }
.campos-materia {
    flex-wrap: wrap;
    }
.borde-CRN,
.borde-clave,
.borde-SEC {
    width: 25%;
    }
.borde-materia,
.borde-apellido-paterno,
.borde-apellido-materno,
.borde-nombres,
.borde-otro,
.borde-motivo,
.borde-codigo {
    width: 70%;
    }
.borde-folio {
    width: 45%;
    }
.texto-CRN,
.texto-materia,
.texto-clave,
.texto-SEC,
.texto-folio,
.texto-apellido-paterno,
.texto-apellido-materno,
.texto-nombres,
.texto-codigo,
.texto-motivo,
.texto-otro,
.texto-movimiento,
.texto-contrato {
    font-size: 0.8rem;
    }
}


@media screen and (max-width: 768px) {
.borde-CRN,
.borde-materia,
.borde-clave,
.borde-SEC,
.borde-folio,
.borde-apellido-paterno,
.borde-apellido-materno,
.borde-nombres,
.borde-codigo,
.borde-motivo,
.borde-otro,
.borde-movimiento,
.borde-contrato {
    width: 100%;
    margin-top: 20;
    }
.campos-profesor {
    margin-top: 20;
    }
.campos-materia {
    margin-top: 40;
    }
.campos-profesor,
.campos-motivos,
.campos-materia {
    flex-wrap: wrap;
    }
}

@media screen and (max-width: 630px) {
  .modal-content {
    width: 95%; 
  }
  .contenedor-botones button {
  font-size: 0.8rem;
  border-radius: 7px;
  }
}
</style>

<!-- Script para las funciones del despliegue de contenedor hacia abajo al hacer click -->
<script src="./JS/personal-solicitud-cambios/personal-solicitud-cambios.js"></script>
<script src="./JS/pestañas-plantilla.js"></script>

<?php include ("./template/footer.php"); ?>
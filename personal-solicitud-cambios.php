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
            <button class="boton-pdf" id="boton-pdf" style="background-color: #EC4E4E; margin-top: 10px; margin-bottom: 5px;">
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

<!-- Modal para las solicitudes de baja académica -->
<div id="solicitud-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button" id="boton-cancelar">&times;</span>
        <h2 class="titulo-modal">Solicitud de baja académica</h2>
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

        <!-- Encabezado para profesor actual -->
        <h3 class="titulo-profesor">Profesor actual</h3>

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
                    <!-- Ejemplos muy equis de xd -->
                    <option value="" disabled selected>Seleccione el motivo de baja</option>
                    <option value="no-asistir">No asiste a clases con regularidad</option>
                    <option value="no-respetar">No respeta a los alumnos de la clase</option>
                    <option value="no-vive">Fallecimiento del profesor</option>
                    <option value="no-trabaja">Jubilacion del profesor</option>
                </select>
            </div>
            <div class="borde-otro">
                <p>Otro</p>
                <input class="texto-otro" id="texto-otro" type="text"> 
            </div>
        </div>

        <!-- Encabezado para profesor propuesto -->
        <h3 class="titulo-profesor">Profesor propuesto</h3>

        <!-- Campos relacionados al profesor propuesto (Mismos campos y estilos de profesor CSS) 
        a excepcion de los ID's -->
        <div class="campos-profesor">
            <div class="borde-apellido-paterno">
                <p>Apellido paterno</p>
                <input class="texto-apellido-paterno" id="texto-apellido-paterno_propuesto" type="text">
            </div>
            <div class="borde-apellido-materno">
                <p>Apellido materno</p>
                <input class="texto-apellido-materno" id="texto-apellido-materno_propuesto" type="text"> 
            </div>
            <div class="borde-nombres">
                <p>Nombre(s)</p>
                <input class="texto-nombres" id="texto-nombres_propuesto" type="text"> 
            </div>
            <div class="borde-codigo" id="borde-codigo-margin">
                <p>Código</p>
                <input class="texto-codigo" id="texto-codigo_propuesto" type="text">  
            </div>
        </div>

        <!-- Botones finales -->
        <div class="contenedor-botones">
            <a href="./personal-solicitud-cambios.php"><button class="boton-guardar" id="boton-cancelar"><i class="fa fa-check-circle" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-guardar"></i>Guardar</button></a>
            <a href="./personal-solicitud-cambios.php"><button class="boton-pdf" id="boton-pdf" style="background-color: #EC4E4E;"><i class="fa fa-file-text" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-descargar"></i>Guardar y descargar</button></a>
            <a href="./personal-solicitud-cambios.php"><button class="boton-cancelar" id="boton-cancelar" style="background-color: #a7b3b9;"><i class="fa fa-times-circle" aria-hidden="true" style="margin-right: 0.5vw;" id="mod-cancelar"></i>Cancelar</button></a>
        </div>
    </div>
</div>

<!-- No funciona este script DOM si lo colocamos en el personal-solicitud-cambios.js -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('nueva-solicitud-btn');
    const lista = document.getElementById('lista-opciones');
    const inputs = lista.querySelectorAll('input'); // Selecciona todos los inputs dentro de la lista
    const textareas = lista.querySelectorAll('textarea'); // Selecciona todos los textarea dentro de la lista

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        lista.classList.toggle('show');
    });

    lista.addEventListener('click', function(e) {
        if (e.target.tagName === 'A') {
            e.preventDefault();
        }
    });

    document.addEventListener('click', function(e) {
        if (!btn.contains(e.target) && !lista.contains(e.target)) {
            lista.classList.remove('show');
            clearInputs(); // Borra el contenido de los inputs y textareas cuando se hace clic afuera
        }
    });

    // Función que borra el contenido de todos los inputs y textareas
    function clearInputs() {
        inputs.forEach(input => {
            input.value = ''; // Borra el valor de cada input
        });
        textareas.forEach(textarea => {
            textarea.value = ''; // Borra el valor de cada textarea
        });
    }
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
  position: relative;
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
  top: 30px;
  height: 90vh;
  overflow-y: auto; 
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
}

/* Estilos del texto-titulo del campo */
.campos-materia p,
.campos-profesor p,
.campos-motivos p {
  position: relative;
  bottom: 43px;
}

.titulo-profesor {
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
.borde-otro {
  position: relative;
  right: 33px;
  margin-left: 1.1vw;
  margin-top: 40px;
  height: 50px;
  border-style: ridge;
  border-color: rgb(174, 170, 170, 0.2);
  border-width: 2px;
  border-radius: 12px;
}

/* Estilos de los bordes especificos */
.borde-CRN,
.borde-clave,
.borde-SEC {
  width: 120px;
}
.borde-materia {
  width: 700px;
}
.borde-folio {
  width: 240px;
}
.borde-apellido-paterno,
.borde-apellido-materno,
.borde-codigo,
.borde-otro {
  width: 250px;
}
.borde-nombres {
  width: 500px;
}
.borde-motivo {
  width: 640px;
}
#borde-codigo-margin {
  margin-bottom: 20px;
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
.texto-otro {
  position: relative;
  bottom: 40px;
  left: 10px;
  font-size: 1rem;
  outline: none;
  border-style: none;
}

/* Estilos especificos de los input */
.texto-CRN,
.texto-clave,
.texto-SEC {
  width: 83%;
}
.texto-materia {
  width: 97%;
}
.texto-folio {
  width: 91%;
}
.texto-apellido-materno,
.texto-apellido-paterno,
.texto-codigo {
  width: 93%;
}
.texto-nombres {
  width: 96%;
}
.texto-motivo {
  outline: none;
  border-style: none;
  width: 97%;
}
.texto-otro {
  width: 93%;
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
.borde-otro {
    width: 100%;
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
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
    <!-- Contenedor principal de cada solicitud. -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-1', this.querySelector('.icono-despliegue i'))">
        <div class="color-en_revision"></div> <!-- Color de estado de solicitud al extremo izquierdo -->
        <div class="texto-superior"> <!-- Contenedor del titulo de solicitud -->
            <p class="nombre-solicitud" id="solicitud">Solicitud de baja</p>
        </div>
        <div class="texto-medio"> <!-- Contenedor del nombre del departamento -->
            <p class="nombre-departamento" id="departamento">Departamento de contaduria</p>
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
            <div class="contenedor-izquierdo">
                <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                </p>
                <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                </p>
                <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                </p>
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
                <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                </p>
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

    <!-- Prueba para solicitudes rechazadas -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-2', this.querySelector('.icono-despliegue i'))">
        <div class="color-rechazado"></div>
        <div class="texto-superior">
            <p class="nombre-solicitud" id="solicitud">Solicitud de propuesta</p>
        </div>
        <div class="texto-medio">
            <p class="nombre-departamento" id="departamento">Departamento de negocios internacionales</p>
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
            <div class="contenedor-izquierdo">
                <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                </p>
                <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                </p>
                <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                </p>
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
                <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                </p>
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

    <!-- Prueba para solicitudes aprobadas -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-3', this.querySelector('.icono-despliegue i'))">
        <div class="color-aprobado"></div>
        <div class="texto-superior">
            <p class="nombre-solicitud" id="solicitud">Solicitud de baja-propuesta</p>
        </div>
        <div class="texto-medio">
            <p class="nombre-departamento" id="departamento">Departamento de administracion</p>
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
            <div class="contenedor-izquierdo">
                <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                </p>
                <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                </p>
                <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                </p>
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
                <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                </p>
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

    <!-- Prueba para solicitudes pendientes -->
    <div class="solicitud-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-4', this.querySelector('.icono-despliegue i'))">
        <div class="color-pendiente"></div>
        <div class="texto-superior">
            <p class="nombre-solicitud" id="solicitud">Solicitud de baja-propuesta</p>
        </div>
        <div class="texto-medio">
            <p class="nombre-departamento" id="departamento">Departamento de sistemas de la informacion</p>
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
            <div class="contenedor-izquierdo">
                <p class="CRN">CRN:
                    <p id="info-CRN">154875</p>
                </p>
                <p class="materia">Materia:
                    <p id="info-materia">Competividad de la actividad gastronomica</p>
                </p>
                <p class="clave">Clave:
                    <p id="info-clave">IC366</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="SEC">SEC:
                    <p id="info-SEC">C02</p>
                </p>
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
                <p class="nombres">Nombre(s):
                    <p id="info-nombres">Rafael Eduardo Alfonso</p>
                </p>
            </div>
            <div class="contenedor-derecho">
                <p class="codigo">Código:
                    <p id="info-codigo">215195673</p>
                </p>
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

<!-- Modal para las solicitudes de baja-propuesta -->

<div id="solicitud-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button" id="boton-cancelar">&times;</span>
        <h2 class="titulo-modal">Solicitud de baja-propuesta</h2>
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
            <div class="borde-codigo">
                <p>Código</p>
                <input class="texto-codigo" id="texto-codigo_propuesto" type="text">  
            </div>
        </div>

        <!-- Botones finales -->
        <div class="contenedor-botones">
            <a href="./personal-solicitud-cambios.php"><button class="boton-guardar" id="boton-cancelar">Guardar</button></a>
            <a href="./personal-solicitud-cambios.php"><button class="boton-cancelar" id="boton-cancelar" style="background-color: #a7b3b9;">Cancelar</button></a>
        </div>
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
        });

        lista.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                e.preventDefault();
            }
        });

        document.addEventListener('click', function(e) {
            if (!btn.contains(e.target) && !lista.contains(e.target)) {
                lista.classList.remove('show');
            }
        });
    });
</script>

<!-- Estilos del modal (No funcionan en el .css) -->
<style>
.modal {
  display: none; 
  position: fixed;
  z-index: 1000; 
  left: 0;
  bottom: 0;
  width: 100%; 
  overflow: hidden; 
  background-color: rgba(0, 0, 0, 0.7);
}

/* Contenido del modal */
.modal-content {
  position: relative;
  background-color: #fff; 
  margin: 15% auto; 
  padding: 20px; 
  top: 20vh;
  border: 1px solid #888; 
  width: 80%;
  max-width: 1600px; 
  border-radius: 10px; 
}

.titulo-modal {
  margin-left: 1.1vw;
  margin-bottom: 45px;
}

/* Botón de cerrar */
.close-button {
  color: #aaa;
  float: right;
  font-size: 28px; 
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
  width: 76vw;
  margin-left: 55px;
  height: 100px;
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
  margin-bottom: 40px;
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
  height: 50px;
  border-style: ridge;
  border-color: rgb(174, 170, 170, 0.3);
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
  width: 290px;
}
.borde-nombres {
  width: 500px;
}
.borde-motivo {
  width: 640px;
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
  margin-bottom: 30px;
  height: 50px;
}
.contenedor-botones button {
  margin: 0 20px; 
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

@media screen and (max-width: 1600px) and (min-width: 1401px) {
    .modal-content {
    top: 15vh;
    }
}

@media screen and (max-width: 1400px) and (min-width: 1201px) {
    .modal-content {
    top: 15vh;
    }
}

@media screen and (max-width: 1200px) and (min-width: 993px) {
    .modal-content {
    top: 7vh;
    }
}

@media screen and (max-width: 992px) and (min-width: 769px) {
    .modal-content {
    top: 7vh;
    }
}

@media screen and (max-width: 768px) and (min-width: 481px) {
    .modal-content {
    top: 4vh;
    }
}

@media screen and (max-width: 480px) {
  .cuadro-principal {
    width: 96vw;
    /*height: fit-content;*/
    margin-left: 2vw;
    margin-right: 2vw;
    font-size: smaller;
    
  }
  .solicitud-contenedor-principal {
    min-width: 97%;
    height: 150px;
  }
  .solicitud-contenedor-principal input {
    max-width: 70vw;
  }
  .texto-superior {
    margin-top: -30px;
    position: relative;
    height: 0;
    width: 50vw;
  }
  .texto-inferior {
    height: 0;
  }
  .texto-inferior p,
  .texto-medio p,
  .texto-superior p {
    width: 10px;
    font-size: 0.5rem;
    white-space: break-word;
  }
  .estado-solicitud,
  .circulo-aprobado,
  .circulo-en_revision,
  .circulo-pendiente,
  .circulo-rechazado {
    display: inline-block;
    margin-top: 20px;
  }
  .icono-despliegue {
    margin-top: 77px;
    right: 5vw;
  }
  .estado-solicitud,
  .circulo-aprobado,
  .circulo-en_revision,
  .circulo-pendiente,
  .circulo-rechazado {
    right: 245px;
  }
  .modal-content {
    top: 1vh;
    }
}
</style>

<!-- Script para las funciones del despliegue de contenedor hacia abajo al hacer click -->
<script src="./JS/personal-solicitud-cambios/personal-solicitud-cambios.js"></script>
<script src="./JS/pestañas-plantilla.js"></>

<?php include ("./template/footer.php"); ?>
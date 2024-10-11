<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Solicitudes de modificaciones</title>
<link rel="stylesheet" href="./CSS/solicitud-baja.css">

<!--Cuadro principal del home -->
<div class="cuadro-principal">
    <div div class="encabezado">
        <div class="titulo-bd">
            <h3>Solicitud de baja</h3>
        </div>
    </div>

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
        <a href="./personal-solicitud-cambios.php"><button class="boton-cancelar" id="boton-cancelar" style="background-color: #a7b3b9;">Cancelar</button></a>
        <a href="./personal-solicitud-cambios.php"><button class="boton-guardar" id="boton-cancelar">Guardar</button></a>
    </div>

</div>

<script src="./JS/pestañas-plantilla.js"></script>

<?php include ("./template/footer.php"); ?>
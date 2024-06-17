<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Control de Eventos</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">

    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Control de eventos</h3>
        </div>
    </div>
    

<!-- Contenido -->
<div class="nombre-evento">
        <p>Nombre del Evento</p>
        <textarea id="event-description" placeholder="Escribe el nombre del evento"></textarea>
    </div>

    <div class="fecha">
        <p>De</p>
        <input type="text" id="event-description" placeholder="Junio 5, 2024"></input>
        <p>a</p>
        <input type="text" id="event-description" placeholder="Junio 5, 2024"></input>
    </div>

    <div class="participantes">
        <p>Participantes</p>
        <textarea id="event-description" placeholder="Escribe el nombre del participante"></textarea>
    </div>

    <div class="notificaciones">
        <p>Notificaciones</p>
        <input type="number" value="1" min="1" max="9" style="width: 30px;">
        <span>7</span>
        <select>
        <option>días</option>
        <option>semanas</option>
        <option>meses</option>
        </select>
        <span>antes a las</span>
        <input type="time" value="19:00">
        <input type="checkbox" id="correo-electronico">
        <label for="correo-electronico">Correo electrónico</label>
        <button>+ Nueva notificación</button>
    </div>
        

    <div class="etiquetas">
        <p>Etiquetas</p>
        <button>+ Nueva Etiqueta</button>
        <div>
        <span>Jefes de Departamento</span>
    <span>Coordinación de personal</span>
    </div>
</div>

    <div class="descripcion">
        <p>Descripción</p>
        <textarea id="event-description" placeholder="Escriba la descripción de las actividades a realizar"></textarea>
    </div>

    </div>

<?php include './template/footer.php' ?>

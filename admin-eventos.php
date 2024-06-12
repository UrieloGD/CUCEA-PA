<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
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
<head>
    <title>Crear Evento</title>
    <link rel="stylesheet" type="text/css" href="controlEv.css">
</head>
<body>
    <h2>Crear Evento</h2>
    <textarea id="event-description" placeholder="Escribe el nombre del evento"></textarea>
</body>

<!-- <th style="text-align: center;">Nombre</th>


<th style="text-align: center;">Participantes</th>
 Barra de Busqueda *comentado* 
<div class="busqueda">
  <input type="text" placeholder="Escribe el nombre del participante" id="search-input"> -->

<?php include './template/footer.php' ?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Añadir Usuarios</title>
<link rel="stylesheet" href="./CSS/adminAU.css"/>

<!--Cuadro principal del home-->
<div class="cuadro-principal">

<!--Pestaña azul-->
<div class="encabezado">
    <div class="titulo-bd">
      <h3>Crear Evento</h3>
    </div>
  </div>
<br><br> 

<head>
    <title>Crear Evento</title>
    <link rel="stylesheet" type="text/css" href="controlEv.css">
</head>
<body>
    <h1>Crear Evento</h1>
    <textarea id="event-description" placeholder="Escribe la descripción del evento aquí..."></textarea>
</body>

<th style="text-align: center;">Nombre</th>


<th style="text-align: center;">Participantes</th>
<!-- Barra de Busqueda-->
<div class="busqueda">
  <input type="text" placeholder="Escribe el nombre del participante" id="search-input">


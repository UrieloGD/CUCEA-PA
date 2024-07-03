<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Calendario</title>
<link rel="stylesheet" href="./CSS/calendario.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Calendario</h3>
        </div>
    </div>

    </head>

<body>
    <div class="container-calendar">
        <div class="header-calendar">
            <h2>Hoy</h2>
        </div>
        <div class="countdown">
            <p>10 días para entregar</p>
            <div class="timer">
                <div class="timer-box">10<br>Días</div>
                <div class="timer-box">05<br>Horas</div>
                <div class="timer-box">27<br>Minutos</div>
            </div>
        </div>
        <div class="activities">
            <h3>Actividades próximas</h3>
            <div class="activity">Actividad 1<br>10 Junio, 12:00</div>
            <div class="activity">Actividad 2<br>15 Junio, 13:00</div>
            <div class="activity">Actividad 3<br>24 Junio, 17:00</div>
        </div>
        <div class="events">
            <h3>Eventos próximos</h3>
            <div class="event">
                <strong>Hoy 15:00</strong><br>
                Reunión de revisión de bases de datos
            </div>
            <div class="event">
                <strong>24 - 26 Jun 13:30</strong><br>
                Cierre de programación académica
            </div>
        </div>
    </div>
</div>
</body>

<div class="date-bar">
    <div class="date-selector">
        <span class="month-year">Junio 2024</span>
        <div class="arrow-buttons">
            <button class="arrow left">&#9664;</button>
            <button class="arrow right">&#9654;</button>
        </div>
    </div>
    <div class="view-options">
        <button class="search-icon">&#128269;</button>
        <button class="list-icon">&#9776;</button>
        <button class="grid-icon">&#9783;</button>
    </div>
</div>
</html>
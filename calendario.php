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
            <h3>Próximos Eventos</h3>
        </div>
    </div>
        
    <!-- aquí inica el código -->
        <div class="calendar-container">
            <div class="left-column">
                <div class="header-calendar">
                    <div class="hoy">
                    <h2>Hoy</h2></div>
                    <div class="countdown">
                        <p>10 días para entregar</p>
                        <div class="timer">
                        <div class="timer-item">
                            <div class="timer-box">10</div>
                            <div class="timer-label">Días</div>
                        </div>
                        <div class="timer-item">
                            <div class="timer-box">05</div>
                            <div class="timer-label">Horas</div>
                        </div>
                        <div class="timer-item">
                            <div class="timer-box">27</div>
                            <div class="timer-label">Minutos</div>
                        </div>
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
                        <div class="event-time">Hoy 15:00</div>
                        <div class="event-description">Reunión de revisión de bases de datos</div>
                        </div>
                        <div class="event">
                        <div class="event-time">24 - 26 Jun 13:30</div>
                        <div class="event-description">Cierre de programación académica</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="right-column">
                <div class="date-bar">
                    <div class="date-selector">
                        <span class="month-year">Junio 2024</span>
                        <div class="arrow-buttons">
                            <button class="arrow left">&#9664;</button>
                            <button class="arrow right">&#9654;</button>
                        </div>
                    </div>
                    <div class="view-options">
                        <button class="search-icon"><img src="./Img/Icons/iconos-calendario/lupa.png"></button>
                        <button class="list-icon"><img src="./Img/Icons/iconos-calendario/filtro.png"></button>
                        <button class="grid-icon"><img src="./Img/Icons/iconos-calendario/escala.png"></button>
                    </div>
                </div>
                <div class="calendar">
                    <?php
                    function generateCalendar($month, $year) {
                        $firstDay = mktime(0,0,0,$month,1,$year);
                        $daysInMonth = date('t', $firstDay);
                        $dayOfWeek = date('w', $firstDay);
                        
                        $calendar = "<table class='calendar-table'>";
                        $calendar .= "<tr><th>Do</th><th>Lu</th><th>Ma</th><th>Mi</th><th>Ju</th><th>Vi</th><th>Sa</th></tr>";
                        
                        $day = 1;
                        $calendar .= "<tr>";
                        
                        for ($i = 0; $i < $dayOfWeek; $i++) {
                            $calendar .= "<td></td>";
                        }
                        
                        while ($day <= $daysInMonth) {
                            if ($dayOfWeek == 7) {
                                $calendar .= "</tr><tr>";
                                $dayOfWeek = 0;
                            }
                            
                            $calendar .= "<td>$day</td>";
                            $day++;
                            $dayOfWeek++;
                        }
                        
                        while ($dayOfWeek < 7) {
                            $calendar .= "<td></td>";
                            $dayOfWeek++;
                        }
                        
                        $calendar .= "</tr></table>";
                        
                        return $calendar;
                    }

                    $month = 6; // Junio
                    $year = 2024;

                    echo generateCalendar($month, $year);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
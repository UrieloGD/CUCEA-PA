<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
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
                    <h2>Hoy</h2>
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
                        <button class="search-icon">&#128269;</button>
                        <button class="list-icon">&#9776;</button>
                        <button class="grid-icon">&#9783;</button>
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
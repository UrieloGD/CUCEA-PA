<?php
// Iniciar la sesión
session_start();
// Conexión a la base de datos
include './config/db.php';

// Obtener el mes, año y usuario desde la sesión o parámetros
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$userId = $_SESSION['user_id']; // ID del usuario actual

// Verificar si es una petición AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
    echo generateCalendar($month, $year, $conexion, $userId);
    exit();
}

// Incluye el header y navbar solo si no es una petición AJAX
include './template/header.php';
include './template/navbar.php';
?>

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

    <!-- aquí inicia el código -->
    <div class="calendar-container">
        <div class="left-column">
            <div class="header-calendar">
                <div class="hoy">
                    <h2>Hoy</h2>
                </div>
                <!-- Aquí podrías agregar un contador dinámico si lo necesitas -->
                <div class="activities">
                    <h3>Actividades próximas</h3>
                    <!-- Actividades estáticas, puedes reemplazarlas si tienes un sistema de actividades en la base de datos -->
                    <div class="activity">Actividad 1<br>10 Junio, 12:00</div>
                    <div class="activity">Actividad 2<br>15 Junio, 13:00</div>
                    <div class="activity">Actividad 3<br>24 Junio, 17:00</div>
                </div>
                <?php
                // Consultar eventos futuros del usuario
                $sql = "SELECT Nombre_Evento, Fecha_Inicio AS Fecha_Evento, Descripcion_Evento AS Descripcion 
        FROM Eventos_Admin 
        WHERE Fecha_Inicio >= CURDATE() AND FIND_IN_SET('$userId', Participantes)";

                $result = mysqli_query($conexion, $sql);
                ?>

                <div class="events">
                    <h3>Eventos próximos</h3>
                    <ul>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            // Mostrar los eventos futuros
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<li>';
                                echo '<strong>' . htmlspecialchars($row['Nombre_Evento']) . ':</strong> ' . '<br>';
                                echo htmlspecialchars($row['Descripcion']) . '<br>';
                                echo htmlspecialchars($row['Fecha_Evento']);
                                echo '</li>';
                            }
                        } else {
                            echo '<li>No tienes eventos próximos.</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="date-bar">
                <div class="date-selector">
                    <span class="month-year"><?php echo date('F Y'); ?></span>
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
                function generateCalendar($month, $year, $conexion, $userId)
                {
                    $firstDay = mktime(0, 0, 0, $month, 1, $year);
                    $daysInMonth = date('t', $firstDay);
                    $dayOfWeek = date('w', $firstDay);

                    // Día actual
                    $today = date('j');

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

                        $class = '';
                        $events = '';

                        // Consultar eventos para este día y usuario
                        $fechaActual = "$year-$month-$day";
                        $sqlEventos = "SELECT Nombre_Evento FROM Eventos_Admin WHERE '$fechaActual' BETWEEN Fecha_Inicio AND Fecha_Fin AND FIND_IN_SET('$userId', Participantes)";
                        $resultEventos = mysqli_query($conexion, $sqlEventos);
                        if (mysqli_num_rows($resultEventos) > 0) {
                            $class = 'day-with-event';
                            while ($rowEvento = mysqli_fetch_assoc($resultEventos)) {
                                $events .= "<span class='event-indicator yellow'>{$rowEvento['Nombre_Evento']}</span>";
                            }
                        }

                        $calendar .= "<td class='$class'>";
                        $calendar .= "<div class='date-number'>$day</div>";
                        $calendar .= "<div class='events-container'>$events</div>";
                        $calendar .= "</td>";

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


                // Obtener el mes, año y usuario desde la sesión o parámetros
                $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
                $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
                $userId = $_SESSION['user_id']; // ID del usuario actual

                echo generateCalendar($month, $year, $conexion, $userId);
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthYearDisplay = document.querySelector('.month-year');
        const leftArrow = document.querySelector('.arrow.left');
        const rightArrow = document.querySelector('.arrow.right');
        let currentMonth = <?php echo $month; ?>;
        let currentYear = <?php echo $year; ?>;

        function updateCalendar(month, year) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `calendario.php?month=${month}&year=${year}&ajax=true`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.querySelector('.calendar').innerHTML = xhr.responseText;
                    monthYearDisplay.textContent = new Date(year, month - 1).toLocaleString('default', {
                        month: 'long',
                        year: 'numeric'
                    });
                }
            };
            xhr.send();
        }

        leftArrow.addEventListener('click', function() {
            if (currentMonth === 1) {
                currentMonth = 12;
                currentYear--;
            } else {
                currentMonth--;
            }
            updateCalendar(currentMonth, currentYear);
        });

        rightArrow.addEventListener('click', function() {
            if (currentMonth === 12) {
                currentMonth = 1;
                currentYear++;
            } else {
                currentMonth++;
            }
            updateCalendar(currentMonth, currentYear);
        });
    });
</script>

</body>

</html>

<?php include './template/footer.php' ?>
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
                $sql = "SELECT Nombre_Evento, Fecha_Inicio AS Fecha_Evento, Descripcion_Evento AS Descripcion, Etiqueta, Hora_Inicio
                        FROM Eventos_Admin 
                        WHERE Fecha_Inicio >= CURDATE() AND FIND_IN_SET('$userId', Participantes)";

                $result = mysqli_query($conexion, $sql);
                ?>

                <hr>

                <div class="events">
                    <h3>Eventos próximos</h3>
                    <div class="events-list">
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Convertir la fecha al formato deseado
                                $fecha = new DateTime($row['Fecha_Evento']);
                                $fecha_formateada = $fecha->format('d/m/Y');

                                echo '<div class="event-item">';
                                echo '<div class="event-date">' . $fecha_formateada . '<br>' . ($row['Hora_Inicio']) . '</div>';
                                echo '<div class="event-content">';
                                echo '<strong>' . htmlspecialchars($row['Nombre_Evento']) . '</strong>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="event-item">No tienes eventos próximos.</div>';
                        }
                        ?>
                    </div>
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


                    $calendar = "<table class='calendar-table'>";
                    $calendar .= "<tr><th>Do</th><th>Lu</th><th>Ma</th><th>Mi</th><th>Ju</th><th>Vi</th><th>Sa</th></tr>";

                    $day = 1;
                    $calendar .= "<tr>";

                    for ($i = 0; $i < $dayOfWeek; $i++) {
                        $calendar .= "<td></td>";
                    }

                    // Día actual
                    $today = date('j');
                    $currentMonth = date('n');
                    $currentYear = date('Y');

                    while ($day <= $daysInMonth) {
                        if ($dayOfWeek == 7) {
                            $calendar .= "</tr><tr>";
                            $dayOfWeek = 0;
                        }

                        $class = '';
                        $events = '';

                        if ($day == $today && $month == $currentMonth && $year == $currentYear) {
                            $class .= ' current-day';
                        }

                        // Consultar eventos para este día y usuario
                        $fechaActual = "$year-$month-$day";
                        $sqlEventos = "SELECT ID_Evento, Nombre_Evento, Etiqueta, Descripcion_Evento, DATE(Fecha_Inicio) AS Fecha_Evento, TIME_FORMAT(Hora_Inicio, '%H:%i') AS Hora_Inicio FROM Eventos_Admin WHERE '$fechaActual' BETWEEN DATE(Fecha_Inicio) AND DATE(Fecha_Fin) AND FIND_IN_SET('$userId', Participantes)";
                        $resultEventos = mysqli_query($conexion, $sqlEventos);
                        if (mysqli_num_rows($resultEventos) > 0) {
                            $class = 'day-with-event';
                            while ($rowEvento = mysqli_fetch_assoc($resultEventos)) {
                                $events .= "<span class='event-indicator lightblue' data-event-id='{$rowEvento['ID_Evento']}'>{$rowEvento['Nombre_Evento']}</span>";
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

        // Modal
        const modal = document.getElementById('eventModal');
        const span = document.getElementsByClassName("close")[0];

        // Cerrar el modal cuando se hace clic en la X
        span.onclick = function() {
            modal.classList.remove('show');
            modal.classList.add('hide');
            setTimeout(function() {
                modal.style.display = "none";
                modal.classList.remove('hide');
            }, 300); // tiempo que dura la transición
        }

        // Cerrar el modal cuando se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('show');
                modal.classList.add('hide');
                setTimeout(function() {
                    modal.style.display = "none";
                    modal.classList.remove('hide');
                }, 300); // tiempo que dura la transición
            }
        }

        // Función para abrir el modal y cargar la información del evento
        function openEventModal(eventId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `get_event_details.php?event_id=${eventId}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const eventDetails = JSON.parse(xhr.responseText);
                    console.log('Event Details:', eventDetails); // Para depuración
                    document.getElementById('eventTitle').textContent = eventDetails.Nombre_Evento;
                    document.getElementById('eventTag').textContent = eventDetails.Etiqueta;
                    document.getElementById('eventDescription').textContent = eventDetails.Descripcion_Evento;
                    document.getElementById('eventDate').textContent = eventDetails.Fecha_Evento;
                    document.getElementById('eventTime').textContent = eventDetails.Hora_Inicio;

                    modal.style.display = "block";
                    setTimeout(function() {
                        modal.classList.add('show');
                    }, 10); // Delay pequeño para activar la transición
                }
            };
            xhr.send();
        }

        // Delegación de eventos para los indicadores de eventos
        document.querySelector('.calendar').addEventListener('click', function(e) {
            if (e.target.classList.contains('event-indicator')) {
                const eventId = e.target.getAttribute('data-event-id');
                openEventModal(eventId);
            }
        });
    });
</script>

<!-- Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2 id="eventTitle"></h2>
        </div>
        <div class="modal-body">
            <div class="event-time">
                <span id="eventDate"></span> • <span id="eventTime"></span>
            </div>
            <div class="event-location">
                <span id="eventTag"></span>
            </div>
            <div class="event-description">
                <p id="eventDescription"></p>
            </div>
        </div>
    </div>
</div>

</body>

</html>

<?php include './template/footer.php' ?>
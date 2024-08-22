<?php
// Iniciar la sesión
session_start();
// Conexión a la base de datos

setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es');
date_default_timezone_set('America/Mexico_City');

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
                // Consultar eventos futuros o en curso del usuario
                $sql = "SELECT Nombre_Evento, Fecha_Inicio, Fecha_Fin, Hora_Inicio, Etiqueta
                        FROM Eventos_Admin 
                        WHERE (Fecha_Inicio >= CURDATE() OR (Fecha_Inicio <= CURDATE() AND Fecha_Fin >= CURDATE()))
                        AND FIND_IN_SET(?, Participantes)
                        ORDER BY Fecha_Inicio ASC, Hora_Inicio ASC
                        /*LIMIT 3*/"; // Limitamos a 3 eventos para no sobrecargar la vista

                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>

                <hr>

                <div class="events">
                    <h3>Eventos próximos</h3>
                    <div class="events-list" style="max-height: 350px; overflow-y: auto;">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $eventCount = 0;
                            while ($row = $result->fetch_assoc()) {
                                // Determinar qué fecha mostrar
                                $fechaEvento = new DateTime($row['Fecha_Inicio']);
                                $fechaFin = new DateTime($row['Fecha_Fin']);
                                $hoy = new DateTime();

                                if ($fechaEvento <= $hoy && $fechaFin >= $hoy) {
                                    $estadoEvento = "En curso";
                                    $fechaMostrar = $fechaFin;
                                } else {
                                    $estadoEvento = "Próximo";
                                    $fechaMostrar = $fechaEvento;
                                }

                                $fecha_formateada = $fechaMostrar->format('d/m/Y');

                                echo '<div class="event-item">';
                                echo '<div class="event-date">' . $fecha_formateada . '<br>' . $row['Hora_Inicio'] . '</div>';
                                echo '<div class="event-content">';
                                echo '<strong>' . htmlspecialchars($row['Nombre_Evento']) . '</strong>';
                                echo '<br><span class="event-tag">' . htmlspecialchars($row['Etiqueta']) . '</span>';
                                echo '<br><span class="event-status">' . $estadoEvento . '</span>';
                                echo '</div>';
                                echo '</div>';

                                $eventCount++;
                            }
                            if ($eventCount > 3) {
                                echo '<div class="event-item" style="text-align: center; font-style: italic;"></div>';
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
                    <div class="arrow-buttons">
                        <button class="arrow left">&#9664;</button>
                        <button class="arrow right">&#9654;</button>
                    </div>
                    <span class="month-year" id="monthYearDisplay"><?php echo date('F Y', strtotime("$year-$month-01")); ?></span>
                    <input type="month" id="monthYearPicker" style="display: none;">
                </div>
                <div class="view-options">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Buscar eventos...">
                        <button class="search-icon"><img src="./Img/Icons/iconos-calendario/lupa.png" style="margin-right: 40px;"></button>
                    </div>
                    <button class="list-icon"><img src="./Img/Icons/iconos-calendario/filtro.png" style="margin-right: 30px;"></button>
                    <!-- <button class="grid-icon"><img src="./Img/Icons/iconos-calendario/escala.png"></button> -->
                </div>
            </div>
            <div class="filter-menu" style="display: none;">
                <button class="close-filter"><i class="fas fa-times"></i></button>
                <button class="filter-btn" data-filter="Programación Académica">Programación Académica</button>
                <button class="filter-btn" data-filter="Oferta Académica">Oferta Académica</button>
                <button class="filter-btn" data-filter="Administrativo">Administrativo</button>
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

                    // Fecha actual
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
                        $eventCount = 0;
                        $eventIds = [];

                        if ($day == $today && $month == $currentMonth && $year == $currentYear) {
                            $class .= ' current-day';
                        }

                        // Construir la fecha actual
                        $fechaActual = sprintf('%04d-%02d-%02d', $year, $month, $day);

                        // Marcar el día como con eventos si hay al menos un evento
                        $sqlEventos = "SELECT 1 FROM Eventos_Admin WHERE '$year-$month-$day' BETWEEN DATE(Fecha_Inicio) AND DATE(Fecha_Fin) AND FIND_IN_SET('$userId', Participantes)";
                        $resultEventos = mysqli_query($conexion, $sqlEventos);
                        if (mysqli_num_rows($resultEventos) > 0) {
                            $class .= ' day-with-event';
                        }

                        // Consultar eventos para este día y usuario
                        $fechaActual = "$year-$month-$day";
                        $sqlEventos = "SELECT ID_Evento, Nombre_Evento, Etiqueta, Descripcion_Evento, DATE(Fecha_Inicio) AS Fecha_Evento, TIME_FORMAT(Hora_Inicio, '%H:%i') AS Hora_Inicio FROM Eventos_Admin WHERE '$fechaActual' BETWEEN DATE(Fecha_Inicio) AND DATE(Fecha_Fin) AND FIND_IN_SET('$userId', Participantes)";
                        $resultEventos = mysqli_query($conexion, $sqlEventos);
                        if (mysqli_num_rows($resultEventos) > 0) {
                            $class .= ' day-with-event';
                            $eventCount = 0;
                            $events = "<div class='events-container'>";
                            while ($rowEvento = mysqli_fetch_assoc($resultEventos)) {
                                if ($eventCount < 2) {
                                    $events .= "<span class='event-indicator lightblue' data-event-id='{$rowEvento['ID_Evento']}' data-event-tag='{$rowEvento['Etiqueta']}' title='{$rowEvento['Nombre_Evento']}'>{$rowEvento['Nombre_Evento']}</span>";
                                }
                                $eventCount++;
                            }
                            if ($eventCount > 2) {
                                $events .= "<span class='event-more' data-date='$fechaActual'> Ver más</span>";
                            }
                            $events .= "</div>";
                        }

                        $calendar .= "<td class='$class'>";
                        $calendar .= "<div class='date-number'>$day</div>";
                        $calendar .= $events;
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

<!-- Modales -->
<!-- Modal para visualizar detalles del evento -->
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
                <img src="./Img/Icons/iconos-calendario/Etiqueta.png" alt="Icono de etiqueta" class="event-icon">
                <span id="eventTag"></span>
            </div>
            <div class="event-description">
                <p id="eventDescription"></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar todos los eventos -->
<div id="eventsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2 class="modal-title"></h2>
        </div>
        <div class="modal-body"></div>
    </div>
</div>

<script>
    var userId = <?php echo json_encode($_SESSION['user_id']); ?>;
</script>

<script src="./JS/calendario/funciones-calendario.js"></script>
<?php include './template/footer.php' ?>
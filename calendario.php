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
<link rel="stylesheet" href="./CSS/calendario.css?v=<?php echo filemtime('./CSS/calendario.css'); ?>" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="cuadro-scroll">
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
                <!-- Contador dinámico de próximas actividades. Temporalmente deshabilitado
                <div class="activities">
                    <h3>Actividades próximas</h3>
                    <div class="activity">Actividad 1<br>10 Junio, 12:00</div>
                    <div class="activity">Actividad 2<br>15 Junio, 13:00</div>
                    <div class="activity">Actividad 3<br>24 Junio, 17:00</div>
                </div> -->
                <?php
                // Consultar eventos futuros o en curso del usuario
                $sql = "SELECT ID_Evento, Nombre_Evento, Fecha_Inicio, Fecha_Fin, Hora_Inicio, Etiqueta
                        FROM eventos_admin 
                        WHERE (Fecha_Inicio >= CURDATE() OR (Fecha_Inicio <= CURDATE() AND Fecha_Fin >= CURDATE()))
                        AND FIND_IN_SET(?, Participantes)
                        ORDER BY Fecha_Inicio ASC, Hora_Inicio ASC
                        /*LIMIT 3*/"; // Limitamos a 3 eventos para no sobrecargar la vista

                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>

                <div class="events">
                    <h3>Eventos próximos</h3>
                    <hr style="margin-top: 15px;">
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

                                // Imprime un evento con su fecha, hora, nombre, etiqueta y estado. 
                                // Cada evento tiene un ID único (ej: data-event-id) para la interacción en JavaScript.
                                echo '<div class="event-item">';
                                echo '<div class="event-date">' . $fecha_formateada . '<br>' . $row['Hora_Inicio'] . '</div>';
                                echo '<div class="event-content">';
                                echo '<strong>' . htmlspecialchars($row['Nombre_Evento']) . '</strong>';
                                echo '<br><span class="event-tag">' . htmlspecialchars($row['Etiqueta']) . '</span>';
                                // echo '<br><span class="event-tag" data-event-id="' . $row['ID_Evento'] . '">' . htmlspecialchars($row['Etiqueta']) . '</span>';
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
                            <div class="box-buttons">
                                <button class="search-icon"><img src="./Img/Icons/iconos-calendario/lupa.png"></button>
                                <button class="list-icon"><img src="./Img/Icons/iconos-calendario/filtro.png"></button>
                            </div>
                    </div>
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
                        $sqlEventos = "SELECT 1 FROM eventos_admin WHERE '$year-$month-$day' BETWEEN DATE(Fecha_Inicio) AND DATE(Fecha_Fin) AND FIND_IN_SET('$userId', Participantes)";
                        $resultEventos = mysqli_query($conexion, $sqlEventos);
                        if (mysqli_num_rows($resultEventos) > 0) {
                            $class .= ' day-with-event';
                        }

                        // Consultar eventos para este día y usuario
                        $fechaActual = "$year-$month-$day";
                        $sqlEventos = "SELECT ID_Evento, Nombre_Evento, Etiqueta, Descripcion_Evento, DATE(Fecha_Inicio) AS Fecha_Evento, TIME_FORMAT(Hora_Inicio, '%H:%i') AS Hora_Inicio FROM eventos_admin WHERE '$fechaActual' BETWEEN DATE(Fecha_Inicio) AND DATE(Fecha_Fin) AND FIND_IN_SET('$userId', Participantes)";
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
                        // $calendar .= "<div class='date-number' style='cursor: pointer;'>$day</div>"; /* cursor pointer para denotar que puede ser clickeado. Temporalmente deshabilitado porque descartamos el modal para crear eventos desde el calendario */
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
</div>

<!-- Llamada al modal con la información sobre los eventos -->
<?php
require_once './functions/calendario/modal-eventos-info.php';
?>

<!-- Modal para crear nuevo evento, temporalmente deshabilitado
<div id="modalOverlay" class="modal-overlay"></div>
<div id="createEventModal" class="side-modal">
    <span class="close-modal"></span>
    <form>
        <div class="crearevento-principal">
            <div class="crearevento-encabezado">Crear nuevo evento</div>
            <div class="crearevento-titulofecha">
                <input class="escribir-titulo" type="text" placeholder="Titulo del evento">
                <div class="escribir-icono">
                    <i class="fa fa-pencil-square" aria-hidden="true"></i>
                </div>
                <div class="seleccionar-fecha">
                    <p>
                        De <input class="date" type="date" name="fecha-evento" id="fecha-evento">
                        a <input class="date" type="date" name="fecha-evento" id="fecha-evento">
                    </p>
                </div>
            </div>
            <div class="crearevento-secciones">
                <div class="subcontenedor-parts">
                    <p>Participantes</p>
                    <input class="escribir-parts" type="text" placeholder="Escribe el nombre del participante">
                    <div id="tabs-participantes" class="tabs-container"></div>
                </div>
                <div class="subcontenedor-etiquetas">
                    <p>Etiquetas</p>
                    <input class="escribir-etiquetas" type="text" placeholder="+ Nueva etiqueta">
                    <div id="tabs-etiquetas" class="tabs-container"></div>
                </div>
                <div class="subcontenedor-descripcion">
                    <p>Descripción</p>
                    <div class="cuadro-descripcion">
                        <textarea class="escribir-descripcion" placeholder="Escriba la descripción de las actividades a realizar..." style="font-family: Dm Sans, sans-serif;"></textarea>
                    </div>
                </div>
                <div>
                    <a href="./calendario.php">
                        <input class="boton-finalizar" type="button" value="Crear evento">
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
-->


<script>
    var userId = <?php echo json_encode($_SESSION['user_id']); ?>;
</script>

<script src="./JS/calendario/funciones-calendario.js?v=<?php echo filemtime('./JS/calendario/funciones-calendario.js'); ?>"></script>
<script src="./JS/calendario/modal-nuevoevento.js?v=<?php echo filemtime('./JS/calendario/modal-nuevoevento.js'); ?>"></script>

<!-- Script y estilos para boton que abre el modal. -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendar = document.querySelector('.calendar');
        const modal = document.getElementById('createEventModal');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModal = document.querySelector('.close-modal');
        let activeCell = null;
        let currentMonth, currentYear;

        // Función para obtener el mes y año actuales del calendario
        function updateCurrentMonthYear() {
            const monthYearText = document.getElementById('monthYearDisplay').textContent;
            const [monthName, year] = monthYearText.split(' ');
            currentMonth = new Date(Date.parse(monthName + " 1, " + year)).getMonth();
            currentYear = parseInt(year);
        }

        updateCurrentMonthYear(); // Llamar esto al inicio y cada vez que cambie el mes

        calendar.addEventListener('click', function(e) {
            const dateNumber = e.target.closest('.date-number');
            if (dateNumber) {
                modal.style.display = 'block';
                modalOverlay.style.display = 'block';

                if (activeCell) {
                    activeCell.classList.remove('highlighted');
                }

                // Obtener la celda que contiene la fecha
                activeCell = dateNumber.closest('td');
                if (activeCell) {
                    activeCell.classList.add('highlighted');
                }

                // Obtener la fecha seleccionada
                const selectedDateNumber = dateNumber.textContent;
                const selectedDate = new Date(currentYear, currentMonth, parseInt(selectedDateNumber));

                // Formatear la fecha como YYYY-MM-DD
                const formattedDate = selectedDate.toISOString().split('T')[0];

                // Establecer la fecha en los campos del modal
                const fechaInicioInput = document.querySelector('.seleccionar-fecha input[name="fecha-evento"]:nth-of-type(1)');
                const fechaFinInput = document.querySelector('.seleccionar-fecha input[name="fecha-evento"]:nth-of-type(2)');

                if (fechaInicioInput) fechaInicioInput.value = formattedDate;
                if (fechaFinInput) fechaFinInput.value = formattedDate;
            }
        });

        function clearModalData() {
            document.querySelector('.escribir-titulo').value = '';
            const fechaInputs = document.querySelectorAll('.seleccionar-fecha input[type="date"]');
            fechaInputs.forEach(input => input.value = '');
            document.querySelector('.escribir-parts').value = '';
            document.querySelector('.escribir-etiquetas').value = '';
            document.getElementById('tabs-participantes').innerHTML = '';
            document.getElementById('tabs-etiquetas').innerHTML = '';
            document.querySelector('.escribir-descripcion').value = '';
        }

        function closeModalAndReset() {
            modal.style.display = 'none';
            modalOverlay.style.display = 'none';
            if (activeCell) {
                activeCell.classList.remove('highlighted');
                const btn = activeCell.querySelector('.create-event-btn');
                if (btn) btn.style.display = 'none';
                activeCell = null;
            }
            clearModalData();
        }

        closeModal.addEventListener('click', closeModalAndReset);
        modalOverlay.addEventListener('click', closeModalAndReset);

        // Actualizar mes y año cuando cambie el calendario
        document.querySelectorAll('.arrow').forEach(arrow => {
            arrow.addEventListener('click', updateCurrentMonthYear);
        });
        document.getElementById('monthYearPicker').addEventListener('change', updateCurrentMonthYear);
    });
</script>

<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1499;
    }

    .side-modal {
        display: none;
        position: fixed;
        height: 90vh;
        top: 73px;
        left: 7.5vw;
        z-index: 1500;
    }

    .calendar td.highlighted {
        background-color: #e0e0e0;
        /* o el color que prefieras */
        box-shadow: #fff;
        z-index: 1500;
    }

    .calendar td {
        position: relative;
    }

    @media screen and (max-width: 1600px) and (min-width: 1401px) {
        .side-modal {
            top: 55px;
        }
    }

    @media screen and (max-width: 1400px) and (min-width: 1201px) {
        .side-modal {
            top: 60px;
        }
    }

    @media screen and (max-width: 1200px) and (min-width: 993px) {
        .side-modal {
            top: 65px;
        }
    }

    @media screen and (max-width: 992px) and (min-width: 769px) {
        .side-modal {
            left: 25vw;
            top: 60.1px;
        }
    }

    @media (max-width: 768px) {
        .side-modal {
            left: 15vw;
            top: 59px;
        }
    }

    @media (max-width: 660px) {
        .side-modal {
            left: 10vw;
            top: 60px;
        }
    }

    @media (max-width: 565px) {
        .side-modal {
            left: 7vw;
            top: 60px;
        }
    }
</style>

<?php include './template/footer.php' ?>
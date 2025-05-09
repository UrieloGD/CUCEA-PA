<?php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 2 && $_SESSION['Rol_ID'] != 0) {
    header("Location: home.php");
    exit();
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php require_once './config/db.php' ?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-eventos/admin-eventos.css?v=<?php echo filemtime('./CSS/admin-eventos/admin-eventos.css'); ?>" />
<link rel="stylesheet" href="./CSS/admin-eventos/admin-crear-eventos.css?v=<?php echo filemtime('./CSS/admin-eventos/admin-crear-eventos.css'); ?>" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="cuadro-scroll">
    <!--Pestaña azul-->
    <!-- <div class="encabezado">
        <div class="titulo-bd">
            <h3>Próximos Eventos</h3>
        </div>
    </div> -->

    <div class="buttons-functions">
        <div class="button-nuevo-evento" id="btnCrearEvento"><i class="fa fa-plus" aria-hidden="true"></i><div class="titulo-nuevoevento">Nuevo evento</div></div>
        <div class="contenedor-select">
            <select name="desplegable-estado-evento" id="desplegable-estado-evento">
                <option value="Selecciona..." disabled selected id="disabled-option">Selecciona un estado</option>
                <option value="En proceso">En proceso</option>
                <option value="Finalizado">Finalizado</option>
                <option value="Proximos">Proximos</option>
            </select>
            <i class="fa fa-caret-down" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Contenido -->
    <?php
    // Consulta modificada para obtener los nombres de los participantes
    $sql = "SELECT e.*, GROUP_CONCAT(CONCAT(u.Nombre, ' ', u.Apellido) SEPARATOR ', ') AS NombresParticipantes
    FROM eventos_admin e
    LEFT JOIN usuarios u ON FIND_IN_SET(u.Codigo, e.Participantes)
    WHERE (e.Fecha_Fin >= CURDATE() OR (e.Fecha_Inicio <= CURDATE() AND e.Fecha_Fin >= CURDATE()))
    AND e.Estado = 'activo'/* Mostrar solo eventos que no han sido cancelados */
    GROUP BY e.ID_Evento
    ORDER BY e.Fecha_Inicio, e.Hora_Inicio";

    $result = mysqli_query($conexion, $sql);
    $eventoAnterior = null;

    if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $eventoActual = $row['Fecha_Inicio'];
        
        if ($eventoAnterior === null || $eventoAnterior !== $eventoActual) {
            $eventoAnterior = $eventoActual; 
            ?>
            
            <div class="evento-fila">
                <div class="event-header">
                    <div class="event-day-container">
                        <div class="event-monthday">
                            <?php
                                $mes_evento = '';
                                if(date('M', strtotime($row['Fecha_Inicio'])) === 'Jan') { $mes_evento = 'Ene'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Feb') { $mes_evento = 'Feb'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Mar') { $mes_evento = 'Mar'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Apr') { $mes_evento = 'Abr'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='May') { $mes_evento = 'May'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Jun') { $mes_evento = 'Jun'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Jul') { $mes_evento = 'Jul'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Aug') { $mes_evento = 'Ago'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Sep') { $mes_evento = 'Sep'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Oct') { $mes_evento = 'Oct'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Nov') { $mes_evento = 'Nov'; }
                                else if(date('M', strtotime($row['Fecha_Inicio'])) ==='Dec') { $mes_evento = 'Dic'; }
                                echo date('d ', strtotime($row['Fecha_Inicio'])); 
                                echo $mes_evento;
                            ?>
                        </div>
                        <div class="event-writtenday">
                            <?php
                                $dia_evento = ''; 
                                if(date('D', strtotime($row['Fecha_Inicio'])) === 'Mon') { $dia_evento = 'Lunes'; }
                                else if(date('D', strtotime($row['Fecha_Inicio'])) ==='Tue') { $dia_evento = 'Martes'; }
                                else if(date('D', strtotime($row['Fecha_Inicio'])) ==='Wed') { $dia_evento = 'Miércoles'; }
                                else if(date('D', strtotime($row['Fecha_Inicio'])) ==='Thu') { $dia_evento = 'Jueves'; }
                                else if(date('D', strtotime($row['Fecha_Inicio'])) ==='Fri') { $dia_evento = 'Viernes'; }
                                else if(date('D', strtotime($row['Fecha_Inicio'])) ==='Sat') { $dia_evento = 'Sábado'; }
                                else if(date('D', strtotime($row['Fecha_Inicio'])) ==='Sun') { $dia_evento = 'Domingo'; }
                                echo $dia_evento;
                            ?>
                        </div>
                    </div>
                </div>
            <?php 
            } else if ($eventoAnterior === $eventoActual) { 
            // La fecha es igual a la anterior
            ?>
            <div class="evento-fila">
                <div class="event-header-repeat">
                </div>
            <?php
            } ?>

            <?php $maxLength = '130'; ?>
            <div class="event-container">
                <div class="event-details">
                    <h3><?php echo htmlspecialchars($row['Nombre_Evento']); ?></h3>
                    <span class="descripcion-evento"><?php echo htmlspecialchars(substr($row['Descripcion_Evento'],0, $maxLength). '...'); ?></span>
                </div>
                <div class="event-participantes-more">
                    <span class="perfil">
                        <?php 
                        $participantes = explode(",", $row['NombresParticipantes']);
                        $totalParticipantes = count($participantes);

                        foreach($participantes as $indice => $participante) {
                            $participante = trim($participante);
                            $nombreSeparado = explode(" ", $participante);
                            
                            if(count($nombreSeparado) >= 2) {
                                $iniciales = strtoupper($nombreSeparado[0][0] . $nombreSeparado[1][0]);
                            } else {
                                $iniciales = strtoupper(substr($participante, 0, 2));
                            }
                            
                            echo '<div class="icono-perfil"><strong>' . $iniciales . '</strong></div>';
                            
                            // Solo mostramos los nombres si el total de participantes es 5 o menos
                            if ($totalParticipantes <= 5) {
                                echo '<div style="margin: 0 20 0 0; white-space: nowrap; font-size: 1rem;">' . htmlspecialchars($participante) . '</div>';
                            }
                        }
                        ?>
                        <?php  ?>
                    </span>
                    <!-- <span class="department"><?php echo htmlspecialchars($row['Etiqueta']); ?></span> -->
                    <div class="datetime-event">
                        <?php
                        $horaInicio = strtotime($row['Hora_Inicio']);
                        $pm_am = ($horaInicio >= strtotime("12:00:00")) ? 'P.M.' : 'A.M.';
                        if ($row['Hora_Inicio'] >= "12:00:00") {
                            $pm_am = 'P.M.';
                        } else {
                            $pm_am = 'A.M.';
                        }
                        ?>
                        <span><i class="fa-solid fa-clock" style="margin: 0 5 0 0;"></i><?php echo date('h:i', $horaInicio) . " " . $pm_am; ?></span>
                        <span><i class="fas fa-map-marker-alt" style="margin: 0 5 0 10;"></i>Modulo O</span>
                    </div>
                </div>
                <div class="event-actions">
                    <button class="action-btn edit-btn" data-event-id="<?php echo $row['ID_Evento']; ?>">
                        <img src="./Img/Icons/iconos-adminAU/editar.png" alt="Editar">
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteEvent(<?php echo $row['ID_Evento']; ?>)">
                        <img src="./Img/Icons/iconos-adminAU/borrar.png" alt="Borrar">
                    </button>
                </div>
            </div>
        </div>
        <?php
        }
    } else {
        ?>
        <div class="event-container" style="text-align: center;">
            <h3>No hay próximos eventos registrados</h3>
        </div>
    <?php
    }
    ?>
    </div>
</div>

<!-- Modal para crear evento -->
<div id="modalCrearEvento" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 style="margin-bottom: 0;">Crear nuevo evento</h2>
            <span class="close">&times;</span>
        </div>
        <hr style="border: 2px solid #0071b0; width: 99%;">
        <form id="formCrearEvento">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="nombre" name="nombre" value="" required>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <div class="fechas">
                        <input type="date" id="FechIn" name="FechIn" value="" required min="<?php echo date('Y-m-d'); ?>">
                        <input type="date" id="FechFi" name="FechFi" value="" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="horas">
                        <span>a las</span>
                        <input type="time" id="HorIn" name="HorIn" value="" required>
                        <span> --> </span>
                        <input type="time" id="HorFin" name="HorFi" value="" required>
                    </div>
                </div>
            </div>

            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <button class="boton-agregar-participantes" type="button" id="abrirModalParticipantes">Añadir participantes</button>
                    <div id="participantes-seleccionados"></div>
                    <div id="input-participantes"></div>
                    <!-- Agregamos un input oculto para asegurar que siempre se envíe algo, incluso vacío -->
                    <input type="hidden" name="participantes[]" value="">
                </div>
                <div class="split-item" id="item-etiqueta">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="etiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="Programación Académica">Programación Académica</option>
                        <option value="Oferta Académica">Oferta Académica</option>
                        <option value="Administrativo">Administrativo</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" id="btnCancelarCrearEvento">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para editar evento -->
<div id="modalEditarEvento" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 style="margin-bottom: 0;">Editar evento</h2>
            <span class="close">&times;</span>
        </div>
        <hr style="border: 2px solid #0071b0; width: 99%;">
        <form id="formEditarEvento">
            <input type="hidden" id="editEventId" name="id_evento">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="editNombre" name="nombre" value="" required>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <div class="fechas">
                        <input type="date" id="editFechIn" name="FechIn" value="" required min="<?php echo date('Y-m-d'); ?>">
                        <input type="date" id="editFechFi" name="FechFi" value="" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="horas">
                        <span>a las</span>
                        <input type="time" id="editHorIn" name="HorIn" value="" required>
                        <span> --> </span>
                        <input type="time" id="editHorFin" name="HorFi" value="" required>
                    </div>
                </div>
            </div>

            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <button class="boton-agregar-participantes" type="button" id="abrirModalParticipantesEdicion">Añadir participantes</button>
                    <div id="participantes-seleccionados-edicion"></div>
                    <div id="input-participantes-edicion"></div>
                    <!-- Agregamos un input oculto para asegurar que siempre se envíe algo, incluso vacío -->
                    <input type="hidden" name="participantes[]" value="">
                </div>
                <div class="split-item">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="editEtiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="Programación Académica">Programación Académica</option>
                        <option value="Oferta Académica">Oferta Académica</option>
                        <option value="Administrativo">Administrativo</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea id="editDescripcion" name="descripcion" rows="4"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-guardar">Actualizar</button>
                <button type="button" class="btn-cancelar" id="btnCancelarEditarEvento">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para agregar participantes -->
<div id="modalParticipantes" class="modal">
    <div class="modal-content-participantes">
        <div class="modal-header">
            <h2 style="margin-bottom: 0;">Seleccionar Participantes</h2>
            <span class="close">&times;</span>
        </div>
        <hr style="border: 2px solid #0071b0; width: 99%;">
        <div class="header-filtrarParticipantes">
            <input type="search" name="filtrarParticipantes" id="filtrarParticipantes" onkeyup="filtrarParticipantes()">
            <i class="fa fa-search" id="icono-busc" aria-hidden="true"></i>
        </div>
        <div class="table-container">
            <table class="part-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="seleccionarTodos" id="seleccionarTodos" onclick="checkTodosParticipantes()" placeholder="Hola"></th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody id="listaParticipantes">
                    <!-- Los participantes se cargarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>
        <div class="button-container">
            <button class="btn-guardar" type="button" id="confirmarParticipantes">Confirmar selección</button>
        </div>
    </div>
</div>

<script src="./JS/admin-eventos/eliminar-evento.js?v=<?php echo filemtime('./JS/admin-eventos/eliminar-evento.js'); ?>"></script>
<script src="./JS/admin-eventos/modal-creacion-y-participantes.js?v=<?php echo filemtime('./JS/admin-eventos/modal-creacion-y-participantes.js'); ?>"></script>
<script src="./JS/admin-eventos/filtro-participantes.js?v=<?php echo filemtime('./JS/admin-eventos/filtro-participantes.js'); ?>"></script>
<script src="./JS/admin-eventos/icono-participantes.js?v=<?php echo filemtime('./JS/admin-eventos/icono-participantes.js'); ?>"></script>
<script src="./JS/admin-eventos/filtro-eventos.js?v=<?php echo filemtime('./JS/admin-eventos/filtro-eventos.js'); ?>"></script>

<?php include './template/footer.php' ?>
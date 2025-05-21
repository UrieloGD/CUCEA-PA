<?php
require_once './functions/error500/manejo-error.php';
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

<?php 
$current_section = 'admin-eventos';

require_once './functions/mantenimiento/mantenimiento-check.php';

checkMaintenance($current_section);
?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-eventos/admin-eventos.css?v=<?php echo filemtime('./CSS/admin-eventos/admin-eventos.css'); ?>" />
<link rel="stylesheet" href="./CSS/admin-eventos/admin-crear-eventos.css?v=<?php echo filemtime('./CSS/admin-eventos/admin-crear-eventos.css'); ?>" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Próximos Eventos</h3>
        </div>
    </div>

    <div class="section-title">Próximos eventos</div>

    <!-- Contenido -->
    <?php
    // Consulta modificada para obtener los nombres de los participantes
    $sql = "SELECT e.*, GROUP_CONCAT(CONCAT(u.Nombre, ' ', u.Apellido) SEPARATOR ', ') AS NombresParticipantes
    FROM eventos_admin e
    LEFT JOIN usuarios u ON FIND_IN_SET(u.Codigo, e.Participantes)
    WHERE (e.Fecha_Fin >= CURDATE() OR (e.Fecha_Inicio <= CURDATE() AND e.Fecha_Fin >= CURDATE()))
    AND e.Estado = 'activo'/* Mostrar solo eventos que no han sido cancelados */
    GROUP BY e.ID_Evento
    ORDER BY e.Fecha_Inicio, e.Hora_Inicio 
    LIMIT 5";

    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
            <div class="event-container">
                <div class="event-header">
                    <div class="event-day-container">
                        <div class="event-day"><?php echo date('d/m/Y', strtotime($row['Fecha_Inicio'])); ?></div>
                        <div class="event-time"><?php echo date('H:i', strtotime($row['Hora_Inicio'])); ?></div>
                    </div>
                </div>
                <div class="event-details">
                    <h3><?php echo htmlspecialchars($row['Nombre_Evento']); ?></h3>
                    <p><?php echo htmlspecialchars($row['Descripcion_Evento']); ?></p>
                    <p>Participantes: <?php echo htmlspecialchars($row['NombresParticipantes']); ?></p>
                    <div class="event-footer">
                        <span class="department"><?php echo htmlspecialchars($row['Etiqueta']); ?></span>
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

    <div class="form-actions">
        <button type="button" class="btn" id="btnCrearEvento">Crear evento</button>
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

<?php include './template/footer.php' ?>
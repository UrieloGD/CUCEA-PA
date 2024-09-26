<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />
<link rel="stylesheet" href="./CSS/admin-crear-eventos.css" />

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
            FROM Eventos_Admin e
            LEFT JOIN Usuarios u ON FIND_IN_SET(u.Codigo, e.Participantes)
            WHERE (e.Fecha_Fin >= CURDATE() OR (e.Fecha_Inicio <= CURDATE() AND e.Fecha_Fin >= CURDATE()))
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
                    <button class="action-btn edit-btn" onclick="editEvent(<?php echo $row['ID_Evento']; ?>)">
                        <img src="./Img/Icons/iconos-adminAU/editar2.png" alt="Editar">
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteEvent(<?php echo $row['ID_Evento']; ?>)">
                        <img src="./Img/Icons/iconos-adminAU/borrar2.png" alt="Borrar">
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
                <input type="text" id="nombre" name="nombre" value="<?php echo $editing ? htmlspecialchars($evento['Nombre_Evento']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <input type="date" id="FechIn" name="FechIn" value="<?php echo $editing ? $evento['Fecha_Inicio'] : ''; ?>" required min="<?php echo date('Y-m-d'); ?>">
                    <input type="date" id="FechFi" name="FechFi" value="<?php echo $editing ? $evento['Fecha_Fin'] : ''; ?>" required  min="<?php echo date('Y-m-d'); ?>">
                    <span>a las</span>
                    <input type="time" id="HorIn" name="HorIn" value="<?php echo $editing ? $evento['Hora_Inicio'] : ''; ?>" required>
                    <span> --> </span>
                    <input type="time" id="HorFin" name="HorFi" value="<?php echo $editing ? $evento['Hora_Fin'] : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Notificaciones
                </label>
                <div class="notificaciones-group">
                    <select id="notificacion" name="notificacion">
                        <option value="1 hora antes" <?php echo $editing && $evento['Notificaciones'] == '1 hora antes' ? 'selected' : ''; ?>>1 hora antes</option>
                        <option value="2 horas antes" <?php echo $editing && $evento['Notificaciones'] == '2 horas antes' ? 'selected' : ''; ?>>2 horas antes</option>
                        <option value="1 día antes" <?php echo $editing && $evento['Notificaciones'] == '1 día antes' ? 'selected' : ''; ?>>1 día antes</option>
                        <option value="1 semana antes" <?php echo $editing && $evento['Notificaciones'] == '1 semana antes' ? 'selected' : ''; ?>>1 semana antes</option>
                        <option value="Sin notificación" <?php echo $editing && $evento['Notificaciones'] == 'Sin notificación' ? 'selected' : ''; ?>>Sin notificación</option>
                    </select>
                    <span>a las</span>
                    <input type="time" id="HorNotif" name="HorNotif" value="<?php echo $editing ? $evento['Hora_Noti'] : ''; ?>" required>
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
                </div>
                <div class="split-item">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="etiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="Programación Académica" <?php echo $editing && $evento['Etiqueta'] == 'Programación Académica' ? 'selected' : ''; ?>>Programación Académica</option>
                        <option value="Oferta Académica" <?php echo $editing && $evento['Etiqueta'] == 'Oferta Académica' ? 'selected' : ''; ?>>Oferta Académica</option>
                        <option value="Administrativo" <?php echo $editing && $evento['Etiqueta'] == 'Administrativo' ? 'selected' : ''; ?>>Administrativo</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="descripcion">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea id="descripcion" name="descripcion" rows="4"><?php echo $editing ? htmlspecialchars($evento['Descripcion_Evento']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" id="btnCancelarCrearEvento">Cancelar</button>
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
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
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

<script src="./JS/admin-eventos/eliminar-evento.js"></script>
<script src="./JS/admin-eventos/editar-evento.js"></script>
<script src="./JS/admin-eventos/modal-creacion-y-participantes.js"></script>

<?php include './template/footer.php' ?>
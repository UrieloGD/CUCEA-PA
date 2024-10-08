<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

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
        <a href="./admin-crear-eventos.php"><button type="button" class="btn">Crear evento</button></a>
    </div>
</div>

<script src="./JS/admin-eventos/eliminar-evento.js"></script>
<script src="./JS/admin-eventos/editar-evento.js"></script>

<?php include './template/footer.php' ?>
<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-visual-eventos.css" />

<!--Cuadro principal del home-->
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
    // Consulta para obtener los eventos ordenados por fecha
    $sql = "SELECT * FROM Eventos_Admin WHERE Fecha_Inicio >= CURDATE() ORDER BY Fecha_Inicio, Hora_Inicio LIMIT 5";
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
                    <div class="event-footer">
                        <span class="department"><?php echo htmlspecialchars($row['Etiqueta']); ?></span>
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

    <div class="form-actions">
        <a href="./admin-eventos.php"><button type="button" class="btn">Crear evento</button></a>
    </div>
</div>
<?php include './template/footer.php' ?>
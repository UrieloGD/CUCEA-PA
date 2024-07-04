<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- css del home -->
    <title>Acceso restringido</title>
    <link rel="stylesheet" href="justificacion.css">
</head>

    <div class="modal">
        <h2>Acceso restringido</h2>
        <p>La fecha límite para subir la plantilla fue el <?php echo date('d de F de Y', strtotime($fecha_limite)); ?>.</p>
        <p>No subir tus actividades a tiempo puede tener graves consecuencias, tales como:</p>
        <ul>
            <li>Atrasar otras tareas.</li>
            <li>Cargar de trabajo a otras personas o áreas.</li>
            <li>Perjudicar la agenda de los alumnos.</li>
        </ul>
        <p>Si deseas subir la plantilla, justifica por qué no subiste la plantilla a tiempo.</p>
        <form action="procesar_justificacion.php" method="post">
            <input type="hidden" name="departamento_id" value="<?php echo $departamento_id; ?>">
            <textarea name="justificacion" placeholder="Escribe tu justificación aquí" required></textarea>
            <button type="submit" class="btn-continuar">Continuar</button>
        </form>
    </div>
<!--header -->
<?php include './template/footer.php' ?>
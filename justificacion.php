<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- css del home -->
    <title>Acceso restringido</title>
    <style>
        /* Estilos para el modal, similar a la imagen proporcionada */
        body {
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .modal {
            background-color: #ffe6e6;
            border-radius: 10px;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }
        .modal h2 {
            color: #d9534f;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }
        .btn-continuar {
            background-color: #0275d8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
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
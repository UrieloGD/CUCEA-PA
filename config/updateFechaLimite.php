<?php
include './db.php';
include './email_functions.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_fecha_limite = $_POST['fecha_limite'];
    $usuario_id = $_SESSION['Codigo'];

    mysqli_begin_transaction($conexion);

    try {
        // Inserta la nueva fecha límite en la tabla Fechas_Limite
        $sql = "INSERT INTO Fechas_Limite (Fecha_Limite, Fecha_Actualizacion, Usuario_ID) VALUES (?, NOW(), ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nueva_fecha_limite, $usuario_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Obtener todos los jefes de departamento
        $sql_jefes = "SELECT Codigo, Correo FROM Usuarios WHERE Rol_ID = 1";
        $result_jefes = mysqli_query($conexion, $sql_jefes);

        $mensaje = "La fecha límite para subir las Bases de Datos ha sido actualizada a: " . date('d/m/Y', strtotime($nueva_fecha_limite));

        while ($jefe = mysqli_fetch_assoc($result_jefes)) {
            // Insertar notificación en la base de datos
            $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) VALUES ('fecha_limite', ?, ?, ?)";
            $stmt_notificacion = mysqli_prepare($conexion, $sql_notificacion);
            mysqli_stmt_bind_param($stmt_notificacion, "sii", $mensaje, $jefe['Codigo'], $usuario_id);
            mysqli_stmt_execute($stmt_notificacion);
            mysqli_stmt_close($stmt_notificacion);

            // Enviar correo electrónico
            $asunto = "Actualización de fecha límite - Programación Académica";
            $cuerpo = "
            <html>
            <body>
                <h2>Actualización de fecha límite</h2>
                <p>{$mensaje}</p>
                <p>Por favor, ingrese al sistema para más detalles.</p>
            </body>
            </html>
            ";
            enviarCorreo($jefe['Correo'], $asunto, $cuerpo);
        }

        mysqli_commit($conexion);

        header("Location: ../data_departamentos.php?success=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo "Error: " . $e->getMessage();
    }
}

mysqli_close($conexion);

<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_fecha_limite = $_POST['fecha_limite'];
    $usuario_id = $_SESSION['Codigo'];

    mysqli_begin_transaction($conexion);

    try {
        // Inserta la nueva fecha límite en la tabla Fechas_Limite
        $sql = "INSERT INTO fechas_limite (Fecha_Limite, Fecha_Actualizacion, Usuario_ID) VALUES (?, NOW(), ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nueva_fecha_limite, $usuario_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Obtener todos los jefes de departamento
        $sql_jefes = "SELECT Codigo, Correo FROM usuarios WHERE Rol_ID = 1";
        $result_jefes = mysqli_query($conexion, $sql_jefes);

        $mensaje = "La fecha límite para subir las Bases de Datos ha sido actualizada a: " . date('d/m/Y', strtotime($nueva_fecha_limite));

        while ($jefe = mysqli_fetch_assoc($result_jefes)) {
            // Insertar notificación en la base de datos
            $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) VALUES ('fecha_limite', ?, ?, ?)";
            $stmt_notificacion = mysqli_prepare($conexion, $sql_notificacion);
            mysqli_stmt_bind_param($stmt_notificacion, "sii", $mensaje, $jefe['Codigo'], $usuario_id);
            mysqli_stmt_execute($stmt_notificacion);
            mysqli_stmt_close($stmt_notificacion);

            // Enviar correo electrónico
            $asunto = "Actualización de fecha límite - Programación Académica";
            $cuerpo = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                    .header { text-align: center; padding-bottom: 20px; }
                    .header img { width: 300px; }
                    .content { padding: 20px; }
                    h2 { color: #2c3e50; }
                    p { line-height: 1.5; color: #333; }
                    .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                    </div>
                    <div class='content'>
                        <h2>Actualización de fecha límite</h2>
                        <p>{$mensaje}</p>
                        <p>Por favor, ingrese al sistema para más detalles.</p>
                    </div>
                    <div class='footer'>
                        <p>Centro para la Sociedad Digital</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            enviarCorreo($jefe['Correo'], $asunto, $cuerpo);
        }

        mysqli_commit($conexion);

        header("Location: ../../admin-data-departamentos.php?success=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo "Error: " . $e->getMessage();
    }
}

mysqli_close($conexion);

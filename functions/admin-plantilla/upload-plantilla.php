<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';
session_start();
date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departamento_id = $_POST['Departamento_ID'];
    error_log("Departamento ID: $departamento_id");

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo_dep = $_FILES['file']['name'];
        $fecha_subida_dep = date('d/m/Y H:i');
        $archivo_temporal = $_FILES['file']['tmp_name'];

        // Leer el contenido del archivo
        $contenido_archivo_dep = file_get_contents($archivo_temporal);
        $contenido_archivo_dep = mysqli_real_escape_string($conexion, $contenido_archivo_dep);

        error_log("Archivo temporal: $archivo_temporal");

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO Plantilla_SA (Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep, Contenido_Archivo_Dep)
                VALUES ('$departamento_id', '$nombre_archivo_dep', '$fecha_subida_dep', '$contenido_archivo_dep')";

        if (mysqli_query($conexion, $sql)) {
            error_log("Inserción en la base de datos exitosa");

            // Obtener el correo del jefe de departamento
            $sql_jefe = "SELECT u.Codigo, u.Correo, d.Departamentos 
                 FROM Usuarios u 
                 JOIN Usuarios_Departamentos ud ON u.Codigo = ud.Usuario_ID 
                 JOIN Departamentos d ON ud.Departamento_ID = d.Departamento_ID 
                 WHERE d.Departamento_ID = ? AND u.Rol_ID = 1";
            $stmt = mysqli_prepare($conexion, $sql_jefe);
            mysqli_stmt_bind_param($stmt, "i", $departamento_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $jefe = mysqli_fetch_assoc($result);

            if ($jefe) {
                $mensaje = "Se te asignado una plantilla para el departamento de {$jefe['Departamentos']}.";


                // Insertar notificación en la tabla Notificaciones
                $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                     VALUES ('plantilla', ?, ?, ?)";
                $stmt_notificacion = mysqli_prepare($conexion, $sql_notificacion);
                mysqli_stmt_bind_param($stmt_notificacion, "sii", $mensaje, $jefe['Codigo'], $_SESSION['Codigo']);
                mysqli_stmt_execute($stmt_notificacion);

                // Enviar correo electrónico
                $asunto = "Nueva plantilla subida - Programación Académica";
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
                            <h2>Nueva plantilla subida</h2>
                            <p>{$mensaje}</p>
                            <p>Nombre del archivo: {$nombre_archivo_dep}</p>
                            <p>Fecha de subida: {$fecha_subida_dep}</p>
                            <p>Por favor, ingrese al sistema para más detalles.</p>
                        </div>
                        <div class='footer'>
                            <p>Centro para la Sociedad Digital</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                if (enviarCorreo($jefe['Correo'], $asunto, $cuerpo)) {
                    error_log("Correo enviado exitosamente al jefe del departamento {$jefe['Departamentos']}");
                } else {
                    error_log("Error al enviar correo al jefe del departamento {$jefe['Departamentos']}");
                }

                error_log("Notificación creada para el jefe del departamento {$jefe['Departamentos']}");
            } else {
                error_log("No se encontró jefe de departamento para el Departamento_ID: $departamento_id");
            }

            echo 'success';
            exit();
        } else {
            error_log("Error al insertar en la base de datos: " . mysqli_error($conexion));
            echo 'Error al añadir registro: ' . mysqli_error($conexion);
            exit();
        }
    } else {
        error_log("Error en la subida de archivo: " . $_FILES['file']['error']);
        echo 'Error: No se subió ningún archivo o hubo un error en la subida.';
        exit();
    }
} else {
    error_log("Método de solicitud no permitido");
    echo 'Error: Método de solicitud no permitido.';
    exit();
}

mysqli_close($conexion);

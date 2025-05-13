<?php
// Asegurarse de que los errores no se muestren en la salida
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Asegurarse de que la salida sea JSON
header('Content-Type: application/json');

// Iniciar la sesión
session_start();

include './../../config/db.php';
include './../notificaciones-correos/email_functions.php'; // Incluimos la función de correo
date_default_timezone_set('America/Mexico_City');

$response = ['success' => false, 'message' => '', 'error' => ''];

try {
    // Validar parámetros
    if (!isset($_POST['user_id']) || !isset($_POST['count'])) {
        throw new Exception("Faltan datos requeridos (user_id, count)");
    }

    $emisor_id = intval($_POST['user_id']);
    $count = intval($_POST['count']);

    // Obtener información del usuario emisor
    $stmt_emisor = $conexion->prepare("SELECT Nombre, Apellido, rol_id FROM usuarios WHERE Codigo = ?");
    $stmt_emisor->bind_param("i", $emisor_id);
    $stmt_emisor->execute();
    $result_emisor = $stmt_emisor->get_result();
    $emisor = $result_emisor->fetch_assoc();

    if (!$emisor) {
        throw new Exception("No se encontró el usuario emisor");
    }

    $nombre_emisor = $emisor['Nombre'] . ' ' . $emisor['Apellido'];
    $tipo_usuario = ($emisor['rol_id'] === 0) ? "administrador" : (($emisor['rol_id'] === 3) ? "coordinador" : "usuario");
    $fecha_accion = date('d/m/Y H:i');

    // Crear mensaje para la notificación en el sistema
    $mensajeSistema = "El administrador $nombre_emisor realizó $count modificaciones en los registros de su base de datos";

    // Notificar a todos los coordinadores (rol 3)
    $stmt_coordinadores = $conexion->prepare("SELECT u.Codigo, u.Correo 
                                             FROM usuarios u 
                                             WHERE u.rol_id = 3 AND u.Codigo != ?");
    $stmt_coordinadores->bind_param("i", $emisor_id);
    $stmt_coordinadores->execute();
    $result_coordinadores = $stmt_coordinadores->get_result();

    // Lista para almacenar correos de coordinadores
    $correos_coordinadores = [];

    while ($coordinador = $result_coordinadores->fetch_assoc()) {
        $coordinador_id = $coordinador['Codigo'];

        // Agregar correo a la lista si existe
        if (!empty($coordinador['Correo'])) {
            $correos_coordinadores[] = $coordinador['Correo'];
        }

        // Insertar notificación en el sistema
        $stmt_notificacion = $conexion->prepare("INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                               VALUES ('modificacion_bd', ?, ?, ?)");
        $stmt_notificacion->bind_param("sii", $mensajeSistema, $coordinador_id, $emisor_id);

        if (!$stmt_notificacion->execute()) {
            error_log("Error al crear notificación para coordinador ID $coordinador_id: " . $stmt_notificacion->error);
        }
    }

    // Notificar a los administradores si el cambio lo hizo un coordinador
    if ($emisor['rol_id'] == 3) {
        $stmt_admins = $conexion->prepare("SELECT u.Codigo, u.Correo 
                                          FROM usuarios u 
                                          WHERE u.rol_id = 0");
        $stmt_admins->execute();
        $result_admins = $stmt_admins->get_result();

        while ($admin = $result_admins->fetch_assoc()) {
            $admin_id = $admin['Codigo'];

            // Insertar notificación en el sistema
            $stmt_notificacion_admin = $conexion->prepare("INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                                         VALUES ('modificacion_bd', ?, ?, ?)");
            $stmt_notificacion_admin->bind_param("sii", $mensajeSistema, $admin_id, $emisor_id);

            if (!$stmt_notificacion_admin->execute()) {
                error_log("Error al crear notificación para admin ID $admin_id: " . $stmt_notificacion_admin->error);
            }
        }
    }
    // Si el cambio lo hizo un administrador, notificar a otros administradores
    else if ($emisor['rol_id'] == 0) {
        $stmt_admins = $conexion->prepare("SELECT u.Codigo, u.Correo 
                                          FROM usuarios u 
                                          WHERE u.rol_id = 0 AND u.Codigo != ?");
        $stmt_admins->bind_param("i", $emisor_id);
        $stmt_admins->execute();
        $result_admins = $stmt_admins->get_result();

        while ($admin = $result_admins->fetch_assoc()) {
            $admin_id = $admin['Codigo'];

            // Insertar notificación en el sistema
            $stmt_notificacion_admin = $conexion->prepare("INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                                         VALUES ('modificacion_bd', ?, ?, ?)");
            $stmt_notificacion_admin->bind_param("sii", $mensajeSistema, $admin_id, $emisor_id);

            if (!$stmt_notificacion_admin->execute()) {
                error_log("Error al crear notificación para admin ID $admin_id: " . $stmt_notificacion_admin->error);
            }
        }
    }

    // Preparar y enviar correo electrónico para notificaciones múltiples
    $asunto = "Cambios en su base de datos";
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
            .changes { margin: 20px 0; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #3498db; }
            .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
            </div>
            <div class='content'>
                <h2>Notificación de cambios en su base de datos</h2>
                <p><strong>$tipo_usuario:</strong> $nombre_emisor</p>
                <p><strong>Total de registros modificados:</strong> $count</p>
                <p><strong>Fecha y hora:</strong> $fecha_accion</p>
                <div class='changes'>
                    <p>Por favor, ingrese al sistema para ver los detalles completos de las modificaciones realizadas.</p>
                </div>
            </div>
            <div class='footer'>
                <p>Centro para la Sociedad Digital</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Enviar el correo a los coordinadores (si el emisor no es coordinador)
    if ($emisor['rol_id'] != 3 && !empty($correos_coordinadores)) {
        foreach ($correos_coordinadores as $correo) {
            if (enviarCorreo($correo, $asunto, $cuerpo)) {
                error_log("Correo múltiple enviado a $correo");
            } else {
                error_log("Error enviando correo múltiple a $correo");
            }
        }
    }

    $response['success'] = true;
    $response['message'] = "Notificaciones creadas correctamente";
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error en notificacion-multiple-coord.php: " . $e->getMessage());
}

// Cerrar conexiones
if (isset($stmt_emisor)) $stmt_emisor->close();
if (isset($stmt_coordinadores)) $stmt_coordinadores->close();
if (isset($stmt_notificacion)) $stmt_notificacion->close();
if (isset($stmt_admins)) $stmt_admins->close();
if (isset($stmt_notificacion_admin)) $stmt_notificacion_admin->close();
if (isset($conexion)) mysqli_close($conexion);

echo json_encode($response);

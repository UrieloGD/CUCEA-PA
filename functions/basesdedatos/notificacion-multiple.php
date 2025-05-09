<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';

session_start();
$response = ['success' => false];

try {
    // Validar parámetros
    if (!isset($_POST['department_id']) || !isset($_POST['user_id']) || !isset($_POST['count'])) {
        throw new Exception("Parámetros incompletos");
    }

    $department_id = $_POST['department_id'];
    $user_id = $_POST['user_id'];
    $count = intval($_POST['count']);

    // Obtener información del departamento
    $stmt_dep = $conexion->prepare("SELECT Departamentos FROM departamentos WHERE Departamento_ID = ?");
    $stmt_dep->bind_param("i", $department_id);
    $stmt_dep->execute();
    $departamento = $stmt_dep->get_result()->fetch_assoc();

    if (!$departamento) {
        throw new Exception("Departamento no encontrado");
    }

    // Obtener información del administrador
    $stmt_admin = $conexion->prepare("SELECT Nombre, Apellido FROM usuarios WHERE Codigo = ?");
    $stmt_admin->bind_param("i", $user_id);
    $stmt_admin->execute();
    $admin = $stmt_admin->get_result()->fetch_assoc();

    $nombre_admin = $admin ? $admin['Nombre'] . ' ' . $admin['Apellido'] : 'Un administrador';
    $fecha_accion = date('d/m/Y H:i');

    // Construir mensaje para sistema
    $mensajeSistema = "$nombre_admin realizó $count modificaciones en registros del departamento {$departamento['Departamentos']}";

    // Insertar notificación en sistema
    $stmt_notif = $conexion->prepare("INSERT INTO notificaciones (Tipo, Mensaje, Departamento_ID, Emisor_ID) VALUES ('modificacion_bd', ?, ?, ?)");
    $stmt_notif->bind_param("sii", $mensajeSistema, $department_id, $user_id);
    $stmt_notif->execute();

    // Construir correo electrónico
    $asunto = "Modificaciones múltiples en Programación Académica";
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
                <h2>Notificación de modificaciones de registros múltiples</h2>
                <p><strong>Administrador:</strong> $nombre_admin</p>
                <p><strong>Departamento:</strong> {$departamento['Departamentos']}</p>
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

    // Obtener correo del jefe
    $stmt_jefe = $conexion->prepare("SELECT u.Correo 
                                   FROM usuarios u
                                   JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID
                                   WHERE ud.Departamento_ID = ? AND u.rol_id = 1");
    $stmt_jefe->bind_param("i", $department_id);
    $stmt_jefe->execute();
    $jefe = $stmt_jefe->get_result()->fetch_assoc();

    if ($jefe && !empty($jefe['Correo'])) {
        // Enviar correo usando tu función PHPMailer
        if (enviarCorreo($jefe['Correo'], $asunto, $cuerpo)) {
            error_log("Correo múltiple enviado a {$jefe['Correo']}");
        } else {
            error_log("Error enviando correo múltiple a {$jefe['Correo']}");
        }
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error en notificacion-multiple: " . $e->getMessage());
}

echo json_encode($response);

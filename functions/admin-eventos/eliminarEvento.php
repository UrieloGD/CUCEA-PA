<?php
ob_start();
session_start(); // Añadido inicio de sesión

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once './../../config/db.php';
require_once '../notificaciones-correos/email_functions.php';

function sendJsonResponse($success, $message, $additionalData = [])
{
    $response = array_merge([
        'success' => $success,
        'message' => $message
    ], $additionalData);

    echo json_encode($response);
    exit();
}

try {
    // Leer y validar input
    $input = file_get_contents("php://input");
    if (empty($input)) {
        throw new Exception('No se recibieron datos');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    if (!isset($data['id'])) {
        throw new Exception('ID de evento no recibido');
    }

    $eventId = (int)$data['id'];
    if ($eventId <= 0) {
        throw new Exception('ID de evento inválido');
    }

    // Declarar variables de statement fuera del bloque try
    $stmt = null;
    $notif_stmt = null;
    $verificar_stmt = null;
    $email_stmt = null;
    $update_stmt = null;

    // Iniciar transacción
    $conexion->autocommit(false);
    $conexion->begin_transaction();

    try {
        // Obtener información del evento antes de marcarlo como inactivo
        $query = "SELECT ID_Evento, Nombre_Evento, Fecha_Inicio, Hora_Inicio, Participantes 
                  FROM eventos_admin 
                  WHERE ID_Evento = ?";

        $stmt = $conexion->prepare($query);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $conexion->error);
        }

        $stmt->bind_param("i", $eventId);
        if (!$stmt->execute()) {
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $evento = $result->fetch_assoc();
        
        // Cerrar este statement
        $stmt->close();
        $stmt = null;

        if (!$evento) {
            throw new Exception('El evento no existe');
        }

        // Guardar información del evento
        $nombreEvento = $evento['Nombre_Evento'];
        $fechaEvento = $evento['Fecha_Inicio'];
        $horaEvento = $evento['Hora_Inicio'];
        $participantes = !empty($evento['Participantes']) ? explode(',', $evento['Participantes']) : [];

        // Asegurarse de que tenemos un emisor_id válido
        $emisor_id = isset($_SESSION['Codigo']) ? $_SESSION['Codigo'] : null;
        if (!$emisor_id) {
            error_log("Advertencia: No se encontró ID del emisor en la sesión");
            $emisor_id = 0; // ID por defecto
        }

        // Crear notificaciones en el sistema
        if (!empty($participantes)) {
            $mensaje = "El evento '$nombreEvento' programado para el " .
                date('d/m/Y', strtotime($fechaEvento)) . " a las " .
                date('H:i', strtotime($horaEvento)) . " ha sido cancelado.";

            // Preparar consulta de verificación para evitar duplicados
            $sql_verificar = "SELECT COUNT(*) as count FROM notificaciones 
                WHERE Tipo = 'evento_cancelado' 
                AND Mensaje = ? 
                AND Usuario_ID = ?";
            $verificar_stmt = $conexion->prepare($sql_verificar);

            // Preparar la consulta de notificación
            $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID, Fecha) 
                                VALUES (?, ?, ?, 0, ?, NOW())";
            $notif_stmt = $conexion->prepare($sql_notificacion);

            if (!$notif_stmt) {
                throw new Exception('Error al preparar la notificación: ' . $conexion->error);
            }

            $tipo = 'evento_cancelado';

            foreach ($participantes as $participante_id) {
                if (!empty($participante_id)) {
                    // Verificar notificaciones similares
                    $verificar_stmt->bind_param("si", $mensaje, $participante_id);
                    $verificar_stmt->execute();
                    $resultado_verificar = $verificar_stmt->get_result();
                    $fila_verificar = $resultado_verificar->fetch_assoc();

                    // Insertar solo si no existe notificación similar
                    if ($fila_verificar['count'] == 0) {
                        // Insertar notificación en el sistema
                        $notif_stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);

                        if (!$notif_stmt->execute()) {
                            error_log("Error al crear notificación para usuario $participante_id: " . $notif_stmt->error);
                            continue;
                        }

                        // Obtener correo para notificación
                        $email_stmt = $conexion->prepare("SELECT Correo FROM usuarios WHERE Codigo = ?");
                        $email_stmt->bind_param("i", $participante_id);
                        $email_stmt->execute();
                        $email_result = $email_stmt->get_result();
                        $usuario = $email_result->fetch_assoc();

                        // Cerrar statement de email
                        $email_stmt->close();
                        $email_stmt = null;

                        if ($usuario && $usuario['Correo']) {
                            $asunto = "Evento Cancelado: $nombreEvento";
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
                                                <h2>El siguiente evento ha sido cancelado:</h2>
                                                <p><strong>Evento:</strong> $nombreEvento</p>
                                                <p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($fechaEvento)) . "</p>
                                                <p><strong>Hora:</strong> " . date('H:i', strtotime($horaEvento)) . "</p>
                                            </div>
                                            <div class='footer'>
                                                <p>Centro para la Sociedad Digital</p>
                                            </div>
                                        </div>
                                    </body>
                                </html>
                            ";
                            enviarCorreo($usuario['Correo'], $asunto, $cuerpo);
                        }
                    }
                }
            }

            // Cerrar statements de notificación
            if ($verificar_stmt) {
                $verificar_stmt->close();
                $verificar_stmt = null;
            }
            if ($notif_stmt) {
                $notif_stmt->close();
                $notif_stmt = null;
            }
        }

        // Actualizar el estado del evento a 'inactivo'
        $update_stmt = $conexion->prepare("UPDATE eventos_admin SET Estado = 'inactivo' WHERE ID_Evento = ?");
        if (!$update_stmt) {
            throw new Exception('Error al preparar la consulta de actualización: ' . $conexion->error);
        }

        $update_stmt->bind_param("i", $eventId);
        if (!$update_stmt->execute()) {
            throw new Exception('Error al ejecutar la actualización: ' . $update_stmt->error);
        }

        $affected_rows = $update_stmt->affected_rows;
        
        // Cerrar statement de actualización
        $update_stmt->close();
        $update_stmt = null;

        if ($affected_rows == 0) {
            throw new Exception('No se actualizó ningún registro');
        }

        // Confirmar la transacción
        $conexion->commit();

        sendJsonResponse(true, 'Evento marcado como inactivo exitosamente', ['affected_rows' => $affected_rows]);

    } catch (Exception $e) {
        // Asegurar cierre de todos los statements
        if ($stmt && !$stmt->closed) $stmt->close();
        if ($notif_stmt && !$notif_stmt->closed) $notif_stmt->close();
        if ($verificar_stmt && !$verificar_stmt->closed) $verificar_stmt->close();
        if ($email_stmt && !$email_stmt->closed) $email_stmt->close();
        if ($update_stmt && !$update_stmt->closed) $update_stmt->close();

        throw $e; // Re-lanzar para manejo externo
    }

} catch (Exception $e) {
    if (isset($conexion) && !$conexion->connect_error) {
        $conexion->rollback();
    }
    error_log("Error en eliminarEvento.php: " . $e->getMessage());
    sendJsonResponse(false, 'Error al marcar evento como inactivo: ' . $e->getMessage());
} finally {
    if (isset($conexion) && !$conexion->connect_error) {
        $conexion->close();
    }
    ob_end_flush();
}
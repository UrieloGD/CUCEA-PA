<?php
ob_start();
session_start(); // Añadido inicio de sesión

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once dirname(dirname(dirname(__FILE__))) . '/config/db.php';
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

    // Iniciar transacción
    $conexion->autocommit(false);
    $conexion->begin_transaction();

    // Obtener información del evento antes de marcarlo como inactivo
    $query = "SELECT ID_Evento, Nombre_Evento, Fecha_Inicio, Hora_Inicio, Participantes 
              FROM Eventos_Admin 
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
    $stmt->close();

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
        // Podrías asignar un ID por defecto o manejarlo de otra manera
        $emisor_id = 0; // O algún ID de sistema por defecto
    }

    // Crear notificaciones en el sistema
    if (!empty($participantes)) {
        $mensaje = "El evento '$nombreEvento' programado para el " .
            date('d/m/Y', strtotime($fechaEvento)) . " a las " .
            date('H:i', strtotime($horaEvento)) . " ha sido cancelado.";

        // Debug log
        error_log("Creando notificaciones para evento cancelado:");
        error_log("Mensaje: " . $mensaje);
        error_log("Emisor ID: " . $emisor_id);
        error_log("Participantes: " . print_r($participantes, true));

        // Preparar la consulta de notificación
        $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID, Fecha) 
                            VALUES (?, ?, ?, 0, ?, NOW())";
        $notif_stmt = $conexion->prepare($sql_notificacion);

        if (!$notif_stmt) {
            throw new Exception('Error al preparar la notificación: ' . $conexion->error);
        }

        $tipo = 'evento_cancelado';

        foreach ($participantes as $participante_id) {
            if (!empty($participante_id)) {
                // Insertar notificación en el sistema
                $notif_stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);

                if (!$notif_stmt->execute()) {
                    error_log("Error al crear notificación para usuario $participante_id: " . $notif_stmt->error);
                    continue;
                }

                // Verificar si la notificación se insertó correctamente
                if ($notif_stmt->affected_rows <= 0) {
                    error_log("No se pudo crear la notificación para el usuario $participante_id");
                }

                // Enviar correo electrónico
                $email_stmt = $conexion->prepare("SELECT Correo FROM Usuarios WHERE Codigo = ?");
                if (!$email_stmt) {
                    throw new Exception('Error al preparar consulta de correo: ' . $conexion->error);
                }

                $email_stmt->bind_param("i", $participante_id);
                if (!$email_stmt->execute()) {
                    throw new Exception('Error al obtener correo: ' . $email_stmt->error);
                }

                $email_result = $email_stmt->get_result();
                $usuario = $email_result->fetch_assoc();

                if ($usuario && $usuario['Correo']) {
                    $asunto = "Evento Cancelado: $nombreEvento";
                    $cuerpo = "
                        <html>
                        <body>
                            <p>El siguiente evento ha sido cancelado:</p>
                            <p><strong>Evento:</strong> $nombreEvento</p>
                            <p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($fechaEvento)) . "</p>
                            <p><strong>Hora:</strong> " . date('H:i', strtotime($horaEvento)) . "</p>
                        </body>
                        </html>
                    ";
                    enviarCorreo($usuario['Correo'], $asunto, $cuerpo);
                }
                $email_stmt->close();
            }
        }
        $notif_stmt->close();
    }

    // Actualizar el estado del evento a 'inactivo'
    $update_stmt = $conexion->prepare("UPDATE Eventos_Admin SET Estado = 'inactivo' WHERE ID_Evento = ?");
    if (!$update_stmt) {
        throw new Exception('Error al preparar la consulta de actualización: ' . $conexion->error);
    }

    $update_stmt->bind_param("i", $eventId);
    if (!$update_stmt->execute()) {
        throw new Exception('Error al ejecutar la actualización: ' . $update_stmt->error);
    }

    $affected_rows = $update_stmt->affected_rows;
    $update_stmt->close();

    if ($affected_rows == 0) {
        throw new Exception('No se actualizó ningún registro');
    }

    // Confirmar la transacción
    $conexion->commit();

    sendJsonResponse(true, 'Evento marcado como inactivo exitosamente', ['affected_rows' => $affected_rows]);
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

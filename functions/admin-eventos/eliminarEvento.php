<?php
ob_start();

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

    // Obtener información del evento antes de eliminarlo
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

    // Crear notificaciones en el sistema ANTES de eliminar el evento
    if (!empty($participantes)) {
        $mensaje = "El evento '$nombreEvento' programado para el " .
            date('d/m/Y', strtotime($fechaEvento)) . " a las " .
            date('H:i', strtotime($horaEvento)) . " ha sido cancelado.";

        // Preparar la consulta de notificación
        $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID, Fecha) 
                            VALUES (?, ?, ?, 0, ?, NOW())";
        $notif_stmt = $conexion->prepare($sql_notificacion);

        if (!$notif_stmt) {
            throw new Exception('Error al preparar la notificación: ' . $conexion->error);
        }

        $tipo = 'evento_cancelado';
        $emisor_id = isset($_SESSION['Codigo']) ? $_SESSION['Codigo'] : null;

        foreach ($participantes as $participante_id) {
            if (!empty($participante_id)) {
                // Insertar notificación en el sistema
                $notif_stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);
                if (!$notif_stmt->execute()) {
                    throw new Exception('Error al crear notificación: ' . $notif_stmt->error);
                }

                // Enviar correo
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

    // Ahora sí, eliminar el evento
    $delete_stmt = $conexion->prepare("DELETE FROM Eventos_Admin WHERE ID_Evento = ?");
    if (!$delete_stmt) {
        throw new Exception('Error al preparar la consulta de eliminación: ' . $conexion->error);
    }

    $delete_stmt->bind_param("i", $eventId);
    if (!$delete_stmt->execute()) {
        throw new Exception('Error al ejecutar la eliminación: ' . $delete_stmt->error);
    }

    $affected_rows = $delete_stmt->affected_rows;
    $delete_stmt->close();

    if ($affected_rows == 0) {
        throw new Exception('No se eliminó ningún registro');
    }

    // Confirmar la transacción
    $conexion->commit();

    sendJsonResponse(true, 'Evento eliminado exitosamente', ['affected_rows' => $affected_rows]);
} catch (Exception $e) {
    if (isset($conexion) && !$conexion->connect_error) {
        $conexion->rollback();
    }
    error_log("Error en eliminarEvento.php: " . $e->getMessage());
    sendJsonResponse(false, 'Error al eliminar evento: ' . $e->getMessage());
} finally {
    if (isset($conexion) && !$conexion->connect_error) {
        $conexion->close();
    }
    ob_end_flush();
}

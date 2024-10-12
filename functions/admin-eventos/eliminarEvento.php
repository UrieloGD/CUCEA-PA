<?php
header('Content-Type: application/json');
require_once __DIR__ . './../../config/db.php';
include './../notificaciones-correos/email_functions.php';  // Para el envío de correos

// Leer datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID de evento no recibido"]);
    exit();
}

$eventId = $data['id'];

// Iniciar una transacción
$conexion->autocommit(false);
$conexion->begin_transaction();

try {
    // Obtener los participantes del evento antes de eliminar
    $sql_participantes = "SELECT Participantes FROM Eventos_Admin WHERE ID_Evento = ?";
    $stmt_participantes = $conexion->prepare($sql_participantes);
    $stmt_participantes->bind_param("i", $eventId);
    $stmt_participantes->execute();
    $result_participantes = $stmt_participantes->get_result();
    $row_participantes = $result_participantes->fetch_assoc();

    // Asegurarse de que hay participantes
    if ($row_participantes && !empty($row_participantes['Participantes'])) {
        $participantesArray = explode(",", $row_participantes['Participantes']);
        
        // Mensaje de correo
        $asunto = "Evento eliminado";
        $cuerpo = "
            <html>
            <body>
                <p>Estimado usuario,</p>
                <p>Se ha eliminado el evento con ID: $eventId.</p>
                <p>Si tienes preguntas, no dudes en contactarnos.</p>
            </body>
            </html>
        ";

        // Enviar correos a los participantes
        foreach ($participantesArray as $participante) {
            // Obtener correo del participante
            $sql_email = "SELECT Correo FROM Usuarios WHERE Codigo = ?";
            $stmt_email = $conexion->prepare($sql_email);
            $stmt_email->bind_param("i", $participante);
            $stmt_email->execute();
            $result_email = $stmt_email->get_result();
            $row_email = $result_email->fetch_assoc();

            if ($row_email) {
                enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
            }
        }
    }

    // Eliminar evento
    $sql = "DELETE FROM Eventos_Admin WHERE ID_Evento = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $stmt->close();

    // Confirmar la transacción
    $conexion->commit();
    echo json_encode(["success" => true, "message" => "Evento eliminado exitosamente"]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar evento: " . $e->getMessage()]);
}

$conexion->close();
?>

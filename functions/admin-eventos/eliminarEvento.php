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
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                    .header { text-align: center; padding-bottom: 20px; }
                    .header img { width: 300px; }
                    .content { padding: 20px; }
                    h2 { color: #2c3e50; text-align: center; }
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
                        <h2>Evento eliminado</h2>
                        <p>Estimado usuario,</p>
                        <p>El evento en el que participas ha sido eliminado.</p>
                    </div>
                    <div class='footer'>
                        <p>Centro para la Sociedad Digital</p>
                    </div>
                </div>
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

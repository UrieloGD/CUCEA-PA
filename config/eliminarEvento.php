<?php
header('Content-Type: application/json');

// Asegúrate de que la ruta sea correcta
require_once __DIR__ . '/db.php';

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
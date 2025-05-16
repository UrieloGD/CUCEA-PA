<?php
include('./../../config/db.php');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Para notificaciones de otras tablas
$sql = "INSERT INTO notificaciones (Tipo, Relacion_ID, Estado)
        SELECT Tipo, ID_Justificacion, 0 
        FROM justificaciones 
        WHERE ID_Justificacion = ?
        ON DUPLICATE KEY UPDATE Estado = 0";

$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();

echo json_encode(['success' => true]);
<?php
require_once './../../config/db.php';
session_start();

$folio = $_GET['folio'] ?? null;

if (!$folio) {
    echo json_encode(['error' => 'Folio no proporcionado']);
    exit;
}

$sql = "SELECT estado FROM solicitudes_baja WHERE folio = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $folio);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['estado' => $row['estado']]);
} else {
    echo json_encode(['error' => 'Solicitud no encontrada']);
}
<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/CUCEA-PA/config/db.php';

// Respuesta en formato JSON
header('Content-Type: application/json');

$sql = "SELECT COUNT(*) as total FROM coord_per_prof";
$result = mysqli_query($conexion, $sql);

if (!$result) {
    echo json_encode(['error' => true, 'message' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit;
}

$row = mysqli_fetch_assoc($result);

// Si hay al menos una plantilla, devuelve true
if ($row['total'] > 0) {
    echo json_encode(['existePlantilla' => true]);
} else {
    echo json_encode(['existePlantilla' => false]);
}
?>
<?php
session_start();

// Deshabilitar la salida de errores para asegurar respuesta JSON limpia
ini_set('display_errors', 0);
error_reporting(0);

// Usar ruta relativa o absoluta correcta
// Opción 1: Ruta relativa (retrocede hasta encontrar la raíz)
include_once(__DIR__ . '/../../../config/db.php');

// Respuesta en formato JSON
header('Content-Type: application/json');

// Verificar conexión
if (!isset($conexion) || !$conexion) {
    echo json_encode(['error' => true, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

try {
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
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?>
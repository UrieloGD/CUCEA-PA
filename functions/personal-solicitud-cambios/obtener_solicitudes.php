<?php
session_start();
include './../../config/db.php';

try {
    $sql = "SELECT * FROM solicitudes_baja ORDER BY FECHA_SOLICITUD_B DESC";
    $resultado = $conexion->query($sql);
    
    $solicitudes = array();
    while ($row = $resultado->fetch_assoc()) {
        $solicitudes[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $solicitudes]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
<?php
session_start();
require_once './../../config/db.php';
header('Content-Type: application/json');

try {
    $year = date('y');
    $sql_count = "SELECT MAX(SUBSTRING_INDEX(OFICIO_NUM_BAJA_PROP, '/', 1)) as ultimo_numero 
                FROM solicitudes_baja_propuesta 
                WHERE OFICIO_NUM_BAJA_PROP LIKE '%/$year'";
    $result_count = $conexion->query($sql_count);
    $row = $result_count->fetch_assoc();
    $ultimo_numero = $row['ultimo_numero'] ? intval($row['ultimo_numero']) : 0;
    $nuevo_numero = $ultimo_numero + 1;
    $nuevo_folio = sprintf('%04d/%s', $nuevo_numero, $year);
    
    echo json_encode([
        'success' => true,
        'folio' => $nuevo_folio
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
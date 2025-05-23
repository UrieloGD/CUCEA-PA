<?php
// obtener_oficio_propuesta.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once './../../../config/db.php';
    
    // Obtener el año actual (solo los últimos dos dígitos)
    $anioActual = date('y');
    
    // Consultar el último número de folio para este año
    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(OFICIO_NUM_PROP, '/', 1) AS UNSIGNED)) AS ultimo_numero 
            FROM solicitudes_propuesta 
            WHERE SUBSTRING_INDEX(OFICIO_NUM_PROP, '/', -1) = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $anioActual);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $fila = $resultado->fetch_assoc();
    $ultimoNumero = $fila['ultimo_numero'];
    
    // Si no hay registros para este año, empezar desde 0
    if ($ultimoNumero === null) {
        $ultimoNumero = 0;
    }
    
    echo json_encode([
        'success' => true,
        'ultimo_numero' => $ultimoNumero
    ]);
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
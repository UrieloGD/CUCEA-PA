<!-- obtener_siguiente_oficio_propuesta.js -->
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

try {
    require_once './../../config/db.php';

    // Obtener año actual en 2 dígitos
    $anio_actual = date('y');
    
    // Obtener el último número de oficio del año actual
    $sql = "SELECT OFICIO_NUM_PROP FROM solicitudes_propuesta 
            WHERE CAST(ANO_P AS CHAR) = ? 
            ORDER BY OFICIO_NUM_PROP DESC LIMIT 1";
            
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("s", $anio_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    $siguiente_numero = "0001";

    if ($result && $result->num_rows > 0) {
        $ultimo = $result->fetch_assoc();
        $ultimo_numero = intval($ultimo['OFICIO_NUM_PROP']);
        $siguiente_numero = str_pad($ultimo_numero + 1, 4, '0', STR_PAD_LEFT);
    }

    echo json_encode([
        'status' => 'success',
        'siguiente_numero' => $siguiente_numero
    ]);

} catch (Exception $e) {
    error_log("Error en obtener_siguiente_oficio_propuesta.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit();
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once './../../config/db.php';
    
    // Obtener el año actual (solo los últimos dos dígitos)
    $anioActual = date('y');
    
    // Consultar el último número de folio para este año
    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(OFICIO_NUM_PROP, '/', 1) AS UNSIGNED)) AS ultimo_folio 
        FROM solicitudes_propuesta 
        WHERE SUBSTRING_INDEX(OFICIO_NUM_PROP, '/', -1) = ?"; // Usar parámetro para el año

    $stmt = mysqli_prepare($conexion, $sql);
    $anioActual = date('y');
    mysqli_stmt_bind_param($stmt, "s", $anioActual);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    $resultado = mysqli_query($conexion, $sql);
    
    if (!$resultado) {
        throw new Exception("Error en la consulta: " . mysqli_error($conexion));
    }
    
    $fila = mysqli_fetch_assoc($resultado);
    $ultimoFolio = $fila['ultimo_folio'];
    
    // Si no hay registros para este año, empezar desde 1
    if ($ultimoFolio === null) {
        $siguienteFolio = 1;
    } else {
        $siguienteFolio = $ultimoFolio + 1;
    }
    
    // Formatear el folio con ceros a la izquierda (4 dígitos) y añadir el año
    $folioFormateado = sprintf("%04d/%s", $siguienteFolio, $anioActual);
    
    echo json_encode([
        'success' => true,
        'folio' => $folioFormateado
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

    mysqli_close($conexion);
}
?>
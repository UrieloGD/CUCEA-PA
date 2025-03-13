<?php
// Limpiar cualquier salida previa
ob_clean();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
require_once './../../../config/db.php';

// Asegurar encabezado JSON limpio
header('Content-Type: application/json; charset=utf-8');

try {
    // Validar método de solicitud
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar ID
    if (!isset($_POST['ids'])) {
        throw new Exception('No se proporcionó ID');
    }

    $id = filter_var($_POST['ids'], FILTER_VALIDATE_INT);
    
    if ($id === false) {
        throw new Exception('ID no válido');
    }

    // Verificar si el registro existe y está en la papelera
    $checkSql = "SELECT ID FROM coord_per_prof WHERE ID = ? AND PAPELERA = 'inactivo'";
    $checkStmt = $conexion->prepare($checkSql);
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception('No se encontró el registro con ID ' . $id . ' en estado inactivo');
    }
    
    // Actualizar registro
    $sql = "UPDATE coord_per_prof SET PAPELERA = 'activo' WHERE ID = ? AND PAPELERA = 'inactivo'";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error preparando la consulta: ' . $conexion->error);
    }
    
    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error ejecutando la consulta: ' . $stmt->error);
    }

    if ($stmt->affected_rows <= 0) {
        throw new Exception('No se actualizó ningún registro. ID: ' . $id);
    }

    // Devolver respuesta JSON limpia
    echo json_encode([
        "success" => true,
        "message" => "Registro restaurado exitosamente",
        "affected_rows" => $stmt->affected_rows,
        "id" => $id
    ]);

} catch (Exception $e) {
    // Asegurar que el error se devuelva como JSON
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    // Cerrar declaraciones y conexión
    if (isset($checkStmt)) $checkStmt->close();
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}
exit(); // Asegurar que no haya salida adicional
?>
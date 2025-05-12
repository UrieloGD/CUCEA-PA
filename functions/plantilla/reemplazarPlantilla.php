<?php
session_start();
include './../../config/db.php';
header('Content-Type: application/json');

// Validación de seguridad
if (!isset($_SESSION['Codigo']) || !isset($_SESSION['Departamento_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$departamento_id = $_SESSION['Departamento_ID'];
$tabla_destino = 'data_' . str_replace(' ', '_', $_SESSION['Nombre_Departamento']);

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // 1. Eliminar registros de la tabla de datos
    $sql_delete_data = "DELETE FROM $tabla_destino WHERE Departamento_ID = ?";
    $stmt_delete_data = $conexion->prepare($sql_delete_data);
    $stmt_delete_data->bind_param("i", $departamento_id);
    
    if (!$stmt_delete_data->execute()) {
        throw new Exception("Error al eliminar datos existentes: " . $stmt_delete_data->error);
    }
    
    // 2. Resetear el autoincremento de la tabla de datos
    $sql_reset_ai = "ALTER TABLE $tabla_destino AUTO_INCREMENT = 1";
    if (!$conexion->query($sql_reset_ai)) {
        throw new Exception("Error al resetear autoincremento: " . $conexion->error);
    }
    
    // 3. Eliminar registros de la tabla de plantillas
    $sql_delete_plantilla = "DELETE FROM plantilla_dep WHERE Departamento_ID = ?";
    $stmt_delete_plantilla = $conexion->prepare($sql_delete_plantilla);
    $stmt_delete_plantilla->bind_param("i", $departamento_id);
    
    if (!$stmt_delete_plantilla->execute()) {
        throw new Exception("Error al eliminar plantilla existente: " . $stmt_delete_plantilla->error);
    }
    
    // Confirmar cambios
    $conexion->commit();
    echo json_encode(['success' => true, 'message' => 'Datos anteriores eliminados correctamente.']);
    
} catch (Exception $e) {
    // Revertir cambios en caso de error
    $conexion->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion->close();
exit;
?>
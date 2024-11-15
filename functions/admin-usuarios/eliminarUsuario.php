<?php
header('Content-Type: application/json');

// Conexión a la base de datos
include './../../config/db.php';

// Leer datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos"]);
    exit();
}

$userId = $data['id'];

// Iniciar una transacción
$conexion->autocommit(false);
$conexion->begin_transaction();

try {
    // Eliminar los registros relacionados en la tabla Usuarios_Departamentos
    $sql = "DELETE FROM Usuarios_Departamentos WHERE Usuario_ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Eliminar el usuario de la tabla Usuarios
    $sql = "DELETE FROM Usuarios WHERE Codigo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Confirmar la transacción
    $conexion->commit();
    echo json_encode(["success" => true, "message" => "Usuario eliminado exitosamente"]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar el usuario: " . $e->getMessage()]);
}

$conexion->close();

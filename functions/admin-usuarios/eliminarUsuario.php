<?php
header('Content-Type: application/json');

session_start();

// Conexión a la base de datos
include './../../config/db.php';

// Leer datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos"]);
    exit();
}

$userId = $data['id'];

// Verificar autenticación
if (!isset($_SESSION['Rol_ID'])) {
    echo json_encode(["success" => false, "message" => "No autenticado"]);
    exit();
}

// Verificar si el usuario actual es Administrador
$es_admin = ($_SESSION['Rol_ID'] === 0);

// Verificar el rol del usuario que se intenta modificar
$sql_verificar_rol = "SELECT Rol_ID FROM usuarios WHERE Codigo = ?";
$stmt_verificar_rol = $conexion->prepare($sql_verificar_rol);
$stmt_verificar_rol->bind_param("i", $userId);
$stmt_verificar_rol->execute();
$result_verificar = $stmt_verificar_rol->get_result();

if ($result_verificar->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Usuario a eliminar no encontrado"]);
    exit();
}

$usuario_a_eliminar = $result_verificar->fetch_assoc();

// Restricción: Solo los administradores pueden eliminar a otros administradores
if ($usuario_a_eliminar['Rol_ID'] === 0 && !$es_admin) {
    echo json_encode(["success" => false, "message" => "No tiene permisos para eliminar un usuario Administrador"]);
    exit();
}

// Iniciar una transacción
$conexion->autocommit(false);
$conexion->begin_transaction();

try {
    // Eliminar las notificaciones asociadas al usuario
    $sql = "DELETE FROM notificaciones WHERE Usuario_ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Eliminar los registros relacionados en la tabla Usuarios_Departamentos
    $sql = "DELETE FROM usuarios_departamentos WHERE Usuario_ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Eliminar el usuario de la tabla Usuarios
    $sql = "DELETE FROM usuarios WHERE Codigo = ?";
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
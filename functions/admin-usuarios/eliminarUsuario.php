<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Leer datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos"]);
    exit();
}

$userId = $data['id'];

// Iniciar una transacción
$conn->autocommit(false);
$conn->begin_transaction();

try {
    // Eliminar los registros relacionados en la tabla Usuarios_Departamentos
    $sql = "DELETE FROM Usuarios_Departamentos WHERE Usuario_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Eliminar el usuario de la tabla Usuarios
    $sql = "DELETE FROM Usuarios WHERE Codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Confirmar la transacción
    $conn->commit();
    echo json_encode(["success" => true, "message" => "Usuario eliminado exitosamente"]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar el usuario: " . $e->getMessage()]);
}

$conn->close();

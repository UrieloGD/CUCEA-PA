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

// Eliminar el usuario de la tabla Usuarios
$sql = "DELETE FROM Usuarios WHERE Codigo = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $userId);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Usuario eliminado exitosamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al eliminar el usuario: " . $stmt->error]);
}

$stmt->close();
$conn->close();

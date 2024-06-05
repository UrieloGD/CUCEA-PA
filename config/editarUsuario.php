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
$nombre = $data['Nombre'];
$apellido = $data['Apellido'];
$correo = $data['Correo'];
$rol = $data['Rol'];  // Rol_ID
$departamento = $data['Departamento'];  // Departamento_ID

// Debug: Ver datos recibidos
error_log("Datos recibidos: " . print_r($data, true));

// Actualizar datos del usuario en la tabla Usuarios
$sql = "UPDATE Usuarios SET Nombre = ?, Apellido = ?, Correo = ?, Rol_ID = ? WHERE Codigo = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
    exit();
}

$stmt->bind_param("sssii", $nombre, $apellido, $correo, $rol, $userId);

// Preparamos la consulta para actualizar el departamento en Usuarios_Departamentos
$sql_departamento = "UPDATE Usuarios_Departamentos SET Departamento_ID = ? WHERE Usuario_ID = ?";
$stmt_departamento = $conn->prepare($sql_departamento);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
    exit();
}

$stmt->bind_param("sssii", $nombre, $apellido, $correo, $rol, $userId);

// Ejecutar la consulta para actualizar los datos del usuario
if ($stmt->execute()) {
    // Preparamos la consulta para actualizar el departamento en Usuarios_Departamentos
    $sql_departamento = "UPDATE Usuarios_Departamentos SET Departamento_ID = ? WHERE Usuario_ID = ?";
    $stmt_departamento = $conn->prepare($sql_departamento);

    if ($stmt_departamento === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta para actualizar el departamento: " . $conn->error]);
        exit();
    }

    $stmt_departamento->bind_param("ii", $departamento, $userId);

    if ($stmt_departamento->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario actualizado exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el departamento del usuario: " . $stmt_departamento->error]);
    }

    $stmt_departamento->close();
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar el usuario: " . $stmt->error]);
}

$stmt->close();
$conn->close();

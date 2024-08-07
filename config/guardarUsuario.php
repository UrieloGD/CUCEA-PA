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
    die(json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]));
}

// Obtener los datos del formulario
$data = json_decode(file_get_contents('php://input'), true);

$codigo = $data['codigo'];
$nombre = $data['nombre'];
$apellido = $data['apellido'];
$correo = $data['correo'];
$rol = $data['rol'];
$departamento = $data['departamento'];
$genero = $data['genero'];
$password = $data['password'];

// Verificar si el código ya existe
$check_sql = "SELECT Codigo FROM Usuarios WHERE Codigo = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $codigo);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "El código de usuario ya existe"]);
    exit();
}
$check_stmt->close();

// Insertar el usuario en la tabla Usuarios
$sql = "INSERT INTO Usuarios (Codigo, Nombre, Apellido, Correo, Pass, Genero, Rol_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $codigo, $nombre, $apellido, $correo, $password, $genero, $rol);

if ($stmt->execute()) {
    // Insertar la relación usuario-departamento en la tabla Usuarios_Departamentos
    $sql_departamento = "INSERT INTO Usuarios_Departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?)";
    $stmt_departamento = $conn->prepare($sql_departamento);
    $stmt_departamento->bind_param("ii", $codigo, $departamento);

    if ($stmt_departamento->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario agregado exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al agregar la relación usuario-departamento: " . $stmt_departamento->error]);
    }

    $stmt_departamento->close();
} else {
    echo json_encode(["success" => false, "message" => "Error al agregar el usuario: " . $stmt->error]);
}

$stmt->close();
$conn->close();

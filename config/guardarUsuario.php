<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$rol = $_POST['rol'];
$departamento = $_POST['departamento'];

// Generar un código único para el usuario
$codigo = mt_rand(1000000000, 9999999999);

// Insertar el usuario en la tabla Usuarios
$sql = "INSERT INTO Usuarios (Codigo, Nombre, Apellido, Correo, Pass, Genero, Rol_ID) VALUES (?, ?, ?, ?, '', '', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssi", $codigo, $nombre, $apellido, $correo, $rol);

if ($stmt->execute()) {
    // Insertar la relación usuario-departamento en la tabla Usuarios_Departamentos
    $sql_departamento = "INSERT INTO Usuarios_Departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?)";
    $stmt_departamento = $conn->prepare($sql_departamento);
    $stmt_departamento->bind_param("ii", $codigo, $departamento);

    if ($stmt_departamento->execute()) {
        echo "Usuario agregado exitosamente";
    } else {
        echo "Error al agregar la relación usuario-departamento: " . $stmt_departamento->error;
    }

    $stmt_departamento->close();
} else {
    echo "Error al agregar el usuario: " . $stmt->error;
}

$stmt->close();
$conn->close();

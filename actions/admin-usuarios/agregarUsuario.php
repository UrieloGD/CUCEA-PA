<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

try {

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

    // Generar un salt aleatorio
    $salt = bin2hex(random_bytes(16));

    // Hashear la contraseña con SHA256 y el salt
    $hashedPassword = hash('sha256', $salt . $password);

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
    $sql = "INSERT INTO Usuarios (Codigo, Nombre, Apellido, Correo, Pass, Salt, Genero, Rol_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $codigo, $nombre, $apellido, $correo, $hashedPassword, $salt, $genero, $rol);

    if ($stmt->execute()) {
        // Verificar si el rol requiere un departamento
        $sql_check_rol = "SELECT Nombre_Rol FROM Roles WHERE Rol_ID = ?";
        $stmt_check_rol = $conn->prepare($sql_check_rol);
        $stmt_check_rol->bind_param("i", $rol);
        $stmt_check_rol->execute();
        $result_rol = $stmt_check_rol->get_result();
        $row_rol = $result_rol->fetch_assoc();
        
        if ($row_rol['Nombre_Rol'] != "Coordinación de Personal" && $row_rol['Nombre_Rol'] != "Secretaría Administrativa") {
            // Insertar la relación usuario-departamento solo si no es un rol especial
            $sql_departamento = "INSERT INTO Usuarios_Departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?)";
            $stmt_departamento = $conn->prepare($sql_departamento);
            $stmt_departamento->bind_param("ii", $codigo, $departamento);
            if (!$stmt_departamento->execute()) {
                echo json_encode(["success" => false, "message" => "Error al agregar la relación usuario-departamento: " . $stmt_departamento->error]);
                exit();
            }
            $stmt_departamento->close();
        }
        echo json_encode(["success" => true, "message" => "Usuario agregado exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al agregar el usuario: " . $stmt->error]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error inesperado: " . $e->getMessage()]);
    exit();
}

$stmt->close();
$conn->close();
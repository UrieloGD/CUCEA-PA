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
    $rol = $data['Rol'];
    $departamento = $data['Departamento'];

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
        // Verificar si el rol requiere un departamento
        $sql_check_rol = "SELECT Nombre_Rol FROM Roles WHERE Rol_ID = ?";
        $stmt_check_rol = $conn->prepare($sql_check_rol);
        $stmt_check_rol->bind_param("i", $rol);
        $stmt_check_rol->execute();
        $result_rol = $stmt_check_rol->get_result();
        $row_rol = $result_rol->fetch_assoc();
        
        if ($row_rol['Nombre_Rol'] != "Coordinación de Personal" && $row_rol['Nombre_Rol'] != "Secretaría Administrativa") {
            // Actualizar o insertar la relación usuario-departamento solo si no es un rol especial
            $sql_departamento = "INSERT INTO Usuarios_Departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?) ON DUPLICATE KEY UPDATE Departamento_ID = VALUES(Departamento_ID)";
            $stmt_departamento = $conn->prepare($sql_departamento);
            $stmt_departamento->bind_param("ii", $userId, $departamento);
            if (!$stmt_departamento->execute()) {
                echo json_encode(["success" => false, "message" => "Error al actualizar la relación usuario-departamento: " . $stmt_departamento->error]);
                exit();
            }
            $stmt_departamento->close();
        } else {
            // Si es un rol especial, eliminar cualquier relación usuario-departamento existente
            $sql_delete_departamento = "DELETE FROM Usuarios_Departamentos WHERE Usuario_ID = ?";
            $stmt_delete_departamento = $conn->prepare($sql_delete_departamento);
            $stmt_delete_departamento->bind_param("i", $userId);
            $stmt_delete_departamento->execute();
            $stmt_delete_departamento->close();
        }
        echo json_encode(["success" => true, "message" => "Usuario actualizado exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el usuario: " . $stmt->error]);
    }
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error inesperado: " . $e->getMessage()]);
    exit();
}

$stmt->close();
$conn->close();

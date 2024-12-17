<?php
header('Content-Type: application/json');

// Conexión a la base de datos
include './../../config/db.php';

try {
    // Leer datos de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "No se recibieron datos"]);
        exit();
    }

    $userId = $data['id'];
    $codigo = $data['Codigo'];
    $nombre = $data['Nombre'];
    $apellido = $data['Apellido'];
    $correo = $data['Correo'];
    $rol = $data['Rol'];
    $departamento = $data['Departamento'];

    // Verificar si el nuevo código ya existe (excepto para el usuario actual)
    $check_sql = "SELECT Codigo FROM usuarios WHERE Codigo = ? AND Codigo != ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("ii", $codigo, $userId);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El código de usuario ya existe"]);
        exit();
    }

    // Actualizar datos del usuario en la tabla Usuarios, incluyendo el código
    $sql = "UPDATE usuarios SET Codigo = ?, Nombre = ?, Apellido = ?, Correo = ?, Rol_ID = ? WHERE Codigo = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]);
        exit();
    }

    $stmt->bind_param("isssii", $codigo, $nombre, $apellido, $correo, $rol, $userId);

    // Ejecutar la consulta para actualizar los datos del usuario
    if ($stmt->execute()) {
        // Actualizar las referencias de código en otras tablas
        $update_departamento = "UPDATE usuarios_departamentos SET Usuario_ID = ? WHERE Usuario_ID = ?";
        $stmt_departamento = $conexion->prepare($update_departamento);
        $stmt_departamento->bind_param("ii", $codigo, $userId);
        $stmt_departamento->execute();

        // Verificar si el rol requiere un departamento
        $sql_check_rol = "SELECT Nombre_Rol FROM roles WHERE Rol_ID = ?";
        $stmt_check_rol = $conexion->prepare($sql_check_rol);
        $stmt_check_rol->bind_param("i", $rol);
        $stmt_check_rol->execute();
        $result_rol = $stmt_check_rol->get_result();
        $row_rol = $result_rol->fetch_assoc();
        
        if ($row_rol['Nombre_Rol'] != "Coordinación de Personal" && $row_rol['Nombre_Rol'] != "Secretaría Administrativa") {
            // Actualizar o insertar la relación usuario-departamento solo si no es un rol especial
            $sql_departamento = "INSERT INTO usuarios_departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?) ON DUPLICATE KEY UPDATE Departamento_ID = VALUES(Departamento_ID)";
            $stmt_departamento = $conexion->prepare($sql_departamento);
            $stmt_departamento->bind_param("ii", $codigo, $departamento);
            if (!$stmt_departamento->execute()) {
                echo json_encode(["success" => false, "message" => "Error al actualizar la relación usuario-departamento: " . $stmt_departamento->error]);
                exit();
            }
            $stmt_departamento->close();
        } else {
            // Si es un rol especial, eliminar cualquier relación usuario-departamento existente
            $sql_delete_departamento = "DELETE FROM usuarios_departamentos WHERE Usuario_ID = ?";
            $stmt_delete_departamento = $conexion->prepare($sql_delete_departamento);
            $stmt_delete_departamento->bind_param("i", $codigo);
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
$conexion->close();
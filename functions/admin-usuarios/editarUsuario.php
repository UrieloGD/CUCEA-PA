<?php
header('Content-Type: application/json');

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
    $genero = $data['Genero'];

    // Verificar el rol del usuario que se intenta modificar
    $sql_verificar_rol = "SELECT r.Nombre_Rol 
                         FROM usuarios u 
                         JOIN roles r ON u.Rol_ID = r.Rol_ID 
                         WHERE u.Codigo = ?";
    $stmt_verificar_rol = $conexion->prepare($sql_verificar_rol);
    $stmt_verificar_rol->bind_param("i", $userId);
    $stmt_verificar_rol->execute();
    $result_verificar = $stmt_verificar_rol->get_result();
    
    if ($result_verificar->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Usuario a modificar no encontrado"]);
        exit();
    }
    
    $usuario_a_modificar = $result_verificar->fetch_assoc();
    
    // Restricción: Solo los administradores pueden modificar a otros administradores
    if ($usuario_a_modificar['Nombre_Rol'] === "Administrador" && !$es_admin) {
        echo json_encode(["success" => false, "message" => "No tiene permisos para modificar un usuario Administrador"]);
        exit();
    }

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Verificar si el nuevo código ya existe (excepto para el usuario actual)
        $check_sql = "SELECT Codigo FROM usuarios WHERE Codigo = ? AND Codigo != ?";
        $check_stmt = $conexion->prepare($check_sql);
        $check_stmt->bind_param("ii", $codigo, $userId);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            throw new Exception("El código de usuario ya existe");
        }

        // Obtener el rol actual del usuario
        $sql_get_rol = "SELECT r.Nombre_Rol 
                       FROM usuarios u 
                       JOIN roles r ON u.Rol_ID = r.Rol_ID 
                       WHERE u.Codigo = ?";
        $stmt_get_rol = $conexion->prepare($sql_get_rol);
        $stmt_get_rol->bind_param("i", $userId);
        $stmt_get_rol->execute();
        $result_rol = $stmt_get_rol->get_result();
        $rol_actual = $result_rol->fetch_assoc();

        // Verificar si es Jefe de Departamento
        $sql_check_new_rol = "SELECT Nombre_Rol FROM roles WHERE Rol_ID = ?";
        $stmt_check_new_rol = $conexion->prepare($sql_check_new_rol);
        $stmt_check_new_rol->bind_param("i", $rol);
        $stmt_check_new_rol->execute();
        $result_new_rol = $stmt_check_new_rol->get_result();
        $nuevo_rol = $result_new_rol->fetch_assoc();

        if ($nuevo_rol['Nombre_Rol'] === "Jefe de Departamento") {
            // Verificar si ya existe un jefe en el departamento destino
            $check_jefe_sql = "SELECT u.Codigo 
                              FROM usuarios u 
                              JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID 
                              JOIN roles r ON u.Rol_ID = r.Rol_ID 
                              WHERE ud.Departamento_ID = ? 
                              AND r.Nombre_Rol = 'Jefe de Departamento' 
                              AND u.Codigo != ?";
            $check_jefe_stmt = $conexion->prepare($check_jefe_sql);
            $check_jefe_stmt->bind_param("ii", $departamento, $userId);
            $check_jefe_stmt->execute();
            $result_jefe = $check_jefe_stmt->get_result();

            if ($result_jefe->num_rows > 0) {
                throw new Exception("Ya existe un jefe asignado a este departamento");
            }
        }

        // Actualizar datos del usuario
        $sql = "UPDATE usuarios SET Codigo = ?, Nombre = ?, Apellido = ?, Correo = ?, Rol_ID = ?, Genero = ? WHERE Codigo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isssisi", $codigo, $nombre, $apellido, $correo, $rol, $genero, $userId);

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el usuario: " . $stmt->error);
        }

        // Manejar la actualización del departamento
        $roles_especiales = ["Coordinación de Personal", "Secretaría Administrativa", "Administrador"];
        if (!in_array($nuevo_rol['Nombre_Rol'], $roles_especiales)) {
            
            // Primero eliminar cualquier asignación existente
            $delete_dept_sql = "DELETE FROM usuarios_departamentos WHERE Usuario_ID = ?";
            $stmt_delete = $conexion->prepare($delete_dept_sql);
            $stmt_delete->bind_param("i", $codigo);
            $stmt_delete->execute();

            // Luego insertar la nueva asignación
            $sql_departamento = "INSERT INTO usuarios_departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?)";
            $stmt_departamento = $conexion->prepare($sql_departamento);
            $stmt_departamento->bind_param("ii", $codigo, $departamento);
            
            if (!$stmt_departamento->execute()) {
                throw new Exception("Error al actualizar la relación usuario-departamento");
            }
        } else {
            // Para roles especiales, eliminar cualquier relación con departamentos
            $sql_delete_departamento = "DELETE FROM usuarios_departamentos WHERE Usuario_ID = ?";
            $stmt_delete_departamento = $conexion->prepare($sql_delete_departamento);
            $stmt_delete_departamento->bind_param("i", $codigo);
            $stmt_delete_departamento->execute();
        }

        // Confirmar transacción
        $conexion->commit();
        echo json_encode(["success" => true, "message" => "Usuario actualizado exitosamente"]);

    } catch (Exception $e) {
        // Revertir cambios si hay error
        $conexion->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    // Cerrar conexiones
    if (isset($stmt_rol_editor)) $stmt_rol_editor->close();
    if (isset($stmt_verificar_rol)) $stmt_verificar_rol->close();
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_departamento)) $stmt_departamento->close();
    if (isset($check_stmt)) $check_stmt->close();
    $conexion->close();
}
<?php
header('Content-Type: application/json');

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexion a Base de datos
include './../../config/db.php';

try {
    // Verificar la conexión
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos del formulario
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
    }

    // Verificar que todos los campos necesarios estén presentes
    $required_fields = ['codigo', 'nombre', 'apellido', 'correo', 'rol', 'departamento', 'genero', 'password'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $codigo = $data['codigo'];
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $correo = $data['correo'];
    $rol = $data['rol'];
    $departamento = $data['departamento'];
    $genero = $data['genero'];
    $password = $data['password'];

    // Usar bcrypt para hashear la contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Verificar si el código ya existe
    $check_sql = "SELECT Codigo FROM usuarios WHERE Codigo = ?";
    $check_stmt = $conexion->prepare($check_sql);
    if ($check_stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    $check_stmt->bind_param("i", $codigo);
    if (!$check_stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $check_stmt->error);
    }
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El código de usuario ya existe"]);
        exit();
    }
    $check_stmt->close();

    // Verificar si el correo ya existe, evitar duplicaciones en creacion de usuario y al modificar
    $check_sql2 = "SELECT Correo FROM usuarios WHERE Correo = ?";
    $check_correo = $conexion->prepare($check_sql2);
    if ($check_correo === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    $check_correo->bind_param("s", $correo);
    if (!$check_correo->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $check_correo->error);
    }
    $check_result2 = $check_correo->get_result();

    if ($check_result2->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El correo registrado ya existe"]);
        exit();
    }
    $check_correo->close();

    // Insertar el usuario en la tabla Usuarios
    $sql = "INSERT INTO usuarios (Codigo, Nombre, Apellido, Correo, Pass, Genero, Rol_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta de inserción: " . $conexion->error);
    }
    $stmt->bind_param("issssss", $codigo, $nombre, $apellido, $correo, $hashedPassword, $genero, $rol);

    if (!$stmt->execute()) {
        throw new Exception("Error al insertar el usuario: " . $stmt->error);
    }

    // Verificar si el rol requiere un departamento
    $sql_check_rol = "SELECT Nombre_Rol FROM roles WHERE Rol_ID = ?";
    $stmt_check_rol = $conexion->prepare($sql_check_rol);
    if ($stmt_check_rol === false) {
        throw new Exception("Error en la preparación de la consulta de roles: " . $conexion->error);
    }
    $stmt_check_rol->bind_param("i", $rol);
    if (!$stmt_check_rol->execute()) {
        throw new Exception("Error al verificar el rol: " . $stmt_check_rol->error);
    }
    $result_rol = $stmt_check_rol->get_result();
    $row_rol = $result_rol->fetch_assoc();
    
    $roles_especiales = ["Coordinación de Personal", "Secretaría Administrativa", "Administrador"];
    if (!in_array($row_rol['Nombre_Rol'], $roles_especiales)){
        // Insertar la relación usuario-departamento solo si no es un rol especial
        $sql_departamento = "INSERT INTO usuarios_departamentos (Usuario_ID, Departamento_ID) VALUES (?, ?)";
        $stmt_departamento = $conexion->prepare($sql_departamento);
        if ($stmt_departamento === false) {
            throw new Exception("Error en la preparación de la consulta de departamento: " . $conexion->error);
        }
        $stmt_departamento->bind_param("ii", $codigo, $departamento);
        if (!$stmt_departamento->execute()) {
            throw new Exception("Error al agregar la relación usuario-departamento: " . $stmt_departamento->error);
        }
        $stmt_departamento->close();
    }
    
    echo json_encode(["success" => true, "message" => "Usuario agregado exitosamente"]);

} catch (Exception $e) {
    error_log("Error en agregarUsuario.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    exit();
}

if (isset($stmt)) $stmt->close();
if (isset($stmt_check_rol)) $stmt_check_rol->close();
$conexion->close();
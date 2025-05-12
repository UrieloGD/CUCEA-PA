<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión de forma segura
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Obtener la conexión
require_once './config/db.php';
$conexion = getConnection();

// Obtener email del usuario desde la sesión
$email = $_SESSION['email'];
$correo_usuario = $_SESSION['email'];

// Consulta SQL para obtener datos del usuario
$sql = "SELECT Nombre, Rol_ID, Genero, Apellido, Codigo FROM usuarios WHERE LOWER(Correo) = LOWER(?)";
$stmt = $conexion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $correo_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Guardar datos en sesión
        $_SESSION['Codigo'] = $row['Codigo'];
        $_SESSION['Rol_ID'] = $row['Rol_ID'];
        $_SESSION['Nombre'] = $row['Nombre'];
        $_SESSION['Apellido'] = $row['Apellido'];

        $nombre = $row['Nombre'];
        $rol_id = $row['Rol_ID'];
        $genero = $row['Genero'];
        $apellido = $row['Apellido'];
        $codigo = $row['Codigo'];

        // Consulta para obtener el nombre del rol
        $sql_rol = "SELECT Nombre_Rol FROM roles WHERE Rol_ID = ?";
        $stmt_rol = $conexion->prepare($sql_rol);

        if ($stmt_rol) {
            $stmt_rol->bind_param("i", $rol_id);
            $stmt_rol->execute();
            $result_rol = $stmt_rol->get_result();

            if ($result_rol->num_rows > 0) {
                $row_rol = $result_rol->fetch_assoc();
                $nombre_rol = $row_rol['Nombre_Rol'];
                $_SESSION['Nombre_rol'] = $nombre_rol;
            } else {
                $nombre_rol = 'Rol no disponible';
            }
            $stmt_rol->close();
        }

        // Si es jefe de departamento, obtener información adicional
        if ($rol_id == 1 || $rol_id == 4) {
            $sql_departamento = "SELECT 
                departamentos.Departamento_ID,
                departamentos.Nombre_Departamento,
                departamentos.Departamentos
            FROM usuarios_departamentos
            INNER JOIN departamentos ON usuarios_departamentos.Departamento_ID = departamentos.Departamento_ID
            WHERE usuario_ID = ?";

            $stmt_departamento = $conexion->prepare($sql_departamento);

            if ($stmt_departamento) {
                $stmt_departamento->bind_param("i", $codigo);
                $stmt_departamento->execute();
                $result_departamento = $stmt_departamento->get_result();

                if ($result_departamento->num_rows > 0) {
                    $row_departamento = $result_departamento->fetch_assoc();
                    $_SESSION['Nombre_Departamento'] = $row_departamento['Nombre_Departamento'];
                    $_SESSION['Departamento_ID'] = $row_departamento['Departamento_ID'];
                    $_SESSION['Departamentos'] = $row_departamento['Departamentos'];
                }
                $stmt_departamento->close();
            }
        }
        $stmt->close();
    } else {
        $nombre = 'Nombre no disponible';
        $nombre_rol = 'Rol no disponible';
    }
}

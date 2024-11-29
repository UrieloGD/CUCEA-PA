<?php
// Incluir el archivo de conexión a la base de datos
require_once './config/db.php';

// Verificar si una sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    // Redirigir al usuario en caso de que no haya iniciado sesión
    header("Location: login.php");
    exit();
}

// Obtener email del usuario loggeado desde la sesión
$correo_usuario = $_SESSION['email'];

// Consulta SQL para obtener la información del usuario loggeado
$sql = "SELECT Nombre, Rol_ID, Genero, Apellido, Codigo FROM Usuarios WHERE Correo = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se obtuvo un resultado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['Nombre'];
    $rol_id = $row['Rol_ID'];
    $genero = $row['Genero'];
    $apellido = $row['Apellido'];
    $codigo = $row['Codigo'];

    // Guardar información básica en la sesión
    $_SESSION['Codigo'] = $codigo;
    $_SESSION['Rol_ID'] = $rol_id;
    $_SESSION['Nombre'] = $nombre;
    $_SESSION['Apellido'] = $apellido;

    // Consulta SQL para obtener el nombre del rol
    $sql_rol = "SELECT Nombre_Rol FROM Roles WHERE Rol_ID = ?";
    $stmt_rol = $conexion->prepare($sql_rol);
    $stmt_rol->bind_param("i", $rol_id);
    $stmt_rol->execute();
    $result_rol = $stmt_rol->get_result();

    if ($result_rol->num_rows > 0) {
        $row_rol = $result_rol->fetch_assoc();
        $nombre_rol = $row_rol['Nombre_Rol'];
    } else {
        $nombre_rol = 'Rol no disponible';
    }

    // Manejo específico para diferentes roles
    if ($rol_id == 1) {
        // Para jefes de departamento (Rol 1)
        $sql_departamento = "SELECT Departamentos.Departamento_ID, Departamentos.Nombre_Departamento, Departamentos.Departamentos
            FROM Usuarios_Departamentos
            INNER JOIN Departamentos ON Usuarios_Departamentos.Departamento_ID = Departamentos.Departamento_ID
            WHERE Usuario_ID = ?";
        $stmt_departamento = $conexion->prepare($sql_departamento);
        $stmt_departamento->bind_param("i", $codigo);
        $stmt_departamento->execute();
        $result_departamento = $stmt_departamento->get_result();

        if ($result_departamento->num_rows > 0) {
            $row_departamento = $result_departamento->fetch_assoc();
            $_SESSION['Nombre_Departamento'] = $row_departamento['Nombre_Departamento'];
            $_SESSION['Departamento_ID'] = $row_departamento['Departamento_ID'];
            $_SESSION['Departamentos'] = $row_departamento['Departamentos'];
        } else {
            // Manejar caso de jefe sin departamento
            echo "El usuario no está asociado a ningún departamento.";
            unset($_SESSION['Departamento_ID']);
            unset($_SESSION['Nombre_Departamento']);
            unset($_SESSION['Departamentos']);
        }
    } elseif ($rol_id == 2 || $rol_id == 3) {
        // Para roles 2 y 3 (administradores o usuarios generales)
        // No establecer información de departamento
        unset($_SESSION['Departamento_ID']);
        unset($_SESSION['Nombre_Departamento']);
        unset($_SESSION['Departamentos']);
    }
} else {
    // Manejar caso de usuario no encontrado
    $nombre = 'Nombre no disponible';
    $nombre_rol = 'Rol no disponible';
}
?>
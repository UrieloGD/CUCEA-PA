<?php
// Incluir el archivo de conexión a la base de datos
require_once './config/db.php';

// Verificar si una sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    // Re-derigir al usuario en caso de que no haya iniciado sesión
    header("Location: login.php");
    exit();
}

// Obtener email del usuario loggeado desde la sesión
$email = $_SESSION['email'];
$correo_usuario = $_SESSION['email'];

// Consulta SQL para obtener el nombre del usuario loggeado
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

    // Guardar el código de usuario y el ID de rol en la sesión (por si acaso)
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

    // Verificar si el usuario es un jefe de departamento
    if ($rol_id == 1) {
        // Consulta SQL para obtener el departamento del usuario
        $sql_departamento = "SELECT Departamentos.Departamento_ID, Departamentos.Nombre_Departamento
            FROM Usuarios_Departamentos
            INNER JOIN Departamentos ON Usuarios_Departamentos.Departamento_ID = Departamentos.Departamento_ID
            WHERE Usuario_ID = ?";
        $stmt_departamento = $conexion->prepare($sql_departamento);
        $stmt_departamento->bind_param("i", $codigo);
        $stmt_departamento->execute();
        $result_departamento = $stmt_departamento->get_result();

        if ($result_departamento->num_rows > 0) {
            $row_departamento = $result_departamento->fetch_assoc();
            $departamento_id = $row_departamento['Departamento_ID'];
            $nombre_departamento = $row_departamento['Nombre_Departamento'];
            $_SESSION['Nombre_Departamento'] = $nombre_departamento; // Guardar el nombre del departamento en la sesión
            $_SESSION['Departamento_ID'] = $departamento_id; // Guardar el ID del departamento en la sesión

        } else {
            echo "El usuario no está asociado a ningún departamento.";
        }
    }
} else {
    $nombre = 'Nombre no disponible';
    $nombre_rol = 'Rol no disponible';
}

?>

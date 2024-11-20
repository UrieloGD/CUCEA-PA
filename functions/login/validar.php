<?php
session_start();

// Función para logging
function logError($message)
{
    $logFile = __DIR__ . '/debug.log';
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, $logFile);
}

try {
    // Log de inicio de proceso
    logError("Iniciando proceso de login");

    // Verificar datos POST
    logError("POST data: " . print_r($_POST, true));

    if (!isset($_POST['email']) || !isset($_POST['pass'])) {
        logError("Datos de formulario incompletos");
        throw new Exception("Datos de formulario incompletos");
    }

    // Sanitización y logging de credenciales
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass'];
    logError("Email sanitizado: " . $email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logError("Email inválido: " . $email);
        throw new Exception("Formato de email inválido");
    }

    // Conexión a la base de datos
    require_once '../../config/db.php';

    if (!$conexion) {
        logError("Error de conexión: " . mysqli_connect_error());
        throw new Exception("Error de conexión a la base de datos");
    }
    logError("Conexión a BD exitosa");

    // Consulta de usuario - Nota el cambio de 'Usuarios' a 'usuarios'
    $consulta = "SELECT u.*, r.Nombre_Rol 
                 FROM usuarios u 
                 LEFT JOIN roles r ON u.Rol_ID = r.Rol_ID 
                 WHERE u.Correo = ?";

    $stmt = mysqli_prepare($conexion, $consulta);
    if (!$stmt) {
        logError("Error en prepare: " . mysqli_error($conexion));
        throw new Exception("Error en la preparación de la consulta");
    }

    mysqli_stmt_bind_param($stmt, "s", $email);

    // Ejecutar consulta
    if (!mysqli_stmt_execute($stmt)) {
        logError("Error en execute: " . mysqli_stmt_error($stmt));
        throw new Exception("Error en la ejecución de la consulta");
    }

    $resultado = mysqli_stmt_get_result($stmt);

    if (!$resultado) {
        logError("Error en get_result: " . mysqli_stmt_error($stmt));
        throw new Exception("Error al obtener resultado");
    }

    if ($fila = mysqli_fetch_assoc($resultado)) {
        logError("Usuario encontrado en BD");
        $storedHash = $fila['Pass'];

        // Debug de contraseña
        logError("Hash almacenado: " . $storedHash);

        if (password_verify($pass, $storedHash)) {
            logError("Contraseña verificada correctamente");

            // Limpiar sesión anterior
            session_unset();

            // Establecer datos de sesión
            $_SESSION['user_id'] = $fila['Codigo'];
            $_SESSION['email'] = $email;
            $_SESSION['nombre'] = $fila['Nombre'];
            $_SESSION['apellido'] = $fila['Apellido'];
            $_SESSION['rol_id'] = $fila['Rol_ID'];
            $_SESSION['rol_nombre'] = $fila['Nombre_Rol'];

            logError("Sesión iniciada para usuario: " . $fila['Codigo']);

            // Regenerar ID de sesión
            session_regenerate_id(true);

            header("location: ../../home.php");
            exit();
        } else {
            logError("Contraseña incorrecta para usuario: " . $email);
            logError("Pass proporcionada (hash): " . password_hash($pass, PASSWORD_DEFAULT));
            throw new Exception("Contraseña incorrecta");
        }
    } else {
        logError("Usuario no encontrado: " . $email);
        throw new Exception("Usuario no encontrado");
    }
} catch (Exception $e) {
    logError("Error capturado: " . $e->getMessage());
    header("location: ../../login.php?error=1");
    exit();
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
}

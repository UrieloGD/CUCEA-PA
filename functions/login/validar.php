<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegurar que la sesión se inicie correctamente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que se reciban los datos del POST
if (!isset($_POST['email']) || !isset($_POST['pass'])) {
    error_log("Error: No se recibieron los datos del formulario");
    header("location:./../../login.php?error=2");
    exit();
}

$email = $_POST['email'];
$pass = $_POST['pass'];
$_SESSION['email'] = $email;

// Verificar que el archivo de conexión existe
if (!file_exists('./../../config/db.php')) {
    error_log("Error: No se encuentra el archivo de conexión a la base de datos");
    header("location:./../../login.php?error=3");
    exit();
}

require_once './../../config/db.php';

try {
    $conexion = getConnection();
} catch (Exception $e) {
    error_log("Error de conexión: " . $e->getMessage());
    header("location:./../../login.php?error=4");
    exit();
}

// Hacer la búsqueda case-insensitive
try {
    $consulta = "SELECT Codigo, Pass FROM usuarios WHERE LOWER(Correo) = LOWER(?)";
    $stmt = mysqli_prepare($conexion, $consulta);

    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . mysqli_error($conexion));
        header("location:./../../login.php?error=5");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        $storedHash = $fila['Pass'];

        if (password_verify($pass, $storedHash)) {
            // Contraseña correcta
            $_SESSION['user_id'] = $fila['Codigo'];

            // Verificar que la sesión se guardó correctamente
            if (!isset($_SESSION['user_id'])) {
                error_log("Error: No se pudo guardar la sesión del usuario");
                header("location:./../../login.php?error=6");
                exit();
            }

            // Asegurarse de que no hay salida antes de la redirección
            ob_clean();

            // Construir la ruta absoluta para la redirección
            $base_path = dirname(dirname(dirname($_SERVER['PHP_SELF'])));
            $redirect_url = $base_path . '../home.php';

            // Intentar la redirección con ruta absoluta
            header("Location: $redirect_url");
            exit();
        } else {
            error_log("Contraseña incorrecta para el usuario: $email");
            header("location:./../../login.php?error=1");
            exit();
        }
    } else {
        error_log("Usuario no encontrado: $email");
        header("location:./../../login.php?error=1");
        exit();
    }
} catch (Exception $e) {
    error_log("Error en la validación: " . $e->getMessage());
    header("location:./../../login.php?error=7");
    exit();
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
}

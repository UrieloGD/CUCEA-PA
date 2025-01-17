<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = $_POST['email'];
$pass = $_POST['pass'];
session_start();
$_SESSION['email'] = $email;

require_once './../../config/db.php';
$conexion = getConnection(); // Obtén la conexión usando la función que definiste

// Hacer la búsqueda case-insensitive
$consulta = "SELECT Codigo, Pass FROM usuarios WHERE LOWER(Correo) = LOWER(?)";
$stmt = mysqli_prepare($conexion, $consulta);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    // Usuario encontrado, ahora verificamos la contraseña
    $storedHash = $fila['Pass'];

    // Comparamos el hash generado con el almacenado
    if (password_verify($pass, $storedHash)) {
        // Contraseña correcta
        $_SESSION['user_id'] = $fila['Codigo'];
        header("location:./../../home.php");
        exit();
    } else {
        // Contraseña incorrecta
        header("location:./../../login.php?error=1");
        exit();
    }
} else {
    // Usuario no encontrado
    header("location:./../../login.php?error=1");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

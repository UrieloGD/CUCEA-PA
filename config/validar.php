<?php
$email = $_POST['email'];
$pass = $_POST['pass'];
session_start();
$_SESSION['email'] = $email;

$conexion = mysqli_connect("localhost", "root", "root", "pa");

// Obtenemos el salt y el hash almacenado para el usuario
$consulta = "SELECT Codigo, Pass, Salt FROM Usuarios WHERE Correo = ?";
$stmt = mysqli_prepare($conexion, $consulta);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    // Usuario encontrado, ahora verificamos la contraseña
    $storedHash = $fila['Pass'];
    $salt = $fila['Salt'];
    
    // Generamos el hash de la contraseña proporcionada
    $hashedPassword = hash('sha256', $salt . $pass);
    
    // Comparamos el hash generado con el almacenado
    if ($hashedPassword === $storedHash) {
        // Contraseña correcta
        $_SESSION['user_id'] = $fila['Codigo'];
        header("location:../home.php");
    } else {
        // Contraseña incorrecta
        header("location:../login.php?error=1");
    }
} else {
    // Usuario no encontrado
    header("location:../login.php?error=1");
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

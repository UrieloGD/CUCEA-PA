<?php
$email = $_POST['email'];
$pass = $_POST['pass'];
$codigo = $_POST['Codigo'];
$rol_id = $_POST['Rol_ID'];
session_start();
$_SESSION['email'] = $email;
$_SESSION['Codigo'] = $codigo;
$_SESSION['Rol_ID'] = $rol_id;
// Después de una autenticación exitosa

$conexion = mysqli_connect("localhost", "root", "root", "pa");

$consulta = "SELECT*FROM Usuarios where Correo='$email' and pass='$pass'";
$resultado = mysqli_query($conexion, $consulta);

$filas = mysqli_num_rows($resultado);

if ($filas) {
    header("location:../home.php");
} else {
?>

    <script>
        alert("Error de autenticación")
        window.location.replace("../login.php");
    </script>
<?php

}

mysqli_free_result($resultado);
mysqli_close($conexion);
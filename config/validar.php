<?php
$email = $_POST['email'];
$pass = $_POST['pass'];
session_start();
$_SESSION['email'] = $email;

$conexion = mysqli_connect("localhost", "root", "", "pa");

$consulta = "SELECT*FROM usuarios where Correo='$email' and pass='$pass'";
$resultado = mysqli_query($conexion, $consulta);

$filas = mysqli_num_rows($resultado);

if ($filas) {
    header("location:home.php");
} else {
?>
    <?php
    include("login.php");
    ?>
    <script>
        alert("Error de autenticación")
    </script>
<?php

}

mysqli_free_result($resultado);
mysqli_close($conexion);

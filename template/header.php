<?php
// Incluir el archivo de conexión a la base de datos
require_once './config/db.php';

// Obtener el correo electrónico del usuario loggeado desde la sesión
session_start();

$correo_usuario = $_SESSION['email'];

// Consulta SQL para obtener el nombre del usuario loggeado
$sql = "SELECT Nombre, rol, Genero, Apellido FROM usuarios WHERE Correo = '$correo_usuario'";
$result = $conexion->query($sql);

// Verificar si se obtuvo un resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $nombre = $row['Nombre'];
  $rol = $row['rol'];
  $genero = $row['Genero'];
  $apellido = $row['Apellido'];
} else {
  $nombre = 'Nombre no disponible';
  $rol = 'Rol no disponible';
}
?>

<div class="container">
  <div class="header">
    <div class="titulo">
      <h3>Programación Académica</h3>
    </div>
    <div class="rol">
      <h3><?php echo $rol; ?></h3>
    </div>
    <li class="icono-notificaciones">
      <a href="#">
        <i class="fas fa-bell" style="font-size: 28px; color: black   ;"></i>
      </a>
    </li>
  </div>
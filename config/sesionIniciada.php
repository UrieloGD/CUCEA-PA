<?php
// Incluir el archivo de conexión a la base de datos
require_once './config/db.php';
session_start();

if (!isset($_SESSION['email'])) {
  // Re-derigir al usuario en caso de que no haya iniciado sesión
  header("Location: login.php");
  exit();
}

// Obtener email del usuario loggeado desde la sesión
$email = $_SESSION['email'];
$correo_usuario = $_SESSION['email'];

// Después de una autenticación exitosa guardamos el codigo para las acciones que haga en el sistema
//$codigo_usuario = $_SESSION['Codigo'];

// Consulta SQL para obtener el nombre del usuario loggeado
$sql = "SELECT Nombre, Rol_ID, Genero, Apellido, Codigo FROM Usuarios WHERE Correo = '$correo_usuario'";
$result = $conexion->query($sql);

// Verificar si se obtuvo un resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $nombre = $row['Nombre'];
  $rol_id = $row['Rol_ID'];
  $genero = $row['Genero'];
  $apellido = $row['Apellido'];
  $codigo_usuario = $row['Codigo'];
  // $codigo = $row['Codigo'];

  // Consulta SQL para obtener el nombre del rol
  $sql_rol = "SELECT Nombre_Rol FROM Roles WHERE Rol_ID = $rol_id";
  $result_rol = $conexion->query($sql_rol);

  if ($result_rol->num_rows > 0) {
    $row_rol = $result_rol->fetch_assoc();
    $nombre_rol = $row_rol['Nombre_Rol'];
  } else {
    $nombre_rol = 'Rol no disponible';
  }
} else {
  $nombre = 'Nombre no disponible';
  $nombre_rol = 'Rol no disponible';
}

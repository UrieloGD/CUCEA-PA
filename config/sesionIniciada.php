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
$codigo = $_SESSION['Codigo'];
$rol_id = $_SESSION['Rol_ID'];

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
  $codigo = $row['Codigo'];

  // Consulta SQL para obtener el nombre del rol
  $sql_rol = "SELECT Nombre_Rol FROM Roles WHERE Rol_ID = $rol_id";
  $result_rol = $conexion->query($sql_rol);

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
        WHERE Usuario_ID = $codigo";
    $result_departamento = $conexion->query($sql_departamento);

    if ($result_departamento->num_rows > 0) {
        $row_departamento = $result_departamento->fetch_assoc();
        $departamento_id = $row_departamento['Departamento_ID'];
        $nombre_departamento = $row_departamento['Nombre_Departamento'];
        // Puedes utilizar $departamento_id y $nombre_departamento según tus necesidades
    } else {
        echo "El usuario no está asociado a ningún departamento.";
    }
}
} else {
  $nombre = 'Nombre no disponible';
  $nombre_rol = 'Rol no disponible';
}
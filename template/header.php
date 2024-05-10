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

// Consulta SQL para obtener el nombre del usuario loggeado
$sql = "SELECT Nombre, Rol_ID, Genero, Apellido FROM Usuarios WHERE Correo = '$correo_usuario'";
$result = $conexion->query($sql);

// Verificar si se obtuvo un resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $nombre = $row['Nombre'];
  $rol_id = $row['Rol_ID'];
  $genero = $row['Genero'];
  $apellido = $row['Apellido'];

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
?>

<div class="container">
  <div class="header">
  <div class="header-content"> <!-- Contenedor para alinear contenidos del header creo? -->
  <!-- Icono Menú hamburguesa -->
  <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    
<!-- Menú hamburguesa -->
<div class="mobile-menu">
    <ul>
        <li><a href="./home.php">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-home-b.png" alt="">
            <img class="blue-icon" src="./Img//Icons/iconos-navbar/iconos-azules/icono-home.png" alt="">
            Inicio
        </a></li>
        <li><a href="./basesdedatos.php">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-registro-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-registro.png" alt="">
            Bases de Datos
        </a></li>
        <li><a href="#">
        <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png" alt="">
        <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" alt="">
            Oferta
        </a></li>
        <li><a href="#">
        <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png" alt="">
        <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" alt="">
            Espacios
        </a></li>
        <li><a href="./plantilla.php">
        <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png" alt="">
        <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" alt="">
            Plantilla
        </a></li>
        <li><a href="./guia.php">
        <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png" alt="">
        <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" alt="">
            Guía
        </a></li>
        <!-- Perfil y Cerrar sesión van juntos -->
        <li class="profile-item">
          <a href="#">
        <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-usuario-b.png" alt="">
        <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono=perfil.png" alt="">
            Perfil
        </a>
        <a href="./config/cerrarsesion.php">
          <button>Cerrar sesión</button>
          </a>
      </li>
    </ul>
</div>
    <div class="titulo">
      <h3>Programación Académica</h3>
    </div>
    <div class="rol">
      <h3><?php echo $nombre_rol; ?></h3>
    </div>
    <li class="icono-notificaciones">
      <a href="#" id="notification-icon">
        <i class="fas fa-bell" style="font-size: 28px; color: black;"></i>
      </a>
    </li>
    <!-- Add the notification menu container -->
    <div class="notification-menu" id="notification-menu">
      <div class="date">
        <span id="current-time"></span><br>
        <span id="current-date"></span>
      </div>
      <hr>
      <ul id="notifications">
      </ul>
      <div class="icons">
        <i id="notification-icon" class="fas fa-bell" style="font-size: 28px; color: black;"></i>
        <i id="calendar-icon" class="fas fa-calendar" style="font-size: 28px; color: black;"></i>
      </div>
    </div>
    </div>
  </div>
  <script src="./JS/header.js"></script>
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
    <div class="titulo">
      <h3>Programación Académica</h3>
    </div>
    <div class="rol">
      <h3><?php echo $nombre_rol; ?></h3>
    </div>
    <li class="icono-notificaciones">
      <a href="#" id="notification-icon">
        <i class="fas fa-bell" style="font-size: 28px; color: black   ;"></i>
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
        <i id="notification-icon" class="fas fa-bell" style="font-size: 28px; color: black   ;"></i>
        <i id="calendar-icon" class="fas fa-calendar" style="font-size: 28px; color: black   ;"></i>
      </div>
    </div>
  </div>

  <script>
    // Get the notification icon and menu
    const notificationIcon = document.getElementById('notification-icon');
    const notificationMenu = document.getElementById('notification-menu');

    // Add a click event listener to the notification icon
    notificationIcon.addEventListener('click', () => {
      // Toggle the visibility of the notification menu
      notificationMenu.style.display = (notificationMenu.style.display === 'none') ? 'block' : 'none';
    });

    // Get the current date and time
    const currentDate = document.getElementById('current-date');
    const currentTime = document.getElementById('current-time');

    // Update the current date and time every second
    setInterval(() => {
      const now = new Date();
      const options = {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      }; // Use 'long' for month name
      currentDate.textContent = now.toLocaleDateString('es-MX', options);
      currentTime.textContent = now.toLocaleTimeString('es-MX');
    }, 1000); // Update every second

    // Add notifications here
    const notifications = document.getElementById('notifications');

    // Example notifications

    const notification1 = document.createElement('li');
    notification1.textContent = 'Aldo Ceja está enojao pq no le has mandado la Base de Datos';
    notification1.classList.add('urgente'); // Agregar la clase 'urgente' para notificaciones urgentes (rojo)
    notifications.appendChild(notification1);

    const notification2 = document.createElement('li');
    notification2.textContent = 'Notificación 2';
    notification2.classList.add('urgente'); // Agregar la clase 'urgente' para notificaciones urgentes (rojo)
    notifications.appendChild(notification2);

    const notification3 = document.createElement('li');
    notification3.textContent = 'Notificación 3';
    notification3.classList.add('normal'); // Agregar la clase 'normal' para notificaciones normales (verde)
    notifications.appendChild(notification3);

    const notification4 = document.createElement('li');
    notification4.textContent = 'Notificación 4';
    notification4.classList.add('advertencia'); // Agregar la clase 'advertencia' para notificaciones de advertencia (amarillo)
    notifications.appendChild(notification4);
  </script>
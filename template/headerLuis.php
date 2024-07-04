<!-- headerLuis.php -->

<?php
  include('./config/sesionIniciada.php')
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
        <li>
          <a href="./home.php">
        <li>
          <a href="./home.php">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-home-b.png" alt="">
            <img class="blue-icon" src="./Img//Icons/iconos-navbar/iconos-azules/icono-home.png" alt="">
            Inicio</a>
        </li>
        <li>
          <?php 
          // Redirigir según el rol del usuario
          if ($rol_id == 1) {
            // Si el usuario es jefe de departamento, redirigir a la base de datos del departamento correspondiente
            if (isset($_SESSION['Nombre_Departamento'])) {
              // Obtener el nombre del departamento desde la sesión
              $nombre_departamento = $_SESSION['Nombre_Departamento'];
              echo "<a href='./basesdedatos.php'>";
            } else {
              // Manejar el caso en que no se encuentre asociado a ningún departamento
              echo "<a href='./data_departamentos.php'>";
            }
          } else { 
            // Otros roles o manejo de errores aquí
            echo "<a href='./data_departamentos.php'>";
          }
          ?>
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-registro-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-registro.png" alt="">
            Bases de Datos</a>
        </li>
        <li>
          <a href="dashboard_oferta.php">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" alt="">
            Oferta</a>
        </li>
        <li>
            <a href="#">
              <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png" alt="">
              <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" alt="">
              Espacios</a>
        </li>
        <li>
        <?php 
          // Redirigir según el rol del usuario
          if ($rol_id == 1) {
            // Si el usuario es jefe de departamento, redirigir a la base de datos del departamento correspondiente
            if (isset($_SESSION['Nombre_Departamento'])) {
              // Obtener el nombre del departamento desde la sesión
              $nombre_departamento = $_SESSION['Nombre_Departamento'];
              echo "<a href='./plantilla.php'>";
            } else {
              // Manejar el caso en que no se encuentre asociado a ningún departamento
              echo "<a href='./plantillaspa.php'>";
            }
          } else { 
            // Otros roles o manejo de errores aquí
            echo "<a href='./plantillaspa.php'>";
          }
          ?>
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" alt="">
            Plantilla</a>
        </li>
        <li>
          <a href="./guia.php">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" alt="">
            Guía
          </a>
        </li>
        <?php
          if ($rol_id == 2) { // Mostrar ícono de admin solo si el usuario es secretaria administrativa
        ?>
        <li>
          <a href="./admin-home.php">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-admin-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono-admin.png" alt="">
            Admin
          </a>
        </li>
        <?php
          }
        ?>
        <!-- Perfil y Cerrar sesión van juntos -->
        <li class="profile-item">
          <a href="#">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-usuario-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono=perfil.png" alt="">
            <img class="white-icon" src="./Img/Icons/iconos-navbar/iconos-blancos/icono-usuario-b.png" alt="">
            <img class="blue-icon" src="./Img/Icons/iconos-navbar/iconos-azules/icono=perfil.png" alt="">
            Perfil
          </a>
        </li>
        <li class="profile-button">
          <a href="./config/cerrarsesion.php">
            <button>Cerrar sesión</button>
          </a>
        </li>
        <li class="profile-button">
          <a href="./config/cerrarsesion.php">
            <button>Cerrar sesión</button>
          </a>
        </li>
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
  <script src="./JS/headerLuis.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
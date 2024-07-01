<?php
  include('./config/sesionIniciada.php')
?>
<link rel="stylesheet" href="./CSS/notificaciones.css" />
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
          <a href="./guiaPA.php">
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
      <a href="javascript:void(0);" id="notification-icon" onclick="toggleNav()">
        <i class="fas fa-bell" style="font-size: 28px; color: black;"></i>
      </a>
    </li>
    </div>
    <div id="mySidebar" class="sidebar">
  <div class="contenedor-fecha-hora">
    <div class="fecha-hora-info">
      <div class="hora">13:20:59</div>
      <div class="fecha">17 de junio de 2024</div>
    </div>
    <button class="marcar-leido">Marcar como leído</button>
  </div>
  <div class="contenedor-notificacion">
    <div class="imagen">
      <div class="circulo"></div>
    </div>
    <div class="info-notificacion">
      <div class="usuario">Coordinación de Personal</div>
      <div class="descripcion">Ha realizado una solicitud de cambios</div>
      <div class="fecha-hora">3 de junio, 14:20 horas</div>
    </div>
  </div>
  <div class="contenedor-notificacion">
            <div class="imagen">
                <div class="circulo"></div>
            </div>
            <div class="info-notificacion">
                <div class="usuario">Estudios regionales</div>
                <div class="descripcion">La base de datos no ha sido actualizada</div>
                <div class="fecha-hora">3 de junio, 11:30 horas</div>
            </div>
        </div>
        <div class="contenedor-notificacion">
            <div class="imagen">
                <div class="circulo"></div>
            </div>
            <div class="info-notificacion">
                <div class="usuario">Políticas públicas</div>
                <div class="descripcion">La base de datos ha sido actualizada correctamente</div>
                <div class="fecha-hora">3 de junio, 11:30 horas</div>
            </div>
        </div>
        <div class="contenedor-notificacion">
            <div class="imagen">
                <div class="circulo"></div>
            </div>
            <div class="info-notificacion">
                <div class="usuario">Control Escolar</div>
                <div class="descripcion">Ha enviado una solicitud de apertura de sección</div>
                <div class="fecha-hora">2 de junio, 11:30 horas</div>
            </div>
        </div>
  <!-- Agrega aquí las demás notificaciones del segundo código -->
</div>
  </div>
  <script src="./JS/header.js"></script>
  <script src="./JS/barra-notificaciones.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
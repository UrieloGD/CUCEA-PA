<?php
  include('./config/sesionIniciada.php')
?>

<div class="container">
  <div class="header">
    <div class="header-content">
      <!-- Icono Menú hamburguesa -->
      <button class="menu-toggle">
        <i class="fas fa-bars"></i>
      </button>
    
      <!-- Menú hamburguesa -->
      <div class="mobile-menu">
        <!-- ... (código del menú hamburguesa sin cambios) ... -->
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
      
      <!-- Nuevo menú de notificaciones -->
      <div class="notification-menu" id="notification-menu">
        <div class="contenedor-fecha-hora">
          <div class="fecha-hora-info">
            <div class="hora" id="current-time"></div>
            <div class="fecha" id="current-date"></div>
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
        <!-- Más notificaciones aquí -->
      </div>
    </div>
  </div>
</div>
<script src="./JS/headerCass.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
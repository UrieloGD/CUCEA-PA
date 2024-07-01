<?php
include('./config/sesionIniciada.php')
?>


<!-- Adquirir justificaciones -->
<?php
date_default_timezone_set('America/Mexico_City');
// Añade esto justo después de incluir 'sesionIniciada.php'
$notificaciones = [];
if ($rol_id == 2) { // 2 es el ID del rol de Secretaría Administrativa

  // Conexión a la base de datos
  $servername = "localhost";
  $username = "root";
  $password = "root";
  $dbname = "pa";
  $conn = mysqli_connect($servername, $username, $password, 'PA');

  // Consulta para obtener las justificaciones
  $query = "SELECT 'justificacion' AS tipo, j.ID_Justificacion AS id, j.Fecha_Justificacion AS fecha, d.Departamentos, u.Nombre, u.Apellido 
  FROM Justificaciones j
  JOIN Departamentos d ON j.Departamento_ID = d.Departamento_ID
  JOIN Usuarios u ON j.Codigo_Usuario = u.Codigo
  WHERE j.Justificacion_Enviada = 1
  UNION ALL
  SELECT 'plantilla' AS tipo, p.ID_Archivo_Dep AS id, p.Fecha_Subida_Dep AS fecha, d.Departamentos, u.Nombre, u.Apellido
  FROM Plantilla_Dep p
  JOIN Departamentos d ON p.Departamento_ID = d.Departamento_ID
  JOIN Usuarios u ON p.Usuario_ID = u.Codigo
  WHERE p.Notificacion_Vista = 0
  ORDER BY fecha DESC
  LIMIT 10";

  $result = mysqli_query($conn, $query);

  while ($row = mysqli_fetch_assoc($result)) {
    $notificaciones[] = $row;
  }

  mysqli_close($conn);
}
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
          <div id="hora-dinamica" class="hora"></div>
          <div id="fecha-dinamica" class="fecha"></div>
        </div>
        <button class="marcar-leido">Marcar como leído</button>
      </div>
      <?php if ($rol_id == 2) : // Solo mostrar para Secretaría Administrativa 
      ?>
        <?php foreach ($notificaciones as $notificacion) : ?>
          <div class="contenedor-notificacion" data-id="<?php echo $notificacion['id']; ?>" data-tipo="<?php echo $notificacion['tipo']; ?>">
            <div class="imagen">
              <div class="circulo"></div>
            </div>
            <div class="info-notificacion">
              <div class="usuario"><?php echo $notificacion['Departamentos']; ?></div>
              <div class="descripcion">
                <?php
                if ($notificacion['tipo'] == 'justificacion') {
                  echo $notificacion['Nombre'] . ' ' . $notificacion['Apellido'] . ' ha enviado una justificación';
                } else {
                  echo $notificacion['Nombre'] . ' ' . $notificacion['Apellido'] . ' ha subido su Base de Datos';
                }
                ?>
              </div>
              <div class="fecha-hora">
                <?php echo date('d/m/Y H:i:s', strtotime($notificacion['fecha'])); ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($notificaciones)) : ?>
          <div class="contenedor-notificacion" data-id="<?php echo $notificacion['id']; ?>" data-tipo="<?php echo $notificacion['tipo']; ?>">
            <div class="info-notificacion">
              <div class="descripcion">No hay nuevas notificaciones</div>
            </div>
          </div>
        <?php endif; ?>
      <?php else : ?>
        <div class="contenedor-notificacion" data-id="<?php echo $notificacion['id']; ?>" data-tipo="<?php echo $notificacion['tipo']; ?>">
          <div class="info-notificacion">
            <div class="descripcion">No tienes notificaciones</div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- actualizar fecha y hora en tiempo real -->
  <script>
    function actualizarFechaHora() {
      const ahora = new Date();

      // Actualizar hora
      const hora = ahora.getHours().toString().padStart(2, '0');
      const minutos = ahora.getMinutes().toString().padStart(2, '0');
      const segundos = ahora.getSeconds().toString().padStart(2, '0');
      document.getElementById('hora-dinamica').textContent = `${hora}:${minutos}:${segundos}`;

      // Actualizar fecha
      const opciones = {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      };
      let fechaFormateada = ahora.toLocaleDateString('es-ES', opciones);

      // Agregar "de" entre el mes y el año
      fechaFormateada = fechaFormateada.replace(/(\d+)\s+de?\s*(\w+)\s+de?\s*(\d+)/, '$1 de $2 de $3');

      document.getElementById('fecha-dinamica').textContent = fechaFormateada;
    }

    // Actualizar cada segundo
    setInterval(actualizarFechaHora, 1000);

    // Llamar una vez al cargar la página
    actualizarFechaHora();
  </script>


  <script src="./JS/header.js"></script>
  <script src="./JS/barra-notificaciones.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
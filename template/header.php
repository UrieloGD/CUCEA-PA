<?php
include('./config/sesionIniciada.php');
include './config/db.php';

date_default_timezone_set('America/Mexico_City');
if ($rol_id == 1 || $rol_id == 2 || $rol_id == 3) { // Mostrar notificaciones para los tres roles
  $notificaciones = [];
  $codigo_usuario = $_SESSION['Codigo'];

  if ($rol_id == 2) { // Secretaría administrativa
    $query = "SELECT 'justificacion' AS tipo, j.ID_Justificacion AS id, j.Fecha_Justificacion AS fecha, 
                     d.Departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                     j.Notificacion_Vista AS vista, 
                     u.Codigo AS Emisor_ID,
                     NULL AS Mensaje
            FROM Justificaciones j
            JOIN Departamentos d ON j.Departamento_ID = d.Departamento_ID
            JOIN Usuarios u ON j.Codigo_Usuario = u.Codigo
            WHERE j.Justificacion_Enviada = 1
            
            UNION ALL
            
            SELECT 'plantilla' AS tipo, p.ID_Archivo_Dep AS id, p.Fecha_Subida_Dep AS fecha, d.Departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                   p.Notificacion_Vista AS vista, u.Codigo AS Emisor_ID,
                   NULL AS Mensaje
            FROM Plantilla_Dep p
            JOIN Departamentos d ON p.Departamento_ID = d.Departamento_ID
            JOIN Usuarios u ON p.Usuario_ID = u.Codigo
            
            UNION ALL
            
            SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, '' AS Departamentos, 
                   e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Vista AS vista, n.Emisor_ID, n.Mensaje
            FROM Notificaciones n
            LEFT JOIN Usuarios e ON n.Emisor_ID = e.Codigo
            WHERE n.Usuario_ID = $codigo_usuario
            
            ORDER BY fecha DESC
            LIMIT 10";
  } else if ($rol_id == 1) { // Jefe de departamento
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, n.Mensaje, n.Vista AS vista,
                  e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Emisor_ID
              FROM Notificaciones n
              LEFT JOIN Usuarios e ON n.Emisor_ID = e.Codigo
              WHERE n.Usuario_ID = " . $_SESSION['Codigo'] . "
              ORDER BY n.Fecha DESC
              LIMIT 10";
  } else if ($rol_id == 3) { // Coordinacion de Personal
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, n.Mensaje, n.Vista AS vista,
                  e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Emisor_ID
              FROM Notificaciones n
              LEFT JOIN Usuarios e ON n.Emisor_ID = e.Codigo
              WHERE n.Usuario_ID = " . $_SESSION['Codigo'] . "
              ORDER BY n.Fecha DESC
              LIMIT 10";
  }

  $result = mysqli_query($conexion, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $notificaciones[] = $row;
  }
  mysqli_close($conexion);
}
?>

<head>
  <link rel="icon" href="./Img/Icons/iconos-header/pestaña.png" type="image/png">
</head>

<link rel="stylesheet" href="./CSS/notificaciones.css" />
<link rel="stylesheet" href="./CSS/header.css"> <!-- Esto regresa a su lugar -->
<div class="container">
  <div class="header">
    <div class="header-content"> <!-- Contenedor para alinear contenidos del header creo? -->

      <!-- Aqui iba el menu hamburguesa -->

      <div class="titulo">
        <h3>Programación Académica</h3>
      </div>
      <div class="rol">
        <h3><?php echo $nombre_rol; ?></h3>
      </div>
      <li class="icono-notificaciones">
        <a href="javascript:void(0);" id="notification-icon" onclick="toggleNav()">
          <img src="./Img/Icons/iconos-header/Notificacion.png" alt="">
          <span id="notification-badge" class="notification-badge"></span>
        </a>
      </li>
    </div>

    <!-- Notificaciones -->
    <div id="mySidebar" class="sidebar">
      <div class="contenedor-fecha-hora">
        <div class="fecha-hora-info">
          <div id="hora-dinamica" class="hora"></div>
          <div id="fecha-dinamica" class="fecha"></div>
        </div>
        <button class="marcar-leido">Marcar como leído</button>
      </div>
      <?php if ($rol_id == 1 || $rol_id == 2 || $rol_id == 3) : // Mostrar para los tres roles
      ?>
        <?php if (!empty($notificaciones)) : ?>
          <?php foreach ($notificaciones as $notificacion) : ?>
            <div class="contenedor-notificacion <?php echo $notificacion['vista'] ? 'vista' : ''; ?>" data-id="<?php echo $notificacion['id']; ?>" data-tipo="<?php echo $notificacion['tipo']; ?>">
              <div class="imagen">
                <?php if (isset($notificacion['Nombre']) && isset($notificacion['Apellido']) && isset($notificacion['IconoColor'])) : ?>
                  <div class="circulo-notificaciones" style="background-color: <?php echo $notificacion['IconoColor']; ?>">
                    <?php
                    $nombreInicial = strtoupper(substr($notificacion['Nombre'], 0, 1));
                    $apellidoInicial = strtoupper(substr($notificacion['Apellido'], 0, 1));
                    echo $nombreInicial . $apellidoInicial;
                    ?>
                  </div>
                <?php else : ?>
                  <div class="circulo-notificaciones" style="background-color: #ccc;">
                    <span>SA</span>
                  </div>
                <?php endif; ?>
              </div>
              <div class="info-notificacion">
                <?php if ($rol_id == 2) : ?>
                  <div class="usuario"><?php echo $notificacion['Departamentos'] ?? 'Secretaría Administrativa'; ?></div>
                  <div class="descripcion">
                    <?php if ($rol_id == 2) : ?>
                      <div class="usuario"><?php echo $notificacion['Departamentos'] ?? 'Secretaría Administrativa'; ?></div>
                      <div class="descripcion">
                        <?php
                        if ($notificacion['tipo'] == 'justificacion') {
                          echo ($notificacion['Nombre'] ?? 'Usuario') . ' ' . ($notificacion['Apellido'] ?? '') . ' ha enviado una justificación';
                        } elseif ($notificacion['tipo'] == 'plantilla') {
                          echo ($notificacion['Nombre'] ?? 'Usuario') . ' ' . ($notificacion['Apellido'] ?? '') . ' ha subido su Base de Datos';
                        } elseif (!empty($notificacion['Mensaje'])) {
                          echo $notificacion['Mensaje'];
                        } else {
                          echo 'Nueva notificación';
                        }
                        ?>
                      </div>
                    <?php else : ?>
                      <div class="descripcion"><?php echo $notificacion['Mensaje'] ?? 'Nueva notificación'; ?></div>
                    <?php endif; ?>
                  </div>
                <?php else : ?>
                  <div class="descripcion"><?php echo $notificacion['Mensaje'] ?? 'Nueva notificación'; ?></div>
                <?php endif; ?>
                <div class="fecha-hora">
                  <?php echo date('d/m/Y H:i:s', strtotime($notificacion['fecha'])); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div class="mensaje-sin-notificaciones">
            <div class="info-notificacion">
              <div class="descripcion">No hay nuevas notificaciones</div>
            </div>
          </div>
    </div>
  <?php endif; ?>
<?php else : ?>
  <div class="mensaje-sin-notificaciones">
    <div class="info-notificacion">
      <div class="descripcion">No tienes notificaciones</div>
    </div>
  </div>
  </div>
<?php endif; ?>
</div>
</div>


<script src="./JS/header/header.js"></script>
<script src="./JS/notificaciones/barra-notificaciones.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

<!-- Inicializar budget para notificaciones -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    if (typeof actualizarBadgeNotificaciones === 'function') {
      actualizarBadgeNotificaciones();
    } else {
      console.error('La función actualizarBadgeNotificaciones no está definida');
    }
  });
</script>
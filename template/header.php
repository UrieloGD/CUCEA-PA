<?php
include('./config/sesiones.php');

date_default_timezone_set('America/Mexico_City');
if ($rol_id == 0 || $rol_id == 1 || $rol_id == 2 || $rol_id == 3 || $rol_id == 4) { // Secretaría Administrativa y Administrador

  $notificaciones = [];
  $codigo_usuario = $_SESSION['Codigo'];

  if ($rol_id == 0 || $rol_id == 2) { // Secretaría Administrativa y Administrador
    $query = "SELECT 'justificacion' AS tipo, j.ID_Justificacion AS id, j.Fecha_Justificacion AS fecha, 
                     d.departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                     j.Notificacion_Vista AS vista, 
                     u.Codigo AS Emisor_ID,
                     NULL AS Mensaje
            FROM justificaciones j
            JOIN departamentos d ON j.Departamento_ID = d.Departamento_ID
            JOIN usuarios u ON j.Codigo_Usuario = u.Codigo
            WHERE j.Justificacion_Enviada = 1
            
            UNION ALL
            
            SELECT 'plantilla' AS tipo, p.ID_Archivo_Dep AS id, p.Fecha_Subida_Dep AS fecha, d.departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                   p.Notificacion_Vista AS vista, u.Codigo AS Emisor_ID,
                   NULL AS Mensaje
            FROM plantilla_dep p
            JOIN departamentos d ON p.Departamento_ID = d.Departamento_ID
            JOIN usuarios u ON p.Usuario_ID = u.Codigo
            
            UNION ALL
            
            SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, '' AS departamentos, 
                   e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Vista AS vista, n.Emisor_ID, n.Mensaje
            FROM notificaciones n
            LEFT JOIN usuarios e ON n.Emisor_ID = e.Codigo
            WHERE n.Usuario_ID = $codigo_usuario
            
            ORDER BY fecha DESC
            LIMIT 10";
  } else if ($rol_id == 1 || $rol_id == 4) { // Jefe de departamento
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, n.Mensaje, n.Vista AS vista,
                  e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Emisor_ID
              FROM notificaciones n
              LEFT JOIN usuarios e ON n.Emisor_ID = e.Codigo
              WHERE n.Usuario_ID = " . $_SESSION['Codigo'] . "
              ORDER BY n.Fecha DESC
              LIMIT 10";
  } else if ($rol_id == 3) { // Coordinacion de Personal
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, n.Mensaje, n.Vista AS vista,
                  e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Emisor_ID
              FROM notificaciones n
              LEFT JOIN usuarios e ON n.Emisor_ID = e.Codigo
              WHERE n.Usuario_ID = " . $_SESSION['Codigo'] . "
              ORDER BY n.Fecha DESC
              LIMIT 10";
  }

  $result = mysqli_query($conexion, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $notificaciones[] = $row;
  }

  $notificaciones_agrupadas = [];
  foreach ($notificaciones as $notificacion) {
      $fecha = date('Y-m-d', strtotime($notificacion['fecha']));
      $notificaciones_agrupadas[$fecha][] = $notificacion;
  }
}
?>

<head>
  <link rel="icon" href="./Img/Icons/iconos-header/pestaña.png" type="image/png">
</head>

<link rel="stylesheet" href="./CSS/notificaciones.css?v=<?php echo filemtime('./CSS/notificaciones.css'); ?>">
<link rel="stylesheet" href="./CSS/header.css?v=<?php echo filemtime('./CSS/header.css'); ?>">
<div class="container">
  <div class="header">
    <div class="header-content"> <!-- Contenedor para alinear contenidos del header creo? -->

      <!-- Aqui iba el menu hamburguesa -->

      <div class="titulo">
        <h3>Programación Académica</h3>
      </div>
      <div class="rol">
        <h3><?php echo $nombre_rol; ?></h3>
        <li class="icono-notificaciones">
          <a href="javascript:void(0);" id="notification-icon" onclick="toggleNav()">
            <img src="./Img/Icons/iconos-header/Notificacion.png" alt="">
            <span id="notification-badge" class="notification-badge"></span>
          </a>
        </li>
      </div>
    </div>

    <!-- Notificaciones -->
    <div class="sidebar" id="mySidebar">
      <div class="contenedor-fecha-hora">
        <div class="fecha-hora-info">
          <div id="hora-dinamica" class="hora"></div>
          <div id="fecha-dinamica" class="fecha"></div>
        </div>
        <button class="marcar-leido">Marcar como leído</button>
      </div>

  <?php if ($rol_id == 0 || $rol_id == 1 || $rol_id == 2 || $rol_id == 3 || $rol_id == 4) : ?>
    <?php if (!empty($notificaciones_agrupadas)) : ?>
      <?php foreach ($notificaciones_agrupadas as $fecha => $grupo) : ?>
        <div class="grupo-fecha">
          <div class="fecha-encabezado">
            <?= date('d \d\e F', strtotime($fecha)) ?>
          </div>
          <?php foreach ($grupo as $notificacion) : ?>
            <div class="contenedor-notificacion <?= $notificacion['vista'] ? 'vista' : '' ?>" 
                 data-id="<?= $notificacion['id'] ?>" 
                 data-tipo="<?= $notificacion['tipo'] ?>">
              <div class="boton-descartar" onclick="descartarNotificacion(event, <?= $notificacion['id'] ?>, '<?= $notificacion['tipo'] ?>')">
                ×
              </div>
              <div class="imagen">
                <?php if (isset($notificacion['Nombre']) && isset($notificacion['Apellido']) && isset($notificacion['IconoColor'])) : ?>
                  <div class="circulo-notificaciones" style="background-color: <?= $notificacion['IconoColor'] ?>">
                    <?= strtoupper(substr($notificacion['Nombre'], 0, 1)) . strtoupper(substr($notificacion['Apellido'], 0, 1)) ?>
                  </div>
                <?php else : ?>
                  <div class="circulo-notificaciones" style="background-color: #ccc;">SA</div>
                <?php endif; ?>
              </div>
              <div class="info-notificacion">
                <?php if ($rol_id == 2 || $rol_id == 0) : ?>
                  <div class="usuario"><?= $notificacion['departamentos'] ?? 'Secretaría Administrativa' ?></div>
                  <div class="descripcion">
                    <?php
                    if ($notificacion['tipo'] == 'justificacion') {
                      echo ($notificacion['Nombre'] ?? 'Usuario') . ' ' . ($notificacion['Apellido'] ?? '') . ' ha enviado una justificación';
                    } elseif ($notificacion['tipo'] == 'plantilla') {
                      echo ($notificacion['Nombre'] ?? 'Usuario') . ' ' . ($notificacion['Apellido'] ?? '') . ' ha subido su Base de Datos';
                    }elseif (($notificacion['tipo'] == 'modificacion_bd')) {
                      echo "El administrador " . ($notificacion['Nombre'] ?? '') . " modificó su base de datos";
                    }else {
                      echo $notificacion['Mensaje'] ?? 'Nueva notificación';
                    }
                    ?>
                    "El administrador " . ($notificacion['Nombre'] ?? '') . " modificó su base de datos";
                  </div>
                <?php else : ?>
                  <div class="descripcion"><?= $notificacion['Mensaje'] ?? 'Nueva notificación' ?></div>
                <?php endif; ?>
                <div class="fecha-hora">
                  <?= date('H:i', strtotime($notificacion['fecha'])) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="mensaje-sin-notificaciones">
        <div class="info-notificacion">
          <div class="descripcion">No hay nuevas notificaciones</div>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
</div>


<script src="./JS/header/header.js?v=<?php echo filemtime('./JS/header/header.js'); ?>"></script>
<script src="./JS/notificaciones/barra-notificaciones.js?v=<?php echo filemtime('./JS/notificaciones/barra-notificaciones.js'); ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- fa fa icons para firefox y safari -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/67e2f7a9f95a2519093a71d6/1in79erpc';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
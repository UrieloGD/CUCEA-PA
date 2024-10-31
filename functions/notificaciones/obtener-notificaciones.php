<?
session_start();
include './../../config/db.php';

$rol_id = $_SESSION['Rol_ID'];
$codigo_usuario = $_SESSION['Codigo'];

if ($rol_id == 1) { // Jefe de departamento
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, n.Mensaje, n.Vista AS vista,
              e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Emisor_ID
          FROM Notificaciones n
          LEFT JOIN Usuarios e ON n.Emisor_ID = e.Codigo
          WHERE n.Usuario_ID = $codigo_usuario
          ORDER BY n.Fecha DESC
          LIMIT 10";
} else if ($rol_id == 2) { // Secretaría administrativa
    $query = "SELECT 'justificacion' AS tipo, j.ID_Justificacion AS id, j.Fecha_Justificacion AS fecha, 
                   d.Departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                   j.Notificacion_Vista AS vista, u.Codigo AS Emisor_ID
            FROM Justificaciones j
            JOIN Departamentos d ON j.Departamento_ID = d.Departamento_ID
            JOIN Usuarios u ON j.Codigo_Usuario = u.Codigo
            WHERE j.Justificacion_Enviada = 1
            UNION ALL
            SELECT 'plantilla' AS tipo, p.ID_Archivo_Dep AS id, p.Fecha_Subida_Dep AS fecha, 
                   d.Departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                   p.Notificacion_Vista AS vista, u.Codigo AS Emisor_ID
            FROM Plantilla_Dep p
            JOIN Departamentos d ON p.Departamento_ID = d.Departamento_ID
            JOIN Usuarios u ON p.Usuario_ID = u.Codigo
            UNION ALL
            SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, 
                   '' AS Departamentos, e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID,
                   n.Vista AS vista, n.Emisor_ID
            FROM Notificaciones n
            LEFT JOIN Usuarios e ON n.Emisor_ID = e.Codigo
            WHERE n.Usuario_ID = $codigo_usuario
            ORDER BY fecha DESC
            LIMIT 10";
}

$result = mysqli_query($conexion, $query);
$notificaciones = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($notificaciones as $notificacion) :
?>
    <div class="contenedor-notificacion <?php echo $notificacion['vista'] ? 'vista' : ''; ?>" data-id="<?php echo $notificacion['id']; ?>" data-tipo="<?php echo $notificacion['tipo']; ?>">
        <div class="imagen">
            <div class="circulo-notificaciones" style="background-color: <?php echo $notificacion['IconoColor']; ?>">
                <?php
                $nombreInicial = strtoupper(substr($notificacion['Nombre'], 0, 1));
                $apellidoInicial = strtoupper(substr($notificacion['Apellido'], 0, 1));
                echo $nombreInicial . $apellidoInicial;
                ?>
            </div>
        </div>
        <div class="info-notificacion">
            <div class="descripcion">
                <?php
                if ($notificacion['tipo'] == 'justificacion') {
                    echo ($notificacion['Nombre'] ?? 'Usuario') . ' ' . ($notificacion['Apellido'] ?? '') . ' ha enviado una justificación';
                } elseif ($notificacion['tipo'] == 'plantilla') {
                    echo ($notificacion['Nombre'] ?? 'Usuario') . ' ' . ($notificacion['Apellido'] ?? '') . ' ha subido su Base de Datos';
                } elseif ($notificacion['tipo'] == 'evento_cancelado') {
                    echo $notificacion['Mensaje'] ?? 'Evento cancelado';
                } else {
                    echo $notificacion['Mensaje'] ?? 'Nueva notificación';
                }
                ?>
            </div>
            <div class="fecha-hora">
                <?php echo date('d/m/Y H:i:s', strtotime($notificacion['fecha'])); ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
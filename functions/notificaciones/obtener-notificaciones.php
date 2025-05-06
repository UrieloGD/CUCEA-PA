<?php
session_start();
include './../../config/db.php';

date_default_timezone_set('America/Mexico_City');

$rol_id = $_SESSION['Rol_ID'];
$codigo_usuario = $_SESSION['Codigo'];

if ($rol_id == 1 || $rol_id == 4) {
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, 
                            n.Mensaje, n.Vista AS vista, 
                            e.Nombre, e.Apellido, e.IconoColor,
                            n.Departamento_ID
              FROM notificaciones n
              LEFT JOIN usuarios e ON n.Emisor_ID = e.Codigo
              WHERE n.Departamento_ID = " . $_SESSION['Departamento_ID'] . "
              AND (n.Tipo = 'modificacion_bd' OR n.Tipo = 'eliminacion_bd' OR n.Tipo = 'restauracion_bd')
              ORDER BY n.Fecha DESC
              LIMIT 10";
} else if ($rol_id == 0 || $rol_id == 2) { // Administrador y Secretaría administrativa
    $query = "SELECT 'justificacion' AS tipo, j.ID_Justificacion AS id, j.Fecha_Justificacion AS fecha, 
                     d.Departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                     j.Notificacion_Vista AS vista, 
                     u.Codigo AS Emisor_ID,
                     NULL AS Mensaje
            FROM justificaciones j
            JOIN departamentos d ON j.Departamento_ID = d.Departamento_ID
            JOIN usuarios u ON j.Codigo_Usuario = u.Codigo
            WHERE j.Justificacion_Enviada = 1
            
            UNION ALL
            
            SELECT 'plantilla' AS tipo, p.ID_Archivo_Dep AS id, p.Fecha_Subida_Dep AS fecha, d.Departamentos, u.Nombre, u.Apellido, u.IconoColor, u.Codigo AS Usuario_ID,
                   p.Notificacion_Vista AS vista, u.Codigo AS Emisor_ID,
                   NULL AS Mensaje
            FROM plantilla_dep p
            JOIN departamentos d ON p.Departamento_ID = d.Departamento_ID
            JOIN usuarios u ON p.Usuario_ID = u.Codigo
            
            UNION ALL
            
            SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, 
                   d.Departamentos, 
                   e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, 
                   n.Vista AS vista, n.Emisor_ID, n.Mensaje
            FROM notificaciones n
            LEFT JOIN usuarios e ON n.Emisor_ID = e.Codigo
            LEFT JOIN departamentos d ON n.Departamento_ID = d.Departamento_ID
            WHERE n.Usuario_ID = $codigo_usuario OR (n.Usuario_ID IS NULL AND n.Departamento_ID IS NOT NULL)
            
            ORDER BY fecha DESC
            LIMIT 10";
} else if ($rol_id == 3) { // Coordinacion de Personal
    $query = "SELECT n.Tipo AS tipo, n.ID AS id, n.Fecha AS fecha, n.Mensaje, n.Vista AS vista,
              e.Nombre, e.Apellido, e.IconoColor, n.Usuario_ID, n.Emisor_ID
          FROM notificaciones n
          LEFT JOIN usuarios e ON n.Emisor_ID = e.Codigo
          WHERE n.Usuario_ID = $codigo_usuario
          ORDER BY n.Fecha DESC
          LIMIT 10";
}

$result = mysqli_query($conexion, $query);
$notificaciones = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($notificaciones as $notificacion) {
?>
    <div class="contenedor-notificacion <?php echo $notificacion['vista'] ? 'vista' : ''; ?>" data-id="<?php echo $notificacion['id']; ?>" data-tipo="<?php echo strtolower($notificacion['tipo']); ?>">
        <div class="imagen">
            <div class="circulo-notificaciones" style="background-color: <?php echo $notificacion['IconoColor'] ?? '#808080'; ?>">
                <?php
                $nombreInicial = !empty($notificacion['Nombre']) ? strtoupper(substr($notificacion['Nombre'], 0, 1)) : 'U';
                $apellidoInicial = !empty($notificacion['Apellido']) ? strtoupper(substr($notificacion['Apellido'], 0, 1)) : '';
                echo $nombreInicial . $apellidoInicial;
                ?>
            </div>
        </div>
        <div class="info-notificacion">
            <div class="descripcion">
                <?php if ($rol_id == 0 || $rol_id == 2) : ?>
                    <div class="usuario"><?php echo $notificacion['Departamentos'] ?? 'Administración'; ?></div>
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
            <div class="fecha-hora">
                <?php echo date('d/m/Y H:i:s', strtotime($notificacion['fecha'])); ?>
            </div>
        </div>
    </div>
<?php
}
?>
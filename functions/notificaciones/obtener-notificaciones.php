<?php
session_start();
include './../../config/db.php';

date_default_timezone_set('America/Mexico_City');

$rol_id = $_SESSION['Rol_ID'];
$codigo_usuario = $_SESSION['Codigo'];
$notificaciones = [];
$notificaciones_agrupadas = [];

// Consultas según el rol del usuario
if ($rol_id == 1 || $rol_id == 4) { // Jefe de departamento
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

if ($result) {
    $notificaciones = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Agrupar notificaciones por fecha
    foreach ($notificaciones as $notificacion) {
        $fecha = date('Y-m-d', strtotime($notificacion['fecha']));
        $notificaciones_agrupadas[$fecha][] = $notificacion;
    }
}
?>

<?php if (!empty($notificaciones_agrupadas)) : ?>
    <?php foreach ($notificaciones_agrupadas as $fecha => $grupo) : ?>
        <div class="grupo-fecha">
            <div class="fecha-encabezado">
                <?= date('d \d\e F', strtotime($fecha)) ?>
            </div>
            <?php foreach ($grupo as $notificacion) : ?>
                <div class="contenedor-notificacion <?= $notificacion['vista'] ? 'vista' : '' ?>"
                    data-id="<?= $notificacion['id'] ?>"
                    data-tipo="<?= strtolower($notificacion['tipo']) ?>">
                    <div class="boton-descartar" onclick="descartarNotificacion(event, <?= $notificacion['id'] ?>, '<?= $notificacion['tipo'] ?>')">
                        ×
                    </div>
                    <div class="imagen">
                        <div class="circulo-notificaciones" style="background-color: <?= $notificacion['IconoColor'] ?? '#808080' ?>">
                            <?php
                            $nombreInicial = !empty($notificacion['Nombre']) ? strtoupper(substr($notificacion['Nombre'], 0, 1)) : 'U';
                            $apellidoInicial = !empty($notificacion['Apellido']) ? strtoupper(substr($notificacion['Apellido'], 0, 1)) : '';
                            echo $nombreInicial . $apellidoInicial;
                            ?>
                        </div>
                    </div>
                    <div class="info-notificacion">
                        <?php if ($rol_id == 0 || $rol_id == 2) : ?>
                            <div class="usuario"><?= $notificacion['Departamentos'] ?? 'Administración' ?></div>
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
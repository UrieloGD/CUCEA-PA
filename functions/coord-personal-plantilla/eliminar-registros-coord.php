<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php'; // Incluimos la función de correo
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
date_default_timezone_set('America/Mexico_City');

$tabla_departamento = "coord_per_prof";
$usuario_admin_id = $_SESSION['Codigo']; // ID del administrador que realiza la acción

// Función para crear notificación para coordinadores
function crearNotificacion($conexion, $tipo, $mensaje, $emisor_id) {
    // Obtener todos los usuarios con rol 3 (Coordinadores)
    $sql_coordinadores = "SELECT Codigo FROM usuarios WHERE rol_id = 3";
    $result_coordinadores = mysqli_query($conexion, $sql_coordinadores);
    
    if (!$result_coordinadores) {
        error_log("Error al obtener coordinadores: " . mysqli_error($conexion));
        return false;
    }
    
    $notificaciones_creadas = 0;
    
    // Crear una notificación para cada coordinador
    while ($coordinador = mysqli_fetch_assoc($result_coordinadores)) {
        $sql = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, emisor_ID) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ssii", $tipo, $mensaje, $coordinador['Codigo'], $emisor_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $notificaciones_creadas++;
        } else {
            error_log("Error al crear notificación para coordinador {$coordinador['Codigo']}: " . mysqli_stmt_error($stmt));
        }
    }
    
    return $notificaciones_creadas > 0;
}

// Función para enviar correo a los coordinadores
function enviarCorreoNotificacion($conexion, $mensaje, $tipo_accion, $registros_afectados, $emisor_id) {
    // Obtener todos los coordinadores
    $sql_coordinadores = "SELECT Codigo, Nombre, Correo FROM usuarios WHERE rol_id = 3";
    $result_coordinadores = mysqli_query($conexion, $sql_coordinadores);
    
    if (!$result_coordinadores) {
        error_log("Error al obtener coordinadores: " . mysqli_error($conexion));
        return false;
    }
    
    // Obtener información del administrador emisor
    $sql_emisor = "SELECT Nombre FROM usuarios WHERE Codigo = ?";
    $stmt_emisor = mysqli_prepare($conexion, $sql_emisor);
    mysqli_stmt_bind_param($stmt_emisor, "i", $emisor_id);
    mysqli_stmt_execute($stmt_emisor);
    $result_emisor = mysqli_stmt_get_result($stmt_emisor);
    $emisor = mysqli_fetch_assoc($result_emisor);
    $nombre_emisor = $emisor ? $emisor['Nombre'] : 'Un administrador';
    
    // Información adicional para el correo
    $fecha_accion = date('d/m/Y H:i');
    $detalles_accion = $tipo_accion === 'truncate' 
        ? "Se han eliminado todos los registros de la base de datos de coordinación." 
        : "Se han marcado como inactivos $registros_afectados registro" . ($registros_afectados > 1 ? "s" : "") . ".";
    
    $correos_enviados = 0;
    
    // Enviar un correo a cada coordinador
    while ($coordinador = mysqli_fetch_assoc($result_coordinadores)) {
        // Asunto y cuerpo del correo
        $asunto = "Alerta de eliminación de datos - Programación Académica";
        $cuerpo = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                .header { text-align: center; padding-bottom: 20px; }
                .header img { width: 300px; }
                .content { padding: 20px; }
                h2 { color: #2c3e50; }
                p { line-height: 1.5; color: #333; }
                .warning { color: #e74c3c; font-weight: bold; }
                .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                </div>
                <div class='content'>
                    <h2>Notificación de eliminación de datos</h2>
                    <p class='warning'>{$mensaje}</p>
                    <p><strong>Acción realizada por:</strong> {$nombre_emisor}</p>
                    <p><strong>Fecha y hora:</strong> {$fecha_accion}</p>
                    <p><strong>Detalles:</strong> {$detalles_accion}</p>
                    <p>Por favor, ingrese al sistema para más información o si necesita restaurar datos.</p>
                </div>
                <div class='footer'>
                    <p>Centro para la Sociedad Digital</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        if (enviarCorreo($coordinador['Correo'], $asunto, $cuerpo)) {
            error_log("Correo enviado exitosamente al coordinador {$coordinador['Nombre']}");
            $correos_enviados++;
        } else {
            error_log("Error al enviar correo al coordinador {$coordinador['Nombre']}");
        }
    }
    
    return $correos_enviados > 0;
}

// Si se solicita truncar toda la tabla
if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
    mysqli_autocommit($conexion, false);

    try {
        $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
        if (!mysqli_query($conexion, $sql_truncate)) {
            throw new Exception("Error al truncar la tabla: " . mysqli_error($conexion));
        }
        
        // Crear notificación de eliminación completa
        $mensaje = "Un administrador ha eliminado toda la base de datos de coordinación";
        crearNotificacion($conexion, "eliminacion_bd", $mensaje, $usuario_admin_id);
        
        // Enviar notificación por correo
        enviarCorreoNotificacion($conexion, $mensaje, 'truncate', 0, $usuario_admin_id);

        mysqli_commit($conexion);
        echo "Tabla truncada correctamente.";
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo $e->getMessage();
    } finally {
        mysqli_close($conexion);
    }
    exit;
}

// Verificar que se hayan enviado IDs
if (!isset($_POST['ids']) || empty($_POST['ids'])) {
    echo "No se proporcionaron IDs para eliminar";
    exit;
}

// Convertir cadena de IDs a array
$ids = explode(',', $_POST['ids']);
$num_registros = count($ids);

// Desactivar autocommit para manejar transacción
mysqli_autocommit($conexion, false);

try {
    // Modificar el estado de Papelera a 'inactivo' para los registros seleccionados
    $stmt = mysqli_prepare($conexion, "UPDATE coord_per_prof SET Papelera = 'inactivo' WHERE ID = ?");
    
    if (!$stmt) {
        throw new Exception("Error preparando la declaración: " . mysqli_error($conexion));
    }

    // Actualizar cada registro por su ID
    foreach ($ids as $id) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al actualizar el registro con ID: " . $id . " - " . mysqli_stmt_error($stmt));
        }
    }

    mysqli_stmt_close($stmt);
    
    // Crear notificación de eliminación de registros
    $mensaje = "Un administrador ha eliminado $num_registros registro" . ($num_registros > 1 ? "s" : "") . " de su base de datos";
    crearNotificacion($conexion, "eliminacion_bd", $mensaje, $usuario_admin_id);
    
    // Enviar notificación por correo
    enviarCorreoNotificacion($conexion, $mensaje, 'eliminacion', $num_registros, $usuario_admin_id);
    
    mysqli_commit($conexion);
    echo "Registros marcados como inactivos correctamente";
} catch (Exception $e) {
    mysqli_rollback($conexion);
    echo $e->getMessage();
} finally {
    mysqli_close($conexion);
}
?>
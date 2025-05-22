<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php'; // Incluimos la función de correo
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
date_default_timezone_set('America/Mexico_City'); // Añadimos zona horaria como en upload-plantilla.php

$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';
$usuario_admin_id = $_SESSION['Codigo']; // ID del administrador que realiza la acción

// Obtener información del departamento
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt_departamento = mysqli_prepare($conexion, $sql_departamento);
mysqli_stmt_bind_param($stmt_departamento, "i", $departamento_id);
mysqli_stmt_execute($stmt_departamento);
$result_departamento = mysqli_stmt_get_result($stmt_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento_nombre = $row_departamento['Departamentos'];

$tabla_departamento = "data_" . $nombre_departamento;

// Función para crear notificación
function crearNotificacion($conexion, $tipo, $mensaje, $departamento_id, $emisor_id)
{
    $sql = "INSERT INTO notificaciones (Tipo, Mensaje, Departamento_ID, Emisor_ID) 
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssii", $tipo, $mensaje, $departamento_id, $emisor_id);

    if (!mysqli_stmt_execute($stmt)) {
        error_log("Error al crear notificación: " . mysqli_stmt_error($stmt));
        return false;
    }

    return true;
}

// Función para enviar correo al jefe de departamento
function enviarCorreoNotificacion($conexion, $departamento_id, $mensaje, $tipo_accion, $registros_afectados, $emisor_id)
{
    // Obtener el correo del jefe de departamento
    $sql_jefe = "SELECT u.Codigo, u.Correo, d.Departamentos 
                 FROM usuarios u 
                 JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID
                 JOIN departamentos d ON ud.Departamento_ID = d.Departamento_ID 
                 WHERE d.Departamento_ID = ? AND u.rol_id = 1";
    $stmt = mysqli_prepare($conexion, $sql_jefe);
    mysqli_stmt_bind_param($stmt, "i", $departamento_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $jefe = mysqli_fetch_assoc($result);

    if ($jefe) {
        // Obtener información del administrador emisor
        $sql_emisor = "SELECT Nombre, Apellido FROM usuarios WHERE Codigo = ?";
        $stmt_emisor = mysqli_prepare($conexion, $sql_emisor);
        mysqli_stmt_bind_param($stmt_emisor, "i", $emisor_id);
        mysqli_stmt_execute($stmt_emisor);
        $result_emisor = mysqli_stmt_get_result($stmt_emisor);
        $emisor = mysqli_fetch_assoc($result_emisor);
        $nombre_emisor = $emisor ? $emisor['Nombre'] . ' ' . $emisor['Apellido'] : 'Un administrador';

        // Insertar notificación en la tabla Notificaciones para el jefe
        $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, emisor_ID) 
                             VALUES ('eliminacion_bd', ?, ?, ?)";
        $stmt_notificacion = mysqli_prepare($conexion, $sql_notificacion);
        mysqli_stmt_bind_param($stmt_notificacion, "sii", $mensaje, $jefe['Codigo'], $emisor_id);
        mysqli_stmt_execute($stmt_notificacion);

        // Información adicional para el correo
        $fecha_accion = date('d/m/Y H:i');
        $detalles_accion = $tipo_accion === 'truncate'
            ? "Se han eliminado todos los registros de la base de datos."
            : "Se han marcado como inactivos $registros_afectados registro" . ($registros_afectados > 1 ? "s" : "") . ".";

        // Enviar correo electrónico
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
                    <p><strong>Departamento:</strong> {$jefe['Departamentos']}</p>
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

        if (enviarCorreo($jefe['Correo'], $asunto, $cuerpo)) {
            error_log("Correo enviado exitosamente al jefe del departamento {$jefe['Departamentos']}");
            return true;
        } else {
            error_log("Error al enviar correo al jefe del departamento {$jefe['Departamentos']}");
            return false;
        }
    } else {
        error_log("No se encontró jefe de departamento para el Departamento_ID: $departamento_id");
        return false;
    }
}

// Verificar si es una solicitud de truncate (borrar toda la base)
if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
    // Iniciar transacción
    mysqli_begin_transaction($conexion);

    try {
        // Truncar la tabla de datos del departamento
        $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
        if (!mysqli_query($conexion, $sql_truncate)) {
            throw new Exception("Error al truncar la tabla: " . mysqli_error($conexion));
        }

        // Eliminar todos los registros de plantilla para este departamento
        $sql_delete_plantilla = "DELETE FROM plantilla_dep WHERE Departamento_ID = ?";
        $stmt_plantilla = mysqli_prepare($conexion, $sql_delete_plantilla);
        mysqli_stmt_bind_param($stmt_plantilla, "i", $departamento_id);
        if (!mysqli_stmt_execute($stmt_plantilla)) {
            throw new Exception("Error al eliminar registros de plantilla: " . mysqli_stmt_error($stmt_plantilla));
        }

        // Crear notificación de eliminación completa
        $mensaje = "Un administrador ha eliminado toda la base de datos del departamento de $departamento_nombre";
        crearNotificacion($conexion, "eliminacion_bd", $mensaje, $departamento_id, $usuario_admin_id);

        // Enviar notificación por correo
        enviarCorreoNotificacion($conexion, $departamento_id, $mensaje, 'truncate', 0, $usuario_admin_id);

        // Confirmar la transacción
        mysqli_commit($conexion);
        echo "Tabla truncada y registros de plantilla eliminados correctamente.";
    } catch (Exception $e) {
        // Revertir en caso de error
        mysqli_rollback($conexion);
        echo $e->getMessage();
        exit;
    }

    mysqli_close($conexion);
    exit;
}

// Si llegamos aquí, es una eliminación de registros específicos
$ids = explode(',', $_POST['ids']);
$num_registros = count($ids);

mysqli_autocommit($conexion, false);

try {
    foreach ($ids as $id) {
        // Cambiamos la consulta DELETE por UPDATE para marcar como inactivo
        $stmt = mysqli_prepare($conexion, "UPDATE `$tabla_departamento` SET PAPELERA = 'INACTIVO' WHERE ID_Plantilla = ? AND Departamento_ID = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $departamento_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al marcar los registros como inactivos: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    }

    // Crear notificación de eliminación de registros
    $mensaje = "Un administrador ha eliminado $num_registros registro" . ($num_registros > 1 ? "s" : "") . " de la base de datos del departamento de $departamento_nombre";
    crearNotificacion($conexion, "eliminacion_bd", $mensaje, $departamento_id, $usuario_admin_id);

    // Enviar notificación por correo
    enviarCorreoNotificacion($conexion, $departamento_id, $mensaje, 'eliminacion', $num_registros, $usuario_admin_id);

    mysqli_commit($conexion);
    echo "Registros marcados como inactivos correctamente.";
} catch (Exception $e) {
    mysqli_rollback($conexion);
    echo $e->getMessage();
    exit;
}

mysqli_close($conexion);
exit;

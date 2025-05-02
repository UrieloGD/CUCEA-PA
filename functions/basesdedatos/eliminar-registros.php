<?php
include './../../config/db.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

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
function crearNotificacion($conexion, $tipo, $mensaje, $departamento_id, $emisor_id) {
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
    
    mysqli_commit($conexion);
    echo "Registros marcados como inactivos correctamente.";
} catch (Exception $e) {
    mysqli_rollback($conexion);
    echo $e->getMessage();
    exit;
}

mysqli_close($conexion);
exit;
?>
<?php
include './../../config/db.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';
$admin_codigo = $_SESSION['Codigo']; // Código del administrador que realiza la acción

$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt_departamento = mysqli_prepare($conexion, $sql_departamento);
mysqli_stmt_bind_param($stmt_departamento, "i", $departamento_id);
mysqli_stmt_execute($stmt_departamento);
$result_departamento = mysqli_stmt_get_result($stmt_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "data_" . $nombre_departamento;

// Obtener información del administrador
$sql_admin = "SELECT Nombre /*, Apellido */ FROM usuarios WHERE Codigo = ?";
$stmt_admin = mysqli_prepare($conexion, $sql_admin);
mysqli_stmt_bind_param($stmt_admin, "i", $admin_codigo);
mysqli_stmt_execute($stmt_admin);
$result_admin = mysqli_stmt_get_result($stmt_admin);
$row_admin = mysqli_fetch_assoc($result_admin);
$nombre_admin = $row_admin['Nombre'] . ' ' . $row_admin['Apellido'];

// Verificar si es una solicitud de truncate
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

        // Crear notificación para el departamento
        $mensaje = "El administrador $nombre_admin ha borrado toda su base de datos";
        $fecha_actual = date('Y-m-d H:i:s');
        $tipo = "eliminacion_bd";
        
        $sql_notificacion = "INSERT INTO notificaciones (Usuario_ID, Emisor_ID, Mensaje, Fecha, Vista, Tipo, Departamento_ID) 
                            VALUES (0, ?, ?, ?, 0, ?, ?)";
        $stmt_notificacion = mysqli_prepare($conexion, $sql_notificacion);
        mysqli_stmt_bind_param($stmt_notificacion, "isssi", $admin_codigo, $mensaje, $fecha_actual, $tipo, $departamento_id);
        
        if (!mysqli_stmt_execute($stmt_notificacion)) {
            throw new Exception("Error al crear notificación: " . mysqli_stmt_error($stmt_notificacion));
        }

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

$ids = explode(',', $_POST['ids']);

mysqli_autocommit($conexion, false);

try {
    foreach ($ids as $id) {
        // Obtener información del registro antes de marcarlo como inactivo
        $stmt_info = mysqli_prepare($conexion, "SELECT * FROM `$tabla_departamento` WHERE ID_Plantilla = ?");
        mysqli_stmt_bind_param($stmt_info, "i", $id);
        mysqli_stmt_execute($stmt_info);
        $result_info = mysqli_stmt_get_result($stmt_info);
        $row_info = mysqli_fetch_assoc($result_info);
        
        // Cambiamos la consulta DELETE por UPDATE para marcar como inactivo
        $stmt = mysqli_prepare($conexion, "UPDATE `$tabla_departamento` SET PAPELERA = 'INACTIVO' WHERE ID_Plantilla = ? AND Departamento_ID = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $departamento_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al marcar los registros como inactivos: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
        
        // Crear notificación por cada registro eliminado
        $mensaje = "El administrador $nombre_admin ha borrado la fila con el ID $id de su base de datos";
        $fecha_actual = date('Y-m-d H:i:s');
        $tipo = "eliminacion_bd";
        
        $sql_notificacion = "INSERT INTO notificaciones (Usuario_ID, Emisor_ID, Mensaje, Fecha, Vista, Tipo, Departamento_ID) 
                            VALUES (0, ?, ?, ?, 0, ?, ?)";
        $stmt_notificacion = mysqli_prepare($conexion, $sql_notificacion);
        mysqli_stmt_bind_param($stmt_notificacion, "isssi", $admin_codigo, $mensaje, $fecha_actual, $tipo, $departamento_id);
        
        if (!mysqli_stmt_execute($stmt_notificacion)) {
            throw new Exception("Error al crear notificación: " . mysqli_stmt_error($stmt_notificacion));
        }
    }
    
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
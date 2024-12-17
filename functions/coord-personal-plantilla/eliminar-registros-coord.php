<?php
include './../../config/db.php';
session_start();

$tabla_departamento = "coord_per_prof";

// Si se solicita truncar toda la tabla
if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
    // Desactivar autocommit para manejar transacción
    mysqli_autocommit($conexion, false);

    try {
        // Truncar la tabla completamente
        $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
        if (!mysqli_query($conexion, $sql_truncate)) {
            throw new Exception("Error al truncar la tabla: " . mysqli_error($conexion));
        }

        // Confirmar la transacción
        mysqli_commit($conexion);
        echo "Tabla truncada correctamente.";
    } catch (Exception $e) {
        // Si hay error, revertir cambios
        mysqli_rollback($conexion);
        echo $e->getMessage();
    } finally {
        // Cerrar conexión
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

// Desactivar autocommit para manejar transacción
mysqli_autocommit($conexion, false);

try {
    // Preparar declaración de eliminación
    $stmt = mysqli_prepare($conexion, "DELETE FROM coord_per_prof WHERE ID = ?");
    
    // Verificar preparación del statement
    if (!$stmt) {
        throw new Exception("Error preparando la declaración: " . mysqli_error($conexion));
    }

    // Eliminar cada registro por su ID
    foreach ($ids as $id) {
        // Vincular parámetro ID
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        // Ejecutar eliminación
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al eliminar el registro con ID: " . $id . " - " . mysqli_stmt_error($stmt));
        }
    }

    // Cerrar statement
    mysqli_stmt_close($stmt);

    // Confirmar transacción
    mysqli_commit($conexion);
    echo "Registros eliminados correctamente";
} catch (Exception $e) {
    // Revertir cambios en caso de error
    mysqli_rollback($conexion);
    echo $e->getMessage();
} finally {
    // Cerrar conexión
    mysqli_close($conexion);
}
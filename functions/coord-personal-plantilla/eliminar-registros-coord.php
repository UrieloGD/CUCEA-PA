<?php
include './../../config/db.php';
session_start();

$tabla_departamento = "coord_per_prof";

// Si se solicita truncar toda la tabla
if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
    mysqli_autocommit($conexion, false);

    try {
        $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
        if (!mysqli_query($conexion, $sql_truncate)) {
            throw new Exception("Error al truncar la tabla: " . mysqli_error($conexion));
        }

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
    mysqli_commit($conexion);
    echo "Registros marcados como inactivos correctamente";
} catch (Exception $e) {
    mysqli_rollback($conexion);
    echo $e->getMessage();
} finally {
    mysqli_close($conexion);
}
?>
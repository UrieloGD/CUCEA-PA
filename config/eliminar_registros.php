<?php
// Conexión a la base de datos
include '../config/db.php';

// Obtener los IDs de los registros a eliminar
$ids = explode(',', $_POST['ids']);

// Iniciar una transacción
mysqli_autocommit($conexion, false);

// Recorrer los IDs y eliminar cada registro
foreach ($ids as $id) {
    $stmt = mysqli_prepare($conexion, "DELETE FROM Data_Plantilla WHERE ID_Plantilla = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (!mysqli_stmt_execute($stmt)) {
        // Si hay un error, revertir la transacción
        mysqli_rollback($conexion);
        echo "Error al eliminar los registros: " . mysqli_stmt_error($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Si no hubo errores, confirmar la transacción
mysqli_commit($conexion);

// Cerrar la conexión
mysqli_close($conexion);

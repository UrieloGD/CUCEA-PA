<?php
// Conexión a la base de datos
include '../config/db.php';
session_start(); // Iniciar la sesión

// Obtener el nombre y el ID del departamento del usuario desde la sesión
$nombre_departamento = $_SESSION['Nombre_Departamento'];
$departamento_id = $_SESSION['Departamento_ID'];

// Obtener los IDs de los registros a eliminar
$ids = explode(',', $_POST['ids']);

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

// Iniciar una transacción
mysqli_autocommit($conexion, false);

// Recorrer los IDs y eliminar cada registro
foreach ($ids as $id) {
    $stmt = mysqli_prepare($conexion, "DELETE FROM `$tabla_departamento` WHERE ID_Plantilla = ? AND Departamento_ID = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $departamento_id);
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
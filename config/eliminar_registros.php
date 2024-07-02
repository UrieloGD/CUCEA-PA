<?php
include '../config/db.php';
session_start(); // Iniciar la sesión

$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "Data_" . $nombre_departamento;

if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
    $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
    if (mysqli_query($conexion, $sql_truncate)) {
        echo "Tabla truncada correctamente.";
    } else {
        echo "Error al truncar la tabla: " . mysqli_error($conexion);
    }
    mysqli_close($conexion);
    exit;
}

$ids = explode(',', $_POST['ids']);

mysqli_autocommit($conexion, false);

foreach ($ids as $id) {
    $stmt = mysqli_prepare($conexion, "DELETE FROM `$tabla_departamento` WHERE ID_Plantilla = ? AND Departamento_ID = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $departamento_id);
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_rollback($conexion);
        echo "Error al eliminar los registros: " . mysqli_stmt_error($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);
}

mysqli_commit($conexion);
mysqli_close($conexion);

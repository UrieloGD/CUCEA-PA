<?php
include './../../config/db.php';
session_start();

$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
//$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "Coord_Per_Prof";

if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
    mysqli_autocommit($conexion, false);

    // Truncar la tabla de datos
    $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
    if (!mysqli_query($conexion, $sql_truncate)) {
        mysqli_rollback($conexion);
        echo "Error al truncar la tabla: " . mysqli_error($conexion);
        exit;
    }

    // Eliminar el archivo de plantilla_dep correspondiente al departamento
    $sql_delete_plantilla = "DELETE FROM Coord_Per_Prof WHERE Departamento_ID = ?";
    $stmt = mysqli_prepare($conexion, $sql_delete_plantilla);
    mysqli_stmt_bind_param($stmt, "i", $departamento_id);
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_rollback($conexion);
        echo "Error al eliminar el archivo de plantilla: " . mysqli_stmt_error($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);

    if (mysqli_commit($conexion)) {
        echo "Tabla truncada y archivo de plantilla eliminado correctamente.";
    } else {
        mysqli_rollback($conexion);
        echo "Error al realizar las operaciones: " . mysqli_error($conexion);
    }

    mysqli_close($conexion);
    exit;
}

$ids = explode(',', $_POST['ids']);

mysqli_autocommit($conexion, false);

foreach ($ids as $id) {
    $stmt = mysqli_prepare($conexion, "DELETE FROM Coord_Per_Prof WHERE ID = ? AND Departamento_ID = ?");
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

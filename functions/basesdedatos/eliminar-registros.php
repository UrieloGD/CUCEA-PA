<?php
include './../../config/db.php';
session_start();

$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt_departamento = mysqli_prepare($conexion, $sql_departamento);
mysqli_stmt_bind_param($stmt_departamento, "i", $departamento_id);
mysqli_stmt_execute($stmt_departamento);
$result_departamento = mysqli_stmt_get_result($stmt_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "data_" . $nombre_departamento;

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

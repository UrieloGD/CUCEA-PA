<?php
include './../../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Validar que se recibió el Departamento_ID
$departamento_id = isset($_POST['departamento_id']) ? (int)$_POST['departamento_id'] : 0;

if ($departamento_id <= 0) {
    echo json_encode(['error' => 'ID de departamento inválido']);
    exit;
}

// Obtener el nombre del departamento
$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt = mysqli_prepare($conexion, $sql_departamento);
mysqli_stmt_bind_param($stmt, "i", $departamento_id);
mysqli_stmt_execute($stmt);
$result_departamento = mysqli_stmt_get_result($stmt);

if (!$result_departamento || mysqli_num_rows($result_departamento) == 0) {
    echo json_encode(['error' => 'Departamento no encontrado']);
    exit;
}

$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$tabla_departamento = "data_" . $nombre_departamento;

// Iniciar transacción
mysqli_begin_transaction($conexion);

try {
    if (isset($_POST['truncate']) && $_POST['truncate'] == '1') {
        // Truncar la tabla
        $sql_truncate = "TRUNCATE TABLE `$tabla_departamento`";
        if (!mysqli_query($conexion, $sql_truncate)) {
            throw new Exception("Error al truncar la tabla: " . mysqli_error($conexion));
        }

        // Eliminar registros de plantilla_dep
        $sql_delete_plantilla = "DELETE FROM plantilla_dep WHERE Departamento_ID = ?";
        $stmt = mysqli_prepare($conexion, $sql_delete_plantilla);
        mysqli_stmt_bind_param($stmt, "i", $departamento_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al eliminar plantilla: " . mysqli_stmt_error($stmt));
        }
    } else {
        // Eliminar registros específicos
        if (!isset($_POST['ids']) || empty($_POST['ids'])) {
            throw new Exception("No se especificaron IDs para eliminar");
        }

        $ids = explode(',', $_POST['ids']);
        $ids = array_map('intval', $ids); // Sanitizar los IDs

        // Preparar la consulta DELETE
        $sql_delete = "DELETE FROM `$tabla_departamento` WHERE ID_Plantilla = ? AND Departamento_ID = ?";
        $stmt = mysqli_prepare($conexion, $sql_delete);

        foreach ($ids as $id) {
            mysqli_stmt_bind_param($stmt, "ii", $id, $departamento_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al eliminar registro $id: " . mysqli_stmt_error($stmt));
            }
        }
    }

    // Confirmar la transacción
    mysqli_commit($conexion);
    echo json_encode(['success' => true, 'message' => 'Operación completada con éxito']);
} catch (Exception $e) {
    mysqli_rollback($conexion);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conexion);
}

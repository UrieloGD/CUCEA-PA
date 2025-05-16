<?php
include './../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $tipo = $_POST['tipo'];

    if ($tipo == 'justificacion') {
        $sql = "UPDATE justificaciones SET Notificacion_Vista = 1 WHERE ID_Justificacion = ?";
    } elseif ($tipo == 'plantilla') {
        $sql = "UPDATE plantilla_dep SET Notificacion_Vista = 1 WHERE ID_Archivo_Dep = ?";
    } elseif (
        $tipo == 'fecha_limite' || $tipo == 'evento' || $tipo == 'evento_actualizado' ||
        $tipo == 'evento_removido' || $tipo == 'evento_cancelado' ||
        $tipo == 'modificacion_bd' || $tipo == 'modificación_bd' ||
        $tipo == 'eliminacion_bd' || $tipo == 'restauracion_bd'
    ) {
        $sql = "UPDATE notificaciones SET Vista = 1 WHERE ID = ?";
    } else {
        echo json_encode(['success' => false, 'error' => 'Tipo de notificación no válido']);
        exit;
    }

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}

$conexion->close();

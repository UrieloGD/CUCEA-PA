<?php
include './../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $tipo = $_POST['tipo'];

    if ($tipo == 'justificacion') {
        $sql = "UPDATE Justificaciones SET Notificacion_Vista = 1 WHERE ID_Justificacion = ?";
    } elseif ($tipo == 'plantilla') {
        $sql = "UPDATE Plantilla_Dep SET Notificacion_Vista = 1 WHERE ID_Archivo_Dep = ?";
    } elseif ($tipo == 'fecha_limite') {
        $sql = "UPDATE Notificaciones SET Vista = 1 WHERE ID = ?";
    } elseif ($tipo == 'evento') {
        $sql = "UPDATE Notificaciones SET Vista = 1 WHERE ID = ?";
    } elseif ($tipo == 'evento_actualizado') {
        $sql = "UPDATE Notificaciones SET Vista = 1 WHERE ID = ?";
    } elseif ($tipo == 'evento_removido') {
        $sql = "UPDATE Notificaciones SET Vista = 1 WHERE ID = ?";
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

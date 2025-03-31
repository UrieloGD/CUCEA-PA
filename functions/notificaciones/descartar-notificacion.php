<?php
include('./../../config/db.php');

$id = $_POST['id'];
$tipo = $_POST['tipo'];

try {
    if ($tipo === 'justificacion') {
        $sql = "UPDATE justificaciones SET Notificacion_Vista = 2 WHERE ID_Justificacion = ?";
    } elseif ($tipo === 'plantilla') {
        $sql = "UPDATE plantilla_dep SET Notificacion_Vista = 2 WHERE ID_Archivo_Dep = ?";
    } else {
        $sql = "UPDATE notificaciones SET Vista = 2 WHERE ID = ?";
    }
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
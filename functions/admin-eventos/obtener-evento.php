<?php
header('Content-Type: application/json');
include './../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM eventos_admin WHERE ID_Evento = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($evento = $result->fetch_assoc()) {
        echo json_encode($evento);
    } else {
        echo json_encode(['error' => 'Evento no encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID de evento no proporcionado']);
}

$conexion->close();
?>
<?php
include './../../config/db.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM Eventos_Admin WHERE ID_Evento = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $evento = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'evento' => $evento]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró el evento']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se proporcionó ID de evento']);
}

$conexion->close();
?>
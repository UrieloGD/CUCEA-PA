<?php
include './config/db.php';

if (isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);
    $sql = "SELECT Nombre_Evento, Etiqueta, Descripcion_Evento, 
                   DATE_FORMAT(Fecha_Inicio, '%d/%m/%y') AS Fecha_Evento, 
                   TIME_FORMAT(Hora_Inicio, '%H:%i') AS Hora_Inicio 
            FROM Eventos_Admin 
            WHERE ID_Evento = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Evento no encontrado"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "ID de evento no proporcionado"]);
}

$conexion->close();

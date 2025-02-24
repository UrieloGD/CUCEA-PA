<?php
include '../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['FechIn'];
    $fecha_fin = $_POST['FechFi'];
    $hora_inicio = $_POST['HorIn'];
    $hora_fin = $_POST['HorFi'];
    $etiqueta = $_POST['etiqueta'];
    $participantes = isset($_POST['participantes']) ? implode(',', $_POST['participantes']) : '';
    $notificaciones = $_POST['notificacion'];
    $hora_noti = $_POST['HorNotif'];

    $sql = "UPDATE eventos_admin SET 
                Nombre_Evento = ?, 
                Descripcion_Evento = ?, 
                Fecha_Inicio = ?, 
                Fecha_Fin = ?, 
                Hora_Inicio = ?, 
                Hora_Fin = ?, 
                Etiqueta = ?, 
                Participantes = ? 
            WHERE ID_Evento = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $descripcion, $fecha_inicio, $fecha_fin, 
    $hora_inicio, $hora_fin, $etiqueta, $participantes, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Evento actualizado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id_evento']) ? $_POST['id_evento'] : null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fechIn = $_POST['FechIn'];
    $fechFi = $_POST['FechFi'];
    $horIn = $_POST['HorIn'];
    $horFi = $_POST['HorFi'];
    $etiqueta = $_POST['etiqueta'];
    $participantes = isset($_POST['participantes']) ? implode(",", $_POST['participantes']) : '';
    $notif = $_POST['notificacion'];
    $horNotif = $_POST['HorNotif'];

    // Cálculo de la notificación
    $fechaInicio = new DateTime($fechIn . ' ' . $horIn);
    $fechaNotificacion = null;

    // Manejo específico para participantes
    $participantes = '';
    if (isset($_POST['participantes']) && is_array($_POST['participantes'])) {
        // Filtrar valores vacíos y unir con comas
        $participantesArray = array_filter($_POST['participantes'], function($value) {
            return !empty($value);
        });
        $participantes = implode(",", $participantesArray);
    }
    
    // Depuración
    error_log("Participantes recibidos: " . print_r($_POST['participantes'], true));
    error_log("Participantes procesados: " . $participantes);

    switch ($notif) {
        case '1 hora antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-1 hour');
            break;
        case '2 horas antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-2 hours');
            break;
        case '1 día antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-1 day');
            break;
        case '1 semana antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-1 week');
            break;
        case 'Sin notificación':
            $fechaNotificacion = null;
            break;
    }

    if ($id) {
        // Actualizar evento existente
        $sql = "UPDATE eventos_admin SET 
                Nombre_Evento = ?, 
                Descripcion_Evento = ?, 
                Fecha_Inicio = ?, 
                Fecha_Fin = ?, 
                Hora_Inicio = ?, 
                Hora_Fin = ?, 
                Etiqueta = ?, 
                Participantes = ?, 
                Notificaciones = ?, 
                Hora_Noti = ?
                WHERE ID_Evento = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssssssi", $nombre, $descripcion, $fechIn, $fechFi, $horIn, $horFi, $etiqueta, $participantes, $notif, $horNotif, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Evento actualizado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el evento: ' . $stmt->error]);
        }
    } else {
        $sql = "INSERT INTO eventos_admin (Nombre_Evento, Descripcion_Evento, Fecha_Inicio, Fecha_Fin, 
                Hora_Inicio, Hora_Fin, Etiqueta, Participantes, Notificaciones, Hora_Noti)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssssss", $nombre, $descripcion, $fechIn, $fechFi, $horIn, 
                         $horFi, $etiqueta, $participantes, $notif, $horNotif);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Nuevo evento creado con éxito',
                'participantes' => $participantes // Para depuración
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Error al crear el evento: ' . $stmt->error
            ]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no válido']);
}

$conexion->close();
?>
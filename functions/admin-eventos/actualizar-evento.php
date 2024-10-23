<?php
// Verifica si el método de solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura los datos enviados desde el formulario a través del método POST
    $id = $_POST['id'];  // ID del evento a actualizar
    $nombre = $_POST['nombre'];  // Nombre del evento
    $descripcion = $_POST['descripcion'];  // Descripción del evento
    $fecha_inicio = $_POST['FechIn'];  // Fecha de inicio del evento
    $fecha_fin = $_POST['FechFi'];  // Fecha de finalización del evento
    $hora_inicio = $_POST['HorIn'];  // Hora de inicio del evento
    $hora_fin = $_POST['HorFi'];  // Hora de finalización del evento
    $etiqueta = $_POST['etiqueta'];  // Etiqueta asociada al evento
    // Convierte el array de participantes en una cadena separada por comas, o deja vacío si no hay participantes
    $participantes = isset($_POST['participantes']) ? implode(',', $_POST['participantes']) : '';  
    $notificaciones = $_POST['notificacion'];  // Preferencia de notificaciones para el evento
    $hora_noti = $_POST['HorNotif'];  // Hora de notificación para el evento

    // Consulta SQL para actualizar los datos del evento
    $sql = "UPDATE Eventos_Admin SET 
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

    // Prepara la consulta para evitar inyección SQL
    $stmt = $conexion->prepare($sql);
    // Asigna los valores a los marcadores de posición de la consulta SQL
    // "ssssssssssi" significa que los primeros 10 son strings y el último es un entero (id del evento)
    $stmt->bind_param("ssssssssssi", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $etiqueta, $participantes, $notificaciones, $hora_noti, $id);

    // Ejecuta la consulta SQL
    if ($stmt->execute()) {
        // Si la ejecución es exitosa, envía una respuesta en formato JSON indicando éxito
        echo json_encode(['status' => 'success', 'message' => 'Evento actualizado correctamente']);
    } else {
        // Si hay un error en la ejecución, envía una respuesta en JSON con el mensaje de error
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
} else {
    // Si el método de solicitud no es POST, envía un mensaje de error
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>

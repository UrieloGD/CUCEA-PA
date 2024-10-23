<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';  // Para el envío de correos

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
        $participantesArray = array_filter($_POST['participantes'], function($value) {
            return !empty($value);
        });
        $participantes = implode(",", $participantesArray);
    }
    
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
            $evento_id = $stmt->insert_id;

            // Lógica para insertar notificación
            $mensaje = "Nuevo evento creado: $nombre";
            $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                 VALUES ('evento', ?, ?, ?)";
            $stmt_notif = $conexion->prepare($sql_notificacion);
            foreach ($participantesArray as $participante) {
                $stmt_notif->bind_param("sii", $mensaje, $participante, $_SESSION['Codigo']);
                $stmt_notif->execute();
            }

            // Enviar correos
            $asunto = "Nuevo evento: $nombre";
            $cuerpo = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                        .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                        .header { text-align: center; padding-bottom: 20px; }
                        .header img { width: 300px; }
                        .content { padding: 20px; }
                        h2 { color: #2c3e50; text-align: center; }
                        p { line-height: 1.5; color: #333; }
                        .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                        </div>
                        <div class='content'>
                            <h2>Se ha creado un nuevo evento</h2>
                            <p>Nombre del evento: <strong>$nombre</strong>.</p>
                            <p>Descripción: $descripcion</p>
                            <p>Fecha de inicio: $fechIn $horIn.</p>
                            <p>Fecha de fin: $fechFi $horFi.</p>
                            <p>Por favor, ingresa al sistema para más detalles.</p>
                        </div>
                        <div class='footer'>
                            <p>Centro para la Sociedad Digital</p>
                        </div>
                    </div>
                </body>
                </html>
                ";
            foreach ($participantesArray as $participante) {
                // Obtener correo del participante
                $sql_email = "SELECT Correo FROM Usuarios WHERE Codigo = ?";
                $stmt_email = $conexion->prepare($sql_email);
                $stmt_email->bind_param("i", $participante);
                $stmt_email->execute();
                $result_email = $stmt_email->get_result();
                $row_email = $result_email->fetch_assoc();

                enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
            }

            echo json_encode(['status' => 'success', 'message' => 'Nuevo evento creado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el evento: ' . $stmt->error]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no válido']);
}

$conexion->close();
?>

<?php
session_start();
header('Content-Type: application/json');
error_reporting(0); // Disable error reporting for production
ini_set('display_errors', 0); // Ensure PHP errors don't mix with JSON output

include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';

try {
    // Función para crear notificaciones de eventos
    function crearNotificacionEvento($conexion, $evento_id, $nombre_evento, $descripcion, $fecha_inicio, $hora_inicio, $participantes, $emisor_id)
    {
        try {
            $mensaje = "Nuevo evento: $nombre_evento - " . date('d/m/Y', strtotime($fecha_inicio)) . " a las " . date('H:i', strtotime($hora_inicio));

            // Preparar consulta para verificar notificaciones existentes
            $sql_verificar = "SELECT COUNT(*) as count FROM notificaciones 
                WHERE Tipo = 'evento' AND Mensaje = ? AND Usuario_ID = ?";
            $stmt_verificar = $conexion->prepare($sql_verificar);

            $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                                VALUES (?, ?, ?, 0, ?)";

            $stmt = $conexion->prepare($sql_notificacion);

            if (!empty($participantes)) {
                $participantes_array = explode(',', $participantes);
                foreach ($participantes_array as $participante_id) {
                    if (!empty($participante_id)) {
                         // Verificar si ya existe una notificación similar
                        $stmt_verificar->bind_param("si", $mensaje, $participante_id);
                        $stmt_verificar->execute();
                        $resultado_verificar = $stmt_verificar->get_result();
                        $fila_verificar = $resultado_verificar->fetch_assoc();

                        // Insertar solo si no existe una notificación similar
                        if ($fila_verificar['count'] == 0) {
                            $tipo = 'evento';
                            if (!$stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id)) {
                                throw new Exception("Error al vincular parámetros: " . $stmt->error);
                            }
                            if (!$stmt->execute()) {
                                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
                            }
                        }
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error en crearNotificacionEvento: " . $e->getMessage());
            throw $e;
        }
    }

    // Función para actualizar notificaciones de eventos
    function actualizarNotificacionEvento($conexion, $evento_id, $nombre_evento, $descripcion, $fecha_inicio, $hora_inicio, $participantes, $emisor_id)
    {
        try {
            $mensaje = "Evento actualizado: $nombre_evento - " . date('d/m/Y', strtotime($fecha_inicio)) . " a las " . date('H:i', strtotime($hora_inicio));

            // Obtener participantes anteriores
            $sql_antiguos = "SELECT Participantes FROM eventos_admin WHERE ID_Evento = ?";
            $stmt_antiguos = $conexion->prepare($sql_antiguos);
            if (!$stmt_antiguos) {
                throw new Exception("Error al preparar consulta: " . $conexion->error);
            }

            $stmt_antiguos->bind_param("i", $evento_id);
            $stmt_antiguos->execute();
            $resultado = $stmt_antiguos->get_result();
            $row = $resultado->fetch_assoc();
            $participantes_antiguos = !empty($row['Participantes']) ? explode(',', $row['Participantes']) : [];

            // Nuevos participantes
            $participantes_nuevos = !empty($participantes) ? explode(',', $participantes) : [];

            // Preparar consulta para verificar notificaciones existentes
            $sql_verificar = "SELECT COUNT(*) as count FROM notificaciones 
                WHERE Tipo = ? AND Mensaje = ? AND Usuario_ID = ?";
            $stmt_verificar = $conexion->prepare($sql_verificar);

            $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                                VALUES (?, ?, ?, 0, ?)";
            $stmt = $conexion->prepare($sql_notificacion);

            // Notificar a participantes actuales sobre la actualización
            foreach ($participantes_nuevos as $participante_id) {
                if (!empty($participante_id)) {
                    $tipo = 'evento_actualizado';

                    // Verificar si ya existe una notificación similar
                    $stmt_verificar->bind_param("ssi", $tipo, $mensaje, $participante_id);
                    $stmt_verificar->execute();
                    $resultado_verificar = $stmt_verificar->get_result();
                    $fila_verificar = $resultado_verificar->fetch_assoc();

                    // Insertar solo si no existe una notificación similar
                    if ($fila_verificar['count'] == 0) {
                        $stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);
                        $stmt->execute();
                    }
                }
            }

            // Notificar a participantes removidos
            $participantes_removidos = array_diff($participantes_antiguos, $participantes_nuevos);
            if (!empty($participantes_removidos)) {
                // Preparar consulta de verificación
                $sql_verificar = "SELECT COUNT(*) as count FROM notificaciones 
                    WHERE Tipo = 'evento_removido' AND Mensaje = ? AND Usuario_ID = ?";
                $stmt_verificar = $conexion->prepare($sql_verificar);

                // Obtener información del evento para el mensaje
                $mensaje_removido = "Has sido removido del evento: $nombre_evento (programado para el " .
                    date('d/m/Y', strtotime($fecha_inicio)) . " a las " .
                    date('H:i', strtotime($hora_inicio)) . ")";

                foreach ($participantes_removidos as $participante_id) {
                    if (!empty($participante_id)) {
                        // Verificar si ya existe una notificación similar
                        $stmt_verificar->bind_param("si", $mensaje_removido, $participante_id);
                        $stmt_verificar->execute();
                        $resultado_verificar = $stmt_verificar->get_result();
                        $fila_verificar = $resultado_verificar->fetch_assoc();

                        if ($fila_verificar['count'] == 0) {
                            $tipo = 'evento_removido';
                            $stmt->bind_param("ssii", $tipo, $mensaje_removido, $participante_id, $emisor_id);
                            if (!$stmt->execute()) {
                                throw new Exception("Error al insertar notificación de remoción: " . $stmt->error);
                            }

                            // Enviar correo al usuario removido
                            $sql_email = "SELECT Correo FROM usuarios WHERE Codigo = ?";
                            $stmt_email = $conexion->prepare($sql_email);
                            $stmt_email->bind_param("i", $participante_id);
                            $stmt_email->execute();
                            $result_email = $stmt_email->get_result();
                            $row_email = $result_email->fetch_assoc();

                            if ($row_email) {
                                $asunto = "Removido del evento: $nombre_evento";
                                $cuerpo = "
                                    <html>
                                        <head>
                                            <style>
                                                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                                                .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                                                .header { text-align: center; padding-bottom: 20px; }
                                                .header img { width: 300px; }
                                                .content { padding: 20px; }
                                                h2 { color: #2c3e50; }
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
                                                    <h2>Has sido removido del siguiente evento:</h2>
                                                    <p><strong>Evento:</strong> $nombre_evento</p>
                                                    <p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($fecha_inicio)) . "</p>
                                                    <p><strong>Hora:</strong> " . date('H:i', strtotime($hora_inicio)) . "</p>
                                                </div>
                                                <div class='footer'>
                                                    <p>Centro para la Sociedad Digital</p>
                                                </div>
                                            </div>
                                        </body>
                                    </html>
                                ";
                                enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarNotificacionEvento: " . $e->getMessage());
            throw $e;
        }
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = isset($_POST['id_evento']) ? $_POST['id_evento'] : null;
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $fechIn = $_POST['FechIn'];
        $fechFi = $_POST['FechFi'];
        $horIn = $_POST['HorIn'];
        $horFi = $_POST['HorFi'];
        $etiqueta = $_POST['etiqueta'];
        $notif = $_POST['notificacion'];
        $horNotif = $_POST['HorNotif'];

        // Procesar participantes
        $participantes = '';
        $participantesArray = [];
        if (isset($_POST['participantes']) && is_array($_POST['participantes'])) {
            $participantesArray = array_filter($_POST['participantes'], 'strlen');
            $participantes = implode(",", $participantesArray);
        }

        if ($id) {
            // Obtener participantes actuales antes de la actualización
            $sql_current = "SELECT Participantes FROM eventos_admin WHERE ID_Evento = ?";
            $stmt_current = $conexion->prepare($sql_current);
            $stmt_current->bind_param("i", $id);
            $stmt_current->execute();
            $result_current = $stmt_current->get_result();
            $row_current = $result_current->fetch_assoc();
            $participantes_actuales = !empty($row_current['Participantes']) ? explode(',', $row_current['Participantes']) : [];

            // Identificar usuarios eliminados antes de la actualización
            $usuarios_eliminados = array_diff($participantes_actuales, $participantesArray);

            // Preparar notificaciones para usuarios eliminados
            if (!empty($usuarios_eliminados)) {
                $mensaje_eliminado = "Has sido removido del evento: $nombre (programado para el " .
                    date('d/m/Y', strtotime($fechIn)) . " a las " .
                    date('H:i', strtotime($horIn)) . ")";

                $sql_notif = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                             VALUES (?, ?, ?, 0, ?)";
                $stmt_notif = $conexion->prepare($sql_notif);

                foreach ($usuarios_eliminados as $usuario_id) {
                    if (!empty($usuario_id)) {
                        $tipo = 'evento_removido';
                        $stmt_notif->bind_param("ssii", $tipo, $mensaje_eliminado, $usuario_id, $_SESSION['Codigo']);
                        $stmt_notif->execute();

                        // Enviar correo al usuario eliminado
                        $sql_email = "SELECT Correo FROM usuarios WHERE Codigo = ?";
                        $stmt_email = $conexion->prepare($sql_email);
                        $stmt_email->bind_param("i", $usuario_id);
                        $stmt_email->execute();
                        $result_email = $stmt_email->get_result();
                        $row_email = $result_email->fetch_assoc();

                        if ($row_email) {
                            $asunto = "Removido del evento: $nombre";
                            $cuerpo = "
                                <html>
                                    <head>
                                        <style>
                                            body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                                            .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                                            .header { text-align: center; padding-bottom: 20px; }
                                            .header img { width: 300px; }
                                            .content { padding: 20px; }
                                            h2 { color: #2c3e50; }
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
                                                <h2>Has sido removido del siguiente evento:</h2>
                                                <p><strong>Evento:</strong> $nombre</p>
                                                <p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($fechIn)) . "</p>
                                                <p><strong>Hora:</strong> " . date('H:i', strtotime($horIn)) . "</p>
                                            </div>
                                            <div class='footer'>
                                                <p>Centro para la Sociedad Digital</p>
                                            </div>
                                        </div>
                                    </body>
                                </html>
                            ";
                            enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
                        }
                    }
                }
            }

            // Actualizar el evento
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
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $conexion->error);
            }

            $stmt->bind_param(
                "ssssssssssi",
                $nombre,
                $descripcion,
                $fechIn,
                $fechFi,
                $horIn,
                $horFi,
                $etiqueta,
                $participantes,
                $notif,
                $horNotif,
                $id
            );

            if ($stmt->execute()) {
                // Notificar a los participantes actuales sobre la actualización
                actualizarNotificacionEvento(
                    $conexion,
                    $id,
                    $nombre,
                    $descripcion,
                    $fechIn,
                    $horIn,
                    $participantes,
                    $_SESSION['Codigo']
                );

                echo json_encode(['status' => 'success', 'message' => 'Evento actualizado con éxito']);
            } else {
                throw new Exception("Error al actualizar el evento: " . $stmt->error);
            }
        } else {
            // Crear nuevo evento
            $sql = "INSERT INTO eventos_admin (
                    Nombre_Evento, Descripcion_Evento, Fecha_Inicio, Fecha_Fin, 
                    Hora_Inicio, Hora_Fin, Etiqueta, Participantes, Notificaciones, Hora_Noti
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $conexion->error);
            }

            $stmt->bind_param(
                "ssssssssss",
                $nombre,
                $descripcion,
                $fechIn,
                $fechFi,
                $horIn,
                $horFi,
                $etiqueta,
                $participantes,
                $notif,
                $horNotif
            );

            if ($stmt->execute()) {
                $evento_id = $stmt->insert_id;

                crearNotificacionEvento(
                    $conexion,
                    $evento_id,
                    $nombre,
                    $descripcion,
                    $fechIn,
                    $horIn,
                    $participantes,
                    $_SESSION['Codigo']
                );

                // Enviar correos
                if (!empty($participantesArray)) {
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
                                    h2 { color: #2c3e50; }
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
                                        <p><strong>Nombre del evento:</strong> $nombre</p>
                                        <p><strong>Descripción:</strong> $descripcion</p>
                                        <p><strong>Fecha:</strong> $fechIn $horIn</p>
                                    </div>
                                    <div class='footer'>
                                        <p>Centro para la Sociedad Digital</p>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ";

                    foreach ($participantesArray as $participante) {
                        $sql_email = "SELECT Correo FROM usuarios WHERE Codigo = ?";
                        $stmt_email = $conexion->prepare($sql_email);
                        $stmt_email->bind_param("i", $participante);
                        $stmt_email->execute();
                        $result_email = $stmt_email->get_result();
                        $row_email = $result_email->fetch_assoc();

                        enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
                    }
                }

                echo json_encode(['status' => 'success', 'message' => 'Nuevo evento creado con éxito']);
            } else {
                throw new Exception("Error al crear el evento: " . $stmt->error);
            }
        }
    } else {
        throw new Exception("Método de solicitud no válido");
    }
} catch (Exception $e) {
    error_log("Error en guardar-evento.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conexion->close();

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

            $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                                VALUES (?, ?, ?, 0, ?)";

            $stmt = $conexion->prepare($sql_notificacion);

            if (!empty($participantes)) {
                $participantes_array = explode(',', $participantes);
                foreach ($participantes_array as $participante_id) {
                    if (!empty($participante_id)) {
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
            $sql_antiguos = "SELECT Participantes FROM Eventos_Admin WHERE ID_Evento = ?";
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

            $sql_notificacion = "INSERT INTO Notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                                VALUES (?, ?, ?, 0, ?)";
            $stmt = $conexion->prepare($sql_notificacion);

            // Notificar a participantes actuales
            foreach ($participantes_nuevos as $participante_id) {
                if (!empty($participante_id)) {
                    $tipo = 'evento_actualizado';
                    $stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);
                    $stmt->execute();
                }
            }

            // Notificar a participantes removidos
            $participantes_removidos = array_diff($participantes_antiguos, $participantes_nuevos);
            if (!empty($participantes_removidos)) {
                $mensaje_removido = "Has sido removido del evento: $nombre_evento";
                foreach ($participantes_removidos as $participante_id) {
                    if (!empty($participante_id)) {
                        $tipo = 'evento_removido';
                        $stmt->bind_param("ssii", $tipo, $mensaje_removido, $participante_id, $emisor_id);
                        $stmt->execute();
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

        $participantes = '';
        if (isset($_POST['participantes']) && is_array($_POST['participantes'])) {
            $participantesArray = array_filter($_POST['participantes'], 'strlen');
            $participantes = implode(",", $participantesArray);
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

                // Enviar correos de actualización
                if (!empty($participantesArray)) {
                    $asunto = "Evento actualizado: $nombre";
                    $cuerpo = "
                        <html>
                        <body>
                            <p>Se ha actualizado el evento: $nombre</p>
                            <p>Descripción: $descripcion</p>
                            <p>Nueva fecha: $fechIn $horIn</p>
                        </body>
                        </html>
                    ";

                    foreach ($participantesArray as $participante) {
                        $sql_email = "SELECT Correo FROM Usuarios WHERE Codigo = ?";
                        $stmt_email = $conexion->prepare($sql_email);
                        $stmt_email->bind_param("i", $participante);
                        $stmt_email->execute();
                        $result_email = $stmt_email->get_result();
                        $row_email = $result_email->fetch_assoc();

                        enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
                    }
                }

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
                        <body>
                            <p>Se ha creado un nuevo evento: $nombre</p>
                            <p>Descripción: $descripcion</p>
                            <p>Fecha: $fechIn $horIn</p>
                        </body>
                        </html>
                    ";

                    foreach ($participantesArray as $participante) {
                        $sql_email = "SELECT Correo FROM Usuarios WHERE Codigo = ?";
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

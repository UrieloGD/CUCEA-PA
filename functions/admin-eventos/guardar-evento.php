<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';

try {
    // Función para crear notificaciones de eventos
    function crearNotificacionEvento($conexion, $evento_id, $nombre_evento, $descripcion, $fecha_inicio, $hora_inicio, $participantes, $emisor_id) {
        try {
            $mensaje = "Nuevo evento: $nombre_evento - " . date('d/m/Y', strtotime($fecha_inicio)) . " a las " . date('H:i', strtotime($hora_inicio));

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
                        $stmt_verificar->bind_param("si", $mensaje, $participante_id);
                        $stmt_verificar->execute();
                        $resultado_verificar = $stmt_verificar->get_result();
                        $fila_verificar = $resultado_verificar->fetch_assoc();

                        if ($fila_verificar['count'] == 0) {
                            $tipo = 'evento';
                            $stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);
                            $stmt->execute();
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

    // Función para actualizar notificaciones
    function actualizarNotificacionEvento($conexion, $evento_id, $nombre_evento, $descripcion, $fecha_inicio, $hora_inicio, $participantes, $emisor_id) {
        try {
            $mensaje = "Evento actualizado: $nombre_evento - " . date('d/m/Y', strtotime($fecha_inicio)) . " a las " . date('H:i', strtotime($hora_inicio));

            $sql_antiguos = "SELECT Participantes FROM eventos_admin WHERE ID_Evento = ?";
            $stmt_antiguos = $conexion->prepare($sql_antiguos);
            $stmt_antiguos->bind_param("i", $evento_id);
            $stmt_antiguos->execute();
            $resultado = $stmt_antiguos->get_result();
            $row = $resultado->fetch_assoc();
            $participantes_antiguos = !empty($row['Participantes']) ? explode(',', $row['Participantes']) : [];

            $participantes_nuevos = !empty($participantes) ? explode(',', $participantes) : [];

            // Notificar a participantes nuevos
            $sql_verificar = "SELECT COUNT(*) as count FROM notificaciones 
                            WHERE Tipo = ? AND Mensaje = ? AND Usuario_ID = ?";
            $stmt_verificar = $conexion->prepare($sql_verificar);

            $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                                VALUES (?, ?, ?, 0, ?)";
            $stmt = $conexion->prepare($sql_notificacion);

            foreach ($participantes_nuevos as $participante_id) {
                if (!empty($participante_id)) {
                    $tipo = 'evento_actualizado';
                    $stmt_verificar->bind_param("ssi", $tipo, $mensaje, $participante_id);
                    $stmt_verificar->execute();
                    $resultado_verificar = $stmt_verificar->get_result();
                    $fila_verificar = $resultado_verificar->fetch_assoc();

                    if ($fila_verificar['count'] == 0) {
                        $stmt->bind_param("ssii", $tipo, $mensaje, $participante_id, $emisor_id);
                        $stmt->execute();
                    }
                }
            }

            // Notificar a participantes eliminados
            $participantes_removidos = array_diff($participantes_antiguos, $participantes_nuevos);
            if (!empty($participantes_removidos)) {
                $mensaje_removido = "Has sido removido del evento: $nombre_evento (programado para el " .
                                  date('d/m/Y', strtotime($fecha_inicio)) . " a las " .
                                  date('H:i', strtotime($hora_inicio)) . ")";

                foreach ($participantes_removidos as $participante_id) {
                    if (!empty($participante_id)) {
                        $tipo = 'evento_removido';
                        $stmt->bind_param("ssii", $tipo, $mensaje_removido, $participante_id, $emisor_id);
                        $stmt->execute();

                        // Enviar correo
                        $sql_email = "SELECT Correo FROM usuarios WHERE Codigo = ?";
                        $stmt_email = $conexion->prepare($sql_email);
                        $stmt_email->bind_param("i", $participante_id);
                        $stmt_email->execute();
                        $row_email = $stmt_email->get_result()->fetch_assoc();

                        if ($row_email) {
                            $asunto = "Removido del evento: $nombre_evento";
                            $cuerpo = "<html>...</html>";
                            enviarCorreo($row_email['Correo'], $asunto, $cuerpo);
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
        $id = $_POST['id_evento'] ?? null;
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'] ?? '';
        $fechIn = $_POST['FechIn'];
        $fechFi = $_POST['FechFi'];
        $horIn = $_POST['HorIn'];
        $horFi = $_POST['HorFi'];
        $etiqueta = $_POST['etiqueta'];
        $participantes = implode(',', $_POST['participantes'] ?? []);

        if ($id) {
            // Lógica de actualización
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
            $stmt->bind_param("ssssssssi", $nombre, $descripcion, $fechIn, $fechFi, $horIn, $horFi, $etiqueta, $participantes, $id);
            
            if ($stmt->execute()) {
                actualizarNotificacionEvento($conexion, $id, $nombre, $descripcion, $fechIn, $horIn, $participantes, $_SESSION['Codigo']);
                echo json_encode(['status' => 'success', 'message' => 'Evento actualizado']);
            } else {
                throw new Exception("Error al actualizar: " . $stmt->error);
            }

        } else {
            // Lógica de creación
            $sql = "INSERT INTO eventos_admin (
                    Nombre_Evento, Descripcion_Evento, Fecha_Inicio, Fecha_Fin, 
                    Hora_Inicio, Hora_Fin, Etiqueta, Participantes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssssss", $nombre, $descripcion, $fechIn, $fechFi, $horIn, $horFi, $etiqueta, $participantes);
            
            if ($stmt->execute()) {
                $evento_id = $stmt->insert_id;
                crearNotificacionEvento($conexion, $evento_id, $nombre, $descripcion, $fechIn, $horIn, $participantes, $_SESSION['Codigo']);
                echo json_encode(['status' => 'success', 'message' => 'Evento creado']);
            } else {
                throw new Exception("Error al crear: " . $stmt->error);
            }
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conexion->close();
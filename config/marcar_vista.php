<?php
header('Content-Type: application/json');
session_start();

try {
    include('./db.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

        if ($id === 0 || empty($tipo)) {
            throw new Exception('ID o tipo de notificación inválidos');
        }

        if ($tipo == 'justificacion') {
            $query = "UPDATE Justificaciones SET Notificacion_Vista = 1 WHERE ID_Justificacion = ?";
        } else if ($tipo == 'plantilla') {
            $query = "UPDATE Plantilla_Dep SET Notificacion_Vista = 1 WHERE ID_Archivo_Dep = ?";
        } else {
            throw new Exception('Tipo de notificación no reconocido');
        }

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Error preparando la consulta: ' . $conn->error);
        }

        $stmt->bind_param("i", $id);
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Error ejecutando la consulta: ' . $stmt->error);
        }
    } else {
        throw new Exception('Método de solicitud inválido');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

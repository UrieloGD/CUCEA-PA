<?php
include('./../../config/db.php');
session_start();

$id = $_POST['id'];
$tipo = $_POST['tipo'];
$usuario_id = $_SESSION['Codigo'];

try {
    // Determinar qué columna usar según el tipo
    $tipo_columna = '';

    if ($tipo === 'justificacion') {
        $tipo_columna = 'Justificacion_ID';
    } elseif ($tipo === 'plantilla') {
        $tipo_columna = 'Plantilla_ID';
    } else {
        $tipo_columna = 'Notificacion_ID';
    }

    // Comprobar si existe un registro para este usuario y esta notificación
    $check_sql = "SELECT ID FROM usuarios_notificaciones 
                  WHERE Usuario_ID = ? AND $tipo_columna = ? AND Tipo = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("iis", $usuario_id, $id, $tipo);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Actualizar el registro existente
        $update_sql = "UPDATE usuarios_notificaciones 
                      SET Vista = 2, Oculta = 1 
                      WHERE Usuario_ID = ? AND $tipo_columna = ? AND Tipo = ?";
        $update_stmt = $conexion->prepare($update_sql);
        $update_stmt->bind_param("iis", $usuario_id, $id, $tipo);
        $result = $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Crear un nuevo registro
        $insert_sql = "INSERT INTO usuarios_notificaciones (Usuario_ID, $tipo_columna, Tipo, Vista, Oculta) 
                      VALUES (?, ?, ?, 2, 1)";
        $insert_stmt = $conexion->prepare($insert_sql);
        $insert_stmt->bind_param("iis", $usuario_id, $id, $tipo);
        $result = $insert_stmt->execute();
        $insert_stmt->close();
    }

    $check_stmt->close();

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conexion->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

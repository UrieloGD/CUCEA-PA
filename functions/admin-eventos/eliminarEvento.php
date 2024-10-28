<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

function sendJsonResponse($success, $message, $additionalData = [])
{
    $response = array_merge([
        'success' => $success,
        'message' => $message
    ], $additionalData);

    echo json_encode($response);
    exit();
}

try {
    // Debug: Imprimir rutas actuales
    $currentPath = __DIR__;
    $dbPath = dirname(dirname(dirname(__FILE__))) . '/config/db.php';

    // Log de información de rutas
    error_log("Ruta actual: " . $currentPath);
    error_log("Ruta intentada para db.php: " . $dbPath);

    // Intentar con rutas alternativas
    $possiblePaths = [
        dirname(dirname(dirname(__FILE__))) . '/config/db.php',
        dirname(dirname(__FILE__)) . '/config/db.php',
        __DIR__ . '/../../config/db.php',
        __DIR__ . '/../config/db.php',
        __DIR__ . '/config/db.php'
    ];

    $dbPathFound = false;
    foreach ($possiblePaths as $path) {
        error_log("Intentando ruta: " . $path);
        if (file_exists($path)) {
            $dbPath = $path;
            $dbPathFound = true;
            error_log("¡Archivo encontrado en: " . $path);
            break;
        }
    }

    if (!$dbPathFound) {
        throw new Exception('Archivo de base de datos no encontrado. Rutas intentadas: ' . implode(', ', $possiblePaths));
    }

    // Incluir el archivo de la base de datos
    require_once $dbPath;

    // Verificar la conexión
    if (!isset($conexion)) {
        throw new Exception('Variable de conexión no definida después de incluir db.php');
    }

    if ($conexion->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . $conexion->connect_error);
    }

    // Leer y validar input
    $input = file_get_contents("php://input");
    if (empty($input)) {
        throw new Exception('No se recibieron datos');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    if (!isset($data['id'])) {
        throw new Exception('ID de evento no recibido');
    }

    $eventId = (int)$data['id'];
    if ($eventId <= 0) {
        throw new Exception('ID de evento inválido');
    }

    // Iniciar transacción
    $conexion->autocommit(false);
    $conexion->begin_transaction();

    // Verificar si el evento existe
    $check_stmt = $conexion->prepare("SELECT COUNT(*) as count FROM Eventos_Admin WHERE ID_Evento = ?");
    if (!$check_stmt) {
        throw new Exception('Error al preparar la consulta de verificación: ' . $conexion->error);
    }

    $check_stmt->bind_param("i", $eventId);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    $check_stmt->close();

    if ($row['count'] == 0) {
        throw new Exception('El evento no existe');
    }

    // Eliminar el evento
    $delete_stmt = $conexion->prepare("DELETE FROM Eventos_Admin WHERE ID_Evento = ?");
    if (!$delete_stmt) {
        throw new Exception('Error al preparar la consulta de eliminación: ' . $conexion->error);
    }

    $delete_stmt->bind_param("i", $eventId);
    if (!$delete_stmt->execute()) {
        throw new Exception('Error al ejecutar la eliminación: ' . $delete_stmt->error);
    }

    $affected_rows = $delete_stmt->affected_rows;
    $delete_stmt->close();

    if ($affected_rows == 0) {
        throw new Exception('No se eliminó ningún registro');
    }

    // Confirmar la transacción
    $conexion->commit();

    sendJsonResponse(true, 'Evento eliminado exitosamente', ['affected_rows' => $affected_rows]);
} catch (Exception $e) {
    if (isset($conexion) && !$conexion->connect_error) {
        $conexion->rollback();
    }
    error_log("Error en eliminarEvento.php: " . $e->getMessage());
    sendJsonResponse(false, 'Error al eliminar evento: ' . $e->getMessage());
} finally {
    if (isset($conexion) && !$conexion->connect_error) {
        $conexion->close();
    }
    ob_end_flush();
}

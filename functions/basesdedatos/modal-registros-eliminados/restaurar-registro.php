<?php
// Archivo: restaurar-registro.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
header('Content-Type: application/json');

require_once './../../../config/db.php';

// Para depuración
$debug_info = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'request' => file_get_contents('php://input')
];

// Comprobar si hay datos en POST
if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Intentar recuperar datos de php://input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if ($data && isset($data['id'])) {
        $_POST['id'] = $data['id'];
    }
}

// Validar sesión y parámetros
if (!isset($_SESSION['Departamento_ID']) && !isset($_POST['departamento_id'])) {
    error_log("Departamento_ID no disponible: " . json_encode($_SESSION));
    echo json_encode(['success' => false, 'message' => 'Departamento_ID requerido', 'debug' => $debug_info]);
    exit();
}

try {
    $id = (int)$_POST['id'];
    // Usar departamento_id de POST si está disponible, de lo contrario usar el de la sesión
    $departamento_id = isset($_POST['departamento_id']) ? (int)$_POST['departamento_id'] : (int)$_SESSION['Departamento_ID'];

    // Obtener nombre del departamento
    $stmt = $conexion->prepare("SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?");
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $departamento = $result->fetch_assoc();

    if (!$departamento) {
        throw new Exception('Departamento no encontrado: ' . $departamento_id);
    }

    $nombre_departamento = $departamento['Nombre_Departamento'];
    $tabla = "data_" . str_replace(' ', '_', $nombre_departamento);

    // Comprobar si la tabla existe
    $result_check = $conexion->query("SHOW TABLES LIKE '$tabla'");
    if ($result_check->num_rows === 0) {
        throw new Exception("Tabla $tabla no existe");
    }

    // Actualizar registro
    $conexion->begin_transaction();
    $stmt = $conexion->prepare("UPDATE $tabla SET PAPELERA = 'activo' WHERE ID_Plantilla = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Registro no encontrado: ID=$id en tabla $tabla");
    }

    $conexion->commit();
    echo json_encode(['success' => true, 'message' => 'Registro restaurado correctamente']);
} catch (Exception $e) {
    if ($conexion->connect_error === null) {
        $conexion->rollback();
    }
    error_log("Error en restaurar-registro.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => $debug_info
    ]);
}

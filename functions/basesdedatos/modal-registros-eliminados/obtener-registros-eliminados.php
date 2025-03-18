<?php
// ./functions/basesdedatos/modal-registros-eliminados/obtener-registros-eliminados.php

// Conexión a la base de datos
include './../../../config/db.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

try {
    // Obtener el departamento_id
    $departamento_id = isset($_POST['Departamento_ID']) ? (int)$_POST['Departamento_ID'] : null;    
    // Si no se proporciona departamento_id, intentar obtenerlo de la sesión
    if (!$departamento_id && isset($_SESSION['Departamento_ID'])) {
        $departamento_id = $_SESSION['Departamento_ID'];
    }
    
    // Obtener información del departamento
    $sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
    $stmt = $conexion->prepare($sql_departamento);
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result_departamento = $stmt->get_result();
    
    $row_departamento = $result_departamento->fetch_assoc();
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    
    // Construir nombre de la tabla del departamento
    $tabla_departamento = "data_" . str_replace(' ', '_', $nombre_departamento);
    
    // Comprobar si la tabla existe
    $sql_check_table = "SHOW TABLES LIKE '$tabla_departamento'";
    $result_check_table = $conexion->query($sql_check_table);
    
    // Comprobar si la columna PAPELERA existe en la tabla
    $sql_check_column = "SHOW COLUMNS FROM $tabla_departamento LIKE 'PAPELERA'";
    $result_check_column = $conexion->query($sql_check_column);
    
    if ($result_check_column->num_rows === 0) {
        throw new Exception("La columna PAPELERA no existe en la tabla $tabla_departamento");
    }
    
    // Buscar registros inactivos
    $sql = "SELECT * FROM $tabla_departamento WHERE PAPELERA = 'inactivo'";
    
    if ($departamento_id) {
        $sql .= " AND Departamento_ID = $departamento_id";
    }
    
    $result = $conexion->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    // Convertir resultados a array
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['data' => $data]);
    exit();

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}

?>
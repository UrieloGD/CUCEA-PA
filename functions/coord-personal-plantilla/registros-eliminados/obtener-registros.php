<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './../../../config/db.php';

// Control de errores para JSON
header('Content-Type: application/json');

try {
    // Verificar si se ha enviado el parámetro Papelera
    $papelera = isset($_POST['Papelera']) ? $_POST['Papelera'] : 'inactivo'; 

    // Consulta SQL para obtener los registros
    $sql = "SELECT * FROM coord_per_prof WHERE Papelera = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Error preparando consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $papelera);
    
    if ($stmt->execute() === false) {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();

    // Crear un array con los datos obtenidos de la consulta
    $datos = array();
    while ($fila = $result->fetch_assoc()) {
        $datos[] = $fila;
    }

    // Verificar si hay datos
    if (empty($datos)) {
        echo json_encode([
            "data" => [],
            "mensaje" => "No se encontraron registros con Papelera = '$papelera'"
        ]);
        exit;
    }

    // Imprimir JSON
    echo json_encode(["data" => $datos]);

    $stmt->close();
    $conexion->close();
} catch (Exception $e) {
    // Manejar cualquier error
    echo json_encode([
        "error" => true,
        "mensaje" => $e->getMessage()
    ]);
}
?>
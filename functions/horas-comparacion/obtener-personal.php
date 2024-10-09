<?php

// Evitar que se muestren errores en la salida
error_reporting(0);
ini_set('display_errors', 0);

include './../../config/db.php';

// Verificar la conexión
if (!$conexion) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// Obtener el departamento
$departamento = isset($_POST['departamento']) ? $_POST['departamento'] : '';

try {
    // Construir la consulta base
    if ($departamento === 'todos') {
        $query = "SELECT Codigo, Nombre_completo, Tipo_plaza, Horas_frente_grupo, 
                         Carga_horaria, Horas_definitivas 
                  FROM Coord_Per_Prof 
                  ORDER BY Nombre_completo";
        $stmt = $conexion->prepare($query);
    } else {
        $query = "SELECT Codigo, Nombre_completo, Tipo_plaza, Horas_frente_grupo, 
                         Carga_horaria, Horas_definitivas 
                  FROM Coord_Per_Prof 
                  WHERE Departamento = ? 
                  ORDER BY Nombre_completo";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $departamento);
    }

    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Obtener los resultados
    $personal = array();
    while ($row = $result->fetch_assoc()) {
        // Asegurarse de que los valores numéricos sean números
        $row['Horas_frente_grupo'] = intval($row['Horas_frente_grupo']);
        $row['Horas_definitivas'] = intval($row['Horas_definitivas']);
        $personal[] = $row;
    }

    // Enviar la respuesta
    header('Content-Type: application/json');
    echo json_encode($personal);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}

// Cerrar la conexión y el statement
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conexion)) {
    $conexion->close();
}
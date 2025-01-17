<?php
include __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $modulo = mysqli_real_escape_string($conexion, $_GET['modulo']);
    $espacio = mysqli_real_escape_string($conexion, $_GET['espacio']);

    $query = "SELECT Equipo, Observaciones, Reportes FROM espacios 
              WHERE Modulo = '$modulo' AND Espacio = '$espacio'";

    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            'success' => true,
            'equipo' => $row['Equipo'],
            'observaciones' => $row['Observaciones'],
            'reportes' => $row['Reportes']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontró información']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}

mysqli_close($conexion);

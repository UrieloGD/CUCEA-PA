<?php
include __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulo = mysqli_real_escape_string($conexion, $_POST['modulo']);
    $espacio = mysqli_real_escape_string($conexion, $_POST['espacio']);
    $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);
    $reportes = mysqli_real_escape_string($conexion, $_POST['reportes']);

    // Define the boolean columns
    $booleanColumns = [
        'Computadora', 'Proyector', 'Cortina_Proyector', 
        'Cortina_Luz', 'Doble_Pintarron', 'Pantalla', 
        'Camara', 'Bocinas', 'Pintarron'
    ];

    // Prepare the update query dynamically
    $updateParts = [];
    
    // Add boolean columns to update
    foreach ($booleanColumns as $column) {
        $value = isset($_POST[$column]) && $_POST[$column] === 'true' ? 1 : 0;
        $updateParts[] = "$column = $value";
    }

    // Add observaciones and reportes
    $updateParts[] = "Observaciones = '$observaciones'";
    $updateParts[] = "Reportes = '$reportes'";

    // Combine update parts
    $updateString = implode(', ', $updateParts);

    $query = "UPDATE espacios SET $updateString 
              WHERE Modulo = '$modulo' AND Espacio = '$espacio'";

    if (mysqli_query($conexion, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => mysqli_error($conexion)
        ]);
    }

    mysqli_close($conexion);
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
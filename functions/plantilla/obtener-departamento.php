<?php
session_start();
header('Content-Type: application/json');

// Verificar si existe el ID de departamento en la sesión
if (isset($_SESSION['Departamento_ID'])) {
    echo json_encode([
        'success' => true,
        'departamento_id' => $_SESSION['Departamento_ID'],
        'nombre_departamento' => $_SESSION['Nombre_Departamento'] ?? 'Desconocido'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No hay departamento activo en la sesión'
    ]);
}

exit;
?>
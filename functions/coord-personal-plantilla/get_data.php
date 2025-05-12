<?php
session_start();
include './../../config/db.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['Codigo'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$tabla_departamento = "coord_per_prof";

// Obtener parámetros de paginación (para futura implementación de paginación server-side)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$size = isset($_GET['size']) ? intval($_GET['size']) : 0; // 0 = todos los registros

// Consulta SQL para obtener registros activos
$sql = "SELECT * FROM $tabla_departamento WHERE Papelera = 'activo'";
$result = mysqli_query($conexion, $sql);

if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Si se solicita paginación desde el servidor (para futuras implementaciones)
if ($size > 0) {
    $total = count($data);
    $totalPages = ceil($total / $size);
    $offset = ($page - 1) * $size;
    $paginatedData = array_slice($data, $offset, $size);
    
    echo json_encode([
        'last_page' => $totalPages,
        'data' => $paginatedData,
        'total' => $total
    ]);
} else {
    // Enviar todos los datos (paginación se hará del lado del cliente)
    echo json_encode($data);
}
?>
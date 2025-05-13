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

// Obtener parámetros de paginación
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$size = isset($_GET['size']) ? intval($_GET['size']) : 50; // Valor predeterminado: 50

// Verificar si se solicitan todos los registros
$showAll = isset($_GET['all']) && $_GET['all'] === "true";

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

$total = count($data);

// Determinar si se muestran todos los registros o se pagina
if ($showAll) {
    // Mostrar todos los registros
    echo json_encode([
        'last_page' => 1,
        'data' => $data,
        'total' => $total
    ]);
} else {
    // Paginar los datos
    $totalPages = ceil($total / $size);
    $offset = ($page - 1) * $size;
    $paginatedData = array_slice($data, $offset, $size);
    
    echo json_encode([
        'last_page' => $totalPages,
        'data' => $paginatedData,
        'total' => $total
    ]);
}
?>
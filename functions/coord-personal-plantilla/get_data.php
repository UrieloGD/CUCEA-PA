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

// Obtener parámetros
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$size = isset($_GET['size']) ? intval($_GET['size']) : 50;
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;
$dir = isset($_GET['dir']) ? $_GET['dir'] : "asc";
$search = isset($_GET['search']) ? mysqli_real_escape_string($conexion, $_GET['search']) : "";
$showAll = isset($_GET['all']) && $_GET['all'] === "true";

// Obtener filtros por columna
$filters = [];
foreach ($_GET as $key => $value) {
    if (strpos($key, 'headerFilter_') === 0 && !empty($value)) {
        $field = str_replace('headerFilter_', '', $key);
        $filters[$field] = mysqli_real_escape_string($conexion, $value);
    }
}

// Construir consulta SQL
$sql = "SELECT * FROM $tabla_departamento WHERE Papelera = 'activo'";

// Búsqueda global
if (!empty($search)) {
    $searchFields = ['ID', 'Datos', 'Codigo', 'Paterno', 'Materno', 'Nombres', 'Nombre_completo'];
    $conditions = [];
    foreach ($searchFields as $field) {
        $conditions[] = "$field LIKE '%$search%'";
    }
    $sql .= " AND (" . implode(" OR ", $conditions) . ")";
    $showAll = true;
}

// Filtros por columna
foreach ($filters as $field => $value) {
    $sql .= " AND $field LIKE '%$value%'";
}

// Ordenación
if ($sort) {
    $sql .= " ORDER BY $sort $dir";
} else {
    $sql .= " ORDER BY ID ASC";
}

// Obtener total de registros
$totalResult = mysqli_query($conexion, $sql);
if (!$totalResult) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}
$total = mysqli_num_rows($totalResult);

// Paginación
if (!$showAll) {
    $offset = ($page - 1) * $size;
    $sql .= " LIMIT $offset, $size";
}

$result = mysqli_query($conexion, $sql);
if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Respuesta
if ($showAll) {
    echo json_encode([
        'last_page' => 1,
        'data' => $data,
        'total' => $total
    ]);
} else {
    $totalPages = ceil($total / $size);
    echo json_encode([
        'last_page' => $totalPages,
        'data' => $data,
        'total' => $total
    ]);
}
?>
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
$sql = "SELECT * FROM $tabla_departamento WHERE Papelera = 'activo'";
$result = mysqli_query($conexion, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
<?php
include './../../config/db.php';
header('Content-Type: application/json');

// Validar ID de departamento
if (!isset($_GET['departamento_id'])) {
    echo json_encode(['existe' => false, 'error' => 'No se proporcionó ID de departamento']);
    exit;
}

$departamento_id = intval($_GET['departamento_id']);

// Consulta para verificar si existe plantilla
$sql = "SELECT COUNT(*) as total FROM plantilla_dep WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Responder con JSON indicando si existe plantilla
echo json_encode(['existe' => $row['total'] > 0]);
$conexion->close();
exit;
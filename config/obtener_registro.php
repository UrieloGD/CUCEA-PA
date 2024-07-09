<?php
include 'db.php';
session_start();

if (!isset($_SESSION['Codigo'])) {
    die(json_encode(['error' => 'Usuario no autenticado']));
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;

if (!$id || !$departamento_id) {
    die(json_encode(['error' => 'ID de registro o departamento no proporcionado']));
}

$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "Data_" . str_replace(' ', '_', $nombre_departamento);

$sql = "SELECT * FROM `$tabla_departamento` WHERE ID_Plantilla = ? AND Departamento_ID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id, $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Registro no encontrado']);
}

$stmt->close();
$conexion->close();
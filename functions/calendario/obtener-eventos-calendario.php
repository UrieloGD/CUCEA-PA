<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Ajusta esta ruta si es necesario
require_once './../../config/db.php';

$date = $_GET['date'];
$userId = $_GET['user_id'];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;

// Log de los parámetros recibidos
error_log("Fecha: $date, User ID: $userId, Limit: " . ($limit ?? 'null'));

// Verificar si la conexión a la base de datos se estableció correctamente
if (!$conexion) {
    error_log("Error de conexión a la base de datos: " . mysqli_connect_error());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$sql = "SELECT ID_Evento, Nombre_Evento, Etiqueta, Descripcion_Evento, DATE(Fecha_Inicio) AS Fecha_Evento, TIME_FORMAT(Hora_Inicio, '%H:%i') AS Hora_Inicio 
        FROM eventos_admin 
        WHERE ? BETWEEN DATE(Fecha_Inicio) AND DATE(Fecha_Fin) AND FIND_IN_SET(?, Participantes)";

if ($limit) {
    $sql .= " LIMIT ?";
}

error_log("SQL Query: $sql");

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    error_log("Error en la preparación: " . $conexion->error);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la preparación: ' . $conexion->error]);
    exit;
}

if ($limit) {
    $stmt->bind_param("ssi", $date, $userId, $limit);
} else {
    $stmt->bind_param("ss", $date, $userId);
}

if (!$stmt->execute()) {
    error_log("Error en la ejecución: " . $stmt->error);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la ejecución: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

error_log("Eventos encontrados: " . count($events));
error_log("Eventos: " . json_encode($events));

header('Content-Type: application/json');
echo json_encode($events);
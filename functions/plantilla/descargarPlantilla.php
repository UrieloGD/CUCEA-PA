<?php
include './../../config/db.php';

// Validate department ID
if (!isset($_GET['departamento_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'No se proporcionó el ID de departamento'
    ]);
    exit;
}

$departamento_id = intval($_GET['departamento_id']);

// Prepared statement to fetch file
$sql = "SELECT Nombre_Archivo_Dep, Contenido_Archivo_Dep FROM plantilla_sa WHERE Departamento_ID = ? ORDER BY ID_Archivo_Dep DESC LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre_archivo = $row['Nombre_Archivo_Dep'];
    $contenido_archivo = $row['Contenido_Archivo_Dep'];

    // Set headers for file download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    header('Content-Length: ' . strlen($contenido_archivo));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output file contents
    echo $contenido_archivo;
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'No se encontró plantilla para este departamento'
    ]);
    exit;
}
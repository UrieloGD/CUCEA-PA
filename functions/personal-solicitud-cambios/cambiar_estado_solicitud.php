<?php
session_start();
require_once('./../../config/db.php');

// Headers para mejor rendimiento
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Verificación rápida de sesión
if (!isset($_SESSION['Rol_ID']) || $_SESSION['Rol_ID'] != 3) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.'
    ]);
    exit;
}

// Validación rápida de datos
$requiredFields = ['folio', 'tipo', 'estado'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Faltan datos requeridos: $field"
        ]);
        exit;
    }
}

$folio = trim($_POST['folio']);
$tipo = trim($_POST['tipo']);
$nuevoEstado = trim($_POST['estado']);
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

// Validar estados permitidos
$estadosPermitidos = ['Aprobado', 'Rechazado'];
if (!in_array($nuevoEstado, $estadosPermitidos)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Estado no válido. Solo se permite Aprobado o Rechazado.'
    ]);
    exit;
}

// Configuración de tablas optimizada
$configuraciones = [
    'baja' => [
        'tabla' => 'solicitudes_baja',
        'estado' => 'ESTADO_B',
        'folio' => 'OFICIO_NUM_BAJA',
        'comentario' => 'COMENTARIOS',
        'fecha_modificacion' => 'FECHA_MODIFICACION_ACEPTADO_CANCELADO'
    ],
    'propuesta' => [
        'tabla' => 'solicitudes_propuesta',
        'estado' => 'ESTADO_P',
        'folio' => 'OFICIO_NUM_PROP',
        'comentario' => 'COMENTARIOS',
        'fecha_modificacion' => 'FECHA_MODIFICACION_ACEPTADO_CANCELADO'
    ],
    'baja-propuesta' => [
        'tabla' => 'solicitudes_baja_propuesta',
        'estado' => 'ESTADO_P',
        'folio' => 'OFICIO_NUM_BAJA_PROP',
        'comentario' => 'COMENTARIOS',
        'fecha_modificacion' => 'FECHA_MODIFICACION_ACEPTADO_CANCELADO'
    ]
];

if (!isset($configuraciones[$tipo])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Tipo de solicitud no válido.'
    ]);
    exit;
}

$config = $configuraciones[$tipo];

// Obtener fecha y hora actual en formato MySQL
$fechaActual = date('Y-m-d H:i:s');

// Construir consulta SQL incluyendo la fecha de modificación
$sql = "UPDATE {$config['tabla']} SET {$config['estado']} = ?, {$config['fecha_modificacion']} = ?";
$params = [$nuevoEstado, $fechaActual];
$types = "ss";

// Agregar comentario si existe
if (!empty($comentario)) {
    $sql .= ", {$config['comentario']} = ?";
    $params[] = $comentario;
    $types .= "s";
}

$sql .= " WHERE {$config['folio']} = ?";
$params[] = $folio;
$types .= "s";

// Ejecutar consulta
try {
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conexion));
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        throw new Exception('Error al ejecutar la consulta: ' . mysqli_error($conexion));
    }

    $affected_rows = mysqli_stmt_affected_rows($stmt);
    
    if ($affected_rows === 0) {
        throw new Exception('No se encontró la solicitud o no se realizaron cambios.');
    }

    // Formatear fecha para mostrar en el mensaje
    $fechaFormateada = date('d/m/Y H:i:s', strtotime($fechaActual));
    
    $mensaje = $nuevoEstado === 'Aprobado' ? 
        "Solicitud aprobada correctamente el {$fechaFormateada}." : 
        "Solicitud rechazada correctamente el {$fechaFormateada}.";
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'fecha_modificacion' => $fechaFormateada,
        'estado' => $nuevoEstado
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conexion);
}
?>
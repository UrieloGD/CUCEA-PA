<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); 
require_once './../../../config/db.php';

if (!isset($_SESSION['Codigo'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$folio = isset($_GET['folio']) ? $_GET['folio'] : '';

if (empty($folio)) {
    echo json_encode(['error' => 'Folio no proporcionado']);
    exit();
}

if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit();
}

try {
    error_log("Buscando folio: " . $folio);
    
    $sql = "SELECT 
            p.ID_PROP as id,
            p.OFICIO_NUM_PROP as folio,
            p.APELLIDO_P_PROF_P as apellido_paterno_p,
            p.APELLIDO_M_PROF_P as apellido_materno_p,
            p.NOMBRES_PROF_P as nombres_p,
            p.CODIGO_PROF_P as codigo_prof_p,
            p.DIA_P as dia,
            p.MES_P as mes,
            p.ANO_P as ano,
            p.CRN_P as crn,
            p.CODIGO_PUESTO_P as codigo_puesto,
            p.DESCRIPCION_PUESTO_P as puesto,
            p.CLASIFICACION_PUESTO_P as clasificacion_p,
            p.CATEGORIA_P as categoria,
            p.CARRIERA_PROF_P as carrera,
            p.NUM_PUESTO_P as num_puesto,
            p.HRS_SEMANALES_P as horas_sem,
            p.CARGO_ATC_P as cargo,
            p.APELLIDO_P_PROF_SUST as apellido_paterno_sust,
            p.APELLIDO_M_PROF_SUST as apellido_materno_sust,
            p.NOMBRES_PROF_SUST as nombres_sust,
            p.CODIGO_PROF_SUST as codigo_prof_sust,
            p.CAUSA_P as motivo,
            p.PERIODO_ASIG_DESDE_P as periodo_desde,
            p.PERIODO_ASIG_HASTA_P as periodo_hasta,
            p.FECHA_SOLICITUD_P as fecha,
            p.PROFESSION_PROFESOR_P as profession
        FROM 
            solicitudes_propuesta p
        WHERE 
            p.OFICIO_NUM_PROP = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        error_log("Error al preparar consulta: " . $conexion->error);
        echo json_encode(['error' => 'Error al preparar consulta: ' . $conexion->error]);
        exit();
    }
    
    $stmt->bind_param("s", $folio);
    
    if (!$stmt->execute()) {
        error_log("Error al ejecutar consulta: " . $stmt->error);
        echo json_encode(['error' => 'Error al ejecutar consulta: ' . $stmt->error]);
        exit();
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("Solicitud no encontrada para folio: " . $folio);
        echo json_encode(['error' => 'Solicitud no encontrada']);
        exit();
    }
    
    $solicitud = $result->fetch_assoc();
    
    error_log("Datos obtenidos: " . print_r($solicitud, true));
    
    // RESPUESTA CON NOMBRES QUE COINCIDEN EXACTAMENTE CON JAVASCRIPT
    $respuesta = [
        'folio' => $solicitud['folio'] ?? '',
        'tipo' => 'propuesta',
        'fecha' => $solicitud['fecha'] ? date('d/m/Y', strtotime($solicitud['fecha'])) : '',
        'dia' => $solicitud['dia'] ?? '',
        'mes' => $solicitud['mes'] ?? '',
        'ano' => $solicitud['ano'] ?? '',
        'crn' => $solicitud['crn'] ?? '',
        'codigo_puesto' => $solicitud['codigo_puesto'] ?? '',
        'puesto' => $solicitud['puesto'] ?? '',
        'categoria' => $solicitud['categoria'] ?? '',
        'clasificacion_p' => $solicitud['clasificacion_p'] ?? '',
        'carrera' => $solicitud['carrera'] ?? '',
        'num_puesto' => $solicitud['num_puesto'] ?? '',
        'cargo' => $solicitud['cargo'] ? 'Sí' : 'No',
        'horas_sem' => $solicitud['horas_sem'] ?? '',
        'periodo_desde' => $solicitud['periodo_desde'] ? date('d/m/Y', strtotime($solicitud['periodo_desde'])) : '',
        'periodo_hasta' => $solicitud['periodo_hasta'] ? date('d/m/Y', strtotime($solicitud['periodo_hasta'])) : '',
        'motivo' => $solicitud['motivo'] ?? '',
        'profession' => $solicitud['profession'] ?? '',
        
        'profesor_propuesto' => [
            'paterno' => $solicitud['apellido_paterno_p'] ?? '',
            'materno' => $solicitud['apellido_materno_p'] ?? '',
            'nombres' => $solicitud['nombres_p'] ?? '',
            'codigo' => $solicitud['codigo_prof_p'] ?? '',
            'profession' => $solicitud['profession'] ?? ''
        ],
        'profesor_actual' => [
            'paterno' => $solicitud['apellido_paterno_sust'] ?? '',
            'materno' => $solicitud['apellido_materno_sust'] ?? '',
            'nombres' => $solicitud['nombres_sust'] ?? '',
            'codigo' => $solicitud['codigo_prof_sust'] ?? ''
        ]
    ];

    error_log("Respuesta final: " . print_r($respuesta, true));

    if (ob_get_length()) ob_clean();
    
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}

exit();
?>
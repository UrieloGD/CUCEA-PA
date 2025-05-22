<?php
session_start();
header('Content-Type: application/json'); 
require_once './../../config/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Codigo'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener folio
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';

if (empty($folio)) {
    echo json_encode(['error' => 'Folio no proporcionado']);
    exit();
}

// Verifica si la conexión a la base de datos es exitosa
if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit();
}

try {
    $sql = "SELECT 
            bp.ID_BAJA_PROP as id,
            bp.OFICIO_NUM_BAJA_PROP as folio,
            bp.FECHA_SOLICITUD_BAJA_PROP as fecha_creacion,
            bp.HORA_CREACION as hora_creacion,
            bp.ESTADO_P as estado,
            bp.CRN_BAJA as crn,
            bp.NOMBRE_MATERIA_BAJA as materia,
            bp.CVE_MATERIA_BAJA as clave,
            bp.GDO_GPO_TURNO_BAJA as sec,
            bp.MOTIVO_BAJA as motivo,
            bp.PROFESSION_PROFESOR_BAJA as profession_actual,
            bp.APELLIDO_P_PROF_BAJA as profesor_actual_paterno,
            bp.APELLIDO_M_PROF_BAJA as profesor_actual_materno,
            bp.NOMBRES_PROF_BAJA as profesor_actual_nombres,
            bp.CODIGO_PROF_BAJA as profesor_actual_codigo,
            bp.NUM_PUESTO_TEORIA_BAJA as num_puesto_teoria_baja,
            bp.NUM_PUESTO_PRACTICA_BAJA as num_puesto_practica_baja,
            bp.HRS_SEM_MES_TEORIA_BAJA as hrs_teoria_baja,
            bp.HRS_SEM_MES_PRACTICA_BAJA as hrs_practica_baja,
            bp.CARRERA_BAJA as carrera_baja,
            bp.TIPO_ASIGNACION_BAJA as tipo_asignacion_baja,
            bp.APELLIDO_P_PROF_PROP as profesor_propuesto_paterno,
            bp.APELLIDO_M_PROF_PROP as profesor_propuesto_materno,
            bp.NOMBRES_PROF_PROP as profesor_propuesto_nombres,
            bp.CODIGO_PROF_PROP as profesor_propuesto_codigo,
            bp.NUM_PUESTO_TEORIA_PROP as num_puesto_teoria_prop,
            bp.NUM_PUESTO_PRACTICA_PROP as num_puesto_practica_prop,
            bp.HRS_SEM_MES_TEORIA_PROP as hrs_teoria_prop,
            bp.HRS_SEM_MES_PRACTICA_PROP as hrs_practica_prop,
            bp.INTER_TEMP_DEF_PROP as inter_temp_def,
            bp.TIPO_ASIGNACION_PROP as tipo_asignacion_prop,
            bp.PERIODO_ASIG_DESDE_PROP as periodo_desde,
            bp.PERIODO_ASIG_HASTA_PROP as periodo_hasta,
            bp.SIN_EFFECTOS_APARTH_BAJA as fecha_efectos
        FROM 
            solicitudes_baja_propuesta bp
        WHERE 
            bp.OFICIO_NUM_BAJA_PROP = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Error al preparar consulta: ' . $conexion->error]);
        exit();
    }
    
    $stmt->bind_param("s", $folio);
    
    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Error al ejecutar consulta: ' . $stmt->error]);
        exit();
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Solicitud no encontrada']);
        exit();
    }
    
    $solicitud = $result->fetch_assoc();
    
    // Formatear la respuesta con nombres que coincidan con el JavaScript
    $respuesta = [
        'folio' => $solicitud['folio'],
        'tipo' => 'baja-propuesta',
        'fecha' => $solicitud['fecha_creacion'] ? date('d/m/Y', strtotime($solicitud['fecha_creacion'])) : '',
        'hora' => $solicitud['hora_creacion'] ? date('H:i', strtotime($solicitud['hora_creacion'])) : '',
        'estado' => $solicitud['estado'] ?? '',
        'crn' => $solicitud['crn'] ?? '',
        'materia' => $solicitud['materia'] ?? '',
        'clave' => $solicitud['clave'] ?? '',
        'sec' => $solicitud['sec'] ?? '',
        'motivo' => $solicitud['motivo'] ?? '',
        'fecha_efectos' => $solicitud['fecha_efectos'] ? date('d/m/Y', strtotime($solicitud['fecha_efectos'])) : '',
        'periodo_desde' => $solicitud['periodo_desde'] ? date('d/m/Y', strtotime($solicitud['periodo_desde'])) : '',
        'periodo_hasta' => $solicitud['periodo_hasta'] ? date('d/m/Y', strtotime($solicitud['periodo_hasta'])) : '',
        
        // Datos de baja
        'num_puesto_teoria_baja' => $solicitud['num_puesto_teoria_baja'] ?? '',
        'num_puesto_practica_baja' => $solicitud['num_puesto_practica_baja'] ?? '',
        'hrs_teoria_baja' => $solicitud['hrs_teoria_baja'] ?? '',
        'hrs_practica_baja' => $solicitud['hrs_practica_baja'] ?? '',
        'carrera_baja' => $solicitud['carrera_baja'] ?? '',
        'tipo_asignacion_baja' => $solicitud['tipo_asignacion_baja'] ?? '',
        
        // Datos de propuesta
        'num_puesto_teoria_prop' => $solicitud['num_puesto_teoria_prop'] ?? '',
        'num_puesto_practica_prop' => $solicitud['num_puesto_practica_prop'] ?? '',
        'hrs_teoria_prop' => $solicitud['hrs_teoria_prop'] ?? '',
        'hrs_practica_prop' => $solicitud['hrs_practica_prop'] ?? '',
        'inter_temp_def' => $solicitud['inter_temp_def'] ?? '',
        'tipo_asignacion_prop' => $solicitud['tipo_asignacion_prop'] ?? '',
        
        'profesor_actual' => [
            'paterno' => $solicitud['profesor_actual_paterno'] ?? '',
            'materno' => $solicitud['profesor_actual_materno'] ?? '',
            'nombres' => $solicitud['profesor_actual_nombres'] ?? '',
            'codigo' => $solicitud['profesor_actual_codigo'] ?? '',
            'profession' => $solicitud['profession_actual'] ?? ''
        ],
        'profesor_propuesto' => [
            'paterno' => $solicitud['profesor_propuesto_paterno'] ?? '',
            'materno' => $solicitud['profesor_propuesto_materno'] ?? '',
            'nombres' => $solicitud['profesor_propuesto_nombres'] ?? '',
            'codigo' => $solicitud['profesor_propuesto_codigo'] ?? ''
        ]
    ];

    // Asegurar que no hay salida previa
    if (ob_get_length()) ob_clean();
    
    // Enviar la respuesta JSON
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Si hay algún error, devolver el mensaje de error
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}

exit();
?>
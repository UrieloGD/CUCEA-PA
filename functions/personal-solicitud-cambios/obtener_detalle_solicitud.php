<?php
session_start();
ini_set('display_errors', 1); // Oculta errores HTML
header('Content-Type: application/json'); // Asegura que la respuesta sea JSON
require_once './../../config/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Codigo'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener parámetros
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

if (empty($folio) || empty($tipo)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Parámetros incompletos']);
    exit();
}

try {
    // Consulta específica para solicitudes de baja
    if ($tipo === 'baja') {
        $sql = "SELECT 
                b.ID_BAJA as id,
                b.OFICIO_NUM_BAJA as folio,
                b.FECHA_SOLICITUD_B as fecha_creacion,
                b.HORA_CREACION as hora_creacion,
                b.ESTADO_B as estado,
                -- d.nombre as departamento,  -- Corregido a d.nombre
                b.CRN_B as crn,
                '' as materia,
                b.DESCRIPCION_PUESTO_B as puesto,
                b.CLASIFICACION_BAJA_B as clasificacion_b,
                b.SIN_EFFECTOS_DESDE_B as fecha_efecto,
                b.MOTIVO_B as motivo,
                b.APELLIDO_P_PROF_B as profesor_actual_paterno,
                b.APELLIDO_M_PROF_B as profesor_actual_materno,
                b.NOMBRES_PROF_B as profesor_actual_nombres,
                b.CODIGO_PROF_B as profesor_actual_codigo
            FROM solicitudes_baja b
            JOIN departamentos d ON b.Departamento_ID = d.Departamento_ID  -- JOIN corregido
            WHERE b.OFICIO_NUM_BAJA = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $folio);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Solicitud no encontrada']);
            exit();
        }
        
        $solicitud = $result->fetch_assoc();
        
        // Formatear la respuesta
        $respuesta = [
            'folio' => $solicitud['folio'],
            'tipo' => 'Solicitud de baja',
            'fecha' => date('d/m/Y', strtotime($solicitud['fecha_creacion'])),
            'hora' => date('H:i', strtotime($solicitud['hora_creacion'])),
            'estado' => $solicitud['estado'],
            'departamento' => $solicitud['departamento'],
            'crn' => $solicitud['crn'],
            'puesto' => $solicitud['puesto'],
            'materia' => $solicitud['materia'],
            'clasificacion_b' => $solicitud['clasificacion_b'],
            'efecto' => date('d/m/Y', strtotime($solicitud['fecha_efecto'])),
            'motivo' => $solicitud['motivo'],
            'profesor_actual' => [
                'paterno' => $solicitud['profesor_actual_paterno'],
                'materno' => $solicitud['profesor_actual_materno'],
                'nombres' => $solicitud['profesor_actual_nombres'],
                'codigo' => $solicitud['profesor_actual_codigo']
            ]
        ];
        
    } 
    // Consulta específica para solicitudes de propuesta
    else if ($tipo === 'propuesta') {
        $sql = "SELECT 
                p.ID_PROP as id,
                p.OFICIO_NUM_PROP as folio,
                p.FECHA_SOLICITUD_P as fecha_creacion,
                p.HORA_CREACION as hora_creacion,
                p.ESTADO_P as estado,
                d.Nombre as departamento,
                p.CRN_P as crn,
                '' as materia, -- Ajusta según corresponda
                p.DESCRIPCION_PUESTO_P as puesto,
                p.CLASIFICACION_PUESTO_P as clasificacion_p,
                p.HRS_SEMANALES_P as horas_sem,
                p.PERIODO_ASIG_DESDE_P as periodo_desde,
                p.PERIODO_ASIG_HASTA_P as periodo_hasta,
                p.CAUSA_P as motivo,
                p.PROFESSION_PROFESOR_P as profession,
                p.APELLIDO_P_PROF_P as profesor_actual_paterno,
                p.APELLIDO_M_PROF_P as profesor_actual_materno,
                p.NOMBRES_PROF_P as profesor_actual_nombres,
                p.CODIGO_PROF_P as profesor_actual_codigo,
                p.APELLIDO_P_PROF_SUST as profesor_propuesto_paterno,
                p.APELLIDO_M_PROF_SUST as profesor_propuesto_materno,
                p.NOMBRES_PROF_SUST as profesor_propuesto_nombres,
                p.CODIGO_PROF_SUST as profesor_propuesto_codigo
            FROM 
                solicitudes_propuesta p
            JOIN 
                departamentos d ON p.Departamento_ID = d.ID
            WHERE 
                p.OFICIO_NUM_PROP = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $folio);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Solicitud no encontrada']);
            exit();
        }
        
        $solicitud = $result->fetch_assoc();
        
        // Formatear la respuesta
        $respuesta = [
            'folio' => $solicitud['folio'],
            'tipo' => 'Solicitud de propuesta',
            'fecha' => date('d/m/Y', strtotime($solicitud['fecha_creacion'])),
            'hora' => date('H:i', strtotime($solicitud['hora_creacion'])),
            'estado' => $solicitud['estado'],
            'departamento' => $solicitud['departamento'],
            'crn' => $solicitud['crn'],
            'puesto' => $solicitud['puesto'],
            'materia' => $solicitud['materia'],
            'clasificacion_p' => $solicitud['clasificacion_p'],
            'horas_sem' => $solicitud['horas_sem'],
            'periodo_desde' => date('d/m/Y', strtotime($solicitud['periodo_desde'])),
            'periodo_hasta' => date('d/m/Y', strtotime($solicitud['periodo_hasta'])),
            'motivo' => $solicitud['motivo'],
            'profesor_actual' => [
                'paterno' => $solicitud['profesor_actual_paterno'],
                'materno' => $solicitud['profesor_actual_materno'],
                'nombres' => $solicitud['profesor_actual_nombres'],
                'codigo' => $solicitud['profesor_actual_codigo']
            ],
            'profesor_propuesto' => [
                'paterno' => $solicitud['profesor_propuesto_paterno'],
                'materno' => $solicitud['profesor_propuesto_materno'],
                'nombres' => $solicitud['profesor_propuesto_nombres'],
                'codigo' => $solicitud['profesor_propuesto_codigo']
            ]
        ];
        
    } 
    // Consulta específica para solicitudes de baja-propuesta
    else if ($tipo === 'baja-propuesta') {
        $sql = "SELECT 
                bp.ID_BAJA_PROP as id,
                bp.OFICIO_NUM_BAJA_PROP as folio,
                bp.FECHA_SOLICITUD_BAJA_PROP as fecha_creacion,
                bp.HORA_CREACION as hora_creacion,
                bp.ESTADO_P as estado,
                d.Nombre as departamento,
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
                bp.APELLIDO_P_PROF_PROP as profesor_propuesto_paterno,
                bp.APELLIDO_M_PROF_PROP as profesor_propuesto_materno,
                bp.NOMBRES_PROF_PROP as profesor_propuesto_nombres,
                bp.CODIGO_PROF_PROP as profesor_propuesto_codigo
            FROM 
                solicitudes_baja_propuesta bp
            JOIN 
                departamentos d ON bp.Departamento_ID = d.ID
            WHERE 
                bp.OFICIO_NUM_BAJA_PROP = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $folio);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Solicitud no encontrada']);
            exit();
        }
        
        $solicitud = $result->fetch_assoc();
        
        // Formatear la respuesta
        $respuesta = [
            'folio' => $solicitud['folio'],
            'tipo' => 'Solicitud de baja-propuesta',
            'fecha' => date('d/m/Y', strtotime($solicitud['fecha_creacion'])),
            'hora' => date('H:i', strtotime($solicitud['hora_creacion'])),
            'estado' => $solicitud['estado'],
            'departamento' => $solicitud['departamento'],
            'crn' => $solicitud['crn'],
            'materia' => $solicitud['materia'],
            'clave' => $solicitud['clave'],
            'sec' => $solicitud['sec'],
            'motivo' => $solicitud['motivo'],
            'profesor_actual' => [
                'paterno' => $solicitud['profesor_actual_paterno'],
                'materno' => $solicitud['profesor_actual_materno'],
                'nombres' => $solicitud['profesor_actual_nombres'],
                'codigo' => $solicitud['profesor_actual_codigo']
            ],
            'profesor_propuesto' => [
                'paterno' => $solicitud['profesor_propuesto_paterno'],
                'materno' => $solicitud['profesor_propuesto_materno'],
                'nombres' => $solicitud['profesor_propuesto_nombres'],
                'codigo' => $solicitud['profesor_propuesto_codigo']
            ]
        ];
        
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Tipo de solicitud no válido']);
        exit();
    }

    header('Content-Type: application/json');
    echo json_encode($respuesta);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
    exit();
}

// Verifica si la conexión a la base de datos es exitosa
if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit();
}

// Verifica si hay errores en la consulta preparada
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Error en la consulta: ' . $conexion->error]);
    exit();
}

// Verifica si hay resultados
if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Solicitud no encontrada']);
    exit();
}

// Después de obtener $respuesta:
ob_clean(); // Limpia cualquier salida no deseada
echo json_encode($respuesta);
exit();
?>
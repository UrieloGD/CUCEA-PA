<?php
session_start();
header('Content-Type: application/json'); 
require_once './../../config/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Codigo'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener parámetros
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'baja'; // Aceptamos tipo pero no lo usamos

if (empty($folio)) {
    echo json_encode(['error' => 'Folio no proporcionado']);
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
                b.CRN_B as crn,
                b.PROFESSION_PROFESOR_B as profession,
                b.DESCRIPCION_PUESTO_B as puesto,
                b.CLASIFICACION_BAJA_B as clasificacion_b,
                b.SIN_EFFECTOS_DESDE_B as fecha_efecto,
                b.MOTIVO_B as motivo,
                b.APELLIDO_P_PROF_B as profesor_actual_paterno,
                b.APELLIDO_M_PROF_B as profesor_actual_materno,
                b.NOMBRES_PROF_B as profesor_actual_nombres,
                b.CODIGO_PROF_B as profesor_actual_codigo
            FROM solicitudes_baja b
            JOIN departamentos d ON b.Departamento_ID = d.Departamento_ID
            WHERE b.OFICIO_NUM_BAJA = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $folio);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
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
            'crn' => $solicitud['crn'],
            'puesto' => $solicitud['puesto'],
            'clasificacion_b' => $solicitud['clasificacion_b'],
            'efecto' => date('d/m/Y', strtotime($solicitud['fecha_efecto'])),
            'motivo' => $solicitud['motivo'],
            'profession' => $solicitud['profession'] ?? '',
            'profesor_actual' => [
                'paterno' => $solicitud['profesor_actual_paterno'],
                'materno' => $solicitud['profesor_actual_materno'],
                'nombres' => $solicitud['profesor_actual_nombres'],
                'codigo' => $solicitud['profesor_actual_codigo'],
                'profession' => $solicitud['profession'] ?? ''
            ]
        ];
        
    } 

    // Asegurar que no hay salida previa
    if (ob_get_length()) ob_clean();
    
    // Enviar la respuesta JSON
    echo json_encode($respuesta);
    
} catch (Exception $e) {
    // Si hay algún error, devolver el mensaje de error
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}

exit();
?>
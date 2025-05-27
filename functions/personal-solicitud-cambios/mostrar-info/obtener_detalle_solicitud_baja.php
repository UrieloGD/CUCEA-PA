<?php
// obtener_detalle_solicitud_baja.php
error_reporting(0); // Desactivar reportes de error para evitar HTML en JSON
ini_set('display_errors', 0);

// Asegurar que solo se devuelva JSON
header('Content-Type: application/json; charset=utf-8');

include('./../../../config/db.php');

if (!isset($_GET['folio'])) {
    echo json_encode(['error' => 'Folio no proporcionado']);
    exit;
}

$folio = $_GET['folio'];

try {
    // Verificar conexión a la base de datos
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Consulta para obtener los datos de la solicitud de baja
    $sql = "SELECT 
                sb.OFICIO_NUM_BAJA as folio,
                sb.FECHA_SOLICITUD_B as fecha,
                sb.PROFESSION_PROFESOR_B as profession,
                sb.APELLIDO_P_PROF_B as paterno,
                sb.APELLIDO_M_PROF_B as materno,
                sb.NOMBRES_PROF_B as nombres,
                sb.CODIGO_PROF_B as codigo,
                sb.DESCRIPCION_PUESTO_B as puesto,
                sb.CRN_B as crn,
                sb.CLASIFICACION_BAJA_B as clasificacion_b,
                sb.SIN_EFFECTOS_DESDE_B as fecha_efectos,
                sb.MOTIVO_B as motivo,
                sb.ESTADO_B as estado,
                sb.NOMBRE_ARCHIVO_VALIDACION as archivo_nombre,
                sb.TIPO_ARCHIVO_VALIDACION as archivo_tipo,
                sb.TAMAÑO_ARCHIVO_VALIDACION as archivo_tamaño,
                CASE 
                    WHEN sb.ARCHIVO_ADJUNTO_VALIDACION IS NOT NULL 
                    THEN 1
                    ELSE 0
                END as tiene_archivo
            FROM solicitudes_baja sb 
            WHERE sb.OFICIO_NUM_BAJA = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error en la preparación de la consulta: ' . $conexion->error);
    }
    
    $stmt->bind_param("s", $folio);
    
    if (!$stmt->execute()) {
        throw new Exception('Error en la ejecución de la consulta: ' . $stmt->error);
    }
    
    $resultado = $stmt->get_result();
    
    if ($fila = $resultado->fetch_assoc()) {
        // Estructurar los datos para el frontend
        $response = [
            'folio' => $fila['folio'] ?? '',
            'fecha' => $fila['fecha'] ?? '',
            'crn' => $fila['crn'] ?? '',
            'puesto' => $fila['puesto'] ?? '',
            'clasificacion_b' => $fila['clasificacion_b'] ?? '',
            'fecha_efectos' => $fila['fecha_efectos'] ?? '',
            'motivo' => $fila['motivo'] ?? '',
            'estado' => $fila['estado'] ?? '',
            'profesor_actual' => [
                'profession' => $fila['profession'] ?? '',
                'paterno' => $fila['paterno'] ?? '',
                'materno' => $fila['materno'] ?? '',
                'nombres' => $fila['nombres'] ?? '',
                'codigo' => $fila['codigo'] ?? ''
            ],
            // Información del archivo adjunto
            'archivo_nombre' => $fila['archivo_nombre'] ?? null,
            'archivo_tipo' => $fila['archivo_tipo'] ?? null,
            'archivo_tamaño' => $fila['archivo_tamaño'] ?? null,
            'tiene_archivo' => (bool)$fila['tiene_archivo'],
            // Generar ruta del archivo si existe
            'archivo_ruta' => $fila['archivo_nombre'] ? 
                "./functions/personal-solicitud-cambios/descargar_archivo.php?folio=" . urlencode($fila['folio']) . "&tipo=baja" : 
                null
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['error' => 'Solicitud no encontrada']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>
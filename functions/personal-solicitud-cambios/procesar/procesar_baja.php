<?php
ob_start();
session_start();
date_default_timezone_set('America/Mexico_City');

ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_clean();
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception('Método no permitido');
    }

    $db_path = './../../../config/db.php';
    if (!file_exists($db_path)) {
        throw new Exception("El archivo de configuración de la base de datos no existe");
    }

    require_once $db_path;

    if (!isset($_SESSION['Codigo']) || !isset($_SESSION['Departamento_ID'])) {
        throw new Exception("Variables de sesión no encontradas");
    }

    // Validar campos requeridos
    $campos_requeridos = [
        'fecha', 'profesion', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'codigo_prof', 'descripcion', 'crn', 'clasificacion', 
        'fecha_efectos', 'motivo'
    ];

    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
            throw new Exception("El campo $campo es requerido");
        }
    }

    // Variables para archivo (inicializar como NULL)
    $archivo_adjunto = NULL;
    $nombre_archivo = NULL;
    $tipo_archivo = NULL;
    $tamaño_archivo = NULL;
    $archivo_procesado = false;

    // Procesar archivo si existe
    if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo_adjunto'];
        
        error_log("Procesando archivo: " . $archivo['name'] . " (" . $archivo['size'] . " bytes)");
        
        // Validaciones
        $tipos_permitidos = [
            'application/pdf', 
            'image/jpeg', 
            'image/jpg', 
            'image/png', 
            'image/gif'
        ];
        $tamaño_maximo = 5 * 1024 * 1024; // 5MB
        
        // Usar finfo para detectar tipo real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipo_real = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipo_real, $tipos_permitidos)) {
            throw new Exception('Tipo de archivo no permitido. Detectado: ' . $tipo_real);
        }
        
        if ($archivo['size'] > $tamaño_maximo) {
            throw new Exception('Archivo demasiado grande. Máximo: 5MB');
        }
        
        // Leer archivo como base64 para evitar problemas con caracteres binarios
        $contenido_archivo = file_get_contents($archivo['tmp_name']);
        if ($contenido_archivo === false) {
            throw new Exception('Error al leer el archivo');
        }
        
        $archivo_adjunto = base64_encode($contenido_archivo);
        $nombre_archivo = $archivo['name'];
        $tipo_archivo = $tipo_real;
        $tamaño_archivo = $archivo['size'];
        $archivo_procesado = true;
        
        error_log("Archivo procesado exitosamente: " . strlen($archivo_adjunto) . " caracteres en base64");
    }

    // Generar número de oficio
    $anio_actual = date('y');
    $siguiente_numero = '0001';

    $sql_ultimo = "SELECT OFICIO_NUM_BAJA FROM solicitudes_baja 
                   WHERE OFICIO_NUM_BAJA LIKE ? 
                   ORDER BY ID_BAJA DESC LIMIT 1";
    
    $stmt = $conexion->prepare($sql_ultimo);
    if (!$stmt) {
        throw new Exception("Error al preparar consulta de número: " . $conexion->error);
    }
    
    $pattern = "SA/CP/%/$anio_actual";
    $stmt->bind_param("s", $pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $ultimo = $result->fetch_assoc()['OFICIO_NUM_BAJA'];
        if (preg_match('/SA\/CP\/(\d{4})\/'.$anio_actual.'/', $ultimo, $matches)) {
            $ultimo_numero = intval($matches[1]);
            $siguiente_numero = str_pad($ultimo_numero + 1, 4, '0', STR_PAD_LEFT);
        }
    }

    $oficio_num = "SA/CP/$siguiente_numero/$anio_actual";

    // Preparar inserción CON campos de archivo
    $sql = "INSERT INTO solicitudes_baja (
        USUARIO_ID, Departamento_ID, OFICIO_NUM_BAJA, FECHA_SOLICITUD_B, 
        HORA_CREACION, PROFESSION_PROFESOR_B, APELLIDO_P_PROF_B, 
        APELLIDO_M_PROF_B, NOMBRES_PROF_B, CODIGO_PROF_B, 
        DESCRIPCION_PUESTO_B, CRN_B, CLASIFICACION_BAJA_B, 
        SIN_EFFECTOS_DESDE_B, MOTIVO_B, ESTADO_B,
        ARCHIVO_ADJUNTO_VALIDACION, NOMBRE_ARCHIVO_VALIDACION,
        TIPO_ARCHIVO_VALIDACION, TAMAÑO_ARCHIVO_VALIDACION
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $conexion->error);
    }

    $estado = 'Pendiente';
    $hora_actual = date('H:i:s');
    $usuario_id = $_SESSION['Codigo'];
    $departamento_id = $_SESSION['Departamento_ID'];

    // Todos los parámetros como string para evitar problemas
    $stmt->bind_param("iisssssssisssssssssi",
        $usuario_id,                    // i
        $departamento_id,               // i
        $oficio_num,                    // s
        $_POST['fecha'],                // s
        $hora_actual,                   // s
        $_POST['profesion'],            // s
        $_POST['apellido_paterno'],     // s
        $_POST['apellido_materno'],     // s
        $_POST['nombres'],              // s
        $_POST['codigo_prof'],          // s
        $_POST['descripcion'],          // s
        $_POST['crn'],                  // s
        $_POST['clasificacion'],        // s
        $_POST['fecha_efectos'],        // s
        $_POST['motivo'],               // s
        $estado,                        // s
        $archivo_adjunto,               // s (base64)
        $nombre_archivo,                // s
        $tipo_archivo,                  // s
        $tamaño_archivo                 // i
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar consulta: " . $stmt->error);
    }

    // Respuesta exitosa
    $response = [
        'status' => 'success',
        'message' => 'Solicitud de baja guardada exitosamente',
        'oficio_num' => $oficio_num
    ];

    // Agregar información del archivo si se procesó
    if ($archivo_procesado) {
        $response['archivo_info'] = [
            'nombre' => $nombre_archivo,
            'tipo' => $tipo_archivo,
            'tamaño' => $tamaño_archivo . ' bytes'
        ];
        $response['message'] .= ' (con archivo adjunto)';
    }

    error_log("Solicitud guardada exitosamente. ID: " . $conexion->insert_id);

    echo json_encode($response);

} catch (Exception $e) {
    ob_clean();
    error_log("Error en procesar_baja.php: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit();
?>
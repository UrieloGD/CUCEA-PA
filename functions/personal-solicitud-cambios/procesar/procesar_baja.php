<?php
session_start();
date_default_timezone_set('America/Mexico_City');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log("Iniciando procesar_baja.php");

header('Content-Type: application/json');

try {
    // Verificar si el archivo existe
    $db_path = './../../../config/db.php';
    if (!file_exists($db_path)) {
        throw new Exception("El archivo de configuración de la base de datos no existe");
    }

    require_once $db_path;
    error_log("Archivo de configuración cargado");

    // Verificar variables de sesión
    if (!isset($_SESSION['Codigo']) || !isset($_SESSION['Departamento_ID'])) {
        throw new Exception("Variables de sesión no encontradas");
    }

    error_log("POST data recibida: " . print_r($_POST, true));
    error_log("FILES data recibida: " . print_r($_FILES, true));

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception('Método no permitido');
    }

    // Validar que todos los campos necesarios estén presentes
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

    // Variables para el archivo adjunto (NUEVO CAMPO SEPARADO)
    $archivo_adjunto_validacion = null;
    $nombre_archivo_validacion = null;
    $tipo_archivo_validacion = null;
    $tamaño_archivo_validacion = null;

    // Procesar archivo adjunto si existe
    if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo_adjunto'];
        
        // Procesar archivo adjunto si existe
    if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo_adjunto'];
        
        // Validaciones del archivo (mantener igual)
        $tipos_permitidos = [
            'application/pdf', 
            'image/jpeg', 
            'image/jpg', 
            'image/png', 
            'image/gif'
        ];
        $tamaño_maximo = 5 * 1024 * 1024; // 5MB en bytes
        
        // Validar tipo de archivo usando finfo para mayor seguridad
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipo_real = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipo_real, $tipos_permitidos) && !in_array($archivo['type'], $tipos_permitidos)) {
            throw new Exception('Tipo de archivo no permitido. Solo se aceptan PDF, JPG, PNG y GIF.');
        }
        
        if ($archivo['size'] > $tamaño_maximo) {
            throw new Exception('El archivo es demasiado grande. Tamaño máximo: 5MB.');
        }
        
        if (!is_uploaded_file($archivo['tmp_name'])) {
            throw new Exception('Error en la subida del archivo.');
        }
        
        // CORRECCIÓN: Leer el archivo como binario
        $archivo_adjunto_validacion = file_get_contents($archivo['tmp_name']);
        if ($archivo_adjunto_validacion === false) {
            throw new Exception('Error al leer el archivo.');
        }
        
        $nombre_archivo_validacion = $archivo['name'];
        $tipo_archivo_validacion = $tipo_real; // Usar el tipo real detectado por finfo
        $tamaño_archivo_validacion = $archivo['size'];
        
        error_log("Archivo procesado: " . $nombre_archivo_validacion . " (" . $tipo_archivo_validacion . ", " . $tamaño_archivo_validacion . " bytes)");
        
    } else if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Manejar errores de subida
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario.',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal.',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION => 'Una extensión PHP detuvo la subida del archivo.'
        ];
        
        $error_code = $_FILES['archivo_adjunto']['error'];
        $error_message = isset($error_messages[$error_code]) ? $error_messages[$error_code] : 'Error desconocido en la subida del archivo.';
        throw new Exception($error_message);
    }

    // Generar número de oficio
    $anio_actual = date('y');
    $siguiente_numero = '0001';

    $sql_ultimo = "SELECT OFICIO_NUM_BAJA FROM solicitudes_baja 
                   WHERE OFICIO_NUM_BAJA LIKE ? 
                   ORDER BY ID_BAJA DESC LIMIT 1";
    
    $stmt = $conexion->prepare($sql_ultimo);
    $pattern = "SA/CP/%/$anio_actual";
    $stmt->bind_param("s", $pattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $ultimo = $result->fetch_assoc()['OFICIO_NUM_BAJA'];
        preg_match('/SA\/CP\/(\d{4})\/'.$anio_actual.'/', $ultimo, $matches);
        $ultimo_numero = intval($matches[1]);
        $siguiente_numero = str_pad($ultimo_numero + 1, 4, '0', STR_PAD_LEFT);
    }

    $oficio_num = "SA/CP/$siguiente_numero/$anio_actual";

    // Preparar la inserción - SOLO agregamos los nuevos campos, mantenemos todos los existentes
    $sql = "INSERT INTO solicitudes_baja (
        USUARIO_ID, 
        Departamento_ID, 
        OFICIO_NUM_BAJA, 
        FECHA_SOLICITUD_B, 
        HORA_CREACION, 
        PROFESSION_PROFESOR_B,
        APELLIDO_P_PROF_B, 
        APELLIDO_M_PROF_B, 
        NOMBRES_PROF_B,
        CODIGO_PROF_B, 
        DESCRIPCION_PUESTO_B, 
        CRN_B,
        CLASIFICACION_BAJA_B, 
        SIN_EFFECTOS_DESDE_B, 
        MOTIVO_B, 
        ESTADO_B,
        ARCHIVO_ADJUNTO_VALIDACION,
        NOMBRE_ARCHIVO_VALIDACION,
        TIPO_ARCHIVO_VALIDACION,
        TAMAÑO_ARCHIVO_VALIDACION
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $estado = 'Pendiente';
    $hora_actual = date('H:i:s');
    $usuario_id = $_SESSION['Codigo'];
    $departamento_id = $_SESSION['Departamento_ID'];

    // CORRECCIÓN: Usar 'b' para datos binarios (BLOB)
    $stmt->bind_param("iisssssssisssssssbsi",
        $usuario_id,                        // i - int
        $departamento_id,                   // i - int  
        $oficio_num,                        // s - string
        $_POST['fecha'],                    // s - string (date)
        $hora_actual,                       // s - string (time)
        $_POST['profesion'],                // s - string
        $_POST['apellido_paterno'],         // s - string
        $_POST['apellido_materno'],         // s - string
        $_POST['nombres'],                  // s - string
        $_POST['codigo_prof'],              // s - string
        $_POST['descripcion'],              // s - string
        $_POST['crn'],                      // i - int
        $_POST['clasificacion'],            // s - string
        $_POST['fecha_efectos'],            // s - string (date)
        $_POST['motivo'],                   // s - string
        $estado,                            // s - string
        $archivo_adjunto_validacion,        // b - BLOB (CORREGIDO)
        $nombre_archivo_validacion,         // s - string
        $tipo_archivo_validacion,           // s - string
        $tamaño_archivo_validacion          // i - int
    );

    // CORRECCIÓN: Enviar datos largos para el BLOB
    if ($archivo_adjunto_validacion !== null) {
        $stmt->send_long_data(16, $archivo_adjunto_validacion); // 16 es el índice del BLOB
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $response = [
        'status' => 'success',
        'message' => 'Solicitud guardada exitosamente',
        'oficio_num' => $oficio_num
    ];

    // Agregar información del archivo si se subió
    if ($archivo_adjunto_validacion !== null) {
        $response['archivo_info'] = [
            'nombre' => $nombre_archivo_validacion,
            'tipo' => $tipo_archivo_validacion,
            'tamaño' => $tamaño_archivo_validacion,
            'guardado_en' => 'base_de_datos_campo_separado'
        ];
        $response['message'] .= ' (con archivo adjunto)';
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error en procesar_baja.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit();
?>
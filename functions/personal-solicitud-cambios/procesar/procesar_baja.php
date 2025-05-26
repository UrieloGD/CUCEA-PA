<?php
session_start();
date_default_timezone_set('America/Mexico_City');
error_reporting(E_ALL);
ini_set('display_errors', 1); // Cambiamos a 1 temporalmente para ver errores
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

    // Preparar la inserción
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
        ESTADO_B
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $estado = 'Pendiente';
    $hora_actual = date('H:i:s');
    $usuario_id = $_SESSION['Codigo'];
    $departamento_id = $_SESSION['Departamento_ID'];

    $stmt->bind_param("iissssssssssssss",
        $usuario_id,
        $departamento_id,
        $oficio_num,
        $_POST['fecha'],
        $hora_actual,
        $_POST['profesion'],
        $_POST['apellido_paterno'],
        $_POST['apellido_materno'],
        $_POST['nombres'],
        $_POST['codigo_prof'],
        $_POST['descripcion'],
        $_POST['crn'],
        $_POST['clasificacion'],
        $_POST['fecha_efectos'],
        $_POST['motivo'],
        $estado
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Solicitud guardada exitosamente',
        'oficio_num' => $oficio_num
    ]);

} catch (Exception $e) {
    error_log("Error en procesar_baja.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit();
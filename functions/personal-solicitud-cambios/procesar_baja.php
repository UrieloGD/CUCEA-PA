<?php
// procesar_baja.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json'); // Importante para la respuesta JSON

try {
    require_once './../../config/db.php';

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception('Método no permitido');
    }

    // Obtener año actual en 2 dígitos (24 para 2024)
    $anio_actual = date('y');

    // Obtener el último número de oficio DEL AÑO ACTUAL
    $sql_ultimo = "SELECT OFICIO_NUM_BAJA FROM solicitudes_baja 
                   WHERE OFICIO_NUM_BAJA LIKE 'SA/CP/%/$anio_actual' 
                   ORDER BY ID_BAJA DESC LIMIT 1";
    $result = $conexion->query($sql_ultimo);
    
    if ($result && $result->num_rows > 0) {
        $ultimo = $result->fetch_assoc()['OFICIO_NUM_BAJA'];
        // Extraer el número de la cadena usando el año actual
        preg_match('/SA\/CP\/(\d{4})\/'.$anio_actual.'/', $ultimo, $matches);
        $ultimo_numero = intval($matches[1]);
        $siguiente_numero = str_pad($ultimo_numero + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $siguiente_numero = '0001'; // Primer número del año
    }
    
    // Crear número de oficio con año actual
    $oficio_num = "SA/CP/$siguiente_numero/$anio_actual";

    // Validar el resto de campos requeridos
    $campos_requeridos = [
        'fecha' => 'Fecha',
        'profesion' => 'Profesión',
        'apellido_paterno' => 'Apellido paterno',
        'apellido_materno' => 'Apellido materno',
        'nombres' => 'Nombres',
        'codigo' => 'Código',
        'descripcion' => 'Descripción',
        'crn' => 'CRN',
        'clasificacion' => 'Clasificación',
        'fecha_efectos' => 'Fecha de efectos',
        'motivo' => 'Motivo'
    ];
    
    $errores = [];
    foreach ($campos_requeridos as $campo => $nombre) {
        if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
            $errores[] = "El campo $nombre es requerido";
        }
    }
    
    if (!empty($errores)) {
        throw new Exception(implode(", ", $errores));
    }

    // Procesar los datos
    $fecha_solicitud = $_POST['fecha'];
    $profesion = $_POST['profesion'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $nombres = $_POST['nombres'];
    $codigo = intval($_POST['codigo']);
    $descripcion = $_POST['descripcion'];
    $crn = intval($_POST['crn']);
    $clasificacion = $_POST['clasificacion'];
    $fecha_efectos = $_POST['fecha_efectos'];
    $motivo = $_POST['motivo'];
    $estado = 'Pendiente';

    $sql = "INSERT INTO solicitudes_baja (
        OFICIO_NUM_BAJA, FECHA_SOLICITUD_B, PROFESSION_PROFESOR_B,
        APELLIDO_P_PROF_B, APELLIDO_M_PROF_B, NOMBRES_PROF_B,
        CODIGO_PROF_B, DESCRIPCION_PUESTO_B, CRN_B,
        CLASIFICACION_BAJA_B, SIN_EFFECTOS_DESDE_B, MOTIVO_B, ESTADO_B
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("ssssssissssss",
        $oficio_num, $fecha_solicitud, $profesion,
        $apellido_paterno, $apellido_materno, $nombres,
        $codigo, $descripcion, $crn,
        $clasificacion, $fecha_efectos, $motivo, $estado
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
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
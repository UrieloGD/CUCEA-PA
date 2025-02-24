<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        require_once './../../config/db.php';
        
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['Codigo'])) {
            throw new Exception('Usuario no autenticado');
        }
        
        // Obtener datos del formulario
        $usuario_id = $_SESSION['Codigo'];
        $profesion = isset($_POST['profesion_p']) ? $_POST['profesion_p'] : '';
        $nombres = isset($_POST['nombres_p']) ? $_POST['nombres_p'] : '';
        $apellido_paterno = isset($_POST['apellido_paterno_p']) ? $_POST['apellido_paterno_p'] : '';
        $apellido_materno = isset($_POST['apellido_materno_p']) ? $_POST['apellido_materno_p'] : '';
        $codigo_prof = isset($_POST['codigo_prof_p']) ? $_POST['codigo_prof_p'] : '';
        $dia = isset($_POST['dia_p']) ? $_POST['dia_p'] : '';
        $mes = isset($_POST['mes_p']) ? $_POST['mes_p'] : '';
        $ano = isset($_POST['ano_p']) ? $_POST['ano_p'] : '';
        $descripcion = isset($_POST['descripcion_p']) ? $_POST['descripcion_p'] : '';
        $codigo_puesto = isset($_POST['codigo_puesto_p']) ? $_POST['codigo_puesto_p'] : '';
        $clasificacion = isset($_POST['clasificacion_p']) ? $_POST['clasificacion_p'] : '';
        $hrs_semanales = isset($_POST['hrs_semanales']) ? intval($_POST['hrs_semanales']) : 0;
        $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
        $carrera = isset($_POST['carrera']) ? $_POST['carrera'] : '';
        $crn = isset($_POST['crn_p']) ? $_POST['crn_p'] : '';
        $num_puesto = isset($_POST['num_puesto']) ? intval($_POST['num_puesto']) : 0;
        $cargo_atc = isset($_POST['cargo_atc']) && $_POST['cargo_atc'] == 'Si' ? 1 : 0;
        
        // Datos del profesor a sustituir
        $nombres_sust = isset($_POST['nombres_sust']) ? $_POST['nombres_sust'] : '';
        $apellido_paterno_sust = isset($_POST['apellido_paterno_sust']) ? $_POST['apellido_paterno_sust'] : '';
        $apellido_materno_sust = isset($_POST['apellido_materno_sust']) ? $_POST['apellido_materno_sust'] : '';
        $codigo_prof_sust = isset($_POST['codigo_prof_sust']) ? $_POST['codigo_prof_sust'] : '';
        $causa = isset($_POST['causa']) ? $_POST['causa'] : '';
        $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
        $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    
        // Generar número de oficio único
        $oficio_num = mt_rand(10000, 99999);
        
        // Verificar la conexión
        if (!$conexion) {
            throw new Exception('Error de conexión a la base de datos: ' . mysqli_connect_error());
        }
        
        // Verificar que tenemos el departamento_id
        if (!isset($_SESSION['Departamento_ID'])) {
            $departamento_id = 1; // Valor por defecto si no está en sesión
        } else {
            $departamento_id = $_SESSION['Departamento_ID'];
        }
        
        // Corrigiendo la consulta SQL para asegurar que coincide con bind_param
        $sql = "INSERT INTO solicitudes_propuesta (
            USUARIO_ID, OFICIO_NUM_PROP, FECHA_SOLICITUD_P, PROFESSION_PROFESOR_P,
            APELLIDO_P_PROF_P, APELLIDO_M_PROF_P, NOMBRES_PROF_P, CODIGO_PROF_P,
            DIA_P, MES_P, ANO_P, DESCRIPCION_PUESTO_P, CODIGO_PUESTO_P,
            CLASIFICACION_PUESTO_P, HRS_SEMANALES_P, CATEGORIA_P, CARRIERA_PROF_P,
            CRN_P, NUM_PUESTO_P, CARGO_ATC_P, CODIGO_PROF_SUST, APELLIDO_P_PROF_SUST,
            APELLIDO_M_PROF_SUST, NOMBRES_PROF_SUST, CAUSA_P, PERIODO_ASIG_DESDE_P,
            PERIODO_ASIG_HASTA_P, ESTADO_P, HORA_CREACION, Departamento_ID
        ) VALUES (
            ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente', CURTIME(), ?
        )";
    
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $conexion->error);
        }

        $placeholders_count = substr_count($sql, '?');
        $types = "iissssiiiisssissiisissssssi"; // Tu cadena de tipos actual
        $types_count = strlen($types);
        $variables_count = 27; // Ajusta este número al número correcto de variables

        // Comprueba que todo coincide
        if ($placeholders_count !== $types_count || $types_count !== $variables_count) {
            echo json_encode([
                'success' => false,
                'message' => "Error: Desajuste en los parámetros. Marcadores: $placeholders_count, Tipos: $types_count, Variables: $variables_count"
            ]);
            exit;
        }
    
        $stmt->bind_param("iissssiiiisssissiisissssssi",
            $usuario_id, $oficio_num, $profesion, $apellido_paterno, $apellido_materno,
            $nombres, $codigo_prof, $dia, $mes, $ano, $descripcion, $codigo_puesto,
            $clasificacion, $hrs_semanales, $categoria, $carrera, $crn, $num_puesto,
            $cargo_atc, $codigo_prof_sust, $apellido_paterno_sust, $apellido_materno_sust,
            $nombres_sust, $causa, $fecha_inicio, $fecha_fin, $departamento_id
        );
    
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Propuesta guardada exitosamente',
                'oficio_num' => $oficio_num
            ]);
        } else {
            throw new Exception('Error al guardar en la base de datos: ' . $stmt->error);
        }
        
        $stmt->close();
        $conexion->close();
        
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
        exit;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de solicitud no válido'
    ]);
}
exit;
?>
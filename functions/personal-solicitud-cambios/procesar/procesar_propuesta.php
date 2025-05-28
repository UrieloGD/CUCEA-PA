<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        require_once './../../../config/db.php';
        
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['Codigo'])) {
            throw new Exception('Usuario no autenticado');
        }

        // Inicializar variables de archivo
        $archivo_adjunto_validacion = null;
        $nombre_archivo_validacion = null;
        $tipo_archivo_validacion = null;
        $tamaño_archivo_validacion = null;

        // Procesar archivo adjunto si se subió uno
        if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['archivo_adjunto'];
            
            // Validar tipo de archivo
            $tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipo_real = finfo_file($finfo, $archivo['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($tipo_real, $tipos_permitidos)) {
                throw new Exception('Tipo de archivo no permitido. Solo se permiten PDF e imágenes.');
            }
            
            // Validar tamaño (5MB máximo)
            if ($archivo['size'] > 5 * 1024 * 1024) {
                throw new Exception('El archivo es demasiado grande. Máximo 5MB.');
            }
            
            // Leer el archivo
            $archivo_adjunto_validacion = file_get_contents($archivo['tmp_name']);
            $nombre_archivo_validacion = $archivo['name'];
            $tipo_archivo_validacion = $tipo_real;
            $tamaño_archivo_validacion = $archivo['size'];
        }
        
        // Obtener datos del formulario
        $usuario_id = $_SESSION['Codigo'];
        $profesion = isset($_POST['profesion_p']) ? $_POST['profesion_p'] : '';
        $nombres = isset($_POST['nombres_p']) ? $_POST['nombres_p'] : '';
        $apellido_paterno = isset($_POST['apellido_paterno_p']) ? $_POST['apellido_paterno_p'] : '';
        $apellido_materno = isset($_POST['apellido_materno_p']) ? $_POST['apellido_materno_p'] : '';
        $codigo_prof = isset($_POST['codigo_prof_p']) ? intval($_POST['codigo_prof_p']) : 0;
        $dia = isset($_POST['dia_p']) ? intval($_POST['dia_p']) : 0;
        $mes = isset($_POST['mes_p']) ? intval($_POST['mes_p']) : 0;
        $ano = isset($_POST['ano_p']) ? intval($_POST['ano_p']) : 0;
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
        $codigo_prof_sust = isset($_POST['codigo_prof_sust']) ? intval($_POST['codigo_prof_sust']) : 0;
        $causa = isset($_POST['causa']) ? $_POST['causa'] : '';
        $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
        $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

        // Obtener número de oficio del formulario
        $oficio_num = isset($_POST['oficio_num_prop']) ? $_POST['oficio_num_prop'] : '';
        
        // Si no hay número de oficio, generarlo
        if (empty($oficio_num)) {
            // Generar número de oficio
            $año_actual = date('y'); // Año de 2 dígitos
            
            // Obtener el último número de oficio del año actual
            $query_ultimo = "SELECT MAX(CAST(SUBSTRING_INDEX(OFICIO_NUM_PROP, '/', 1) AS UNSIGNED)) as ultimo_numero 
                           FROM solicitudes_propuesta 
                           WHERE YEAR(FECHA_SOLICITUD_P) = YEAR(CURDATE())";
            
            $result_ultimo = mysqli_query($conexion, $query_ultimo);
            $ultimo_numero = 0;
            
            if ($result_ultimo && $row = mysqli_fetch_assoc($result_ultimo)) {
                $ultimo_numero = intval($row['ultimo_numero']);
            }
            
            $nuevo_numero = $ultimo_numero + 1;
            $oficio_num = sprintf("%04d/%s", $nuevo_numero, $año_actual);
        }
                
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
        
        // Preparar la consulta SQL
        $sql = "INSERT INTO solicitudes_propuesta (
            USUARIO_ID, OFICIO_NUM_PROP, FECHA_SOLICITUD_P, PROFESSION_PROFESOR_P,
            APELLIDO_P_PROF_P, APELLIDO_M_PROF_P, NOMBRES_PROF_P, CODIGO_PROF_P,
            DIA_P, MES_P, ANO_P, DESCRIPCION_PUESTO_P, CODIGO_PUESTO_P,
            CLASIFICACION_PUESTO_P, HRS_SEMANALES_P, CATEGORIA_P, CARRIERA_PROF_P,
            CRN_P, NUM_PUESTO_P, CARGO_ATC_P, CODIGO_PROF_SUST, APELLIDO_P_PROF_SUST,
            APELLIDO_M_PROF_SUST, NOMBRES_PROF_SUST, CAUSA_P, PERIODO_ASIG_DESDE_P,
            PERIODO_ASIG_HASTA_P, ESTADO_P, HORA_CREACION, Departamento_ID,
            ARCHIVO_ADJUNTO_VALIDACION, NOMBRE_ARCHIVO_VALIDACION, TIPO_ARCHIVO_VALIDACION, TAMAÑO_ARCHIVO_VALIDACION
        ) VALUES (
            ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente', CURTIME(), ?, ?, ?, ?, ?
        )";
    
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $conexion->error);
        }

        $stmt->bind_param("isssssiiiisssisssiiissssssisssi",
            $usuario_id,                   // 1  - i
            $oficio_num,                   // 2  - s
            $profesion,                    // 3  - s
            $apellido_paterno,             // 4  - s
            $apellido_materno,             // 5  - s
            $nombres,                      // 6  - s
            $codigo_prof,                  // 7  - i
            $dia,                          // 8  - i
            $mes,                          // 9  - i
            $ano,                          // 10 - i
            $descripcion,                  // 11 - s
            $codigo_puesto,                // 12 - s
            $clasificacion,                // 13 - s
            $hrs_semanales,                // 14 - i
            $categoria,                    // 15 - s
            $carrera,                      // 16 - s
            $crn,                          // 17 - s
            $num_puesto,                   // 18 - i
            $cargo_atc,                    // 19 - i
            $codigo_prof_sust,             // 20 - i
            $apellido_paterno_sust,        // 21 - s
            $apellido_materno_sust,        // 22 - s
            $nombres_sust,                 // 23 - s
            $causa,                        // 24 - s
            $fecha_inicio,                 // 25 - s
            $fecha_fin,                    // 26 - s
            $departamento_id,              // 27 - i
            $archivo_adjunto_validacion,   // 28 - s
            $nombre_archivo_validacion,    // 29 - s
            $tipo_archivo_validacion,      // 30 - s
            $tamaño_archivo_validacion     // 31 - i
        );

        // Para archivos grandes, usar send_long_data si es necesario
        if ($archivo_adjunto_validacion !== null) {
            $stmt->send_long_data(27, $archivo_adjunto_validacion);
        }  
    
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Propuesta guardada exitosamente',
                'oficio_num_prop' => $oficio_num
            ]);
        } else {
            throw new Exception('Error al guardar en la base de datos: ' . $stmt->error);
        }
        
        $stmt->close();
        $conexion->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error inesperado: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de solicitud no válido'
    ]);
}
?>
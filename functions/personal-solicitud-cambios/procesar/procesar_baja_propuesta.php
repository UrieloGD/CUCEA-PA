<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        require_once './../../../config/db.php';
        
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
            
            // Verificar que se leyó correctamente
            if ($archivo_adjunto_validacion === false) {
                throw new Exception('Error al leer el archivo');
            }
        }
        
        // Obtener datos del formulario para la baja
        $usuario_id = $_SESSION['Codigo'];
        
        if (isset($_SESSION['Departamento_ID'])) {
            $departamento_id = $_SESSION['Departamento_ID'];
        } else {
            $sql_dept = "SELECT Departamento_ID FROM usuarios WHERE Codigo = ?";
            $stmt_dept = $conexion->prepare($sql_dept);
            $stmt_dept->bind_param("i", $usuario_id);
            $stmt_dept->execute();
            $result_dept = $stmt_dept->get_result();
            
            if ($row_dept = $result_dept->fetch_assoc()) {
                $departamento_id = $row_dept['Departamento_ID'];
                $_SESSION['Departamento_ID'] = $departamento_id;
            } else {
                $departamento_id = 17;
            }
            
            $stmt_dept->close();
        }
        
        // Obtener datos del formulario
        $nombres_baja = $_POST['nombres_baja'] ?? '';
        $apellido_paterno_baja = $_POST['apellido_paterno_baja'] ?? '';
        $apellido_materno_baja = $_POST['apellido_materno_baja'] ?? '';
        $codigo_prof_baja = $_POST['codigo_prof_baja'] ?? '';
        $profesion_baja = $_POST['profesion_baja'] ?? '';
        $num_puesto_teoria_baja = $_POST['num_puesto_teoria_baja'] ?? '';
        $num_puesto_practica_baja = $_POST['num_puesto_practica_baja'] ?? '';
        $cve_materia_baja = $_POST['cve_materia_baja'] ?? '';
        $nombre_materia_baja = $_POST['nombre_materia_baja'] ?? '';
        $crn_baja = $_POST['crn_baja'] ?? '';
        $hrs_teoria_baja = $_POST['hrs_teoria_baja'] ?? 0;
        $hrs_practica_baja = $_POST['hrs_practica_baja'] ?? 0;
        $carrera_baja = $_POST['carrera_baja'] ?? '';
        $gdo_gpo_turno_baja = $_POST['gdo_gpo_turno_baja'] ?? '';
        $tipo_asignacion_baja = $_POST['tipo_asignacion_baja'] ?? '';
        $sin_efectos_baja = $_POST['sin_efectos_baja'] ?? '';
        $motivo_baja = $_POST['motivo_baja'] ?? '';

        $nombres_prop = $_POST['nombres_prop'] ?? '';
        $apellido_paterno_prop = $_POST['apellido_paterno_prop'] ?? '';
        $apellido_materno_prop = $_POST['apellido_materno_prop'] ?? '';
        $codigo_prof_prop = $_POST['codigo_prof_prop'] ?? '';
        $num_puesto_teoria_prop = $_POST['num_puesto_teoria_prop'] ?? '';
        $num_puesto_practica_prop = $_POST['num_puesto_practica_prop'] ?? '';
        $hrs_teoria_prop = !empty($_POST['hrs_teoria_prop']) ? intval($_POST['hrs_teoria_prop']) : 0;
        $hrs_practica_prop = !empty($_POST['hrs_practica_prop']) ? intval($_POST['hrs_practica_prop']) : 0;
        $inter_temp_def_prop = $_POST['inter_temp_def_prop'] ?? '';
        $tipo_asignacion_prop = $_POST['tipo_asignacion_prop'] ?? '';
        $periodo_desde_prop = $_POST['periodo_desde_prop'] ?? '';
        $periodo_hasta_prop = $_POST['periodo_hasta_prop'] ?? '';

        // Generar folio consecutivo
        $year = date('y');
        $sql_count = "SELECT MAX(SUBSTRING_INDEX(OFICIO_NUM_BAJA_PROP, '/', 1)) as ultimo_numero 
                    FROM solicitudes_baja_propuesta 
                    WHERE OFICIO_NUM_BAJA_PROP LIKE '%/$year'";
        $result_count = $conexion->query($sql_count);
        $row = $result_count->fetch_assoc();
        $ultimo_numero = $row['ultimo_numero'] ? intval($row['ultimo_numero']) : 0;
        $nuevo_numero = $ultimo_numero + 1;
        $oficio_num = sprintf('%04d/%s', $nuevo_numero, $year);

        $sql = "INSERT INTO solicitudes_baja_propuesta (
            USUARIO_ID, 
            OFICIO_NUM_BAJA_PROP, 
            FECHA_SOLICITUD_BAJA_PROP,
            PROFESSION_PROFESOR_BAJA,
            APELLIDO_P_PROF_BAJA,
            APELLIDO_M_PROF_BAJA,
            NOMBRES_PROF_BAJA,
            CODIGO_PROF_BAJA,
            NUM_PUESTO_TEORIA_BAJA,
            NUM_PUESTO_PRACTICA_BAJA,
            CVE_MATERIA_BAJA,
            NOMBRE_MATERIA_BAJA,
            CRN_BAJA,
            HRS_SEM_MES_TEORIA_BAJA,
            HRS_SEM_MES_PRACTICA_BAJA,
            CARRERA_BAJA,
            GDO_GPO_TURNO_BAJA,
            TIPO_ASIGNACION_BAJA,
            SIN_EFFECTOS_APARTH_BAJA,
            MOTIVO_BAJA,
            APELLIDO_P_PROF_PROP,
            APELLIDO_M_PROF_PROP,
            NOMBRES_PROF_PROP,
            CODIGO_PROF_PROP,
            NUM_PUESTO_TEORIA_PROP,
            NUM_PUESTO_PRACTICA_PROP,
            HRS_SEM_MES_TEORIA_PROP,
            HRS_SEM_MES_PRACTICA_PROP,
            INTER_TEMP_DEF_PROP,
            TIPO_ASIGNACION_PROP,
            PERIODO_ASIG_DESDE_PROP,
            PERIODO_ASIG_HASTA_PROP,
            ESTADO_P,
            HORA_CREACION,
            Departamento_ID,
            ARCHIVO_ADJUNTO_VALIDACION,
            NOMBRE_ARCHIVO_VALIDACION,
            TIPO_ARCHIVO_VALIDACION,
            TAMAÑO_ARCHIVO_VALIDACION
        ) VALUES (
            ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURTIME(), ?, ?, ?, ?, ?
        )";
        
        $estado = 'Pendiente';
        
        $stmt = $conexion->prepare($sql);
        
        // Usar siempre el mismo bind_param con todos los parámetros
        $stmt->bind_param(
            "isssssiiissiiissssssssiiiiisssssisssi", // 37 parámetros
            $usuario_id,                        // 1
            $oficio_num,                        // 2
            $profesion_baja,                    // 3
            $apellido_paterno_baja,             // 4
            $apellido_materno_baja,             // 5
            $nombres_baja,                      // 6
            $codigo_prof_baja,                  // 7
            $num_puesto_teoria_baja,            // 8
            $num_puesto_practica_baja,          // 9
            $cve_materia_baja,                  // 10
            $nombre_materia_baja,               // 11
            $crn_baja,                          // 12
            $hrs_teoria_baja,                   // 13
            $hrs_practica_baja,                 // 14
            $carrera_baja,                      // 15
            $gdo_gpo_turno_baja,                // 16
            $tipo_asignacion_baja,              // 17
            $sin_efectos_baja,                  // 18
            $motivo_baja,                       // 19
            $apellido_paterno_prop,             // 20
            $apellido_materno_prop,             // 21
            $nombres_prop,                      // 22
            $codigo_prof_prop,                  // 23
            $num_puesto_teoria_prop,            // 24
            $num_puesto_practica_prop,          // 25
            $hrs_teoria_prop,                   // 26
            $hrs_practica_prop,                 // 27
            $inter_temp_def_prop,               // 28
            $tipo_asignacion_prop,              // 29
            $periodo_desde_prop,                // 30
            $periodo_hasta_prop,                // 31
            $estado,                            // 32
            $departamento_id,                   // 33
            $archivo_adjunto_validacion,        // 34
            $nombre_archivo_validacion,         // 35
            $tipo_archivo_validacion,           // 36
            $tamaño_archivo_validacion          // 37
        );

        // Si hay archivo adjunto, usar send_long_data para enviarlo
        if ($archivo_adjunto_validacion !== null) {
            $stmt->send_long_data(33, $archivo_adjunto_validacion); // índice 33 (parámetro 34)
        }
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Solicitud de baja-propuesta guardada correctamente',
                'debug' => [
                    'usuario_id' => $usuario_id,
                    'departamento_id' => $departamento_id,
                    'archivo_size' => $tamaño_archivo_validacion,
                    'archivo_nombre' => $nombre_archivo_validacion,
                    'archivo_tipo' => $tipo_archivo_validacion,
                    'oficio_num' => $oficio_num
                ]
            ]);
        } else {
            throw new Exception('Error en la base de datos: ' . $stmt->error);
        }
        
        $stmt->close();
        $conexion->close();
        
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
exit;
?>
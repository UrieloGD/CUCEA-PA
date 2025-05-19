<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        require_once './../../config/db.php';
        
        if (!isset($_SESSION['Codigo'])) {
            throw new Exception('Usuario no autenticado');
        }
        
        // Obtener datos del formulario para la baja
        $usuario_id = $_SESSION['Codigo'];
        
        // Aquí está la corrección importante:
        if (isset($_SESSION['Departamento_ID'])) {
            $departamento_id = $_SESSION['Departamento_ID'];
        } else {
            // Si no existe en la sesión, intenta obtenerlo de la base de datos
            $sql_dept = "SELECT Departamento_ID FROM usuarios WHERE Codigo = ?";
            $stmt_dept = $conexion->prepare($sql_dept);
            $stmt_dept->bind_param("i", $usuario_id);
            $stmt_dept->execute();
            $result_dept = $stmt_dept->get_result();
            
            if ($row_dept = $result_dept->fetch_assoc()) {
                $departamento_id = $row_dept['Departamento_ID'];
                // Actualizar también la sesión para futuros usos
                $_SESSION['Departamento_ID'] = $departamento_id;
            } else {
                $departamento_id = 17; // Valor por defecto si no se encuentra
            }
            
            $stmt_dept->close();
        }
        
        // Obtener datos del formulario para la baja
        $usuario_id = $_SESSION['Codigo'];
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

        // Obtener datos del formulario para la propuesta
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

        // Este código que genera un folio consecutivo con formato:
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
            Departamento_ID
        ) VALUES (
            ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURTIME(), ?
        )";
        
        $estado = 'Pendiente';
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param(
            "issssssiissiiiisssssssiiiiiisssss",
            $usuario_id,                        // i
            $oficio_num,                        // s
            $profesion_baja,                    // s
            $apellido_paterno_baja,             // s
            $apellido_materno_baja,             // s
            $nombres_baja,                      // s
            $codigo_prof_baja,                  // s
            $num_puesto_teoria_baja,            // i
            $num_puesto_practica_baja,          // i
            $cve_materia_baja,                  // s
            $nombre_materia_baja,               // s
            $crn_baja,                          // i
            $hrs_teoria_baja,                   // i
            $hrs_practica_baja,                 // i
            $carrera_baja,                      // i
            $gdo_gpo_turno_baja,                // s
            $tipo_asignacion_baja,              // s
            $sin_efectos_baja,                  // s
            $motivo_baja,                       // s
            $apellido_paterno_prop,             // s
            $apellido_materno_prop,             // s
            $nombres_prop,                      // s
            $codigo_prof_prop,                  // i
            $num_puesto_teoria_prop,            // i
            $num_puesto_practica_prop,          // i
            $hrs_teoria_prop,                   // i
            $hrs_practica_prop,                 // i
            $inter_temp_def_prop,               // i
            $tipo_asignacion_prop,              // s
            $periodo_desde_prop,                // s
            $periodo_hasta_prop,                // s
            $estado,                            // s
            $departamento_id                    // s
        );

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Solicitud guardada',
                'debug' => [
                    'usuario_id' => $usuario_id,
                    'departamento_id' => $departamento_id
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
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

        $oficio_num = mt_rand(10000, 99999);
        $departamento_id = $_SESSION['Departamento_ID'] ?? 1;


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
            "iisssssiissiiiissssssssiiiiisssss",  // 31 tipos para 31 variables
            $usuario_id,                         // i
            $oficio_num,                        // i
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
            $gdo_gpo_turno_baja,               // s
            $tipo_asignacion_baja,             // s
            $sin_efectos_baja,                 // s
            $motivo_baja,                      // s
            $apellido_paterno_prop,            // s
            $apellido_materno_prop,            // s
            $nombres_prop,                     // s
            $codigo_prof_prop,
            $num_puesto_teoria_prop,
            $num_puesto_practica_prop,
            $hrs_teoria_prop,                  // i
            $hrs_practica_prop,                // i
            $inter_temp_def_prop,              // i
            $tipo_asignacion_prop,             // s
            $periodo_desde_prop,               // s
            $periodo_hasta_prop,               // s
            $estado,                           // s
            $departamento_id                   // s
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Solicitud guardada']);
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
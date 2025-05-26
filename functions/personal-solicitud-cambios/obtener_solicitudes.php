<!--./functions/personal-solicitud_cambios/obtener_solicitudes.php -->
<?php
date_default_timezone_set('America/Mexico_City');
function obtenerSolicitudes($conexion) {
    $solicitudes = array();
    $rol_usuario = $_SESSION['Rol_ID'];
    $usuario_id = $_SESSION['Codigo'];
    
    // Inicializar la variable filtro_departamento
    $filtro_departamento = "";
    
    // Si el usuario NO es de Coordinación de personal (rol 3)
    if ($rol_usuario != 3) {
        // Obtener el departamento del usuario desde usuarios_departamentos
        $sql_dept = "SELECT Departamento_ID FROM usuarios_departamentos WHERE Usuario_ID = ?";
        $stmt = mysqli_prepare($conexion, $sql_dept);
        mysqli_stmt_bind_param($stmt, "s", $usuario_id);
        mysqli_stmt_execute($stmt);
        $result_dept = mysqli_stmt_get_result($stmt);
        $dept_row = mysqli_fetch_assoc($result_dept);
        
        if (!$dept_row) {
            return $solicitudes; // Retorna array vacío si no hay departamento asociado
        }
        
        $departamento_id = $dept_row['Departamento_ID'];
        $filtro_departamento = " WHERE sb.Departamento_ID = $departamento_id";
    }
    // Si es rol 3 (Coordinación de personal), no se aplica filtro de departamento
    else {
        $filtro_departamento = ""; // No filtramos por departamento
    }

    // Obtener solicitudes de baja
    $sql_baja = "SELECT sb.*, d.Nombre_Departamento 
                 FROM solicitudes_baja sb 
                 JOIN departamentos d ON sb.Departamento_ID = d.Departamento_ID";
    
    // Aplicar filtro de departamento solo si existe
    if (!empty($filtro_departamento)) {
        $sql_baja .= $filtro_departamento;
    }
    
    $sql_baja .= " ORDER BY 
    CASE sb.ESTADO_B
        WHEN 'Pendiente' THEN 1
        WHEN 'En revision' THEN 2
        ELSE 3
    END,
    sb.FECHA_SOLICITUD_B ASC,
    sb.HORA_CREACION ASC";
    
    $result_baja = mysqli_query($conexion, $sql_baja);
    while($row = mysqli_fetch_assoc($result_baja)) {
        $solicitudes[] = array(
            'tipo' => 'Solicitud de baja',
            'departamento' => $row['Nombre_Departamento'],
            'fecha' => $row['FECHA_SOLICITUD_B'],
            'hora' => $row['HORA_CREACION'],
            'estado' => $row['ESTADO_B'],
            'crn' => $row['CRN_B'],
            'puesto' => $row['DESCRIPCION_PUESTO_B'],
            'folio' => $row['OFICIO_NUM_BAJA'],
            'clasificacion_b' => $row['CLASIFICACION_BAJA_B'],
            'efecto' => $row['SIN_EFFECTOS_DESDE_B'],
            'profesor_actual' => array(
                'paterno' => $row['APELLIDO_P_PROF_B'],
                'materno' => $row['APELLIDO_M_PROF_B'],
                'nombres' => $row['NOMBRES_PROF_B'],
                'codigo' => $row['CODIGO_PROF_B']
            ),
            'motivo' => $row['MOTIVO_B']
        );
    }

    // Solicitudes de propuesta
    $sql_prop = "SELECT sp.*, d.Nombre_Departamento 
                 FROM solicitudes_propuesta sp 
                 JOIN departamentos d ON sp.Departamento_ID = d.Departamento_ID";
    
    // Para solicitudes de propuesta, cambiamos el filtro si es necesario
    if ($rol_usuario != 3) {
        $sql_prop .= " WHERE sp.Departamento_ID = $departamento_id";
    }
    
    $sql_prop .= " ORDER BY 
    CASE sp.ESTADO_P
        WHEN 'Pendiente' THEN 1
        WHEN 'En revision' THEN 2
        ELSE 3
    END,
    sp.FECHA_SOLICITUD_P ASC,
    sp.HORA_CREACION ASC";
    
    $result_prop = mysqli_query($conexion, $sql_prop);
    while($row = mysqli_fetch_assoc($result_prop)) {
        $solicitudes[] = array(
            'tipo' => 'Solicitud de propuesta',
            'departamento' => $row['Nombre_Departamento'],
            'fecha' => $row['FECHA_SOLICITUD_P'],
            'hora' => $row['HORA_CREACION'],
            'estado' => $row['ESTADO_P'],
            'crn' => $row['CRN_P'],
            'puesto' => $row['DESCRIPCION_PUESTO_P'],
            'folio' => $row['OFICIO_NUM_PROP'],
            'periodo_desde' => $row['PERIODO_ASIG_DESDE_P'],
            'periodo_hasta' => $row['PERIODO_ASIG_HASTA_P'],
            'clasificacion_p' => $row['CLASIFICACION_PUESTO_P'],
            'horas_sem' => $row['HRS_SEMANALES_P'],
            'profesor_actual' => array(
                'paterno' => $row['APELLIDO_P_PROF_P'],
                'materno' => $row['APELLIDO_M_PROF_P'],
                'nombres' => $row['NOMBRES_PROF_P'],
                'codigo' => $row['CODIGO_PROF_P']
            ),
            'profesor_propuesto' => array(
                'paterno' => $row['APELLIDO_P_PROF_SUST'],
                'materno' => $row['APELLIDO_M_PROF_SUST'],
                'nombres' => $row['NOMBRES_PROF_SUST'],
                'codigo' => $row['CODIGO_PROF_SUST']
            ),
            'motivo' => $row['CAUSA_P']
        );
    }

    // Solicitudes de baja-propuesta
    $sql_baja_prop = "SELECT sbp.*, d.Nombre_Departamento 
                      FROM solicitudes_baja_propuesta sbp 
                      JOIN departamentos d ON sbp.Departamento_ID = d.Departamento_ID";
    
    // Para solicitudes de baja-propuesta, aplicamos el filtro si es necesario
    if ($rol_usuario != 3) {
        $sql_baja_prop .= " WHERE sbp.Departamento_ID = $departamento_id";
    }
    
    $sql_baja_prop .= " ORDER BY 
    CASE sbp.ESTADO_P
        WHEN 'Pendiente' THEN 1
        WHEN 'En revision' THEN 2
        ELSE 3
    END,
    sbp.FECHA_SOLICITUD_BAJA_PROP ASC,
    sbp.HORA_CREACION ASC";
    
    $result_baja_prop = mysqli_query($conexion, $sql_baja_prop);
    while($row = mysqli_fetch_assoc($result_baja_prop)) {
        $solicitudes[] = array(
            'tipo' => 'Solicitud de baja-propuesta',
            'departamento' => $row['Nombre_Departamento'],
            'fecha' => $row['FECHA_SOLICITUD_BAJA_PROP'],
            'hora' => $row['HORA_CREACION'],
            'estado' => $row['ESTADO_P'],
            'crn' => $row['CRN_BAJA'],
            'materia' => $row['NOMBRE_MATERIA_BAJA'],
            'clave' => $row['CVE_MATERIA_BAJA'],
            'folio' => $row['OFICIO_NUM_BAJA_PROP'],
            'profesor_actual' => array(
                'paterno' => $row['APELLIDO_P_PROF_BAJA'],
                'materno' => $row['APELLIDO_M_PROF_BAJA'],
                'nombres' => $row['NOMBRES_PROF_BAJA'],
                'codigo' => $row['CODIGO_PROF_BAJA']
            ),
            'profesor_propuesto' => array(
                'paterno' => $row['APELLIDO_P_PROF_PROP'],
                'materno' => $row['APELLIDO_M_PROF_PROP'],
                'nombres' => $row['NOMBRES_PROF_PROP'],
                'codigo' => $row['CODIGO_PROF_PROP']
            ),
            'motivo' => $row['MOTIVO_BAJA']
        );
    }

    usort($solicitudes, function($a, $b) {
        $prioridad = [
            'Pendiente' => 1,
            'En revision' => 2,
            'Aprobado' => 3,
            'Rechazado' => 4
        ];
        
        $a_prio = $prioridad[$a['estado']] ?? 5;
        $b_prio = $prioridad[$b['estado']] ?? 5;
        
        // Ordenar por prioridad de estado
        if ($a_prio !== $b_prio) {
            return $a_prio - $b_prio;
        }
        
        // Si mismo estado, ordenar por fecha y hora
        $a_fecha = strtotime($a['fecha'] . ' ' . $a['hora']);
        $b_fecha = strtotime($b['fecha'] . ' ' . $b['hora']);
        
        return $a_fecha - $b_fecha;
    });

    return $solicitudes;
}
?>
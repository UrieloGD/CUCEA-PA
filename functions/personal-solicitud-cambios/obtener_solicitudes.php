<!--./functions/personal-solicitud_cambios/obtener_solicitudes.php -->
<?php
date_default_timezone_set('America/Mexico_City');
function obtenerSolicitudes($conexion) {
    $solicitudes = array();
    $rol_usuario = $_SESSION['Rol_ID'];
    $usuario_id = $_SESSION['Codigo'];
    
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

    // Obtener solicitudes de baja
    $sql_baja = "SELECT sb.*, d.Nombre_Departamento 
                 FROM solicitudes_baja sb 
                 JOIN departamentos d ON sb.Departamento_ID = d.Departamento_ID
                 $filtro_departamento
                 ORDER BY sb.FECHA_SOLICITUD_B DESC, sb.HORA_CREACION DESC";
    
    $result_baja = mysqli_query($conexion, $sql_baja);
    while($row = mysqli_fetch_assoc($result_baja)) {
        $solicitudes[] = array(
            'tipo' => 'Solicitud de baja',
            'departamento' => $row['Nombre_Departamento'],
            'fecha' => $row['FECHA_SOLICITUD_B'],
            'hora' => $row['HORA_CREACION'],
            'estado' => $row['ESTADO_B'],
            'crn' => $row['CRN_B'],
            'materia' => $row['DESCRIPCION_PUESTO_B'],
            'folio' => $row['OFICIO_NUM_BAJA'],
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
                 JOIN departamentos d ON sp.Departamento_ID = d.Departamento_ID
                 WHERE sp.Departamento_ID = $departamento_id
                 ORDER BY sp.FECHA_SOLICITUD_P DESC, sp.HORA_CREACION DESC";
    
    $result_prop = mysqli_query($conexion, $sql_prop);
    while($row = mysqli_fetch_assoc($result_prop)) {
        $solicitudes[] = array(
            'tipo' => 'Solicitud de propuesta',
            'departamento' => $row['Nombre_Departamento'],
            'fecha' => $row['FECHA_SOLICITUD_P'],
            'hora' => $row['HORA_CREACION'],
            'estado' => $row['ESTADO_P'],
            'crn' => $row['CRN_P'],
            'materia' => $row['DESCRIPCION_PUESTO_P'],
            'folio' => $row['OFICIO_NUM_PROP'],
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
                      JOIN departamentos d ON sbp.Departamento_ID = d.Departamento_ID
                      WHERE sbp.Departamento_ID = $departamento_id
                      ORDER BY sbp.FECHA_SOLICITUD_BAJA_PROP DESC, sbp.HORA_CREACION DESC";
    
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

    return $solicitudes;
}
?>
<?php
include './../../config/db.php';

$espacio = $_GET['espacio'];
$modulo = $_GET['modulo'];

$departamentos = [
    'Estudios_Regionales', 'Finanzas', 'Ciencias_Sociales', 'PALE', 'Posgrados',
    'Economía', 'Recursos_Humanos', 'Métodos_Cuantitativos', 'Políticas_Públicas',
    'Administración', 'Auditoría', 'Mercadotecnia', 'Impuestos',
    'Sistemas_de_Información', 'Turismo', 'Contabilidad'
];

$horarios = array(
    'Lunes' => array(),
    'Martes' => array(),
    'Miercoles' => array(),
    'Jueves' => array(),
    'Viernes' => array(),
    'Sabado' => array()
);

foreach ($departamentos as $departamento) {
    $tabla = "Data_" . str_replace(' ', '_', $departamento);
    
    $query = "SELECT L, M, I, J, V, S, HORA_INICIAL, HORA_FINAL, CVE_MATERIA, MATERIA, NOMBRE_PROFESOR 
              FROM $tabla 
              WHERE MODULO = '$modulo' AND AULA = '$espacio'
              ORDER BY HORA_INICIAL";

    $result = mysqli_query($conexion, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dias = array('L' => 'Lunes', 'M' => 'Martes', 'I' => 'Miercoles', 'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sabado');
            foreach ($dias as $inicial => $nombreDia) {
                if ($row[$inicial] !== null) {
                    $horarios[$nombreDia][] = array(
                        'hora_inicial' => $row['HORA_INICIAL'],
                        'hora_final' => $row['HORA_FINAL'],
                        'cve_materia' => $row['CVE_MATERIA'],
                        'materia' => $row['MATERIA'],
                        'profesor' => $row['NOMBRE_PROFESOR']
                    );
                }
            }
        }
    }
}

// Ordenar las clases por hora de inicio para cada día
foreach ($horarios as $dia => $clases) {
    usort($horarios[$dia], function($a, $b) {
        return strcmp($a['hora_inicial'], $b['hora_inicial']);
    });
}

echo json_encode($horarios);
?>
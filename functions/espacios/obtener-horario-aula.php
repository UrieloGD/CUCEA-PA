<?php
include './../../config/db.php';
ini_set('display_errors', 0);
error_reporting(0);

$espacio = $_GET['espacio'];
$modulo = $_GET['modulo'];

// Consideramos que el aula puede tener divisiones A, B, C
$espacios_a_buscar = [$espacio];
if (in_array($espacio, ['0001', '0002', '0004'])) {
    $espacios_a_buscar[] = $espacio . 'A';
    $espacios_a_buscar[] = $espacio . 'B';
    $espacios_a_buscar[] = $espacio . 'C';
}

$departamentos = [
    'estudios_regionales',
    'finanzas',
    'ciencias_sociales',
    'pale',
    'posgrados',
    'economía',
    'recursos_humanos',
    'métodos_cuantitativos',
    'políticas_públicas',
    'administración',
    'auditoría',
    'mercadotecnia',
    'impuestos',
    'sistemas_de_información',
    'turismo',
    'contabilidad'
];

$horarios = array(
    'Lunes' => array(),
    'Martes' => array(),
    'Miercoles' => array(),
    'Jueves' => array(),
    'Viernes' => array(),
    'Sabado' => array()
);

// Obtener información adicional del espacio
$query_espacio = "SELECT Modulo, Etiqueta FROM espacios WHERE Modulo = '$modulo' AND Espacio = '$espacio'";
$result_espacio = mysqli_query($conexion, $query_espacio);
$info_espacio = mysqli_fetch_assoc($result_espacio);

$horarios['modulo'] = $info_espacio['Modulo'];
$horarios['tipo'] = $info_espacio['Etiqueta'];

// Array para almacenar clases únicas
$clases_unicas = [];

foreach ($departamentos as $departamento) {
    $tabla = "data_" . str_replace(' ', '_', $departamento);

    // Construimos la condición IN para buscar en múltiples aulas
    $espacios_str = "'" . implode("','", $espacios_a_buscar) . "'";
    
    $query = "SELECT L, M, I, J, V, S, HORA_INICIAL, HORA_FINAL, CVE_MATERIA, MATERIA, NOMBRE_PROFESOR, CUPO, AULA 
              FROM $tabla 
              WHERE MODULO = '$modulo' AND AULA IN ($espacios_str)
              ORDER BY HORA_INICIAL";

    $result = mysqli_query($conexion, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dias = array('L' => 'Lunes', 'M' => 'Martes', 'I' => 'Miercoles', 'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sabado');
            foreach ($dias as $inicial => $nombreDia) {
                if ($row[$inicial] !== null) {
                    // Crear una clave única para cada clase
                    $clase_key = $nombreDia . '_' . $row['HORA_INICIAL'] . '_' . $row['HORA_FINAL'] . '_' . $row['CVE_MATERIA'];

                    // Solo agregar si no existe ya
                    if (!isset($clases_unicas[$clase_key])) {
                        $clases_unicas[$clase_key] = true;
                        $horarios[$nombreDia][] = array(
                            'hora_inicial' => $row['HORA_INICIAL'],
                            'hora_final' => $row['HORA_FINAL'],
                            'cve_materia' => $row['CVE_MATERIA'],
                            'materia' => $row['MATERIA'],
                            'profesor' => $row['NOMBRE_PROFESOR'],
                            'cupo' => $row['CUPO'],
                            'departamento' => ucwords(str_replace('_', ' ', $departamento)), // Añadir departamento
                            'aula_real' => $row['AULA'] // Guardamos el aula real donde se imparte
                        );
                        // Actualizar el cupo general
                        $horarios['cupo'] = $row['CUPO'];
                    }
                }
            }
        }
    } else {
        error_log("Error en la consulta: " . mysqli_error($conexion));
    }
}

// Ordenar los horarios por hora inicial para cada día
foreach ($horarios as $dia => &$clases) {
    if (is_array($clases)) {
        usort($clases, function ($a, $b) {
            return strcmp($a['hora_inicial'], $b['hora_inicial']);
        });
    }
}

// Asegurarse de que no haya salida antes del JSON
ob_clean();

// Establecer las cabeceras correctas
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Imprimir el JSON y terminar la ejecución
echo json_encode($horarios);
exit;
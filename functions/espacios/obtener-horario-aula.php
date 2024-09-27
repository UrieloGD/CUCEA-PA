<?php
include './../../config/db.php';

$espacio = $_GET['espacio'];
$modulo = $_GET['modulo'];

$departamentos = [
    'Estudios_Regionales',
    'Finanzas',
    'Ciencias_Sociales',
    'PALE',
    'Posgrados',
    'Economía',
    'Recursos_Humanos',
    'Métodos_Cuantitativos',
    'Políticas_Públicas',
    'Administración',
    'Auditoría',
    'Mercadotecnia',
    'Impuestos',
    'Sistemas_de_Información',
    'Turismo',
    'Contabilidad'
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
$query_espacio = "SELECT Modulo, Etiqueta FROM Espacios WHERE Modulo = '$modulo' AND Espacio = '$espacio'";
$result_espacio = mysqli_query($conexion, $query_espacio);
$info_espacio = mysqli_fetch_assoc($result_espacio);

$horarios['modulo'] = $info_espacio['Modulo'];
$horarios['tipo'] = $info_espacio['Etiqueta'];

foreach ($departamentos as $departamento) {
    $tabla = "Data_" . str_replace(' ', '_', $departamento);

    $query = "SELECT L, M, I, J, V, S, HORA_INICIAL, HORA_FINAL, CVE_MATERIA, MATERIA, NOMBRE_PROFESOR, CUPO 
              FROM $tabla 
              WHERE MODULO = '$modulo' AND AULA = '$espacio'
              ORDER BY HORA_INICIAL";

    error_log("Consulta ejecutada: " . $query);

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
                        'profesor' => $row['NOMBRE_PROFESOR'],
                        'cupo' => $row['CUPO']
                    );
                    // Después de obtener la información del espacio
                    $horarios['cupo'] = $row['CUPO'];
                }
            }
        }
    } else {
        error_log("Error en la consulta: " . mysqli_error($conexion));
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
// Al final del archivo, justo antes de enviar el JSON
header('Content-Type: application/json');
echo json_encode($horarios);
exit;

<?php
include './../../config/db.php';

$modulo = $_GET['modulo'];
$dia = $_GET['dia'];
$hora_inicio = $_GET['hora_inicio'];
$hora_fin = $_GET['hora_fin'];

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

$espacios_ocupados = array();

foreach ($departamentos as $departamento) {
    $tabla = "Data_" . str_replace(' ', '_', $departamento);

    $query = "SELECT AULA, CVE_MATERIA, MATERIA, NOMBRE_PROFESOR, HORA_INICIAL, HORA_FINAL FROM $tabla 
          WHERE MODULO = '$modulo' 
          AND $dia IS NOT NULL 
          AND (
              (HORA_INICIAL >= '$hora_inicio' AND HORA_INICIAL < '$hora_fin')
              OR (HORA_FINAL > '$hora_inicio' AND HORA_FINAL <= '$hora_fin')
              OR (HORA_INICIAL <= '$hora_inicio' AND HORA_FINAL >= '$hora_fin')
          )";

    $result = mysqli_query($conexion, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!isset($espacios_ocupados[$row['AULA']])) {
                $espacios_ocupados[$row['AULA']] = array(
                    'cve_materia' => $row['CVE_MATERIA'],
                    'materia' => $row['MATERIA'],
                    'profesor' => $row['NOMBRE_PROFESOR']
                );
            }
        }
    } else {
        error_log("Error en la consulta para la tabla $tabla: " . mysqli_error($conexion));
    }
}

echo json_encode($espacios_ocupados);

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
$query_espacio = "SELECT Modulo, Etiqueta FROM espacios WHERE Modulo = '$modulo' AND Espacio = '$espacio'";
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

    $result = mysqli_query($conexion, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dias = array('L' => 'Lunes', 'M' => 'Martes', 'I' => 'Miercoles', 'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sabado');
            foreach ($dias as $inicial => $nombreDia) {
                if ($row[$inicial] !== null) {
                    // Crear un nuevo registro
                    $nuevo_registro = array(
                        'hora_inicial' => $row['HORA_INICIAL'],
                        'hora_final' => $row['HORA_FINAL'],
                        'cve_materia' => $row['CVE_MATERIA'],
                        'materia' => $row['MATERIA'],
                        'profesor' => $row['NOMBRE_PROFESOR'],
                        'cupo' => $row['CUPO'],
                        'departamento' => str_replace('_', ' ', str_replace('Data_', '', $tabla))
                    );

                    // Bandera para verificar si es un duplicado
                    $es_duplicado = false;

                    // Comparar con registros existentes
                    foreach ($horarios[$nombreDia] as $registro_existente) {
                        if ($registro_existente['hora_inicial'] === $nuevo_registro['hora_inicial'] &&
                            $registro_existente['hora_final'] === $nuevo_registro['hora_final'] &&
                            $registro_existente['materia'] === $nuevo_registro['materia'] &&
                            $registro_existente['profesor'] === $nuevo_registro['profesor']) {
                            $es_duplicado = true;
                            break;
                        }
                    }

                    // Si no es duplicado, agregarlo al array
                    if (!$es_duplicado) {
                        $horarios[$nombreDia][] = $nuevo_registro;
                    }

                    $horarios['cupo'] = $row['CUPO'];
                }
            }
        }
    }
}

// Limpiar cualquier salida anterior
ob_clean();

// Establecer las cabeceras
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Enviar respuesta
echo json_encode($horarios);
exit;
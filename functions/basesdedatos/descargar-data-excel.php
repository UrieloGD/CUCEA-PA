<?php
require './../../vendor/autoload.php';
include './../../config/db.php';
session_start();

$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;
$columnas_seleccionadas = isset($_GET['columnas']) ? json_decode($_GET['columnas'], true) : [];

if (empty($departamento_id) || empty($columnas_seleccionadas)) {
    die("Error: Faltan parámetros necesarios.");
}

$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
if ($stmt === false) {
    die("Error preparando la consulta: " . $conexion->error);
}

$mapeo_columnas = [

    'CVE MATERIA' => 'CVE_MATERIA',
    'SECCIÓN' => 'SECCION',
    'NIVEL TIPO' => 'NIVEL_TIPO',
    'C. MIN' => 'C_MIN',
    'H. TOTALES' => 'H_TOTALES',
    'STATUS' => 'ESTATUS',
    'TIPO CONTRATO' => 'TIPO_CONTRATO',
    'CÓDIGO' => 'CODIGO_PROFESOR',
    'NOMBRE PROFESOR' => 'NOMBRE_PROFESOR',
    'CÓDIGO DESCARGA' => 'CODIGO_DESCARGA',
    'NOMBRE DESCARGA' => 'NOMBRE_DESCARGA',
    'NOMBRE DEFINITIVO' => 'NOMBRE_DEFINITIVO',
    'CÓDIGO DEPENDENCIA' => 'CODIGO_DEPENDENCIA',
    'DÍA PRESENCIAL' => 'DIA_PRESENCIAL',
    'DÍA VIRTUAL' => 'DIA_VIRTUAL',
    'FECHA INICIAL' => 'FECHA_INICIAL',
    'FECHA FINAL' => 'FECHA_FINAL',
    'HORA INICIAL' => 'HORA_INICIAL',
    'HORA FINAL' => 'HORA_FINAL',
    'MÓDULO' => 'MODULO',
    'EXTRAORDINARIO' => 'EXAMEN_EXTRAORDINARIO'
];
// Función para convertir el nombre mostrado al nombre real
function obtenerNombreRealColumna($nombre_mostrado, $mapeo_columnas) {
    // Eliminar espacios extras y dejar solo un espacio entre palabras
    $nombre_mostrado = trim(preg_replace('/\s+/', ' ', $nombre_mostrado));
    
    // Primero, verificar si el nombre exacto existe en el mapeo
    if (isset($mapeo_columnas[$nombre_mostrado])) {
        return $mapeo_columnas[$nombre_mostrado];
    }
    
    // Si no existe, crear un nombre de columna estándar
    return strtoupper(str_replace(' ', '_', $nombre_mostrado));
}

// Convertir los nombres seleccionados a los nombres reales
$columnas_reales = array_map(function($columna) use ($mapeo_columnas) {
    return obtenerNombreRealColumna($columna, $mapeo_columnas);
}, $columnas_seleccionadas);

$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
if ($stmt === false) {
    die("Error preparando la consulta de departamento: " . $conexion->error);
}
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$tabla_departamento = "data_" . str_replace(' ', '_', $nombre_departamento);

// Construir la consulta SQL dinámica con los nombres reales de las columnas
$sql = "SELECT " . implode(", ", $columnas_reales) . " FROM `$tabla_departamento` WHERE Departamento_ID = ?";

$stmt = $conexion->prepare($sql);
if ($stmt === false) {
    die("Error preparando la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

// Escribir los encabezados en el Excel (usando los nombres mostrados)
foreach ($columnas_seleccionadas as $index => $header) {
    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . '1', $header);
}

// Escribir los datos
if ($result->num_rows > 0) {
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $col = 1;
        foreach ($columnas_reales as $header_real) {
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 
                $data[$header_real] ?? ''
            );
            $col++;
        }
        $row++;
    }
} else {
    die("No se encontraron resultados para el departamento especificado.");
}

$sheet->setTitle("Data_$nombre_departamento");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_' . $nombre_departamento . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

$stmt->close();
$conexion->close();
exit;
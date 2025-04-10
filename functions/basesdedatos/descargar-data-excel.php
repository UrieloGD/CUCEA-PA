<?php
require './../../vendor/autoload.php';
include './../../config/db.php';
session_start();

// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;
$columnas_seleccionadas = isset($_GET['columnas']) ? json_decode($_GET['columnas'], true) : [];

if (empty($departamento_id) || empty($columnas_seleccionadas)) {
    die("Error: Faltan parámetros necesarios.");
}

// Mapeo de nombres de columnas mostrados a nombres reales en la base de datos
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
    'EXTRAORDINARIO' => 'EXAMEN_EXTRAORDINARIO',
    // Añade aquí cualquier otra columna que necesites mapear
];

function obtenerNombreRealColumna($nombre_mostrado, $mapeo_columnas)
{
    // Eliminar espacios extras y dejar solo un espacio entre palabras
    $nombre_mostrado = trim(preg_replace('/\s+/', ' ', $nombre_mostrado));

    if (isset($mapeo_columnas[$nombre_mostrado])) {
        return $mapeo_columnas[$nombre_mostrado];
    }

    // Si no está en el mapeo, convertir a un nombre seguro
    return preg_replace('/[^a-zA-Z0-9_]/', '_', strtoupper($nombre_mostrado));
}

// Obtener el nombre del departamento
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
$stmt->close();

// Convertir los nombres seleccionados a los nombres reales
$columnas_reales = [];
foreach ($columnas_seleccionadas as $columna) {
    $nombre_real = obtenerNombreRealColumna($columna, $mapeo_columnas);
    if (!empty($nombre_real)) {
        $columnas_reales[] = "`" . $nombre_real . "`";
    }
}

if (empty($columnas_reales)) {
    die("Error: No se pudieron procesar las columnas seleccionadas.");
}

$tabla_departamento = "`data_" . str_replace(' ', '_', $nombre_departamento) . "`";
$sql = "SELECT " . implode(", ", $columnas_reales) . " FROM " . $tabla_departamento . " WHERE Departamento_ID = ? AND (PAPELERA <> 'INACTIVO' OR PAPELERA IS NULL)";

$stmt = $conexion->prepare($sql);
if ($stmt === false) {
    die("Error preparando la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

// Crear el archivo Excel
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Escribir los encabezados
foreach ($columnas_seleccionadas as $index => $header) {
    $sheet->setCellValue(
        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . '1',
        $header
    );
}

// Escribir los datos
if ($result->num_rows > 0) {
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $col = 1;
        foreach ($columnas_reales as $header_real) {
            $header_clean = trim($header_real, '`');
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                $data[$header_clean] ?? ''
            );
            $col++;
        }
        $row++;
    }
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

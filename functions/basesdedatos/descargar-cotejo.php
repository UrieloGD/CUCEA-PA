<?php
require './../../vendor/autoload.php';
include './../../config/db.php';
session_start();

$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;

if (empty($departamento_id)) {
    die("Error: Falta el ID del departamento.");
}

// Obtener el nombre del departamento
$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "Data_" . str_replace(' ', '_', $nombre_departamento);

// Columnas para verificar duplicados
$columnas_cotejo = [
    'CRN',
    'MATERIA',
    'CVE_MATERIA',
    'SECCION',
    'L',
    'M',
    'I',
    'J',
    'V',
    'S',
    'D',
    'MODALIDAD',
    'HORA_INICIAL',
    'HORA_FINAL',
    'MODULO',
    'CUPO'
];

// Columnas a exportar en el orden especificado
$columnas_exportar = [
    'CICLO',
    'CRN',
    'FECHA_INICIAL',
    'FECHA_FINAL',
    'L',
    'M',
    'I',
    'J',
    'V',
    'S',
    'D',
    'HORA_INICIAL',
    'HORA_FINAL',
    'MODULO',
    'AULA',
    'DIA_PRESENCIAL',
    'DIA_VIRTUAL',
    'MODALIDAD'
];

// Construir la consulta SQL para obtener registros únicos
$sql_select = "
SELECT
    MAX(CICLO) AS CICLO,
    " . implode(", ", $columnas_cotejo) . ",
    MAX(FECHA_INICIAL) AS FECHA_INICIAL,
    MAX(FECHA_FINAL) AS FECHA_FINAL,
    MAX(AULA) AS AULA,
    MAX(DIA_PRESENCIAL) AS DIA_PRESENCIAL,
    MAX(DIA_VIRTUAL) AS DIA_VIRTUAL
FROM `$tabla_departamento`
WHERE Departamento_ID = ?
GROUP BY " . implode(", ", $columnas_cotejo) . "
";

$stmt = $conexion->prepare($sql_select);
if ($stmt === false) {
    die("Error preparando la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

// Crear nuevo documento Excel
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Escribir encabezados en el Excel
foreach ($columnas_exportar as $index => $header) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    $sheet->setCellValue($col . '1', $header);

    // Formatear todas las columnas como texto
    $sheet->getStyle($col . '1:' . $col . ($result->num_rows + 1))
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
}

// Formatear columnas de fecha con formato de fecha corta
$fecha_columns = ['FECHA_INICIAL', 'FECHA_FINAL'];
foreach ($fecha_columns as $fecha_column) {
    $col_index = array_search($fecha_column, $columnas_exportar);
    if ($col_index !== false) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_index + 1);
        $sheet->getStyle($col . '2:' . $col . ($result->num_rows + 1))
            ->getNumberFormat()
            ->setFormatCode('DD/MM/YYYY');
    }
}

// Escribir datos
if ($result->num_rows > 0) {
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $col = 1;
        foreach ($columnas_exportar as $columna) {
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                $data[$columna] ?? ''
            );
            $col++;
        }
        $row++;
    }
}

$sheet->setTitle("Cotejada_$nombre_departamento");

// Configurar headers para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_Cotejada_' . $nombre_departamento . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

$stmt->close();
$conexion->close();
exit;

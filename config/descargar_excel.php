<?php
require '../vendor/autoload.php';
include '../config/db.php';
session_start();

$departamento_id = isset($_GET['departamento_id']) ? $_GET['departamento_id'] : '';

$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$nombre_usuario = isset($_SESSION['Nombre']) ? $_SESSION['Nombre'] : 'Usuario';
$apellido_usuario = isset($_SESSION['Apellido']) ? $_SESSION['Apellido'] : '';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator("$nombre_usuario $apellido_usuario")
    ->setLastModifiedBy("$nombre_usuario $apellido_usuario")
    ->setTitle("Exportación de Data_$nombre_departamento")
    ->setSubject("Data_$nombre_departamento")
    ->setDescription("Documento generado automáticamente desde la base de datos.")
    ->setKeywords("phpexcel")
    ->setCategory("Archivo de datos");

$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'CICLO', 'CRN', 'MATERIA', 'CVE_MATERIA', 'SECCION', 'NIVEL', 'NIVEL_TIPO', 'TIPO',
    'C_MIN', 'H_TOTALES', 'ESTATUS', 'TIPO_CONTRATO', 'CODIGO_PROFESOR', 'NOMBRE_PROFESOR',
    'CATEGORIA', 'DESCARGA', 'CODIGO_DESCARGA', 'NOMBRE_DESCARGA', 'NOMBRE_DEFINITIVO',
    'TITULAR', 'HORAS', 'CODIGO_DEPENDENCIA', 'L', 'M', 'I', 'J', 'V', 'S', 'D', 'DIA_PRESENCIAL',
    'DIA_VIRTUAL', 'MODALIDAD', 'FECHA_INICIAL', 'FECHA_FINAL', 'HORA_INICIAL', 'HORA_FINAL',
    'MODULO', 'AULA', 'CUPO', 'OBSERVACIONES', 'EXAMEN_EXTRAORDINARIO'
];

foreach ($headers as $index => $header) {
    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . '1', $header);
}

$tabla_departamento = "Data_" . str_replace(' ', '_', $nombre_departamento);

$sql = "SELECT * FROM `$tabla_departamento` WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 
                $data[$header] ?? ''
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
<?php
require '../vendor/autoload.php';
include '../config/db.php';

// Crear un nuevo objeto de PHPExcel
$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$objPHPExcel->getProperties()->setCreator("Omar Rodríguez")
    ->setLastModifiedBy("Omar Rodríguez")
    ->setTitle("Exportación de Data_Plantilla")
    ->setSubject("Data_Plantilla")
    ->setDescription("Documento generado automáticamente desde la base de datos.")
    ->setKeywords("phpexcel")
    ->setCategory("Archivo de datos");

// Agregar los encabezados de la tabla
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'ID')
    ->setCellValue('B1', 'CICLO')
    ->setCellValue('C1', 'NRC')
    ->setCellValue('D1', 'FECHA INI')
    ->setCellValue('E1', 'FECHA FIN')
    ->setCellValue('F1', 'L')
    ->setCellValue('G1', 'M')
    ->setCellValue('H1', 'I')
    ->setCellValue('I1', 'J')
    ->setCellValue('J1', 'V')
    ->setCellValue('K1', 'S')
    ->setCellValue('L1', 'D')
    ->setCellValue('M1', 'HORA INI')
    ->setCellValue('N1', 'HORA FIN')
    ->setCellValue('O1', 'EDIF')
    ->setCellValue('P1', 'AULA');

// Consulta SQL para obtener los datos de la tabla 'Data_Plantilla'
$sql = "SELECT * FROM Data_Plantilla";
$result = mysqli_query($conexion, $sql);

// Verificar si se obtuvieron resultados
if (mysqli_num_rows($result) > 0) {
    $fila = 2; // Empezar en la segunda fila
    while ($row = mysqli_fetch_assoc($result)) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $fila, $row['ID_Plantilla'])
            ->setCellValue('B' . $fila, $row['CICLO'])
            ->setCellValue('C' . $fila, $row['NRC'])
            ->setCellValue('D' . $fila, $row['FECHA_INI'])
            ->setCellValue('E' . $fila, $row['FECHA_FIN'])
            ->setCellValue('F' . $fila, $row['L'])
            ->setCellValue('G' . $fila, $row['M'])
            ->setCellValue('H' . $fila, $row['I'])
            ->setCellValue('I' . $fila, $row['J'])
            ->setCellValue('J' . $fila, $row['V'])
            ->setCellValue('K' . $fila, $row['S'])
            ->setCellValue('L' . $fila, $row['D'])
            ->setCellValue('M' . $fila, $row['HORA_INI'])
            ->setCellValue('N' . $fila, $row['HORA_FIN'])
            ->setCellValue('O' . $fila, $row['EDIF'])
            ->setCellValue('P' . $fila, $row['AULA']);
        $fila++;
    }
}

// Renombrar hoja
$objPHPExcel->getActiveSheet()->setTitle('Data_Plantilla');

// Establecer la hoja activa
$objPHPExcel->setActiveSheetIndex(0);

// Redirigir salida al navegador
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_Plantilla.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');

// Guardar el archivo
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
$writer->save('php://output');

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
exit;

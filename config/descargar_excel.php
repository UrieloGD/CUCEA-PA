<?php
require '../vendor/autoload.php';
include '../config/db.php';
session_start(); // Iniciar la sesión

// Obtener el ID del departamento desde el formulario
$departamento_id = isset($_GET['departamento_id']) ? $_GET['departamento_id'] : '';

// Obtener el nombre del departamento usando el ID
$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];


// Obtener el nombre y apellido del usuario desde la sesión (con verificación)
$nombre_usuario = isset($_SESSION['Nombre']) ? $_SESSION['Nombre'] : 'Usuario';
$apellido_usuario = isset($_SESSION['Apellido']) ? $_SESSION['Apellido'] : '';

// Crear un nuevo objeto de PHPExcel
$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$objPHPExcel->getProperties()->setCreator("$nombre_usuario $apellido_usuario")
    ->setLastModifiedBy("$nombre_usuario $apellido_usuario")
    ->setTitle("Exportación de Data_$nombre_departamento")
    ->setSubject("Data_$nombre_departamento")
    ->setDescription("Documento generado automáticamente desde la base de datos.")
    ->setKeywords("phpexcel")
    ->setCategory("Archivo de datos");

// Agregar los encabezados de la tabla
$objPHPExcel->setActiveSheetIndex(0)
    //    ->setCellValue('A1', 'ID')
    ->setCellValue('A1', 'CICLO')
    ->setCellValue('B1', 'NRC')
    ->setCellValue('C1', 'FECHA INI')
    ->setCellValue('D1', 'FECHA FIN')
    ->setCellValue('E1', 'L')
    ->setCellValue('F1', 'M')
    ->setCellValue('G1', 'I')
    ->setCellValue('H1', 'J')
    ->setCellValue('I1', 'V')
    ->setCellValue('J1', 'S')
    ->setCellValue('K1', 'D')
    ->setCellValue('L1', 'HORA INI')
    ->setCellValue('M1', 'HORA FIN')
    ->setCellValue('N1', 'EDIF')
    ->setCellValue('O1', 'AULA');

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

// Consulta SQL para obtener los datos de la tabla correspondiente al departamento
$sql = "SELECT * FROM `$tabla_departamento` WHERE Departamento_ID = $departamento_id";
$result = mysqli_query($conexion, $sql);

// Verificar si se obtuvieron resultados
if (mysqli_num_rows($result) > 0) {
    $fila = 2; // Empezar en la segunda fila
    while ($row = mysqli_fetch_assoc($result)) {
        $objPHPExcel->setActiveSheetIndex(0)
            //          ->setCellValue('A' . $fila, $row['ID_Plantilla'])
            ->setCellValue('A' . $fila, $row['CICLO'])
            ->setCellValue('B' . $fila, $row['NRC'])
            ->setCellValue('C' . $fila, $row['FECHA_INI'])
            ->setCellValue('D' . $fila, $row['FECHA_FIN'])
            ->setCellValue('E' . $fila, $row['L'])
            ->setCellValue('F' . $fila, $row['M'])
            ->setCellValue('G' . $fila, $row['I'])
            ->setCellValue('H' . $fila, $row['J'])
            ->setCellValue('I' . $fila, $row['V'])
            ->setCellValue('J' . $fila, $row['S'])
            ->setCellValue('K' . $fila, $row['D'])
            ->setCellValue('L' . $fila, $row['HORA_INI'])
            ->setCellValue('M' . $fila, $row['HORA_FIN'])
            ->setCellValue('N' . $fila, $row['EDIF'])
            ->setCellValue('O' . $fila, $row['AULA']);
        $fila++;
    }
}

// Renombrar hoja
$objPHPExcel->getActiveSheet()->setTitle("Data_$nombre_departamento");

// Establecer la hoja activa
$objPHPExcel->setActiveSheetIndex(0);

// Redirigir salida al navegador
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_' . $nombre_departamento . '.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');

// Guardar el archivo
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
$writer->save('php://output');

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
exit;

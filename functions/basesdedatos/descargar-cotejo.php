<?php
// Asegurarse de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['email'])) {
    die("Error: Usuario no autenticado");
}

// Configurar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir la ruta base
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

// Incluir las dependencias necesarias
require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/config/db.php';

// Obtener el ID del departamento
$departamento_id = isset($_POST['Departamento_ID']) ? (int)$_POST['Departamento_ID'] : (isset($_SESSION['Departamento_ID']) ? (int)$_SESSION['Departamento_ID'] : 0);

// Validar el ID del departamento
if (empty($departamento_id)) {
    die("Error: Falta el ID del departamento. ID recibido: " . print_r($_POST, true));
}

try {
    // Obtener el nombre del departamento
    $sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
    $stmt = $conexion->prepare($sql_departamento);

    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result_departamento = $stmt->get_result();

    if ($result_departamento->num_rows === 0) {
        throw new Exception("No se encontró el departamento especificado");
    }

    $row_departamento = $result_departamento->fetch_assoc();
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    $tabla_departamento = "data_" . str_replace(' ', '_', $nombre_departamento);

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

    // Columnas adicionales a exportar que no están en columnas_cotejo
    $columnas_adicionales = [
        'FECHA_INICIAL',
        'FECHA_FINAL',
        'AULA',
        'DIA_PRESENCIAL',
        'DIA_VIRTUAL'
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

    // Construir la consulta SQL evitando duplicados
    $sql_select = "
        SELECT 
            MAX(CICLO) as CICLO,
            " . implode(", ", array_unique(array_merge(
        $columnas_cotejo,
        $columnas_adicionales
    ))) . "
        FROM `$tabla_departamento`
        WHERE Departamento_ID = ?
        GROUP BY " . implode(", ", $columnas_cotejo);

    $stmt = $conexion->prepare($sql_select);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta de datos: " . $conexion->error . "\nSQL: " . $sql_select);
    }

    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear nuevo documento Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Escribir encabezados
    foreach ($columnas_exportar as $index => $header) {
        $sheet->setCellValue(
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . '1',
            $header
        );
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

    // Guardar archivo
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}

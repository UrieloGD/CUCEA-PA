<?php
require './../../vendor/autoload.php';
include './../../config/db.php';
session_start();

$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;

if (empty($departamento_id)) {
    die("Error: Falta el ID del departamento.");
}

// Obtener el nombre del departamento
$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];

$tabla_departamento = "data_" . str_replace(' ', '_', $nombre_departamento);

$dias_columnas = ['L', 'M', 'I', 'J', 'V', 'S', 'D'];

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

// Consulta SQL modificada para ser compatible con MySQL
$sql_select = "
WITH modalidades_conteo AS (
    SELECT 
        CRN,
        HORA_INICIAL,
        HORA_FINAL,
        MODULO,
        COUNT(DISTINCT MODALIDAD) as modalidades_distintas
    FROM `$tabla_departamento`
    WHERE Departamento_ID = ? AND (PAPELERA <> 'INACTIVO' OR PAPELERA IS NULL)
    GROUP BY CRN, HORA_INICIAL, HORA_FINAL, MODULO
),
registros_base AS (
    SELECT 
        t.*,
        mc.modalidades_distintas
    FROM `$tabla_departamento` t
    JOIN modalidades_conteo mc ON 
        t.CRN = mc.CRN AND 
        t.HORA_INICIAL = mc.HORA_INICIAL AND 
        t.HORA_FINAL = mc.HORA_FINAL AND 
        t.MODULO = mc.MODULO
    WHERE t.Departamento_ID = ? AND (t.PAPELERA <> 'INACTIVO' OR t.PAPELERA IS NULL)
)
SELECT 
    MAX(CICLO) as CICLO,
    rb.CRN,
    MAX(MATERIA) as MATERIA,
    MAX(CVE_MATERIA) as CVE_MATERIA,
    MAX(SECCION) as SECCION,
    MAX(FECHA_INICIAL) as FECHA_INICIAL,
    MAX(FECHA_FINAL) as FECHA_FINAL,
    CASE 
        WHEN modalidades_distintas = 1 THEN MAX(L)
        WHEN MODALIDAD = 'VIRTUAL' THEN
            CASE 
                WHEN FIND_IN_SET('LUNES', DIA_PRESENCIAL) > 0 THEN NULL
                ELSE L
            END
        ELSE
            CASE 
                WHEN FIND_IN_SET('LUNES', DIA_VIRTUAL) > 0 THEN NULL
                ELSE L
            END
    END as L,
    CASE 
        WHEN modalidades_distintas = 1 THEN MAX(M)
        WHEN MODALIDAD = 'VIRTUAL' THEN
            CASE 
                WHEN FIND_IN_SET('MARTES', DIA_PRESENCIAL) > 0 THEN NULL
                ELSE M
            END
        ELSE
            CASE 
                WHEN FIND_IN_SET('MARTES', DIA_VIRTUAL) > 0 THEN NULL
                ELSE M
            END
    END as M,
    CASE 
        WHEN modalidades_distintas = 1 THEN MAX(I)
        WHEN MODALIDAD = 'VIRTUAL' THEN
            CASE 
                WHEN FIND_IN_SET('MIERCOLES', DIA_PRESENCIAL) > 0 THEN NULL
                ELSE I
            END
        ELSE
            CASE 
                WHEN FIND_IN_SET('MIERCOLES', DIA_VIRTUAL) > 0 THEN NULL
                ELSE I
            END
    END as I,
    CASE 
        WHEN modalidades_distintas = 1 THEN MAX(J)
        WHEN MODALIDAD = 'VIRTUAL' THEN
            CASE 
                WHEN FIND_IN_SET('JUEVES', DIA_PRESENCIAL) > 0 THEN NULL
                ELSE J
            END
        ELSE
            CASE 
                WHEN FIND_IN_SET('JUEVES', DIA_VIRTUAL) > 0 THEN NULL
                ELSE J
            END
    END as J,
    CASE 
        WHEN modalidades_distintas = 1 THEN MAX(V)
        WHEN MODALIDAD = 'VIRTUAL' THEN
            CASE 
                WHEN FIND_IN_SET('VIERNES', DIA_PRESENCIAL) > 0 THEN NULL
                ELSE V
            END
        ELSE
            CASE 
                WHEN FIND_IN_SET('VIERNES', DIA_VIRTUAL) > 0 THEN NULL
                ELSE V
            END
    END as V,
    MAX(S) as S,
    MAX(D) as D,
    rb.HORA_INICIAL,
    rb.HORA_FINAL,
    rb.MODULO,
    MAX(AULA) as AULA,
    MAX(DIA_PRESENCIAL) as DIA_PRESENCIAL,
    MAX(DIA_VIRTUAL) as DIA_VIRTUAL,
    rb.MODALIDAD
FROM registros_base rb
GROUP BY 
    rb.CRN,
    rb.HORA_INICIAL,
    rb.HORA_FINAL,
    rb.MODULO,
    rb.MODALIDAD,
    modalidades_distintas
ORDER BY 
    rb.CRN,
    rb.HORA_INICIAL,
    rb.MODALIDAD";

$stmt = $conexion->prepare($sql_select);
if ($stmt === false) {
    die("Error preparando la consulta: " . $conexion->error);
}

$stmt->bind_param("ii", $departamento_id, $departamento_id);
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
            $valor = $data[$columna] ?? '';

            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                $valor
            );
            $col++;
        }
        $row++;
    }
}

$sheet->setTitle("Data_Cotejada_$nombre_departamento");

// Configurar headers para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_Cotejada_' . $nombre_departamento . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

$stmt->close();
$conexion->close();
exit;

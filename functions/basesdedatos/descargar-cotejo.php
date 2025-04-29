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

// Consulta SQL modificada para no combinar cuando MODALIDAD es MIXTA/HIBRIDA
$sql_select = "
WITH modalidades_info AS (
    SELECT 
        CRN,
        HORA_INICIAL,
        HORA_FINAL,
        MODULO,
        COUNT(DISTINCT MODALIDAD) as modalidades_distintas,
        MAX(CASE WHEN UPPER(MODALIDAD) IN ('MIXTA', 'HIBRIDA') THEN 1 ELSE 0 END) as tiene_modalidad_mixta
    FROM `$tabla_departamento`
    WHERE Departamento_ID = ? AND (PAPELERA <> 'INACTIVO' OR PAPELERA IS NULL)
    GROUP BY CRN, HORA_INICIAL, HORA_FINAL, MODULO
),
registros_base AS (
    SELECT 
        t.*,
        mi.modalidades_distintas,
        mi.tiene_modalidad_mixta
    FROM `$tabla_departamento` t
    JOIN modalidades_info mi ON 
        t.CRN = mi.CRN AND 
        t.HORA_INICIAL = mi.HORA_INICIAL AND 
        t.HORA_FINAL = mi.HORA_FINAL AND 
        t.MODULO = mi.MODULO
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
        WHEN tiene_modalidad_mixta = 1 THEN L
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
        WHEN tiene_modalidad_mixta = 1 THEN M
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
        WHEN tiene_modalidad_mixta = 1 THEN I
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
        WHEN tiene_modalidad_mixta = 1 THEN J
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
        WHEN tiene_modalidad_mixta = 1 THEN V
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
    CASE
        WHEN tiene_modalidad_mixta = 1 THEN S
        ELSE MAX(S)
    END as S,
    CASE
        WHEN tiene_modalidad_mixta = 1 THEN D
        ELSE MAX(D)
    END as D,
    rb.HORA_INICIAL,
    rb.HORA_FINAL,
    rb.MODULO,
    CASE
        WHEN tiene_modalidad_mixta = 1 THEN AULA
        ELSE MAX(AULA)
    END as AULA,
    CASE
        WHEN tiene_modalidad_mixta = 1 THEN DIA_PRESENCIAL
        ELSE MAX(DIA_PRESENCIAL)
    END as DIA_PRESENCIAL,
    CASE
        WHEN tiene_modalidad_mixta = 1 THEN DIA_VIRTUAL
        ELSE MAX(DIA_VIRTUAL)
    END as DIA_VIRTUAL,
    rb.MODALIDAD
FROM registros_base rb
GROUP BY 
    rb.CRN,
    rb.HORA_INICIAL,
    rb.HORA_FINAL,
    rb.MODULO,
    rb.MODALIDAD,
    modalidades_distintas,
    tiene_modalidad_mixta,
    CASE WHEN tiene_modalidad_mixta = 1 THEN rb.ID_Plantilla ELSE 0 END
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
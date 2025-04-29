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
$dias_semana = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
$mapeo_dias = [
    'L' => 'LUNES',
    'M' => 'MARTES',
    'I' => 'MIERCOLES',
    'J' => 'JUEVES',
    'V' => 'VIERNES',
    'S' => 'SABADO',
    'D' => 'DOMINGO'
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

// Consulta SQL modificada para mantener siempre la información en DIA_PRESENCIAL y DIA_VIRTUAL
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
    -- Para L (LUNES)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN L
        WHEN modalidades_distintas = 1 THEN MAX(L)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('LUNES', rb.DIA_PRESENCIAL) > 0 THEN L
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('LUNES', rb.DIA_VIRTUAL) > 0 THEN L
        ELSE NULL
    END as L,
    -- Para M (MARTES)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN M
        WHEN modalidades_distintas = 1 THEN MAX(M)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('MARTES', rb.DIA_PRESENCIAL) > 0 THEN M
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('MARTES', rb.DIA_VIRTUAL) > 0 THEN M
        ELSE NULL
    END as M,
    -- Para I (MIERCOLES)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN I
        WHEN modalidades_distintas = 1 THEN MAX(I)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('MIERCOLES', rb.DIA_PRESENCIAL) > 0 THEN I
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('MIERCOLES', rb.DIA_VIRTUAL) > 0 THEN I
        ELSE NULL
    END as I,
    -- Para J (JUEVES)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN J
        WHEN modalidades_distintas = 1 THEN MAX(J)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('JUEVES', rb.DIA_PRESENCIAL) > 0 THEN J
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('JUEVES', rb.DIA_VIRTUAL) > 0 THEN J
        ELSE NULL
    END as J,
    -- Para V (VIERNES)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN V
        WHEN modalidades_distintas = 1 THEN MAX(V)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('VIERNES', rb.DIA_PRESENCIAL) > 0 THEN V
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('VIERNES', rb.DIA_VIRTUAL) > 0 THEN V
        ELSE NULL
    END as V,
    -- Para S (SABADO)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN S
        WHEN modalidades_distintas = 1 THEN MAX(S)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('SABADO', rb.DIA_PRESENCIAL) > 0 THEN S
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('SABADO', rb.DIA_VIRTUAL) > 0 THEN S
        ELSE NULL
    END as S,
    -- Para D (DOMINGO)
    CASE 
        WHEN tiene_modalidad_mixta = 1 THEN D
        WHEN modalidades_distintas = 1 THEN MAX(D)
        WHEN rb.MODALIDAD = 'PRESENCIAL' AND FIND_IN_SET('DOMINGO', rb.DIA_PRESENCIAL) > 0 THEN D
        WHEN rb.MODALIDAD = 'VIRTUAL' AND FIND_IN_SET('DOMINGO', rb.DIA_VIRTUAL) > 0 THEN D
        ELSE NULL
    END as D,
    rb.HORA_INICIAL,
    rb.HORA_FINAL,
    rb.MODULO,
    CASE
        WHEN tiene_modalidad_mixta = 1 THEN AULA
        ELSE MAX(AULA)
    END as AULA,
    -- Mantener siempre DIA_PRESENCIAL tal como está
    GROUP_CONCAT(DISTINCT CASE WHEN DIA_PRESENCIAL IS NOT NULL AND DIA_PRESENCIAL != '' THEN DIA_PRESENCIAL END SEPARATOR ',') as DIA_PRESENCIAL,
    -- Mantener siempre DIA_VIRTUAL tal como está
    GROUP_CONCAT(DISTINCT CASE WHEN DIA_VIRTUAL IS NOT NULL AND DIA_VIRTUAL != '' THEN DIA_VIRTUAL END SEPARATOR ',') as DIA_VIRTUAL,
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
<?php
require './../../vendor/autoload.php';
include './../../config/db.php';
session_start();

$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;

if (empty($departamento_id)) {
    die("Error: Falta el ID del departamento.");
}

$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();

if ($result_departamento->num_rows == 0) {
    die("Error: No se encontró el departamento con ID $departamento_id.");
}

$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento']; // Ya tiene guiones bajos
$nombre_departamento_display = $row_departamento['Departamentos']; // Nombre con espacios/acentos

// Construir el nombre de la tabla correctamente
$tabla_departamento = "data_" . $nombre_departamento;

// Mapeo de días de la semana
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
    'CICLO', 'CRN', 'FECHA_INICIAL', 'FECHA_FINAL',
    'L', 'M', 'I', 'J', 'V', 'S', 'D',
    'HORA_INICIAL', 'HORA_FINAL', 'MODULO', 'AULA',
    'DIA_PRESENCIAL', 'DIA_VIRTUAL', 'MODALIDAD'
];

// Consulta SQL mejorada para manejar correctamente las modalidades y días
$sql_select = "
WITH clases_normalizadas AS (
    SELECT 
        t.*,
        CASE 
            WHEN t.MODALIDAD IS NULL OR TRIM(t.MODALIDAD) = '' THEN 'SIN_ESPECIFICAR'
            WHEN UPPER(t.MODALIDAD) IN ('MIXTA', 'HIBRIDA', 'MIXTO') THEN 'MIXTO'
            WHEN UPPER(t.MODALIDAD) LIKE '%PRESENCIAL%' THEN 'PRESENCIAL'
            WHEN UPPER(t.MODALIDAD) = 'VIRTUAL' THEN 'VIRTUAL'
            ELSE t.MODALIDAD
        END AS modalidad_normalizada,
        CASE 
            WHEN t.MODULO LIKE '%CVIRTU%' THEN 'VIRTUAL'
            WHEN t.MODULO LIKE '%CED%' OR t.MODULO LIKE '%CEDA%' OR t.MODULO LIKE '%CEDC%' OR t.MODULO LIKE '%CEDN%' THEN 'PRESENCIAL'
            ELSE NULL
        END AS tipo_sesion
    FROM `$tabla_departamento` t
    WHERE t.Departamento_ID = ? AND (t.PAPELERA <> 'INACTIVO' OR t.PAPELERA IS NULL)
),
registros_finales AS (
    -- Para modalidades únicas, incluyendo registros sin modalidad especificada
    SELECT DISTINCT
        CICLO, CRN, FECHA_INICIAL, FECHA_FINAL,
        L, M, I, J, V, S, D,
        HORA_INICIAL, HORA_FINAL, MODULO, AULA,
        DIA_PRESENCIAL, DIA_VIRTUAL, modalidad_normalizada AS MODALIDAD
    FROM clases_normalizadas
    WHERE modalidad_normalizada IN ('PRESENCIAL', 'VIRTUAL', 'SIN_ESPECIFICAR')
    
    UNION ALL
    
    -- Para modalidades mixtas, procesar los días según el tipo de sesión
    SELECT 
        cn.CICLO, cn.CRN, cn.FECHA_INICIAL, cn.FECHA_FINAL,
        -- Solo mostrar el día si corresponde al tipo de sesión
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('LUNES', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.L ELSE NULL END AS L,
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('MARTES', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.M ELSE NULL END AS M,
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('MIERCOLES', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.I ELSE NULL END AS I,
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('JUEVES', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.J ELSE NULL END AS J,
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('VIERNES', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.V ELSE NULL END AS V,
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('SABADO', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.S ELSE NULL END AS S,
        CASE WHEN cn.tipo_sesion = 'PRESENCIAL' AND FIND_IN_SET('DOMINGO', COALESCE(cn.DIA_PRESENCIAL, '')) > 0 THEN cn.D ELSE NULL END AS D,
        cn.HORA_INICIAL, cn.HORA_FINAL, cn.MODULO, cn.AULA,
        cn.DIA_PRESENCIAL, cn.DIA_VIRTUAL, cn.modalidad_normalizada AS MODALIDAD
    FROM clases_normalizadas cn
    WHERE cn.modalidad_normalizada = 'MIXTO' AND cn.tipo_sesion = 'PRESENCIAL'
    
    UNION ALL
    
    SELECT 
        cn.CICLO, cn.CRN, cn.FECHA_INICIAL, cn.FECHA_FINAL,
        -- Solo mostrar el día si corresponde al tipo de sesión
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('LUNES', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.L ELSE NULL END AS L,
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('MARTES', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.M ELSE NULL END AS M,
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('MIERCOLES', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.I ELSE NULL END AS I,
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('JUEVES', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.J ELSE NULL END AS J,
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('VIERNES', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.V ELSE NULL END AS V,
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('SABADO', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.S ELSE NULL END AS S,
        CASE WHEN cn.tipo_sesion = 'VIRTUAL' AND FIND_IN_SET('DOMINGO', COALESCE(cn.DIA_VIRTUAL, '')) > 0 THEN cn.D ELSE NULL END AS D,
        cn.HORA_INICIAL, cn.HORA_FINAL, cn.MODULO, cn.AULA,
        cn.DIA_PRESENCIAL, cn.DIA_VIRTUAL, cn.modalidad_normalizada AS MODALIDAD
    FROM clases_normalizadas cn
    WHERE cn.modalidad_normalizada = 'MIXTO' AND cn.tipo_sesion = 'VIRTUAL'
)
SELECT * FROM registros_finales
ORDER BY CRN, HORA_INICIAL, MODALIDAD, MODULO
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

// Aplicar estilo de encabezado
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4'],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
];

// Escribir encabezados en el Excel
foreach ($columnas_exportar as $index => $header) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    $sheet->setCellValue($col . '1', $header);
    
    // Formatear todas las columnas como texto por defecto
    $sheet->getStyle($col . '2:' . $col . ($result->num_rows + 1))
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
}

// Aplicar estilo de encabezado
$lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columnas_exportar));
$sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

// Ajustar el ancho de las columnas
foreach($columnas_exportar as $index => $header) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Formatear columnas de fecha con formato de fecha específico
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
            
            // Convertir fechas al formato correcto
            if (in_array($columna, $fecha_columns) && !empty($valor)) {
                // Asegurarse de que las fechas se formateen correctamente
                try {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(
                        \DateTime::createFromFormat('d/m/Y', $valor) ?: 
                        \DateTime::createFromFormat('Y-m-d', $valor)
                    );
                    $sheet->setCellValue(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                        $date
                    );
                } catch (Exception $e) {
                    // Si hay error en el formato, mantener el valor original
                    $sheet->setCellValueExplicit(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                        $valor,
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                }
            } else {
                // Para valores que no son fechas
                $sheet->setCellValueExplicit(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                    $valor,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }
            $col++;
        }
        $row++;
    }
}

// Aplicar formato de tabla con filas alternadas
$lastRow = $result->num_rows + 1;
$tableStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => 'BFBFBF'],
        ],
    ],
];
$sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray($tableStyle);

// Aplicar colores alternados a las filas
for ($i = 2; $i <= $lastRow; $i++) {
    if ($i % 2 == 0) {
        $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EAF1FB');
    }
}

$sheet->setTitle(mb_substr("Data_Cotejada_" . transliterarTexto($nombre_departamento), 0, 31));

// Sanitizar el nombre del archivo para la descarga
$nombre_archivo_seguro = transliterarTexto($nombre_departamento_display);
$nombre_archivo_seguro = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre_archivo_seguro);

// Configurar headers para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data_Cotejada_' . $nombre_archivo_seguro . '.xlsx"');
header('Cache-Control: max-age=0');

// Función para transliterar texto (quitar acentos y caracteres especiales)
function transliterarTexto($texto) {
    $no_permitidos = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã","Ã›","ü","Ãœ","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
    $permitidos = array("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","U","o","O","i","a","e","U","I","A","E");
    return str_replace($no_permitidos, $permitidos, $texto);
}

try {
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
} catch (\Exception $e) {
    error_log('Error al generar Excel: ' . $e->getMessage());
    echo 'Error al generar el archivo: ' . $e->getMessage();
}

$stmt->close();
$conexion->close();
exit;
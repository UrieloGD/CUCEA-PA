<?php
require './../../vendor/autoload.php';
include './../../config/db.php';
session_start();

$columnas_seleccionadas = isset($_GET['columnas']) ? json_decode($_GET['columnas'], true) : [];

if (empty($columnas_seleccionadas)) {
    die("Error: Faltan parámetros necesarios.");
}

$mapeo_columnas = [

    'CODIGO' => 'Codigo',
    'PATERNO' => 'Paterno',
    'MATERNO' => 'Materno',
    'NOMBRES' => 'Nombres',
    'NOMBRE COMPLETO' => 'Nombre_completo',
    'SEXO' => 'Sexo',
    'DEPARTAMENTO' => 'Departamento',
    'CATEGORIA ACTUAL' => 'Categoria_actual',
    'CATEGORIA ACTUAL DOS' => 'Categoria_actual_dos',
    'HORAS FRENTE A GRUPO' => 'Horas_frente_grupo',
    'DIVISION' => 'Division',
    'TIPO DE PLAZA' => 'Tipo_plaza',
    'CAT.ACT.' => 'Cat_act',
    'CARGA HORARIA' => 'Carga_horaria',
    'HORAS DEFINITIVAS' => 'Horas_definitivas',
    'HORARIO' => 'Horario',
    'TURNO' => 'Turno',
    'INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION' => 'Investigacion_nombramiento_cambio_funcion',
    'S.N.I.' => 'SNI',
    'SNI DESDE' => 'SNI_desde',
    'CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR' => 'Cambio_dedicacion',
    'INICIO' => 'Inicio',
    'FIN' => 'Fin',
    '2024A' => '2024A',
    'TELEFONO PARTICULAR' => 'Telefono_particular',
    'TELEFONO OFICINA O CELULAR' => 'Telefono_oficina',
    'DOMICILIO' => 'Domicilio',
    'COLONIA' => 'Colonia',
    'C.P.' => 'CP',
    'CIUDAD' => 'Ciudad',
    'ESTADO' => 'Estado',
    'NO. AFIL. I.M.S.S.' => 'No_imss',
    'C.U.R.P.' => 'CURP',
    'RFC' => 'RFC',
    'LUGAR DE NACIMIENTO' => 'Lugar_nacimiento',
    'ESTADO CIVIL' => 'Estado_civil',
    'TIPO DE SANGRE' => 'Tipo_sangre',
    'FECHA NAC.' => 'Fecha_nacimiento',
    'EDAD' => 'Edad',
    'NACIONALIDAD' => 'Nacionalidad',
    'CORREO ELECTRONICO' => 'Correo',
    'CORREOS OFICIALES' => 'Correos_oficiales',
    'ULTIMO GRADO' => 'Ultimo_grado',
    'PROGRAMA' => 'Programa',
    'NIVEL' => 'Nivel',
    'INSTITUCION' => 'Institucion',
    'ESTADO/PAIS' => 'Estado_pais',
    'ANIO' => 'Año',
    'GDO EXP' => 'Gdo_exp',
    'OTRO GRADO2' => 'Otro_grado',
    'PROGRAMA2' => 'Otro_programa',
    'NIVEL2' => 'Otro_nivel',
    'INSTITUCION2' => 'Otro_institucion',
    'ESTADO/PAIS2' => 'Otro_estado_pais',
    'ANIO2' => 'Otro_año',
    'GDO EXP2' => 'Otro_gdo_exp',
    'OTRO GRADO3' => 'Otro_grado_alternativo',
    'PROGRAMA3' => 'Otro_programa_alternativo',
    'NIVEL3' => 'Otro_nivel_altenrativo',
    'INSTITUCION3' => 'Otro_institucion_alternativo',
    'ESTADO/PAIS3' => 'Otro_estado_pais_alternativo',
    'ANIO3' => 'Otro_año_alternativo',
    'GDO EXP3' => 'Otro_gdo_exp_alternativo',
    'PROESDE 24-25' => 'Proesde_24_25',
    'A PARTIR DE' => 'A_partir_de',
    'FECHA DE INGRESO' => 'Fecha_ingreso',
    'ANTIGUEDAD' => 'Antiguedad'
];

function obtenerNombreRealColumna($nombre_mostrado, $mapeo_columnas) {
    // Trim y eliminar espacios extras
    $nombre_mostrado = trim(preg_replace('/\s+/', ' ', $nombre_mostrado));
    
    // Primero verificar en el mapeo
    if (isset($mapeo_columnas[$nombre_mostrado])) {
        return $mapeo_columnas[$nombre_mostrado];
    }
    
    // Si no está en el mapeo, convertir a formato de columna
    return str_replace(' ', '_', strtoupper($nombre_mostrado));
}

$columnas_reales = array_map(function ($columna) use ($mapeo_columnas) {
    return obtenerNombreRealColumna($columna, $mapeo_columnas);
}, $columnas_seleccionadas);

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$tabla_departamento = "coord_per_prof";

// Construir la consulta SQL dinámica con los nombres reales de las columnas
$sql = "SELECT " . implode(", ", $columnas_reales) . " FROM `$tabla_departamento`";

$stmt = $conexion->prepare($sql);
if ($stmt === false) {
    die("Error preparando la consulta: " . $conexion->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Escribir los encabezados en el Excel (usando los nombres mostrados)
foreach ($columnas_seleccionadas as $index => $header) {
    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . '1', $header);
}

// Escribir los datos
if ($result->num_rows > 0) {
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        foreach ($columnas_reales as $col => $header_real) {
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . $row,
                $data[$header_real] ?? ''
            );
        }
        $row++;
    }
} else {
    die("No se encontraron resultados.");
}

$sheet->setTitle("Plantilla Académica");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Plantilla_Academica.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

$stmt->close();
$conexion->close();
exit;

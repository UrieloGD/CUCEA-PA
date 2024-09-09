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
    'CATEGORIA ACTUAL' => 'Categoria_actual_dos',
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
    'SIN DESDE' => 'SIN_desde',
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
    'AÑO' => 'Año',
    'GDO EXP' => 'Gdo_exp',
    'OTRO GRADO' => 'Otro_grado',
    'PROGRAMA' => 'Otro_programa',
    'NIVEL' => 'Otro_nivel',
    'INSTITUCION' => 'Otro_institucion',
    'ESTADO/PAIS' => 'Otro_estado_pais',
    'AÑO' => 'Otro_año',
    'GDO EXP' => 'Otro_gdo_exp',
    'OTRO GRADO' => 'Otro_grado_alternativo',
    'PROGRAMA' => 'Otro_programa_alternativo',
    'NIVEL' => 'Otro_nivel_altenrativo',
    'INSTITUCION' => 'Otro_institucion_alternativo',
    'ESTADO/PAIS' => 'Otro_estado_pais_alternativo',
    'AÑO' => 'Otro_año_alternativo',
    'GDO EXP' => 'Otro_gdo_exp_alternativo',
    'PROESDE 24-25' => 'Proesde_24_25',
    'A PARTIR DE' => 'A_partir_de',
    'FECHA DE INGRESO' => 'Fecha_ingreso',
    'ANTIGÜEDAD' => 'Antiguedad'
];
function obtenerNombreRealColumna($nombre_mostrado, $mapeo_columnas)
{
    return isset($mapeo_columnas[$nombre_mostrado]) ? $mapeo_columnas[$nombre_mostrado] : str_replace(' ', '_', $nombre_mostrado);
}

$columnas_reales = array_map(function ($columna) use ($mapeo_columnas) {
    return obtenerNombreRealColumna($columna, $mapeo_columnas);
}, $columnas_seleccionadas);

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$tabla_departamento = "Coord_Per_Prof";

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
    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index) . '1', $header);
}

// Escribir los datos
if ($result->num_rows > 0) {
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $col = 0;
        foreach ($columnas_reales as $header_real) {
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row,
                $data[$header_real] ?? ''
            );
            $col++;
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

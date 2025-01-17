<?php
// Asegurarse de que los errores no se muestren en la salida
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para manejar errores y devolverlos como JSON
function handleError($errno, $errstr, $errfile, $errline)
{
    $response = [
        'success' => false,
        'error' => "Error: [$errno] $errstr en $errfile en la línea $errline"
    ];
    echo json_encode($response);
    exit;
}

// Establecer el manejador de errores
set_error_handler("handleError");

// Asegurarse de que la salida sea JSON
header('Content-Type: application/json');

// Iniciar la sesión
session_start();

include './../../config/db.php';

$response = ['success' => false, 'oldValue' => '', 'error' => ''];

if (isset($_POST['id']) && isset($_POST['column']) && isset($_POST['value'])) {
    $id = mysqli_real_escape_string($conexion, $_POST['id']);
    $column = mysqli_real_escape_string($conexion, $_POST['column']);
    $value = mysqli_real_escape_string($conexion, $_POST['value']);

    // Deupración de código
    error_log("ID recibido: " . $id);
    error_log("Columna recibida: " . $column);
    error_log("Valor recibido: " . $value);
    error_log("Datos recibidos - ID: $id, Columna: $column, Valor: $value");
    // Mapear los nombres de las columnas si es necesario
    $columnMap = [
        'ID' => 'ID',
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


    if (isset($columnMap[$column])) {
        $column = $columnMap[$column];
    }

    $numericColumns = ['Horas_frente_grupo', 'Horas_definitivas', 'Telefono_particular', 'Telefono_oficina', 'CP', 'No_imss', 'Edad', 'Año', 'Otro_año', 'Otro_año_alternativo'];

    if (in_array($column, $numericColumns)) {
        if (!is_numeric($value)) {
            $response['error'] = "El valor para la columna $column debe ser numérico";
            echo json_encode($response);
            exit;
        }
    }

    $tabla = 'coord_per_prof';

    // Verificar si la columna existe en la tabla
    $sql_check_column = "SHOW COLUMNS FROM `$tabla` LIKE '$column'";
    $result_check_column = mysqli_query($conexion, $sql_check_column);
    if (mysqli_num_rows($result_check_column) == 0) {
        $response['error'] = "La columna $column no existe en la tabla";
        error_log($response['error']);
        echo json_encode($response);
        exit;
    }

    // Obtener el valor antiguo antes de actualizar
    $sql_old = "SELECT `$column` FROM `$tabla` WHERE ID = '$id'";
    error_log("Consulta SQL para obtener valor antiguo: $sql_old");
    $result_old = mysqli_query($conexion, $sql_old);
    if (!$result_old) {
        $response['error'] = "Error al obtener el valor antiguo: " . mysqli_error($conexion);
        error_log($response['error']);
        echo json_encode($response);
        exit;
    }
    $row_old = mysqli_fetch_assoc($result_old);
    if ($row_old === null) {
        $response['error'] = "No se encontró ningún registro con el ID proporcionado: $id";
        error_log($response['error']);
        echo json_encode($response);
        exit;
    }
    $response['oldValue'] = $row_old[$column];

    // Actualizar el valor en la base de datos
    $sql = "UPDATE `$tabla` SET `$column` = '$value' WHERE `ID` = '$id'";
    if (mysqli_query($conexion, $sql)) {
        $response['success'] = true;
    } else {
        $response['error'] = "Error al actualizar: " . mysqli_error($conexion);
    }
} else {
    $response['error'] = "Faltan datos requeridos";
}

echo json_encode($response);

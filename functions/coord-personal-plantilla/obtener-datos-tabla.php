<?php
// Archivo: obtener-datos-tabla.php

// Incluye tu archivo de configuración de la base de datos
include './../../config/db.php';

// Al inicio del archivo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Nombre de la tabla
$tabla_departamento = "coord_per_prof";

// Consulta SQL para obtener todos los registros
$sql = "SELECT * FROM $tabla_departamento";
$result = mysqli_query($conexion, $sql);

// Array para almacenar los datos
$data = array();

// Función para convertir fechas de Excel a formato MySQL
function convertExcelDate($value)
{
    if (!is_numeric($value)) {
        return $value;
    }
    $unix_date = ($value - 25569) * 86400;
    return date("Y-m-d", $unix_date);
}

// Función para formatear fechas para mostrar
function formatDateForDisplay($mysqlDate)
{
    if (!$mysqlDate || $mysqlDate == '0000-00-00') {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $mysqlDate);
    return $date ? $date->format('d/m/Y') : '';
}

// Obtener los datos y formatearlos
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = array(
        "",  // Para la columna del checkbox
        htmlspecialchars($row["ID"] ?? ''),
        htmlspecialchars($row["Codigo"] ?? ''),
        htmlspecialchars($row["Paterno"] ?? ''),
        htmlspecialchars($row["Materno"] ?? ''),
        htmlspecialchars($row["Nombres"] ?? ''),
        htmlspecialchars($row["Nombre_completo"] ?? ''),
        htmlspecialchars($row["Sexo"] ?? ''),
        htmlspecialchars($row["Departamento"] ?? ''),
        htmlspecialchars($row["Categoria_actual"] ?? ''),
        htmlspecialchars($row["Categoria_actual_dos"] ?? ''),
        htmlspecialchars($row["Horas_frente_grupo"] ?? ''),
        htmlspecialchars($row["Division"] ?? ''),
        htmlspecialchars($row["Tipo_plaza"] ?? ''),
        htmlspecialchars($row["Cat_act"] ?? ''),
        htmlspecialchars($row["Carga_horaria"] ?? ''),
        htmlspecialchars($row["Horas_definitivas"] ?? ''),
        htmlspecialchars($row["Horario"] ?? ''),
        htmlspecialchars($row["Turno"] ?? ''),
        htmlspecialchars($row["Investigacion_nombramiento_cambio_funcion"] ?? ''),
        htmlspecialchars($row["SNI"] ?? ''),
        htmlspecialchars($row["SIN_desde"] ?? ''),
        htmlspecialchars($row["Cambio_dedicacion"] ?? ''),
        htmlspecialchars($row["Inicio"] ?? ''),
        htmlspecialchars($row["Fin"] ?? ''),
        htmlspecialchars($row["2024A"] ?? ''),
        htmlspecialchars($row["Telefono_particular"] ?? ''),
        htmlspecialchars($row["Telefono_oficina"] ?? ''),
        htmlspecialchars($row["Domicilio"] ?? ''),
        htmlspecialchars($row["Colonia"] ?? ''),
        htmlspecialchars($row["CP"] ?? ''),
        htmlspecialchars($row["Ciudad"] ?? ''),
        htmlspecialchars($row["Estado"] ?? ''),
        htmlspecialchars($row["No_imss"] ?? ''),
        htmlspecialchars($row["CURP"] ?? ''),
        htmlspecialchars($row["RFC"] ?? ''),
        htmlspecialchars($row["Lugar_nacimiento"] ?? ''),
        htmlspecialchars($row["Estado_civil"] ?? ''),
        htmlspecialchars($row["Tipo_sangre"] ?? ''),
        formatDateForDisplay($row["Fecha_nacimiento"] ?? ''),
        htmlspecialchars($row["Edad"] ?? ''),
        htmlspecialchars($row["Nacionalidad"] ?? ''),
        htmlspecialchars($row["Correo"] ?? ''),
        htmlspecialchars($row["Correos_oficiales"] ?? ''),
        htmlspecialchars($row["Ultimo_grado"] ?? ''),
        htmlspecialchars($row["Programa"] ?? ''),
        htmlspecialchars($row["Nivel"] ?? ''),
        htmlspecialchars($row["Institucion"] ?? ''),
        htmlspecialchars($row["Estado_pais"] ?? ''),
        htmlspecialchars($row["Año"] ?? ''),
        htmlspecialchars($row["Gdo_exp"] ?? ''),
        htmlspecialchars($row["Otro_grado"] ?? ''),
        htmlspecialchars($row["Otro_programa"] ?? ''),
        htmlspecialchars($row["Otro_nivel"] ?? ''),
        htmlspecialchars($row["Otro_institucion"] ?? ''),
        htmlspecialchars($row["Otro_estado_pais"] ?? ''),
        htmlspecialchars($row["Otro_año"] ?? ''),
        htmlspecialchars($row["Otro_gdo_exp"] ?? ''),
        htmlspecialchars($row["Otro_grado_alternativo"] ?? ''),
        htmlspecialchars($row["Otro_programa_alternativo"] ?? ''),
        htmlspecialchars($row["Otro_nivel_altenrativo"] ?? ''),
        htmlspecialchars($row["Otro_institucion_alternativo"] ?? ''),
        htmlspecialchars($row["Otro_estado_pais_alternativo"] ?? ''),
        htmlspecialchars($row["Otro_año_alternativo"] ?? ''),
        htmlspecialchars($row["Otro_gdo_exp_alternativo"] ?? ''),
        htmlspecialchars($row["Proesde_24_25"] ?? ''),
        htmlspecialchars($row["A_partir_de"] ?? ''),
        formatDateForDisplay($row["Fecha_ingreso"] ?? ''),
        htmlspecialchars($row["Antiguedad"] ?? ''),
        // Aquí puedes añadir la lógica para el estado (comparación de horas asignadas vs carga horaria)
    );
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);

// Justo antes de echo json_encode...
$jsonData = json_encode(array("data" => $data));
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
    exit;
}

// Devolver los datos en formato JSON
echo json_encode(array("data" => $data));

header('Content-Type: application/json');
echo $jsonData;
?>
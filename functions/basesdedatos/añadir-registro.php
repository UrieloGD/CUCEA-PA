<?php
include './../../config/db.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Codigo'])) {
    die("Usuario no autenticado.");
}

// Obtener el ID del departamento
$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

if (empty($departamento_id)) {
    die("ID de departamento no proporcionado.");
}

// Obtener el nombre del departamento
$sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Nombre_Departamento'];

// Construir el nombre de la tabla
$tabla_departamento = "data_" . str_replace(' ', '_', $nombre_departamento);

// Preparar la consulta SQL
$sql = "INSERT INTO `$tabla_departamento` (
    Departamento_ID, CICLO, CRN, MATERIA, CVE_MATERIA, SECCION, NIVEL, NIVEL_TIPO, TIPO,
    C_MIN, H_TOTALES, ESTATUS, TIPO_CONTRATO, CODIGO_PROFESOR, NOMBRE_PROFESOR,
    CATEGORIA, DESCARGA, CODIGO_DESCARGA, NOMBRE_DESCARGA, NOMBRE_DEFINITIVO,
    TITULAR, HORAS, CODIGO_DEPENDENCIA, L, M, I, J, V, S, D, DIA_PRESENCIAL,
    DIA_VIRTUAL, MODALIDAD, FECHA_INICIAL, FECHA_FINAL, HORA_INICIAL, HORA_FINAL,
    MODULO, AULA, CUPO, OBSERVACIONES, EXAMEN_EXTRAORDINARIO
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Vincular parámetros
$stmt->bind_param("isssssssssssssssssssssssssssssssssssssssss", 
    $departamento_id, 
    $_POST['ciclo'],
    $_POST['crn'],
    $_POST['materia'],
    $_POST['cve_materia'],
    $_POST['seccion'],
    $_POST['nivel'],
    $_POST['nivel_tipo'],
    $_POST['tipo'],
    $_POST['c_min'],
    $_POST['h_totales'],
    $_POST['estatus'],
    $_POST['tipo_contrato'],
    $_POST['codigo_profesor'],
    $_POST['nombre_profesor'],
    $_POST['categoria'],
    $_POST['descarga'],
    $_POST['codigo_descarga'],
    $_POST['nombre_descarga'],
    $_POST['nombre_definitivo'],
    $_POST['titular'],
    $_POST['horas'],
    $_POST['codigo_dependencia'],
    $_POST['l'],
    $_POST['m'],
    $_POST['i'],
    $_POST['j'],
    $_POST['v'],
    $_POST['s'],
    $_POST['d'],
    $_POST['dia_presencial'],
    $_POST['dia_virtual'],
    $_POST['modalidad'],
    $_POST['fecha_inicial'],
    $_POST['fecha_final'],
    $_POST['hora_inicial'],
    $_POST['hora_final'],
    $_POST['modulo'],
    $_POST['aula'],
    $_POST['cupo'],
    $_POST['observaciones'],
    $_POST['examen_extraordinario']
);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registro añadido correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error añadiendo registro: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
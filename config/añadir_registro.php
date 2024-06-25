<?php
include '../config/db.php';
session_start(); // Iniciar la sesión

// Obtener el ID del departamento desde el formulario
$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

// Obtener el nombre del departamento usando el ID
$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

$ciclo = $_POST['ciclo'];
$nrc = $_POST['nrc'];
$fecha_ini = $_POST['fecha_ini'];
$fecha_fin = $_POST['fecha_fin'];
$l = $_POST['l'];
$m = $_POST['m'];
$i = $_POST['i'];
$j = $_POST['j'];
$v = $_POST['v'];
$s = $_POST['s'];
$d = $_POST['d'];
$hora_ini = $_POST['hora_ini'];
$hora_fin = $_POST['hora_fin'];
$edif = $_POST['edif'];
$aula = $_POST['aula'];

$sql = "INSERT INTO `$tabla_departamento` (CICLO, CRN, MATERIA, CVE_MATERIA, SECCION, NIVEL, NIVEL_TIPO, TIPO, C_MIN, H_TOTALES, ESTATUS, TIPO_CONTRATO, CODIGO_PROFESOR, NOMBRE_PROFESOR, CATEGORIA, DESCARGA, CODIGO_DESCARGA, NOMBRE_DESCARGA, NOMBRE_DEFINITIVO, TITULAR, HORAS, CODIGO_DEPENDENCIA, L, M, I, J, V, S, D, DIA_PRESENCIAL, DIA_VIRTUAL, FECHA_INICIAL, FECHA_FINAL, HORA_INICIAL, HORA_FINAL, MODULO, AULA, CUPO, OBSERVACIONES, EXAMEN_EXTRAORDINARIO Departamento_ID)
        VALUES ('$ciclo', '$nrc', '$fecha_ini', '$fecha_fin', '$l', '$m', '$i', '$j', '$v', '$s', '$d', '$hora_ini', '$hora_fin', '$edif', '$aula', $departamento_id)";

if (mysqli_query($conexion, $sql)) {
    echo "Registro añadido correctamente";
} else {
    echo "Error añadiendo registro: " . mysqli_error($conexion);
}

mysqli_close($conexion);

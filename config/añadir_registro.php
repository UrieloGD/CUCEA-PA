<?php
include '../config/db.php';

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

$sql = "INSERT INTO Data_Plantilla (CICLO, NRC, FECHA_INI, FECHA_FIN, L, M, I, J, V, S, D, HORA_INI, HORA_FIN, EDIF, AULA) VALUES ('$ciclo', '$nrc', '$fecha_ini', '$fecha_fin', '$l', '$m', '$i', '$j', '$v', '$s', '$d', '$hora_ini', '$hora_fin', '$edif', '$aula')";

if (mysqli_query($conexion, $sql)) {
    echo "Registro añadido correctamente";
} else {
    echo "Error añadiendo registro: " . mysqli_error($conexion);
}

mysqli_close($conexion);

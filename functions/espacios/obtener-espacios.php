<?php
include './../../config/db.php';

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$modulo = $_GET['modulo'];
$dia = $_GET['dia'];
$hora_inicio = $_GET['hora_inicio'];
$hora_fin = $_GET['hora_fin'];

$query = "SELECT DISTINCT AULA FROM Data_Auditoría 
          WHERE MODULO = '$modulo' 
          AND $dia IS NOT NULL 
          AND HORA_INICIAL <= '$hora_fin' 
          AND HORA_FINAL >= '$hora_inicio'";

$result = mysqli_query($conexion, $query);

$espacios_ocupados = array();
while ($row = mysqli_fetch_assoc($result)) {
    $espacios_ocupados[] = $row['AULA'];
}

echo json_encode($espacios_ocupados);
?>
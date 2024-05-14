<?php
// Conexión a la base de datos
include '../config/db.php';

// Obtener los datos enviados desde el formulario
$id = isset($_POST['id']) ? $_POST['id'] : '';
$ciclo = isset($_POST['CICLO']) ? $_POST['CICLO'] : '';
$nrc = isset($_POST['NRC']) ? $_POST['NRC'] : '';
$fecha_ini = $_POST['FECHA_INI'];
$fecha_fin = $_POST['FECHA_FIN'];
$L = isset($_POST['L']) ? $_POST['L'] : '';
$M = isset($_POST['M']) ? $_POST['M'] : '';
$I = isset($_POST['I']) ? $_POST['I'] : '';
$J = isset($_POST['J']) ? $_POST['J'] : '';
$V = isset($_POST['V']) ? $_POST['V'] : '';
$S = isset($_POST['S']) ? $_POST['S'] : '';
$D = isset($_POST['D']) ? $_POST['D'] : '';
$hora_ini = isset($_POST['HORA_INI']) ? $_POST['HORA_INI'] : '';
$hora_fin = isset($_POST['HORA_FIN']) ? $_POST['HORA_FIN'] : '';
$edif = isset($_POST['EDIF']) ? $_POST['EDIF'] : '';
$aula = isset($_POST['AULA']) ? $_POST['AULA'] : '';


// Agrega los demás campos que deseas actualizar

// Preparar la consulta SQL
$stmt = mysqli_prepare($conexion, "UPDATE Data_Plantilla SET CICLO = ?, NRC = ?, FECHA_INI = ?, FECHA_FIN = ?, L = ?, M = ?, I = ?, J = ?, V = ?, S = ?, D = ?, HORA_INI = ?, HORA_FIN = ?, EDIF = ?, AULA = ? WHERE ID_Plantilla = ?");
mysqli_stmt_bind_param($stmt, "sssssssssssssssi", $ciclo, $nrc, $fecha_ini, $fecha_fin, $L, $M, $I, $J, $V, $S, $D, $hora_ini, $hora_fin, $edif, $aula, $id);


// Ejecutar la consulta
if (mysqli_stmt_execute($stmt)) {
    echo "Registro actualizado correctamente";
} else {
    echo "Error al actualizar el registro: " . mysqli_stmt_error($stmt);
}

// Cerrar la conexión
mysqli_stmt_close($stmt);
mysqli_close($conexion);

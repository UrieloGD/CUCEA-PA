<?php
// Conexión a la base de datos
include '../config/db.php';
session_start(); // Iniciar la sesión

// Obtener el nombre y el ID del departamento del usuario desde la sesión
$nombre_departamento = $_SESSION['Nombre_Departamento'];
$departamento_id = $_SESSION['Departamento_ID'];

// Obtener los datos enviados desde el formulario
$id = isset($_POST['id']) ? $_POST['id'] : '';
$ciclo = isset($_POST['CICLO']) ? $_POST['CICLO'] : '';
$nrc = isset($_POST['NRC']) ? $_POST['NRC'] : '';
$fecha_ini = isset($_POST['FECHA_INI']) ? $_POST['FECHA_INI'] : '';
$fecha_fin = isset($_POST['FECHA_FIN']) ? $_POST['FECHA_FIN'] : '';
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

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

// Verificar que se proporcionó un ID válido
if (!$id) {
    echo "ID de registro no proporcionado";
    exit;
}

// Consulta SQL para actualizar el registro en la base de datos
$sql = "UPDATE `$tabla_departamento` SET CICLO='$ciclo', NRC='$nrc', FECHA_INI='$fecha_ini', FECHA_FIN='$fecha_fin', L='$L', M='$M', I='$I', J='$J', V='$V', S='$S', D='$D', HORA_INI='$hora_ini', HORA_FIN='$hora_fin', EDIF='$edif', AULA='$aula' WHERE ID_Plantilla='$id' AND Departamento_ID='$departamento_id'";

if (mysqli_query($conexion, $sql)) {
    echo "Registro actualizado correctamente";
} else {
    echo "Error al actualizar el registro: " . mysqli_error($conexion);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
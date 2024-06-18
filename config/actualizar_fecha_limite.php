<?php
include './config/db.php';
include './../JS/actualizar_fecha_limite.js'; // Incluye el archivo donde está la función actualizarFechaLimite

// Obtener la nueva fecha límite del formulario
$nuevaFechaLimite = $_POST['nueva_fecha_limite'];

// Actualizar la fecha límite en la base de datos
$query = "UPDATE fechas_limite SET valor = '$nuevaFechaLimite' WHERE clave = 'fecha_limite'";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
    echo "true";
} else {
    echo "false";
}
?>
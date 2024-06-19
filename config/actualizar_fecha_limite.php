<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener la nueva fecha límite del parámetro enviado
$nuevaFechaLimite = $_POST['nueva_fecha_limite'];
$usuarioId = $_SESSION['Codigo'];

// Insertar la nueva fecha límite en la tabla Fechas_Limite
$sql_insertar_fecha_limite = "INSERT INTO Fechas_Limite (Fecha_Limite, Usuario_ID) VALUES ('$nuevaFechaLimite', '$usuarioId')";
if (mysqli_query($conexion, $sql_insertar_fecha_limite)) {
    echo "Fecha límite actualizada correctamente";
} else {
    echo "Error al actualizar la fecha límite: " . mysqli_error($conexion);
}
?>
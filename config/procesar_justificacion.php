<?php
session_start();
include './config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $justificacion = mysqli_real_escape_string($conexion, $_POST['justificacion']);
    $usuario_id = $_SESSION['usuario_id'];
    $departamento_id = $_POST['departamento_id'];
    $fecha_actual = date("Y-m-d H:i:s");

    // Obtener la última fecha límite
    $sql_fecha_limite = "SELECT Fecha_Limite FROM Fechas_Limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
    $result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
    $row_fecha_limite = mysqli_fetch_assoc($result_fecha_limite);
    $fecha_limite = $row_fecha_limite['Fecha_Limite'];

    $sql = "INSERT INTO Justificaciones (Usuario_ID, Departamento_ID, Justificacion, Fecha_Limite_Superada) 
            VALUES ('$usuario_id', '$departamento_id', '$justificacion', '$fecha_limite')";
    
    if (mysqli_query($conexion, $sql)) {
        // Redirigir de vuelta a plantilla.php
        header("Location: plantilla.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
    }
}
?>
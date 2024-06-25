<?php
session_start();
include './config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $justificacion = mysqli_real_escape_string($conexion, $_POST['justificacion']);
    $departamento_id = $_POST['departamento_id'];
    $usuario_id = $_POST['usuario_id'];

    $sql = "INSERT INTO Justificaciones (Departamento_ID, Usuario_ID, Justificacion) VALUES ('$departamento_id', '$usuario_id', '$justificacion')";

    if (mysqli_query($conexion, $sql)) {
        echo "Justificación guardada exitosamente";
    } else {
        echo "Error al guardar la justificación: " . mysqli_error($conexion);
    }
}

mysqli_close($conexion);
header("Location: ../plantilla.php");
exit();

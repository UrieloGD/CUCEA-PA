<?php
session_start();
include './db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $justificacion = mysqli_real_escape_string($conexion, $_POST['justificacion']);
    $departamento_id = $_POST['departamento_id'];
    $codigo_usuario = $_POST['codigo_usuario'];

    $sql = "INSERT INTO Justificaciones (Departamento_ID, Codigo_Usuario, Justificacion) VALUES ('$departamento_id', '$codigo_usuario', '$justificacion')";

    if (mysqli_query($conexion, $sql)) {
        echo "Justificación guardada exitosamente";
    } else {
        echo "Error al guardar la justificación: " . mysqli_error($conexion);
    }
}

mysqli_close($conexion);
header("Location: ../plantilla.php");
exit();

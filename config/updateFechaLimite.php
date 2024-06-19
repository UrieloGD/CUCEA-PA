<?php
include './db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_fecha_limite = $_POST['fecha_limite'];
    $usuario_id = 1; // Asume que el ID del administrador es 1. Ajusta según sea necesario.

    // Inserta la nueva fecha límite en la tabla Fechas_Limite
    $sql = "INSERT INTO Fechas_Limite (Fecha_Limite, Fecha_Actualizacion, Usuario_ID) VALUES (?, NOW(), ?)";

    if ($stmt = mysqli_prepare($conexion, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $nueva_fecha_limite, $usuario_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../data_departamentos.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conexion);
        }
    } else {
        echo "Error: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
}
mysqli_close($conexion);

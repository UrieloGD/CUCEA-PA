<?php
include './db.php';

session_start(); // Iniciar la sesión al comienzo del archivo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_fecha_limite = $_POST['fecha_limite'];
    $usuario_id = $_SESSION['Codigo'];

    // Inserta la nueva fecha límite en la tabla Fechas_Limite
    $sql = "INSERT INTO Fechas_Limite (Fecha_Limite, Fecha_Actualizacion, Usuario_ID) VALUES (?, NOW(), ?)";

    if ($stmt = mysqli_prepare($conexion, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $nueva_fecha_limite, $usuario_id);
        if (mysqli_stmt_execute($stmt)) {
            // Redirige a data_departamentos.php con un parámetro de éxito
            header("Location: ../data_departamentos.php?success=1");
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

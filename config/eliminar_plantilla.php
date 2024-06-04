<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Método de solicitud: POST\n";
    var_dump($_POST);

    $departamentoId = $_POST['departamentoId'];
    echo "Departamento ID: $departamentoId\n";

    // Eliminar la plantilla de la base de datos
    $sql = "DELETE FROM Plantilla_SA WHERE Departamento_ID = '$departamentoId'";
    if (mysqli_query($conexion, $sql)) {
        echo "Plantilla eliminada exitosamente.";
    } else {
        echo "Error al eliminar la plantilla: " . mysqli_error($conexion);
    }
} else {
    echo "Método de solicitud no permitido.";
}

mysqli_close($conexion);
?>
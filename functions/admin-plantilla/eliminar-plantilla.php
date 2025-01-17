<?php
include './../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['departamento_id'])) {
    $departamento_id = $_GET['departamento_id'];

    // Verificar si hay una plantilla para el departamento
    $sql = "SELECT Nombre_Archivo_Dep FROM plantilla_sa WHERE departamento_id = $departamento_id";
    $result = mysqli_query($conexion, $sql);

    if ($result && $result->num_rows > 0) {
        // Eliminar la plantilla
        $delete_sql = "DELETE FROM plantilla_sa WHERE Departamento_ID = $departamento_id";
        if (mysqli_query($conexion, $delete_sql)) {
            echo 'success';
        } else {
            echo 'Error al eliminar la plantilla: ' . mysqli_error($conexion);
        }
    } else {
        echo 'Error: No hay plantilla asignada para este departamento.';
    }
} else {
    echo 'Error: Solicitud inválida.';
}

mysqli_close($conexion);
?>

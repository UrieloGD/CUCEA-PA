<?php
include '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departamento_id = $_POST['Departamento_ID'];
    $nombre_archivo_dep = $_POST['Nombre_Archivo_Dep'];
    $fecha_subida_dep = $_POST['Fecha_Subida_Dep'];

    // Manejo del archivo subido
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $nombre_archivo = basename($_FILES['file']['name']);
        $directorio_subida = '../uploads/'; // Asegúrate de que este directorio exista y tenga permisos de escritura
        $ruta_archivo = $directorio_subida . $nombre_archivo;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $ruta_archivo)) {
            // Insertar los datos en la base de datos
            $sql = "INSERT INTO subir_plantilla (Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep) 
                    VALUES ('$departamento_id', '$nombre_archivo_dep', '$fecha_subida_dep')";

            if (mysqli_query($conexion, $sql)) {
                header('Location: ../progreso_plantillas.php');
                exit();
            } else {
                echo "Error añadiendo registro: " . mysqli_error($conexion);
            }
        } else {
            echo "Error al mover el archivo subido.";
        }
    } else {
        echo "Error en la subida del archivo.";
    }

    mysqli_close($conexion);
} else {
    echo "Método de solicitud no permitido.";
}
?>


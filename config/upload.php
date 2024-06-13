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
                // Redirigir a la página principal tras una subida exitosa
                header("Location: ../plantillasPA.php?success=true&nombre_archivo=$nombre_archivo_dep&fecha_subida=$fecha_subida_dep"); 
                exit();
            } else {
                echo '<script>alert("Error añadiendo registro: ' . mysqli_error($conexion) . '");</script>';
            }
        } else {
            echo '<script>alert("Error al mover el archivo subido.");</script>';
        }
    } else {
        echo '<script>alert("Error en la subida del archivo.");</script>';
    }

<<<<<<< HEAD
    mysqli_close($conexion);
} else {
    echo '<script>alert("Método de solicitud no permitido.");</script>';
}
?>
=======
$conn->close();
>>>>>>> 2a03537f2fdae694844407480815cf52128b76fe

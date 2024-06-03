<?php
include '../config/db.php';
session_start();
date_default_timezone_set('America/Mexico_City');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departamento_id = $_POST['Departamento_ID'];
    $nombre_archivo_dep = $_FILES['file']['name'];
    $fecha_subida_dep = date('d/m/Y H:i');

    $archivo_temporal = $_FILES['file']['tmp_name'];
    $contenido_archivo_dep = null;
    
    // Leer el archivo temporal en chunks
    $fp = fopen($archivo_temporal, 'rb');
    if ($fp) {
        $contenido_archivo_dep = '';
        while (!feof($fp)) {
            $contenido_archivo_dep .= fread($fp, 8192);
        }
        fclose($fp);
    }
    
    // Escapar el contenido del archivo para evitar problemas de inyección SQL
    $contenido_archivo_dep = mysqli_real_escape_string($conexion, $contenido_archivo_dep);
    
    // Insertar los datos en la base de datos
    $sql = "INSERT INTO Plantilla_SA (Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep, Contenido_Archivo_Dep)
            VALUES ('$departamento_id', '$nombre_archivo_dep', '$fecha_subida_dep', '$contenido_archivo_dep')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: ../plantillasPA.php?success=true&nombre_archivo=$nombre_archivo_dep&fecha_subida=$fecha_subida_dep");
        echo '<script>
            Swal.fire({
                title: "Archivo subido",
                text: "El archivo ' . $nombre_archivo_dep . ' se ha subido correctamente.",
                icon: "success",
                confirmButtonText: "Aceptar"
            });
        </script>';
        exit();
    } else {
        echo '<script>alert("Error añadiendo registro: ' . mysqli_error($conexion) . '");</script>';
    }
} else {
    echo '<script>alert("Método de solicitud no permitido.");</script>';
}

mysqli_close($conexion);
?>
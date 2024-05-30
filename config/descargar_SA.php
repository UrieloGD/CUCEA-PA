<?php
include 'config/db.php';

if (isset($_GET['id'])) {
    $id_archivo = $_GET['id'];

    $sql = "SELECT Nombre_Archivo_Dep, Contenido_Archivo_Dep FROM subir_plantilla WHERE ID_Archivo_Dep = $id_archivo";
    $result = mysqli_query($conexion, $sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre_archivo = $row['Nombre_Archivo_Dep'];
        $contenido_archivo = $row['Contenido_Archivo_Dep'];

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
        echo $contenido_archivo;
        exit();
    } else {
        echo 'No se encontró el archivo.';
    }
} else {
    echo 'ID de archivo no proporcionado.';
}

<?php
include './../../config/db.php';

if (isset($_GET['Departamento_ID'])) {
    $departamento_id = $_GET['Departamento_ID'];

    // Consulta a la base de datos para obtener el nombre y contenido del archivo correspondiente al departamento
    $sql = "SELECT Nombre_Archivo_Dep, Contenido_Archivo_Dep FROM plantilla_sa WHERE Departamento_ID = '$departamento_id'";
    $result = mysqli_query($conexion, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nombre_archivo = $row['Nombre_Archivo_Dep'];
        $contenido_archivo = $row['Contenido_Archivo_Dep'];

        // Remover guiones bajos adicionales del nombre del archivo
        $nombre_archivo = str_replace('_', '', $nombre_archivo);

        // Obtener la extensión del archivo
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);

        // Establecer el tipo de contenido según la extensión
        switch ($extension) {
            case 'xlsx':
                $tipo_contenido = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'xls':
                $tipo_contenido = 'application/vnd.ms-excel';
                break;
            case 'pdf':
                $tipo_contenido = 'application/pdf';
                break;
                // Agrega más casos según los tipos de archivo que manejes
            default:
                $tipo_contenido = 'application/octet-stream';
                break;
        }

        // Enviar el archivo al navegador para descargarlo
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $tipo_contenido);
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
        header('Content-Length: ' . strlen($contenido_archivo));
        echo $contenido_archivo;
        exit;
    } else {
        echo "No se encontró ningún archivo para ese departamento.";
    }
} else {
    echo "No se proporcionó un ID de departamento.";
}

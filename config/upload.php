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

<<<<<<< HEAD
            if (mysqli_query($conexion, $sql)) {
                // Redirigir a la página principal tras una subida exitosa
                header("Location: ../plantillasPA.php?success=true&nombre_archivo=$nombre_archivo_dep&fecha_subida=$fecha_subida_dep"); 
=======
// Verificar si se envió un archivo
if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

    // Obtener el ID del usuario actual desde la sesión
    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;
    $departamento_id = $_SESSION['Departamento_ID'] ?? null;

    if ($usuario_id !== null) {
        // Leer el archivo Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // Obtener el rango de datos
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Obtener el ID del departamento del usuario desde la sesión
        $departamento_id = $_SESSION['Departamento_ID'];

        // Preparar la consulta SQL según el departamento del usuario
        switch ($departamento_id) {
            case 1:
                $tabla_destino = 'Data_Estudios_Regionales';
                break;
            case 2:
                $tabla_destino = 'Data_Finanzas';
                break;
            case 3:
                $tabla_destino = 'Data_Ciencias_Sociales';
                break;
            case 4:
                $tabla_destino = 'Data_PALE';
                break;
            case 5:
                $tabla_destino = 'Data_Posgrados';
                break;
            case 6:
                $tabla_destino = 'Data_Economía';
                break;
            case 7:
                $tabla_destino = 'Data_Recursos_Humanos';
                break;
            case 8:
                $tabla_destino = 'Data_Métodos_Cuantitativos';
                break;
            case 9:
                $tabla_destino = 'Data_Políticas_Públicas';
                break;
            case 10:
                $tabla_destino = 'Data_Administración';
                break;
            default:
                echo json_encode(["success" => false, "message" => "Departamento no válido."]);
>>>>>>> eeecb411d5c51d9e1e9036bf630a4f4bd1fb5255
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

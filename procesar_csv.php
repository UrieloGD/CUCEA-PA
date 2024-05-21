<?php
// Configuración para mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

require './config/db.php';

echo "Inicio del script<br>";

// Consulta SQL para obtener el contenido del archivo desde la base de datos
$sql = "SELECT Contenido_Archivo FROM Plantilla_Dep WHERE ID_Archivo_Dep = ?";
$id_archivo = 10; // Reemplaza con el ID del archivo que deseas procesar
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_archivo);
$stmt->execute();
$stmt->bind_result($contenidoArchivo);
$stmt->fetch();

// Verifica si se encontró el contenido del archivo en la base de datos
if (empty($contenidoArchivo)) {
    echo "El contenido del archivo no pudo ser obtenido de la base de datos.";
    exit;
}

// Abre el contenido del archivo como un flujo de datos
$archivoCSV = fopen("data://text/plain;base64," . base64_encode($contenidoArchivo), 'r');

// Verifica si el archivo CSV se abrió correctamente
if (!$archivoCSV) {
    echo "Error al abrir el archivo CSV.";
    exit;
} else {
    echo "Archivo CSV abierto correctamente.<br>";
}

// Itera sobre las filas del archivo CSV y almacena los datos en la otra tabla
while (($datos = fgetcsv($archivoCSV)) !== false) {
    // Los datos están en el arreglo $datos
    // Inserta los datos en la tabla Data_Finanzas
    $sql = "INSERT INTO Data_Finanzas (CICLO, NRC, FECHA_INI, FECHA_FIN, L, M, I, J, V, S, D, HORA_INI, HORA_FIN, EDIF, AULA) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssssssssssss", ...$datos);
    
    if ($stmt->execute()) {
        echo "Datos insertados correctamente en Data_Finanzas.<br>";
    } else {
        echo "Error al insertar datos: " . $stmt->error . "<br>";
    }
}

// Cierra el archivo CSV
fclose($archivoCSV);

echo "Fin del script<br>";
?>

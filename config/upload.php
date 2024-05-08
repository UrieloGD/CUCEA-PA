<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se envió un archivo
if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];
    $fileError = $_FILES["file"]["error"];

    // Mover el archivo a la ubicación deseada
    $uploadDirectory = "../uploads"; // Cambia esta ruta según tus necesidades
    $uploadPath = $uploadDirectory . basename($fileName);

    if (move_uploaded_file($file, $uploadPath)) {
        // Insertar los detalles del archivo en la base de datos MySQL
        $sql = "INSERT INTO archivos (nombre, ruta, tamaño) VALUES ('$fileName', '$uploadPath', $fileSize)";

        if ($conn->query($sql) === TRUE) {
            echo "Archivo cargado y guardado en la base de datos correctamente.";
        } else {
            echo "Error al guardar el archivo en la base de datos: " . $conn->error;
        }
    } else {
        echo "Error al cargar el archivo.";
    }
} else {
    echo "No se recibió ningún archivo.";
}

$conn->close();

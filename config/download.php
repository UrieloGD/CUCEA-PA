<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener el archivo de la tabla "archivos"
$sql = "SELECT ruta, tamaño FROM archivos WHERE nombre = 'Prueba.xlsx'";
$resultado = $conn->query($sql);

// Verificar si se encontró un resultado
if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $archivo_ruta = $fila["ruta"];
    $archivo_tamaño = $fila["tamaño"];

    // Leer el contenido del archivo desde la ruta
    $archivo_contenido = file_get_contents($archivo_ruta);

    // Configurar los encabezados para descargar el archivo
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=Prueba.xlsx");
    header("Content-Length: " . $archivo_tamaño);

    // Enviar el archivo al navegador
    echo $archivo_contenido;
} else {
    http_response_code(404);
}

// Cerrar la conexión a la base de datos
$conn->close();
exit;

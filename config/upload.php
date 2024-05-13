<?php
session_start(); // Inicializar la sesión

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
    $uploadDirectory = "../uploads/"; // Cambia esta ruta según tus necesidades
    $uploadPath = $uploadDirectory . basename($fileName);

    // Obtener el ID del usuario actual desde la sesión
    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;

    // Verificar si el usuario es Jefe de Departamento
    if ($rol_id == 1 && $usuario_id !== null) {
        // Obtener el departamento del usuario
        $sql_departamento = "SELECT Departamentos.Departamento_ID
            FROM Usuarios_Departamentos
            INNER JOIN Departamentos ON Usuarios_Departamentos.Departamento_ID = Departamentos.Departamento_ID
            WHERE Usuario_ID = $usuario_id";
        $result_departamento = $conn->query($sql_departamento);

        if ($result_departamento->num_rows > 0) {
            $row_departamento = $result_departamento->fetch_assoc();
            $departamento_id = $row_departamento['Departamento_ID'];

            // Insertar los detalles del archivo en la tabla Plantilla_Dep
            $sql = "INSERT INTO Plantilla_Dep (Nombre_Archivo_Dep, Ruta_Archivo_Dep, Tamaño_Archivo_Dep, Usuario_ID, Departamento_ID) 
                    VALUES ('$fileName', '$uploadPath', $fileSize, $usuario_id, $departamento_id)";

            if ($conn->query($sql) === TRUE) {
                echo "Archivo cargado y guardado en la base de datos correctamente.";
            } else {
                echo "Error al guardar el archivo en la base de datos: " . $conn->error;
            }
        } else {
            echo "El usuario no está asociado a ningún departamento.";
        }
    } else {
        // Si el usuario no es Jefe de Departamento, guardar en Plantilla_SA
        $sql = "INSERT INTO Plantilla_SA (Nombre_Archivo_SA, Ruta_Archivo_SA, Tamaño_Archivo_SA, Usuario_ID) 
                VALUES ('$fileName', '$uploadPath', $fileSize, $usuario_id)";

        if ($conn->query($sql) === TRUE) {
            echo "Archivo cargado y guardado en la base de datos correctamente.";
        } else {
            echo "Error al guardar el archivo en la base de datos: " . $conn->error;
        }
    }
} else {
    echo "No se recibió ningún archivo.";
}

$conn->close();
?>

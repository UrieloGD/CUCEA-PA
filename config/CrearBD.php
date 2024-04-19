<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dbname = "CREATE DATABASE PA;";

if ($conn->query($dbname) == TRUE) {
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

mysqli_select_db($conn, "PA");

$sql = "CREATE TABLE IF NOT EXISTS Usuarios (
    ID int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    Correo VARCHAR(500) NOT NULL,
    Pass VARCHAR(60) NOT NULL,
    Codigo INT (10) Not NULL,
    Nombre VARCHAR(60) NOT NULL,
    Apellido VARCHAR (60) NULL,
    Rol VARCHAR(80) NOT NULL,
    Genero VARCHAR (20) NOT NULL
    )";

if (mysqli_query($conn, $sql)) {
    echo "Table Usuarios created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// Consulta para insertar registros en la tabla Usuarios
$insert_sql = "INSERT INTO Usuarios (Correo, Pass, Codigo, Nombre, Apellido, Rol, Genero)
VALUES
    ('maria@cucea.udg.mx', '123', 1234567890, 'María', 'García', 'Jefe de Departamento', 'Femenino'),
    ('juan@cucea.udg.mx', '123', 0987654321, 'Juan', 'Rodríguez', 'Secretaría Administrativa', 'Masculino'),
    ('ana@cucea.udg.mx', '123', 5678901234, 'Ana', 'Martínez', 'Jefe de Departamento', 'Femenino'),
    ('pedro@cucea.udg.mx', '123', 2345678901, 'Pedro', 'Gómez', 'Secretaría Administrativa', 'Masculino'),
    ('laura@cucea.udg.mx', '123', 9012345678, 'Laura', 'Hernández', 'Jefe de Departamento', 'Femenino'),
    ('carlos@cucea.udg.mx', '123', 6789012345, 'Carlos', 'López', 'Secretaría Administrativa', 'Masculino'),
    ('sofia@cucea.udg.mx', '123', 3456789012, 'Sofía', 'Sánchez', 'Jefe de Departamento', 'Femenino'),
    ('diego@cucea.udg.mx', '123', 7890123456, 'Diego', 'Ramírez', 'Secretaría Administrativa', 'Masculino'),
    ('isabella@cucea.udg.mx', '123', 4567890123, 'Isabella', 'Torres', 'Jefe de Departamento', 'Femenino'),
    ('alejandro@cucea.udg.mx', '123', 8901234567, 'Alejandro', 'Flores', 'Secretaría Administrativa', 'Masculino')";

if (mysqli_query($conn, $insert_sql)) {
    echo "Records inserted successfully";
} else {
    echo "Error inserting records: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
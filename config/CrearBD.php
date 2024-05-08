<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dbname = "CREATE DATABASE PA;";

if ($conn->query($dbname) == TRUE) {
    echo "Database created successfully <br>";
} else {
    echo "<br>Error creando base de datos: " . $conn->error;
}

mysqli_select_db($conn, "PA");

// Consulta SQL para crear la tabla Usuarios
$sql = "CREATE TABLE IF NOT EXISTS Usuarios (
    Codigo BIGINT (10) Not NULL PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Rol VARCHAR(80) NOT NULL,
    Correo VARCHAR(100) NOT NULL,
    Pass VARCHAR(32) NOT NULL,
    Genero VARCHAR (20) NOT NULL,
    FOREIGN KEY (Rol) REFERENCES Jefes_Departamento(Rol)
    )";

if (mysqli_query($conn, $sql)) {
    echo "Tabla Usuarios creada exitosamente";
} else {
    echo "<br> Error creando tabla Usuarios: " . mysqli_error($conn);
}

// Consulta para insertar registros en la tabla Usuarios
$insert_sql = "INSERT INTO Usuarios (Correo, Pass, Codigo, Nombre, Apellido, Rol, Genero)
VALUES
    ('juan.perez@cucea.udg.mx', '123', 2100123456, 'Juan', 'Perez', 'Jefe de Departamento', 'Masculino'),
    ('maria.lopez@cucea.udg.mx', '123', 2101234567, 'Maria', 'Lopez', 'Secretaria Administrativa', 'Femenino'),
    ('luis.garcia@cucea.udg.mx', '123', 2102345678, 'Luis', 'Garcia', 'Coordinacion de Personal', 'Masculino'),
    ('ana.martinez@cucea.udg.mx', '123', 2103456789, 'Ana', 'Martinez', 'Jefe de Departamento', 'Femenino'),
    ('pedro.rodriguez@cucea.udg.mx', '123', 2104567890, 'Pedro', 'Rodriguez', 'Secretaria Administrativa', 'Masculino'),
    ('sofia.gonzalez@cucea.udg.mx', '123', 2105678901, 'Sofia', 'Gonzalez', 'Coordinacion de Personal', 'Femenino'),
    ('carlos.hernandez@cucea.udg.mx', '123', 2106789012, 'Carlos', 'Hernandez', 'Jefe de Departamento', 'Masculino'),
    ('laura.diaz@cucea.udg.mx', '123', 2107890123, 'Laura', 'Diaz', 'Secretaria Administrativa', 'Femenino'),
    ('daniel.sanchez@cucea.udg.mx', '123', 2108901234, 'Daniel', 'Sanchez', 'Coordinacion de Personal', 'Masculino'),
    ('monica.ramirez@cucea.udg.mx', '123', 2109012345, 'Monica', 'Ramirez', 'Coordinacion de Personal', 'Femenino')";

if (mysqli_query($conn, $sql)) {
    echo "<br>Registros insertados exitosamente en tabla Usuarios";
} else {
    echo "<br>Error insertando los registros en tabla Usuarios: " . mysqli_error($conn);
}

// Consulta SQL para crear la tabla Jefes de Departamento
$sql = "CREATE TABLE IF NOT EXISTS Jefes_de_Departamento (
    Rol VARCHAR(80) NOT NULL,
    Departamento VARCHAR(100) NOT NULL,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Codigo BIGINT(10),
    FOREIGN KEY (Codigo) REFERENCES Usuarios(Codigo),
    PRIMARY KEY (Rol),
    FOREIGN KEY (Nombre, Apellido) REFERENCES Usuarios(Nombre, Apellido)
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabla Jefes de Departamento creada exitosamente";
} else {
    echo "<br> Error creando tabla Jefes de Departamento: " . mysqli_error($conn);
}

// Consulta SQL para crear la tabla Secretaria Administrativa
$sql = "CREATE TABLE IF NOT EXISTS Secretaria_Administrativa (
    Rol VARCHAR(80) NOT NULL,
    Departamento VARCHAR(100) NOT NULL,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Codigo BIGINT(10),
    FOREIGN KEY (Codigo) REFERENCES Usuarios(Codigo),
    PRIMARY KEY (Rol),
    FOREIGN KEY (Nombre, Apellido) REFERENCES Usuarios(Nombre, Apellido)
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabla Secretaria Administrativa creada exitosamente";
} else {
    echo "<br> Error creando tabla Secretaria Administrativa: " . mysqli_error($conn);
}

// Consulta SQL para crear la tabla Coordinacion de Personal
$sql = "CREATE TABLE IF NOT EXISTS Coordinacion_de_Personal (
    Rol VARCHAR(80) NOT NULL,
    Departamento VARCHAR(100) NOT NULL,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Codigo BIGINT(10),
    FOREIGN KEY (Codigo) REFERENCES Usuarios(Codigo),
    PRIMARY KEY (Rol),
    FOREIGN KEY (Nombre, Apellido) REFERENCES Usuarios(Nombre, Apellido)
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabla Secretaria Administrativa creada exitosamente";
} else {
    echo "<br> Error creando tabla Secretaria Administrativa: " . mysqli_error($conn);
}

// Consulta SQL para crear la tabla Archivos
$sql = "CREATE TABLE IF NOT EXISTS archivos (
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    tamaño INT NOT NULL
    )";

if (mysqli_query($conn, $sql)) {
    echo "<br>Table Archivos creada exitosamente";
} else {
    echo "<br>Error creating table Archivos: " . mysqli_error($conn);
}


$sql = "CREATE TABLE IF NOT EXISTS BD (
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    CICLO VARCHAR (10) NOT NULL,
    NRC VARCHAR (15) NOT NULL,
    `FECHA INI` VARCHAR (10) NOT NULL,
    `FECHA FIN` VARCHAR (10) NOT NULL,
    L VARCHAR (5) NOT NULL,
    M VARCHAR (5) NOT NULL,
    I VARCHAR (5) NOT NULL,
    J VARCHAR (5) NOT NULL,
    V VARCHAR (5) NOT NULL,
    S VARCHAR (5) NOT NULL,
    D VARCHAR (5) NOT NULL,
    `HORA INI` VARCHAR (10) NOT NULL,
    `HORA FIN` VARCHAR (10) NOT NULL,
    EDIF VARCHAR (10) NOT NULL,
    AULA VARCHAR (10) NOT NULL
    )";

if (mysqli_query($conn, $sql)) {
    echo "<br>Table BD created successfully";
} else {
    echo "<br>Error creating table BD: " . mysqli_error($conn);
}

mysqli_close($conn);

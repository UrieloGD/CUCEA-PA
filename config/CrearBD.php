<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); //Para iniciar sesión en Windows (WAMP) se deja vacío
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dbname = "CREATE DATABASE IF NOT EXISTS PA;";

if ($conn->query($dbname) == TRUE) {
    echo "Base de datos creada exitosamente";
} else {
    echo "Error creando base de datos: " . $conn->error;
}

mysqli_select_db($conn, "PA");

// Crear tabla Roles
$sql = "CREATE TABLE IF NOT EXISTS Roles (
    Rol_ID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Rol VARCHAR(80) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Roles creada exitosamente";
} else {
    echo "<br>Error creando tabla Roles: " . mysqli_error($conn);
}

// Insertar roles
$insert_roles = "INSERT INTO Roles (Nombre_Rol) VALUES ('Jefe de Departamento'), ('Secretaria Administrativa'), ('Coordinacion de Personal')";

if (mysqli_query($conn, $insert_roles)) {
    echo "<br>Roles insertados exitosamente";
} else {
    echo "<br>Error insertando roles: " . mysqli_error($conn);
}

// Crear tabla Usuarios
$sql = "CREATE TABLE IF NOT EXISTS Usuarios (
    Codigo BIGINT(10) NOT NULL PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Correo VARCHAR(100) NOT NULL,
    Pass VARCHAR(32) NOT NULL,
    Genero VARCHAR(20) NOT NULL,
    Rol_ID INT,
    FOREIGN KEY (Rol_ID) REFERENCES Roles(Rol_ID)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Usuarios creada exitosamente";
} else {
    echo "<br>Error creando tabla Usuarios: " . mysqli_error($conn);
}

// Insertar usuarios
$insert_usuarios = "INSERT INTO Usuarios (Codigo, Nombre, Apellido, Correo, Pass, Genero, Rol_ID)
VALUES
    (2100123456, 'Juan', 'Perez', 'juan.perez@cucea.udg.mx', '123', 'Masculino', 1),
    (2101234567, 'Maria', 'Lopez', 'maria.lopez@cucea.udg.mx', '123', 'Femenino', 2),
    (2102345678, 'Luis', 'Garcia', 'luis.garcia@cucea.udg.mx', '123', 'Masculino', 3),
    (2103456789, 'Ana', 'Martinez', 'ana.martinez@cucea.udg.mx', '123', 'Femenino', 1),
    (2104567890, 'Pedro', 'Rodriguez', 'pedro.rodriguez@cucea.udg.mx', '123', 'Masculino', 2),
    (2105678901, 'Sofia', 'Gonzalez', 'sofia.gonzalez@cucea.udg.mx', '123', 'Femenino', 3),
    (2106789012, 'Carlos', 'Hernandez', 'carlos.hernandez@cucea.udg.mx', '123', 'Masculino', 1),
    (2107890123, 'Laura', 'Diaz', 'laura.diaz@cucea.udg.mx', '123', 'Femenino', 2),
    (2108901234, 'Daniel', 'Sanchez', 'daniel.sanchez@cucea.udg.mx', '123', 'Masculino', 3),
    (2109012345, 'Monica', 'Ramirez', 'monica.ramirez@cucea.udg.mx', '123', 'Femenino', 3),
    (2110123456, 'Pedro', 'Gómez', 'pedro.gomez@cucea.udg.mx', '123', 'Masculino', 1),
    (2111234567, 'Laura', 'Torres', 'laura.torres@cucea.udg.mx', '123', 'Femenino', 1),
    (2112345678, 'Javier', 'Ruiz', 'javier.ruiz@cucea.udg.mx', '123', 'Masculino', 1),
    (2113456789, 'Claudia', 'Flores', 'claudia.flores@cucea.udg.mx', '123', 'Femenino', 1),
    (2114567890, 'Ernesto', 'Mendoza', 'ernesto.mendoza@cucea.udg.mx', '123', 'Masculino', 1),
    (2115678901, 'Mariana', 'Ponce', 'mariana.ponce@cucea.udg.mx', '123', 'Femenino', 1),
    (2116789012, 'Ricardo', 'Vargas', 'ricardo.vargas@cucea.udg.mx', '123', 'Masculino', 1)";

if (mysqli_query($conn, $insert_usuarios)) {
    echo "<br>Usuarios insertados exitosamente";
} else {
    echo "<br>Error insertando usuarios: " . mysqli_error($conn);
}

// Crear tabla Departamentos
$sql = "CREATE TABLE IF NOT EXISTS Departamentos (
    Departamento_ID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Departamento VARCHAR(100) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Departamentos creada exitosamente";
} else {
    echo "<br>Error creando tabla Departamentos: " . mysqli_error($conn);
}

// Insertar departamentos
$insert_departamentos = "INSERT INTO Departamentos (Nombre_Departamento) VALUES
    ('Estudios Regionales'),
    ('Finanzas'),
    ('Ciencias Sociales'),
    ('PALE'),
    ('Posgrados'),
    ('Economía'),
    ('Recursos Humanos'),
    ('Métodos Cuantitativos'),
    ('Políticas Públicas'),
    ('Administración')";

if (mysqli_query($conn, $insert_departamentos)) {
    echo "<br>Departamentos insertados exitosamente";
} else {
    echo "<br>Error insertando departamentos: " . mysqli_error($conn);
}

// Crear tabla Usuarios_Departamentos
$sql = "CREATE TABLE IF NOT EXISTS Usuarios_Departamentos (
    Usuario_ID BIGINT(10) NOT NULL,
    Departamento_ID INT NOT NULL,
    PRIMARY KEY (Usuario_ID, Departamento_ID),
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Codigo) ON DELETE CASCADE,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Usuarios_Departamentos creada exitosamente";
} else {
    echo "<br>Error creando tabla Usuarios_Departamentos: " . mysqli_error($conn);
}

// Insertar relación de usuarios con departamentos (jefes de departamento)
$insert_usuarios_departamentos = "INSERT INTO Usuarios_Departamentos (Usuario_ID, Departamento_ID) VALUES
    (2100123456, 1), -- Juan es jefe del Departamento 1 (Estudios Regionales)
    (2103456789, 2), -- Ana es jefa del Departamento 2 (Finanzas)
    (2106789012, 3), -- Carlos es jefe del Departamento 3 (Ciencias Sociales)
    (2110123456, 4), -- Pedro es jefe del Departamento 4 (PALE)
    (2111234567, 5), -- Laura es jefa del Departamento 5 (Posgrados)
    (2112345678, 6), -- Javier es jefe del Departamento 6 (Economia)
    (2113456789, 7), -- Claudia es jefa del Departamento 7 (Recursos Humanos)
    (2114567890, 8), -- Ernesto es jefe del Departamento 8 (Metodos Cuantitativos)
    (2115678901, 9), -- Mariana es jefa del Departamento 9 (Politicas Publicas)
    (2116789012, 10); -- Ricardo es jefe del Departamento 10 (Administracion)";

if (mysqli_query($conn, $insert_usuarios_departamentos)) {
    echo "<br>Relación de usuarios y departamentos insertada exitosamente";
} else {
    echo "<br>Error insertando relación de usuarios y departamentos: " . mysqli_error($conn);
}

// Crear tabla Plantilla_SA
$sql = "CREATE TABLE IF NOT EXISTS Plantilla_SA (
    ID_Archivo_SA INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo_SA VARCHAR(255) NOT NULL,
    Ruta_Archivo_SA VARCHAR(255) NOT NULL,
    Tamaño_Archivo_SA INT NOT NULL,
    Usuario_ID BIGINT(10),
    Fecha_Subida_SA TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Codigo)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Plantilla_SA creada exitosamente";
} else {
    echo "<br>Error creando tabla Plantilla_SA: " . mysqli_error($conn);
}

// Crear tabla Plantilla_Dep
$sql = "CREATE TABLE IF NOT EXISTS Plantilla_Dep (
    ID_Archivo_Dep INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo_Dep VARCHAR(255) NOT NULL,
    Ruta_Archivo_Dep VARCHAR(255) NOT NULL,
    Tamaño_Archivo_Dep INT NOT NULL,
    Usuario_ID BIGINT(10),
    Fecha_Subida_Dep TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Departamento_ID INT NOT NULL,
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Codigo),
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Plantilla_Dep creada exitosamente";
} else {
    echo "<br>Error creando tabla Plantilla_Dep: " . mysqli_error($conn);
}

// Crear tabla Data_Plantilla
$sql = "CREATE TABLE IF NOT EXISTS Data_Plantilla (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    CICLO VARCHAR(10) NOT NULL,
    NRC VARCHAR(15) NOT NULL,
    `FECHA INI` VARCHAR(10) NOT NULL,
    `FECHA FIN` VARCHAR(10) NOT NULL,
    L VARCHAR(5) NOT NULL,
    M VARCHAR(5) NOT NULL,
    I VARCHAR(5) NOT NULL,
    J VARCHAR(5) NOT NULL,
    V VARCHAR(5) NOT NULL,
    S VARCHAR(5) NOT NULL,
    D VARCHAR(5) NOT NULL,
    `HORA INI` VARCHAR(10) NOT NULL,
    `HORA FIN` VARCHAR(10) NOT NULL,
    EDIF VARCHAR(10) NOT NULL,
    AULA VARCHAR(10) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_plantilla creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_plantilla: " . mysqli_error($conn);
}

// Crear tabla Data_Estudios_Regionales
$sql = "CREATE TABLE IF NOT EXISTS Data_Estudios_Regionales (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    NRC VARCHAR(15) NOT NULL,
    `FECHA INI` VARCHAR(10) NOT NULL,
    `FECHA FIN` VARCHAR(10) NOT NULL,
    L VARCHAR(5) NOT NULL,
    M VARCHAR(5) NOT NULL,
    I VARCHAR(5) NOT NULL,
    J VARCHAR(5) NOT NULL,
    V VARCHAR(5) NOT NULL,
    S VARCHAR(5) NOT NULL,
    D VARCHAR(5) NOT NULL,
    `HORA INI` VARCHAR(10) NOT NULL,
    `HORA FIN` VARCHAR(10) NOT NULL,
    EDIF VARCHAR(10) NOT NULL,
    AULA VARCHAR(10) NOT NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Estudios_Regionales creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Estudios_Regionales: " . mysqli_error($conn);
}

// Crear tabla Data_Finanzas
$sql = "CREATE TABLE IF NOT EXISTS Data_Finanzas (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    NRC VARCHAR(15) NOT NULL,
    `FECHA INI` VARCHAR(10) NOT NULL,
    `FECHA FIN` VARCHAR(10) NOT NULL,
    L VARCHAR(5) NOT NULL,
    M VARCHAR(5) NOT NULL,
    I VARCHAR(5) NOT NULL,
    J VARCHAR(5) NOT NULL,
    V VARCHAR(5) NOT NULL,
    S VARCHAR(5) NOT NULL,
    D VARCHAR(5) NOT NULL,
    `HORA INI` VARCHAR(10) NOT NULL,
    `HORA FIN` VARCHAR(10) NOT NULL,
    EDIF VARCHAR(10) NOT NULL,
    AULA VARCHAR(10) NOT NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Finanzas creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Finanzas: " . mysqli_error($conn);
}

mysqli_close($conn);

<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
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

include('./tabla_espacios.php');

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
$insert_roles = "INSERT INTO Roles (Nombre_Rol) VALUES ('Jefe de Departamento'), ('Secretaría Administrativa'), ('Coordinación de Personal')";

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
    Pass VARCHAR(96) NOT NULL,  
    Salt VARCHAR(32) NOT NULL,  
    Genero VARCHAR(20) NOT NULL,
    Rol_ID INT,
    IconoColor VARCHAR(7),
    FOREIGN KEY (Rol_ID) REFERENCES Roles(Rol_ID)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Usuarios creada exitosamente";
} else {
    echo "<br>Error creando tabla Usuarios: " . mysqli_error($conn);
}

// Para el hasheo de contraseñas
function hashPassword($password) {
    $salt = bin2hex(random_bytes(16));
    $hashedPassword = hash('sha256', $salt . $password);
    return ['hash' => $hashedPassword, 'salt' => $salt];
}
// Usuarios (los necesitamos en arreglo para el hasheo de contraseñas)
$usuarios= [
    [2100123456, 'Juan', 'Perez', 'juan.perez@cucea.udg', '123', 'Masculino', 1],
    [2103456789, 'Ana', 'Martinez', 'ana.martinez@cucea.udg', '123', 'Femenino', 1],
    [2106789012, 'Carlos', 'Hernandez', 'carlos.hernandez@cucea.udg', '123', 'Masculino', 1],
    [2110123456, 'Pedro', 'Gómez', 'pedro.gomez@cucea.udg', '123', 'Masculino', 1],
    [111234567, 'Laura', 'Torres', 'laura.torres@cucea.udg', '123', 'Femenino', 1],
    [2112345678, 'Javier', 'Ruiz', 'javier.ruiz@cucea.udg', '123', 'Masculino', 1],
    [2113456789, 'Sara', 'Robles', 'srobles@cucea.udg', '123', 'Femenino', 1],
    [2114567890, 'Guillermo', 'Sierra', 'gsierraj@cucea.udg', '123', 'Masculino', 1],
    [2115678901, 'Mariana', 'Ponce', 'mariana.ponce@cucea.udg', '123', 'Femenino', 1],
    [2116789012, 'César', 'Mora', 'cesar.mora@cucea.udg', '123', 'Masculino', 1],
    [2121234567, 'Alejandro', 'Campos', 'a.campos@cucea.udg', '123', 'Masculino', 1],
    [2130192837, 'José', 'Sánchez', 'jsanchez@cucea.udg', '123', 'Masculino', 1],
    [2140596871, 'Cristian', 'Alcantar', 'cristian.alcantar@cucea.udg', '123', 'Masculino', 1],
    [2151234098, 'Alejandro', 'López', 'alejandro.lopez@cucea.udg', '123', 'Masculino', 1],
    [2161098234, 'Carlos', 'Flores', 'carlos.flores@cucea.udg', '123', 'Masculino', 1],
    [2176859401, 'Javier', 'Ramirez', 'javierr@cucea.udg', '123', 'Masculino', 1],
    [2101234567, 'Maria', 'Lopez', 'maria.lopez@cucea.udg', '123', 'Femenino', 2],
    [2104567890, 'Denisse', 'Murillo', 'denisse.murillo@cucea.udg', '123', 'Masculino', 2],
    [2107890123, 'Laura', 'Diaz', 'laura.diaz@cucea.udg', '123', 'Femenino', 2],
    [2105678901, 'Sofia', 'Gonzalez', 'sofia.gonzalez@cucea.udg', '123', 'Femenino', 3],
    [2102345678, 'Luis', 'Garcia', 'luis.garcia@cucea.udg', '123', 'Masculino', 3],
    [2108901234, 'Daniel', 'Sanchez', 'daniel.sanchez@cucea.udg', '123', 'Masculino', 3],
    [2109012345, 'Monica', 'Ramirez', 'monica.ramirez@cucea.udg', '123', 'Femenino', 3]
];

$stmt = $conn->prepare("INSERT INTO Usuarios (Codigo, Nombre, Apellido, Correo, Pass, Salt, Genero, Rol_ID, IconoColor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Insertar usuarios
foreach ($usuarios as $usuario) {
    $passwordData = hashPassword($usuario[4]); // El índice 4 corresponde a la contraseña

    $stmt->bind_param(
        "issssssis",
        $usuario[0], // Codigo
        $usuario[1], // Nombre
        $usuario[2], // Apellido
        $usuario[3], // Correo
        $passwordData['hash'], // Pass hasheada
        $passwordData['salt'], // Salt
        $usuario[5], // Genero
        $usuario[6], // Rol_ID
        $usuario[7]  // IconoColor
    );

    $stmt->execute();
}

$stmt->close();

// Crear tabla Departamentos
$sql = "CREATE TABLE IF NOT EXISTS Departamentos (
    Departamento_ID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Departamento VARCHAR(100) NOT NULL,
    Departamentos VARCHAR(100) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Departamentos creada exitosamente";
} else {
    echo "<br>Error creando tabla Departamentos: " . mysqli_error($conn);
}

// Insertar departamentos
$insert_departamentos = "INSERT INTO Departamentos (Nombre_Departamento, Departamentos) VALUES
    ('Estudios_Regionales', 'Estudios Regionales'),
    ('Finanzas', 'Finanzas'),
    ('Ciencias_Sociales', 'Ciencias Sociales'),
    ('PALE', 'PALE'),
    ('Posgrados', 'Posgrados'),
    ('Economía', 'Economía'),
    ('Recursos_Humanos', 'Recursos Humanos'),
    ('Métodos_Cuantitativos', 'Métodos Cuantitativos'),
    ('Políticas_Públicas', 'Políticas Públicas'),
    ('Administración', 'Administración'),
    ('Auditoría', 'Auditoría'),
    ('Mercadotecnia', 'Mercadotecnia y Negocios Internacionales'),
    ('Impuestos', 'Impuestos'),
    ('Sistemas_de_Información', 'Sistemas de Información'),
    ('Turismo', 'Turismo'),
    ('Contabilidad', 'Contabilidad')
    -- ('Secretaría_Administrativa', 'Secretaría Administrativa')
    ";

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
    (2106789012, 3), -- Carlos Hernandez es jefe del Departamento 3 (Ciencias Sociales)
    (2110123456, 4), -- Pedro es jefe del Departamento 4 (PALE)
    (2111234567, 5), -- Laura es jefa del Departamento 5 (Posgrados)
    (2112345678, 6), -- Javier es jefe del Departamento 6 (Economia)
    (2113456789, 7), -- Sara es jefa del Departamento 7 (Recursos Humanos)
    (2114567890, 8), -- Guillermo es jefe del Departamento 8 (Metodos Cuantitativos)
    (2115678901, 9), -- Mariana es jefa del Departamento 9 (Politicas Publicas)
    (2116789012, 10), -- César es jefe del Departamento 10 (Administracion)
    (2121234567, 11), -- Alejandro Campos es jefe del Departamento 11 (Auditoria)
    (2130192837, 12), -- José es jefe del Departamento 12 (Mercadotecnia)
    (2140596871, 13), -- Cristian es jefe del Departamento 13 (Impuestos)
    (2151234098, 14), -- Alejandro Lopez es jefe del Departamento 14 (Sistemas de Informacion)
    (2161098234, 15), -- Carlos Flores es jefe del Departamento 15 (Turismo)
    (2176859401, 16); -- Javier Ramirez es jefe del Departamento 16 (Contabilidad)";

if (mysqli_query($conn, $insert_usuarios_departamentos)) {
    echo "<br>Relación de usuarios y departamentos insertada exitosamente";
} else {
    echo "<br>Error insertando relación de usuarios y departamentos: " . mysqli_error($conn);
}

// Crear tabla Eventos_Admin
$sql = "CREATE TABLE IF NOT EXISTS Eventos_Admin (
    ID_Evento INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Evento VARCHAR(255) NOT NULL,
    Descripcion_Evento TEXT,
    Fecha_Inicio DATE,
    Fecha_Fin DATE,
    Hora_Inicio TIME NOT NULL,
    Hora_Fin TIME NOT NULL,
    Etiqueta VARCHAR(100),
    Participantes TEXT NOT NULL,
    Notificaciones TEXT,
    Hora_Noti TIME NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Eventos_Admin creada exitosamente";
} else {
    echo "<br>Error creando tabla Eventos_Admin: " . mysqli_error($conn);
}

// Crear tabla Plantilla_SA
$sql = "CREATE TABLE IF NOT EXISTS Plantilla_SA (
    ID_Archivo_Dep INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo_Dep VARCHAR(255) NOT NULL,
    Contenido_Archivo_Dep LONGBLOB NOT NULL,
    Fecha_Subida_Dep VARCHAR(255) NOT NULL,
    Departamento_ID INT NOT NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
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
    Tamaño_Archivo_Dep INT NOT NULL,
    Usuario_ID BIGINT(10),
    Fecha_Subida_Dep TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Departamento_ID INT NOT NULL,
    Notificacion_Vista BOOLEAN DEFAULT 0,
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Codigo),
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Plantilla_Dep creada exitosamente";
} else {
    echo "<br>Error creando tabla Plantilla_Dep: " . mysqli_error($conn);
}

$sql = "CREATE TABLE Fechas_Limite (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Fecha_Limite DATETIME,
    Fecha_Actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Usuario_ID BIGINT(10),
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Codigo)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Fechas_limite creada exitosamente";
} else {
    echo "<br>Error creando tabla Fechas_limite: " . mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS Justificaciones (
    ID_Justificacion INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    Codigo_Usuario BIGINT(10) NOT NULL,
    Justificacion TEXT NOT NULL,
    Fecha_Justificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Justificacion_Enviada BOOLEAN DEFAULT 0,
    Notificacion_Vista BOOLEAN DEFAULT 0,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID),
    FOREIGN KEY (Codigo_Usuario) REFERENCES Usuarios(Codigo)
)";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Justificaciones creada exitosamente";
} else {
    echo "<br>Error creando tabla Justificaciones: " . mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS Notificaciones (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Usuario_ID BIGINT(10),
    Vista BOOLEAN DEFAULT 0,
    Emisor_ID INT,
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Codigo)
);";

if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Notificaciones creada exitosamente";
} else {
    echo "<br>Error creando tabla Notificaciones: " . mysqli_error($conn);
}

// Crear tabla Data_Estudios_Regionales
$sql = "CREATE TABLE IF NOT EXISTS Data_Estudios_Regionales (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Estudios_Regionales creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Estudios_Regionales: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Finanzas
$sql = "CREATE TABLE IF NOT EXISTS Data_Finanzas (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Finanzas creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Finanzas: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Ciencias_Sociales
$sql = "CREATE TABLE IF NOT EXISTS Data_Ciencias_Sociales (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Ciencias_Sociales creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Ciencias_Sociales: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_PALE
$sql = "CREATE TABLE IF NOT EXISTS Data_PALE (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_PALE creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_PALE: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Posgrados
$sql = "CREATE TABLE IF NOT EXISTS Data_Posgrados (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Posgrados creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Posgrados: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Economia
$sql = "CREATE TABLE IF NOT EXISTS Data_Economía (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Economia creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Economia: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Recursos_Humanos
$sql = "CREATE TABLE IF NOT EXISTS Data_Recursos_Humanos (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Recursos_Humanos creada exitosamente";
} else {
    echo "<br>Error creando tabla Recursos_Humanos: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Metodos_Cuantitativos
$sql = "CREATE TABLE IF NOT EXISTS Data_Métodos_Cuantitativos (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Metodos_Cuantitativos creada exitosamente";
} else {
    echo "<br>Error creando tabla Metodos_Cuantitativos: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Politicas_Publicas
$sql = "CREATE TABLE IF NOT EXISTS Data_Políticas_Públicas (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Politicas_Publicas creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Politicas_Publicas: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Administracion
$sql = "CREATE TABLE IF NOT EXISTS Data_Administración (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Administracion creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Administracion: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Auditoria
$sql = "CREATE TABLE IF NOT EXISTS Data_Auditoría (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Auditoría creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Auditoría: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Mercadotecnia
$sql = "CREATE TABLE IF NOT EXISTS Data_Mercadotecnia (
        ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Mercadotecnia creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Mercadotecnia: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Impuestos
$sql = "CREATE TABLE IF NOT EXISTS Data_Impuestos (
     ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Impuestos creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Impuestos: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Sistemas_de_Información
$sql = "CREATE TABLE IF NOT EXISTS Data_Sistemas_de_Información (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Sistemas_de_Información creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Sistemas_de_Información: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Turismo
$sql = "CREATE TABLE IF NOT EXISTS Data_Turismo (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Turismo creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Turismo: " . mysqli_error($conn) . "<br>";
}

// Crear tabla Data_Contabilidad
$sql = "CREATE TABLE IF NOT EXISTS Data_Contabilidad (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NOT NULL,
    CRN VARCHAR(15) NOT NULL,
    MATERIA VARCHAR(80) NOT NULL,
    CVE_MATERIA VARCHAR(5) NOT NULL,
    SECCION VARCHAR(5) NOT NULL,
    NIVEL VARCHAR(25) NOT NULL,
    NIVEL_TIPO VARCHAR(25) NOT NULL,
    TIPO VARCHAR(1) NOT NULL,
    C_MIN VARCHAR(2) NOT NULL,
    H_TOTALES VARCHAR(2) NOT NULL,
    ESTATUS VARCHAR(10) NOT NULL,
    TIPO_CONTRATO VARCHAR(30) NOT NULL,
    CODIGO_PROFESOR VARCHAR(9) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(60) NOT NULL,
    CATEGORIA VARCHAR(40) NOT NULL,
    DESCARGA VARCHAR(2) NOT NULL,
    CODIGO_DESCARGA VARCHAR(9) NOT NULL,
    NOMBRE_DESCARGA VARCHAR(60) NOT NULL,
    NOMBRE_DEFINITIVO VARCHAR(60) NOT NULL,
    TITULAR VARCHAR(2) NOT NULL,
    HORAS VARCHAR(1) NULL,
    CODIGO_DEPENDENCIA VARCHAR(4) NULL,
    L VARCHAR(5) NULL,
    M VARCHAR(5) NULL,
    I VARCHAR(5) NULL,
    J VARCHAR(5) NULL,
    V VARCHAR(5) NULL,
    S VARCHAR(5) NULL,
    D VARCHAR(5) NULL,
    DIA_PRESENCIAL VARCHAR(10) NULL,
    DIA_VIRTUAL VARCHAR(10) NULL,
    MODALIDAD VARCHAR(10) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NOT NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES Departamentos(Departamento_ID)
)";
if (mysqli_query($conn, $sql)) {
    echo "<br>Tabla Data_Contabilidad creada exitosamente";
} else {
    echo "<br>Error creando tabla Data_Contabilidad: " . mysqli_error($conn) . "<br>";
}

// Cerrar la conexión
mysqli_close($conn);

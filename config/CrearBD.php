<?php
// Activar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './../config/db.php';

if (!$conexion) {
    die("Connection failed: " . mysqli_connect_error());
}

$dbname = "CREATE DATABASE IF NOT EXISTS pa;";

if ($conexion->query($dbname) == TRUE) {
    echo "Base de datos creada exitosamente";
} else {
    echo "Error creando base de datos: " . $conexion->error . "<br>";
}

mysqli_select_db($conexion, "PA");

include('./td-espacios.php');

// Crear tabla Roles
$sql = "CREATE TABLE IF NOT EXISTS roles (
    Rol_ID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Rol VARCHAR(80) NOT NULL
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Roles creada exitosamente";
} else {
    echo "<br>Error creando tabla Roles: " . mysqli_error($conexion);
}

// Insertar roles
$insert_roles = "INSERT INTO roles (Nombre_Rol) VALUES 
                ('Jefe de Departamento'), 
                ('Secretaría Administrativa'), 
                ('Coordinación de Personal'), 
                ('Asistente de JD'),
                ('Administrador');"; //Esto es rol 0

if (mysqli_query($conexion, $insert_roles)) {
    echo "<br>Roles insertados exitosamente";
} else {
    echo "<br>Error insertando roles: " . mysqli_error($conexion);
}

// Crear tabla Usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    Codigo BIGINT(10) NOT NULL PRIMARY KEY,
    Nombre VARCHAR(45) NOT NULL,
    Apellido VARCHAR(45) NOT NULL,
    Correo VARCHAR(100) NOT NULL,
    Pass VARCHAR(255) NOT NULL,
    Genero VARCHAR(20) NOT NULL,
    Rol_ID INT,
    IconoColor VARCHAR(7),
    FOREIGN KEY (Rol_ID) REFERENCES roles(Rol_ID)
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Usuarios creada exitosamente";
} else {
    echo "<br>Error creando tabla Usuarios: " . mysqli_error($conexion) . "<br>";
}

// Función para hashear contraseñas de manera segura
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

echo "<br>Función hashPassword definida correctamente";

// Usuarios (los necesitamos en arreglo para el hasheo de contraseñas)
$usuarios = [
    [215161264, 'Uriel', 'Valencia', 'uriel.valencia@cucea.udg', '123', 'Masculino', 5, '#FF0000'], //Estudios Regionales
    [7200315, 'Jesús', 'Arroyo', 'jesusarr@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Estudios Regionales
    [8504032, 'José', 'Ponce', 'tponce@cucea.udg', '123', 'Masculino', 1, '#00FF00'], //Finanzas
    [9023615, 'Blanca', 'Silva', 'bsilva@cucea.udg', '123', 'Femenino', 1, '#0000FF'], //Ciencias Sociales
    [2951480, 'Teressa', 'Tarquinio', 'traci@cucea.udg', '123', 'Femenino', 1, '#FF0000'], //PALE
    [2111234, 'Jesús', 'Cardoso', 'jesus.cardoso@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //POSGRADOS
    [8211558, 'Martín', 'Romero', 'mromeromorett@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Economía
    [8102481, 'Sara', 'Robles', 'srobles@cucea.udg', '123', 'Femenino', 1, '#FF0000'], //RRHH
    [2946961, 'Guillermo', 'Sierra', 'gsierraj@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Métodos
    [2946962, 'Carla', 'Aceves', 'carla.aceves@cucea.udg', '123', 'Femenino', 1, '#FF0000'], // Políticas
    [2633086, 'César', 'Mora', 'cesar.mora@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Admin
    [9816054, 'Alejandro', 'Campos', 'a.campos@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Auditoría
    [2212498, 'José', 'Sánchez', 'jsanchez@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Merca
    [2530872, 'Cristian', 'Alcantar', 'cristian.alcantar@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Impuestos
    [2116812, 'Alejandro', 'López', 'alejandro.lopez@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Sistemas
    [2519356, 'Carlos', 'Flores', 'carlos.flores@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Turismo
    [8319855, 'Javier', 'Ramirez', 'javierr@cucea.udg', '123', 'Masculino', 1, '#FF0000'], //Contabilidad
    [2101234567, 'Maria', 'Lopez', 'maria.lopez@cucea.udg', '123', 'Femenino', 2, '#FF0000'], //fake
    [2104567890, 'Denisse', 'Murillo', 'denisse.murillo@cucea.udg', '123', 'Femenino', 2, '#FF0000'],
    [2107890123, 'Aldo', 'Ceja', 'aldo.ceja@cucea.udg', '123', 'Masculino', 2, '#FF0000'], //SA
    [2105678901, 'Sofia', 'Gonzalez', 'sofia.gonzalez@cucea.udg', '123', 'Femenino', 3, '#FF0000'], //fake
    [2102345678, 'Luis', 'Garcia', 'luis.garcia@cucea.udg', '123', 'Masculino', 3, '#FF0000'], //fake
    [2108901234, 'Daniel', 'Sanchez', 'daniel.sanchez@cucea.udg', '123', 'Masculino', 3, '#FF0000'], //fake
    [2201739, 'Iliana', 'Aldrete', 'ibaldrete@cucea.udg', '123', 'Femenino', 3, '#FF0000'] //SA
];

$stmt = $conexion->prepare("INSERT INTO usuarios (Codigo, Nombre, Apellido, Correo, Pass, Genero, Rol_ID, IconoColor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Insertar usuarios
foreach ($usuarios as $index => $usuario) {
    $codigo = $usuario[0]; // Codigo
    $nombre = $usuario[1]; // Nombre
    $apellido = $usuario[2]; // Apellido
    $correo = $usuario[3]; // Correo
    $pass = $pass = hashPassword($usuario[4]); // Pass hasheada
    $genero = $usuario[5]; // Genero
    $rol_id = $usuario[6]; // Rol_ID
    $iconoColor = $usuario[7];  // IconoColor

    $stmt->bind_param("issssssi", $codigo, $nombre, $apellido, $correo, $pass, $genero, $rol_id, $iconoColor);

    if ($stmt->execute()) {
        echo "<br>Usuario " . ($index + 1) . " insertado correctamente";
    } else {
        echo "<br>Error insertando usuario " . ($index + 1) . ": " . $stmt->error . "<br>";
    }
}

//  $stmt->execute();
//}

$stmt->close();

// Crear tabla Departamentos
$sql = "CREATE TABLE IF NOT EXISTS departamentos (
    Departamento_ID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Departamento VARCHAR(100) NOT NULL,
    Departamentos VARCHAR(100) NOT NULL
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Departamentos creada exitosamente";
} else {
    echo "<br>Error creando tabla Departamentos: " . mysqli_error($conexion);
}

// Insertar departamentos
$insert_departamentos = "INSERT INTO departamentos (Nombre_Departamento, Departamentos) VALUES
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

if (mysqli_query($conexion, $insert_departamentos)) {
    echo "<br>Departamentos insertados exitosamente";
} else {
    echo "<br>Error insertando departamentos: " . mysqli_error($conexion);
}

// Crear tabla Usuarios_Departamentos
$sql = "CREATE TABLE IF NOT EXISTS usuarios_departamentos (
    Usuario_ID BIGINT(10) NOT NULL,
    Departamento_ID INT NOT NULL,
    PRIMARY KEY (Usuario_ID, Departamento_ID),
    FOREIGN KEY (Usuario_ID) REFERENCES usuarios(Codigo) ON DELETE CASCADE,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID) ON DELETE CASCADE
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Usuarios_Departamentos creada exitosamente";
} else {
    echo "<br>Error creando tabla Usuarios_Departamentos: " . mysqli_error($conexion);
}

// Insertar relación de usuarios con departamentos (jefes de departamento)
$insert_usuarios_departamentos = "INSERT INTO usuarios_departamentos (Usuario_ID, Departamento_ID) VALUES
    (7200315, 1), -- Jesús Arroyo es jefe del Departamento 1 (Estudios Regionales)
    (8504032, 2), -- José Ponce es jefa del Departamento 2 (Finanzas)
    (9023615, 3), -- Blanca Silva es jefe del Departamento 3 (Ciencias Sociales)
    (2951480, 4), -- Teressa es jefe del Departamento 4 (PALE)
    (2111234, 5), -- Jesus Cardoso es jefa del Departamento 5 (Posgrados)
    (8211558, 6), -- Martin Romero es jefe del Departamento 6 (Economia)
    (8102481, 7), -- Sara Robles es jefa del Departamento 7 (Recursos Humanos)
    (2946961, 8), -- Guillermo Sierra es jefe del Departamento 8 (Metodos Cuantitativos)
    (2946962, 9), -- Carla Aceves es jefa del Departamento 9 (Politicas Publicas)
    (2633086, 10), -- César Mora es jefe del Departamento 10 (Administracion)
    (9816054, 11), -- Alejandro Campos es jefe del Departamento 11 (Auditoria)
    (2212498, 12), -- José Sanchez es jefe del Departamento 12 (Mercadotecnia)
    (2530872, 13), -- Cristian Alcantar es jefe del Departamento 13 (Impuestos)
    (2116812, 14), -- Alejandro Lopez es jefe del Departamento 14 (Sistemas de Informacion)
    (2519356, 15), -- Carlos Flores es jefe del Departamento 15 (Turismo)
    (8319855, 16); -- Javier Ramirez es jefe del Departamento 16 (Contabilidad)";

if (mysqli_query($conexion, $insert_usuarios_departamentos)) {
    echo "<br>Relación de usuarios y departamentos insertada exitosamente";
} else {
    echo "<br>Error insertando relación de usuarios y departamentos: " . mysqli_error($conexion);
}

// Crear tabla Eventos_Admin
$sql = "CREATE TABLE IF NOT EXISTS eventos_admin (
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
    Estado VARCHAR(20) DEFAULT 'activo'
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Eventos_Admin creada exitosamente";
} else {
    echo "<br>Error creando tabla Eventos_Admin: " . mysqli_error($conexion);
}

// Crear tabla Plantilla_SA
$sql = "CREATE TABLE IF NOT EXISTS plantilla_sa (
    ID_Archivo_Dep INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo_Dep VARCHAR(255) NOT NULL,
    Contenido_Archivo_Dep LONGBLOB NOT NULL,
    Fecha_Subida_Dep VARCHAR(255) NOT NULL,
    Departamento_ID INT NOT NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Plantilla_SA creada exitosamente";
} else {
    echo "<br>Error creando tabla Plantilla_SA: " . mysqli_error($conexion);
}

// Crear tabla Plantilla_CoordP
$sql = "CREATE TABLE IF NOT EXISTS plantilla_coordp (
    ID_Archivo_CoordP INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo_CoordP VARCHAR(255) NOT NULL,
    Tamaño_Archivo_CoordP INT NOT NULL,
    Fecha_Subida_CoordP TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Usuario_ID BIGINT(10) NOT NULL,
    FOREIGN KEY (Usuario_ID) REFERENCES usuarios(Codigo)
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Plantilla_CoordP creada exitosamente";
} else {
    echo "<br>Error creando tabla Plantilla_CoordP: " . mysqli_error($conexion);
}

// Crear tabla Plantilla_Dep
$sql = "CREATE TABLE IF NOT EXISTS plantilla_dep (
    ID_Archivo_Dep INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo_Dep VARCHAR(255) NOT NULL,
    Tamaño_Archivo_Dep INT NOT NULL,
    Usuario_ID BIGINT(10),
    Fecha_Subida_Dep TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Departamento_ID INT NOT NULL,
    Notificacion_Vista BOOLEAN DEFAULT 0,
    FOREIGN KEY (Usuario_ID) REFERENCES usuarios(Codigo),
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Plantilla_Dep creada exitosamente";
} else {
    echo "<br>Error creando tabla Plantilla_Dep: " . mysqli_error($conexion);
}

$sql = "CREATE TABLE fechas_limite (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Fecha_Limite DATETIME,
    Fecha_Actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Usuario_ID BIGINT(10),
    FOREIGN KEY (Usuario_ID) REFERENCES usuarios(Codigo)
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Fechas_limite creada exitosamente";
} else {
    echo "<br>Error creando tabla Fechas_limite: " . mysqli_error($conexion);
}

$sql = "CREATE TABLE IF NOT EXISTS justificaciones (
    ID_Justificacion INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    Codigo_Usuario BIGINT(10) NOT NULL,
    Justificacion TEXT NOT NULL,
    Fecha_Justificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Justificacion_Enviada BOOLEAN DEFAULT 0,
    Notificacion_Vista BOOLEAN DEFAULT 0,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID),
    FOREIGN KEY (Codigo_Usuario) REFERENCES usuarios(Codigo)
)";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Justificaciones creada exitosamente";
} else {
    echo "<br>Error creando tabla Justificaciones: " . mysqli_error($conexion);
}

$sql = "CREATE TABLE IF NOT EXISTS notificaciones (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Usuario_ID BIGINT(10),
    Departamento_ID INT(15),
    Vista BOOLEAN DEFAULT 0,
    Emisor_ID INT,
    Oculta BOOLEAN DEFAULT 0,
    FOREIGN KEY (Usuario_ID) REFERENCES usuarios(Codigo)
);";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Notificaciones creada exitosamente";
} else {
    echo "<br>Error creando tabla Notificaciones: " . mysqli_error($conexion);
}

$sql = "CREATE TABLE IF NOT EXISTS usuarios_notificaciones (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Usuario_ID BIGINT(10) NOT NULL,
    Notificacion_ID INT,
    Justificacion_ID INT,
    Plantilla_ID INT,
    Tipo VARCHAR(20) NOT NULL,  -- 'notificacion', 'justificacion', 'plantilla'
    Vista BOOLEAN DEFAULT 0,
    Oculta BOOLEAN DEFAULT 0,
    FOREIGN KEY (Usuario_ID) REFERENCES usuarios(Codigo),
    FOREIGN KEY (Notificacion_ID) REFERENCES notificaciones(ID),
    FOREIGN KEY (Justificacion_ID) REFERENCES justificaciones(ID_Justificacion),
    INDEX idx_usuario_notificacion (Usuario_ID, Notificacion_ID),
    INDEX idx_usuario_justificacion (Usuario_ID, Justificacion_ID),
    INDEX idx_usuario_plantilla (Usuario_ID, Plantilla_ID)
);";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla usuarios_notificaciones creada exitosamente";
} else {
    echo "<br>Error creando tabla usuarios_notificaciones: " . mysqli_error($conexion);
}

// Tabla solicitudes_baja
$sql = "CREATE TABLE IF NOT EXISTS solicitudes_baja (
    ID_BAJA INT AUTO_INCREMENT PRIMARY KEY,
    USUARIO_ID BIGINT(10),
    Departamento_ID INT,
    OFICIO_NUM_BAJA VARCHAR(15) UNIQUE,
    FECHA_SOLICITUD_B DATE,
    HORA_CREACION TIME,
    PROFESSION_PROFESOR_B VARCHAR(15),
    APELLIDO_P_PROF_B VARCHAR(40),
    APELLIDO_M_PROF_B VARCHAR(40),
    NOMBRES_PROF_B VARCHAR(60),
    CODIGO_PROF_B INT(10),
    DESCRIPCION_PUESTO_B VARCHAR(100),
    CRN_B INT(7),
    CLASIFICACION_BAJA_B VARCHAR(15),
    SIN_EFFECTOS_DESDE_B DATE,
    MOTIVO_B VARCHAR(50),
    ESTADO_B VARCHAR(15),
    COMENTARIOS VARCHAR(150),
    PDF_BLOB LONGBLOB,
    FECHA_MODIFICACION_REVISION TIMESTAMP
);";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla solicitudes_baja creada exitosamente";
} else {
    echo "<br>Error creando tabla solicitudes_baja: " . mysqli_error($conexion);
}

// Tabla solicitudes_propuesta
$sql = "CREATE TABLE IF NOT EXISTS solicitudes_propuesta (
    ID_PROP INT AUTO_INCREMENT PRIMARY KEY,
    USUARIO_ID BIGINT(10),
    OFICIO_NUM_PROP VARCHAR(15),
    FECHA_SOLICITUD_P DATE,
    PROFESSION_PROFESOR_P VARCHAR(15),
    APELLIDO_P_PROF_P VARCHAR(40),
    APELLIDO_M_PROF_P VARCHAR(40),
    NOMBRES_PROF_P VARCHAR(60),
    CODIGO_PROF_P INT(10),
    DIA_P VARCHAR(2),
    MES_P VARCHAR(2),
    ANO_P YEAR,
    DESCRIPCION_PUESTO_P VARCHAR(100),
    CODIGO_PUESTO_P VARCHAR(10),
    CLASIFICACION_PUESTO_P VARCHAR(15),
    HRS_SEMANALES_P INT(5),
    CATEGORIA_P VARCHAR(20),
    CARRIERA_PROF_P VARCHAR(50),
    CRN_P INT(7),
    NUM_PUESTO_P INT(5),
    CARGO_ATC_P BOOLEAN,
    CODIGO_PROF_SUST INT(10),
    APELLIDO_P_PROF_SUST VARCHAR(40),
    APELLIDO_M_PROF_SUST VARCHAR(40),
    NOMBRES_PROF_SUST VARCHAR(60),
    CAUSA_P VARCHAR(50),
    PERIODO_ASIG_DESDE_P DATE,
    PERIODO_ASIG_HASTA_P DATE,
    ESTADO_P VARCHAR(15),
    HORA_CREACION TIME,
    Departamento_ID INT,
    PDF_BLOB LONGBLOB,
    FECHA_MODIFICACION_REVISION TIMESTAMP
);";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla solicitudes_propuesta creada exitosamente";
} else {
    echo "<br>Error creando tabla solicitudes_propuesta: " . mysqli_error($conexion);
}

// Tabla solicitudes_baja_propuesta
$sql = "CREATE TABLE IF NOT EXISTS solicitudes_baja_propuesta (
    ID_BAJA_PROP INT AUTO_INCREMENT PRIMARY KEY,
    USUARIO_ID BIGINT(10),
    Departamento_ID INT,
    OFICIO_NUM_BAJA_PROP VARCHAR(15) UNIQUE,
    FECHA_SOLICITUD_BAJA_PROP DATE,
    PROFESSION_PROFESOR_BAJA VARCHAR(15),
    APELLIDO_P_PROF_BAJA VARCHAR(40),
    APELLIDO_M_PROF_BAJA VARCHAR(40),
    NOMBRES_PROF_BAJA VARCHAR(60),
    CODIGO_PROF_BAJA INT(10),
    NUM_PUESTO_TEORIA_BAJA VARCHAR(15),
    NUM_PUESTO_PRACTICA_BAJA VARCHAR(15),
    CVE_MATERIA_BAJA VARCHAR(10),
    NOMBRE_MATERIA_BAJA VARCHAR(100),
    CRN_BAJA INT(7),
    HRS_SEM_MES_TEORIA_BAJA VARCHAR(15),
    HRS_SEM_MES_PRACTICA_BAJA VARCHAR(15),
    CARRERA_BAJA VARCHAR(50),
    GDO_GPO_TURNO_BAJA VARCHAR(20),
    TIPO_ASIGNACION_BAJA VARCHAR(10),
    SIN_EFFECTOS_APARTH_BAJA DATE,
    MOTIVO_BAJA VARCHAR(50),
    NUM_PUESTO_TEORIA_PROP VARCHAR(15),
    NUM_PUESTO_PRACTICA_PROP VARCHAR(15),
    APELLIDO_P_PROF_PROP VARCHAR(40),
    APELLIDO_M_PROF_PROP VARCHAR(40),
    NOMBRES_PROF_PROP VARCHAR(60),
    CODIGO_PROF_PROP INT(10),
    HRS_SEM_MES_TEORIA_PROP VARCHAR(15),
    HRS_SEM_MES_PRACTICA_PROP VARCHAR(15),
    INTER_TEMP_DEF_PROP VARCHAR(30),
    TIPO_ASIGNACION_PROP VARCHAR(10),
    PERIODO_ASIG_DESDE_PROP DATE,
    PERIODO_ASIG_HASTA_PROP DATE,
    ESTADO_P VARCHAR(15),
    HORA_CREACION TIME,
    PDF_BLOB LONGBLOB,
    FECHA_MODIFICACION_REVISION TIMESTAMP,
    FOREIGN KEY (USUARIO_ID) REFERENCES usuarios(Codigo),
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
);";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla solicitudes_baja_propuesta creada exitosamente";
} else {
    echo "<br>Error creando tabla solicitudes_baja_propuesta: " . mysqli_error($conexion);
}

// Crear tabla Data_Estudios_Regionales
$sql = "CREATE TABLE IF NOT EXISTS data_estudios_regionales (
ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Estudios_Regionales creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Estudios_Regionales: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Finanzas
$sql = "CREATE TABLE IF NOT EXISTS data_finanzas (
ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Finanzas creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Finanzas: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Ciencias_Sociales
$sql = "CREATE TABLE IF NOT EXISTS data_ciencias_sociales (
ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Ciencias_Sociales creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Ciencias_Sociales: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_PALE
$sql = "CREATE TABLE IF NOT EXISTS data_pale (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_PALE creada exitosamente";
} else {
    echo "<br>Error creando tabla data_PALE: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Posgrados
$sql = "CREATE TABLE IF NOT EXISTS data_posgrados (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Posgrados creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Posgrados: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Economia
$sql = "CREATE TABLE IF NOT EXISTS data_economía (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Economia creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Economia: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla Recursos_Humanos
$sql = "CREATE TABLE IF NOT EXISTS data_recursos_Humanos (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Recursos_Humanos creada exitosamente";
} else {
    echo "<br>Error creando tabla Recursos_Humanos: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla Metodos_Cuantitativos
$sql = "CREATE TABLE IF NOT EXISTS data_métodos_cuantitativos (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Metodos_Cuantitativos creada exitosamente";
} else {
    echo "<br>Error creando tabla Metodos_Cuantitativos: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Politicas_Publicas
$sql = "CREATE TABLE IF NOT EXISTS data_políticas_públicas (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Politicas_Publicas creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Politicas_Publicas: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Administracion
$sql = "CREATE TABLE IF NOT EXISTS data_administración (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Administracion creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Administracion: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Auditoria
$sql = "CREATE TABLE IF NOT EXISTS data_auditoría (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Auditoría creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Auditoría: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Mercadotecnia
$sql = "CREATE TABLE IF NOT EXISTS data_mercadotecnia (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Mercadotecnia creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Mercadotecnia: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Impuestos
$sql = "CREATE TABLE IF NOT EXISTS data_impuestos (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Impuestos creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Impuestos: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Sistemas_de_Información
$sql = "CREATE TABLE IF NOT EXISTS data_sistemas_de_información (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Sistemas_de_Información creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Sistemas_de_Información: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Turismo
$sql = "CREATE TABLE IF NOT EXISTS data_turismo (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Turismo creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Turismo: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla data_Contabilidad
$sql = "CREATE TABLE IF NOT EXISTS data_contabilidad (
    ID_Plantilla INT PRIMARY KEY AUTO_INCREMENT,
    Departamento_ID INT NOT NULL,
    CICLO VARCHAR(10) NULL,
    CRN VARCHAR(15) NULL,
    MATERIA VARCHAR(150) NULL,
    CVE_MATERIA VARCHAR(5) NULL,
    SECCION VARCHAR(30) NULL,
    NIVEL VARCHAR(25) NULL,
    NIVEL_TIPO VARCHAR(25) NULL,
    TIPO VARCHAR(5) NULL,
    C_MIN VARCHAR(5) NULL,
    H_TOTALES VARCHAR(5) NULL,
    ESTATUS VARCHAR(10) NULL,
    TIPO_CONTRATO VARCHAR(30) NULL,
    CODIGO_PROFESOR VARCHAR(9) NULL,
    NOMBRE_PROFESOR VARCHAR(80) NULL,
    CATEGORIA VARCHAR(40) NULL,
    DESCARGA VARCHAR(2) NULL,
    CODIGO_DESCARGA VARCHAR(9) NULL,
    NOMBRE_DESCARGA VARCHAR(80) NULL,
    NOMBRE_DEFINITIVO VARCHAR(80) NULL,
    TITULAR VARCHAR(2) NULL,
    HORAS VARCHAR(5) NULL,
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
    MODALIDAD VARCHAR(25) NULL,
    FECHA_INICIAL VARCHAR(10) NULL,
    FECHA_FINAL VARCHAR(10) NULL,
    HORA_INICIAL CHAR(10) NULL,
    HORA_FINAL CHAR(10) NULL,
    MODULO VARCHAR(10) NULL,
    AULA CHAR(10) NULL,
    CUPO VARCHAR (3) NULL,
    OBSERVACIONES VARCHAR(150) NULL,
    EXAMEN_EXTRAORDINARIO VARCHAR (2) NULL,
    PAPELERA VARCHAR(15) NULL,
    FOREIGN KEY (Departamento_ID) REFERENCES departamentos(Departamento_ID)
)";
if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla data_Contabilidad creada exitosamente";
} else {
    echo "<br>Error creando tabla data_Contabilidad: " . mysqli_error($conexion) . "<br>";
}

// Crear tabla Coord_Per_Prof
$sql = "CREATE TABLE IF NOT EXISTS coord_per_prof (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Datos VARCHAR(20) NULL,
    Codigo VARCHAR(12) NULL,
    Paterno  VARCHAR(50) NULL,
    Materno VARCHAR(50) NULL,
    Nombres VARCHAR(70) NULL,
    Nombre_completo VARCHAR(80) NULL,
    Departamento VARCHAR(70) NULL,
    Categoria_actual VARCHAR(60) NULL,
    Categoria_actual_dos VARCHAR(20) NULL,
    Horas_frente_grupo INT(6) NULL,
    Division VARCHAR(70) NULL,
    Tipo_plaza VARCHAR(70) NULL,
    Cat_act VARCHAR(70) NULL,
    Carga_horaria VARCHAR(10) NULL,
    Horas_definitivas INT(8) NULL,
    Udg_virtual_CIT VARCHAR(20) NULL,
    Horario VARCHAR(60) NULL,
    Turno VARCHAR(5) NULL,
    Investigacion_nombramiento_cambio_funcion VARCHAR(50) NULL,
    SNI VARCHAR(10) NULL,
    SNI_desde VARCHAR(30) NULL,
    Cambio_dedicacion VARCHAR(40) NULL,
    Telefono_particular VARCHAR(30) NULL,
    Telefono_oficina VARCHAR(30) NULL,
    Domicilio VARCHAR(70) NULL,
    Colonia VARCHAR(60) NULL,
    CP INT(8) NULL,
    Ciudad VARCHAR(30) NULL,
    Estado VARCHAR(30) NULL,
    No_imss VARCHAR(12) NULL,
    CURP VARCHAR(25) NULL,
    RFC VARCHAR(15) NULL,
    Lugar_nacimiento VARCHAR(50) NULL,
    Estado_civil VARCHAR(5) NULL,
    Tipo_sangre VARCHAR(5) NULL,
    Fecha_nacimiento VARCHAR(15) NULL,
    Edad INT(5) NULL,
    Nacionalidad VARCHAR(40) NULL,
    Correo VARCHAR(60) NULL,
    Correos_oficiales VARCHAR(60) NULL,
    Ultimo_grado VARCHAR(5) NULL,
    Programa VARCHAR (70) NULL,
    Nivel VARCHAR(5) NULL,
    Institucion VARCHAR(50) NULL,
    Estado_pais VARCHAR(50) NULL,
    Año INT(5) NULL,
    Gdo_exp VARCHAR(25) NULL,
    Otro_grado VARCHAR(5) NULL,
    Otro_programa VARCHAR(70) NULL,
    Otro_nivel VARCHAR(10) NULL,
    Otro_institucion VARCHAR(30) NULL,
    Otro_estado_pais VARCHAR(25) NULL,
    Otro_año INT(5) NULL,
    Otro_gdo_exp VARCHAR(25) NULL,
    Otro_grado_alternativo VARCHAR(5) NULL,
    Otro_programa_alternativo VARCHAR(70) NULL,
    Otro_nivel_altenrativo VARCHAR(10) NULL,
    Otro_institucion_alternativo VARCHAR(30) NULL,
    Otro_estado_pais_alternativo VARCHAR(25) NULL,
    Otro_año_alternativo VARCHAR(10) NULL,
    Otro_gdo_exp_alternativo VARCHAR(15) NULL,
    Proesde_24_25 VARCHAR(15) NULL,
    A_partir_de VARCHAR(10) NULL,
    Fecha_ingreso VARCHAR(10) NULL,
    Antiguedad VARCHAR(5) NULL,
    PAPELERA VARCHAR(15) NULL
);";

if (mysqli_query($conexion, $sql)) {
    echo "<br>Tabla Coord_Per_Prof creada exitosamente";
} else {
    echo "<br>Error creando tabla Coord_Per_Prof: " . mysqli_error($conexion);
}

// Cerrar la conexión
mysqli_close($conexion);

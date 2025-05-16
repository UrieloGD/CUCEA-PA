<?php
// Comprobar si existe la columna 'Oculta' en la tabla 'notificaciones'
include './../../config/db.php';

$tabla = 'notificaciones';
$sql = "SHOW COLUMNS FROM $tabla LIKE 'Oculta'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    // La columna no existe, añadirla
    $alterSql = "ALTER TABLE $tabla ADD COLUMN Oculta BOOLEAN DEFAULT 0";
    if ($conexion->query($alterSql) === TRUE) {
        echo "La columna 'Oculta' se ha añadido correctamente a la tabla '$tabla'";
    } else {
        echo "Error al añadir la columna: " . $conexion->error;
    }
} else {
    echo "La columna 'Oculta' ya existe en la tabla '$tabla'";
}

// Comprobar 'Oculta' en justificaciones
$tabla = 'justificaciones';
$sql = "SHOW COLUMNS FROM $tabla LIKE 'Oculta'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    $alterSql = "ALTER TABLE $tabla ADD COLUMN Oculta BOOLEAN DEFAULT 0";
    if ($conexion->query($alterSql) === TRUE) {
        echo "<br>La columna 'Oculta' se ha añadido correctamente a la tabla '$tabla'";
    } else {
        echo "<br>Error al añadir la columna: " . $conexion->error;
    }
} else {
    echo "<br>La columna 'Oculta' ya existe en la tabla '$tabla'";
}

// Comprobar 'Oculta' en plantilla_dep
$tabla = 'plantilla_dep';
$sql = "SHOW COLUMNS FROM $tabla LIKE 'Oculta'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    $alterSql = "ALTER TABLE $tabla ADD COLUMN Oculta BOOLEAN DEFAULT 0";
    if ($conexion->query($alterSql) === TRUE) {
        echo "<br>La columna 'Oculta' se ha añadido correctamente a la tabla '$tabla'";
    } else {
        echo "<br>Error al añadir la columna: " . $conexion->error;
    }
} else {
    echo "<br>La columna 'Oculta' ya existe en la tabla '$tabla'";
}

// Comprobar si existe la columna 'Notificacion_Vista' en las tablas necesarias
$tabla = 'justificaciones';
$sql = "SHOW COLUMNS FROM $tabla LIKE 'Notificacion_Vista'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    $alterSql = "ALTER TABLE $tabla ADD COLUMN Notificacion_Vista BOOLEAN DEFAULT 0";
    if ($conexion->query($alterSql) === TRUE) {
        echo "<br>La columna 'Notificacion_Vista' se ha añadido correctamente a la tabla '$tabla'";
    } else {
        echo "<br>Error al añadir la columna: " . $conexion->error;
    }
} else {
    echo "<br>La columna 'Notificacion_Vista' ya existe en la tabla '$tabla'";
}

$tabla = 'plantilla_dep';
$sql = "SHOW COLUMNS FROM $tabla LIKE 'Notificacion_Vista'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    $alterSql = "ALTER TABLE $tabla ADD COLUMN Notificacion_Vista BOOLEAN DEFAULT 0";
    if ($conexion->query($alterSql) === TRUE) {
        echo "<br>La columna 'Notificacion_Vista' se ha añadido correctamente a la tabla '$tabla'";
    } else {
        echo "<br>Error al añadir la columna: " . $conexion->error;
    }
} else {
    echo "<br>La columna 'Notificacion_Vista' ya existe en la tabla '$tabla'";
}

$conexion->close();

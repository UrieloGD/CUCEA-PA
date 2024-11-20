<?php
function getConnection()
{
    static $conexion = null;

    if ($conexion === null) {
        $host = 'localhost';
        $dbname = 'u588700049_PA';
        $username = 'u588700049_Omar';
        $password = 'Cesaromar3621PA!';

        $conexion = mysqli_connect($host, $username, $password, $dbname);

        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        mysqli_set_charset($conexion, "utf8mb4");
    }

    return $conexion;
}

$conexion = getConnection();

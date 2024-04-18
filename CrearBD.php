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
    nip VARCHAR(60) NOT NULL
    )";

if (mysqli_query($conn, $sql)) {
    echo "Table Usuarios created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

<?php

$conexion = mysqli_connect("localhost", "root", "", "pa");
if (!$conexion) {
    die("Connection failed: " . mysqli_connect_error());
}

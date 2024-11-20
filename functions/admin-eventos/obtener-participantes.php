<?php
include './../../config/db.php';

header('Content-Type: application/json');

$sql = "SELECT u.Codigo, u.Nombre, u.Apellido, u.Correo, r.Nombre_Rol
        FROM usuarios u
        LEFT JOIN roles r ON u.Rol_ID = r.Rol_ID";

$resultado = $conexion->query($sql);

$usuarios = array();
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }
}

echo json_encode($usuarios);

$conexion->close();
?>
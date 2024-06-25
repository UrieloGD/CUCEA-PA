<?php
include 'db.php';

if (isset($_GET['role'])) {
    $role = $_GET['role'];
    
    // Query para obtener usuarios por rol
    $sql = "SELECT Codigo, Nombre, Apellido, Correo FROM Usuarios WHERE Rol_ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $role);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $usuarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    // Devolver los usuarios en formato JSON
    echo json_encode($usuarios);
    
    // Cerrar conexión
    $stmt->close();
    $conexion->close();
}
?>

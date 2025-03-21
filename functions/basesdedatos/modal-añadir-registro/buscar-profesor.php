<?php
// ./functions/basesdedatos/modal-añadir-registro/buscar-profesor.php
header('Content-Type: application/json');
include './../../../config/db.php';

$searchTerm = $_POST['term'] ?? null; // Cambiar parámetro a 'term'

if ($searchTerm) {
    try {
        $sql = $sql = "SELECT 
        Codigo, 
        CONCAT(Nombres, ' ', Paterno, ' ', Materno) AS NombreCompleto,
        Tipo_plaza AS Contrato, 
        Categoria_actual AS Categoria 
        FROM coord_per_prof,
        LIMIT 5";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $profesores = [];
        while ($row = $result->fetch_assoc()) {
            $profesores[] = $row;
        }
        echo json_encode($profesores);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode([]);
}

$conexion->close();
?>
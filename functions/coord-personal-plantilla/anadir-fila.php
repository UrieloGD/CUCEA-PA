<?php
include './../../config/db.php';
session_start();

header('Content-Type: application/json');

try {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['Codigo'])) {
        throw new Exception("Usuario no autenticado.");
    }

    // Validar campos requeridos
    $required_fields = ['Codigo', 'Paterno', 'Materno', 'Nombres'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("El campo {$field} es requerido.");
        }
    }

    // Verificar si el código ya existe
    $check_sql = "SELECT Codigo FROM coord_per_prof WHERE Codigo = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("s", $_POST['Codigo']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        throw new Exception("El código ya existe en la base de datos.");
    }
    $check_stmt->close();

    // Preparar la consulta SQL para inserción
    $sql = "INSERT INTO coord_per_prof (
        Codigo, Paterno, Materno, Nombres, Nombre_completo, 
        Sexo, Departamento, Categoria_actual, Horas_frente_grupo, 
        Division, Tipo_plaza
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    // Crear nombre completo
    $nombre_completo = trim($_POST['Nombres'] . ' ' . $_POST['Paterno'] . ' ' . $_POST['Materno']);

    // Valores predeterminados para campos opcionales
    $stmt->bind_param(
        "ssssssssisss", 
        $_POST['Codigo'], 
        $_POST['Paterno'], 
        $_POST['Materno'], 
        $_POST['Nombres'], 
        $nombre_completo,
        $_POST['Sexo'] ?? '',
        $_POST['Departamento'] ?? '',
        $_POST['Categoria_actual'] ?? '',
        intval($_POST['Horas_frente_grupo'] ?? 0),
        $_POST['Division'] ?? '',
        $_POST['Tipo_plaza'] ?? ''
    );

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar: " . $stmt->error);
    }

    echo json_encode([
        "success" => true,
        "message" => "Registro añadido correctamente"
    ]);

} catch (Exception $e) {
    error_log("Error en añadir-profesor.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}
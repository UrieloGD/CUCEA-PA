<?php
require_once('./../../../config/db.php');

// Función para hashear la contraseña de forma segura
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo']; // Ahora obtenemos el código correctamente desde el formulario
    $pass = $_POST['pass'];
    $confpass = $_POST['confpass'];

    $hashedPassword = hashPassword($pass);

    if ($pass === $confpass) {
        // Actualizar la contraseña en la base de datos
        $updateQuery = "UPDATE usuarios SET Pass=? WHERE Codigo=?";
        $stmt = $conexion->prepare($updateQuery);
        if ($stmt) {
            $stmt->bind_param('si', $hashedPassword, $codigo);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Contraseña cambiada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar la contraseña: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo "Error preparando la consulta: " . $conexion->error;
        }
    } else {
        echo "Las contraseñas no coinciden";
    }
} else {
    echo "Método de solicitud no permitido";
}
$conexion->close();

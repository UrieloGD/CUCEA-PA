<?php
require_once('./db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo']; // Ahora obtenemos el código correctamente desde el formulario
    $pass = $_POST['pass'];
    $confpass = $_POST['confpass'];

    if ($pass === $confpass) {
        // Actualizar la contraseña en la base de datos
        $updateQuery = "UPDATE usuarios SET Pass=? WHERE Codigo=?";
        $stmt = $conexion->prepare($updateQuery);
        if ($stmt) {
            $stmt->bind_param('si', $pass, $codigo);
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

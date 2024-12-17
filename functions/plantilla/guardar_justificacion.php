<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';

date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si hay una sesión activa
        if (!isset($_SESSION['Codigo']) || !isset($_SESSION['Departamento_ID'])) {
            throw new Exception("No se ha iniciado sesión correctamente.");
        }

        $departamento_id = $_SESSION['Departamento_ID'];
        $codigo_usuario = $_SESSION['Codigo'];

        $justificacion = isset($_POST['justificacion']) ? 
            mysqli_real_escape_string($conexion, $_POST['justificacion']) : 
            throw new Exception("Justificación no proporcionada");

        // Verificar la longitud de la justificación sin contar espacios
        $justificacion_sin_espacios = preg_replace('/\s+/', '', $justificacion);
        if (strlen($justificacion_sin_espacios) < 60) {
            throw new Exception("La justificación debe tener al menos 60 caracteres sin contar espacios.");
        }

        // Verificar si ya existe una justificación para este usuario y departamento
        $check_sql = "SELECT COUNT(*) as count FROM justificaciones WHERE Departamento_ID = ? AND Codigo_Usuario = ?";
        $check_stmt = $conexion->prepare($check_sql);
        $check_stmt->bind_param("is", $departamento_id, $codigo_usuario);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Ya has enviado una justificación anteriormente.");
        }

        // Insertar justificación
        $sql = "INSERT INTO justificaciones (Departamento_ID, Codigo_Usuario, Justificacion, Justificacion_Enviada) 
                VALUES (?, ?, ?, 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iss", $departamento_id, $codigo_usuario, $justificacion);

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar la justificación: " . $stmt->error);
        }

        // Obtener información del departamento
        $sql_departamento = "SELECT Departamentos FROM departamentos WHERE Departamento_ID = ?";
        $stmt_departamento = $conexion->prepare($sql_departamento);
        $stmt_departamento->bind_param("i", $departamento_id);
        $stmt_departamento->execute();
        $result_departamento = $stmt_departamento->get_result();
        $departamento = $result_departamento->fetch_assoc();

        // Enviar correos a secretaría administrativa
        $sql_secretaria = "SELECT Correo FROM usuarios WHERE Rol_ID = 2";
        $result_secretaria = $conexion->query($sql_secretaria);

        while ($secretaria = $result_secretaria->fetch_assoc()) {
            $destinatario = $secretaria['Correo'];
            $asunto = "Nueva justificación enviada";
            $cuerpo = "
            <html>
            <head>
                <style>
                    /* Estilos CSS previamente definidos */
                </style>
            </head>

            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                    </div>
                    <div class='content'>
                        <h2>Nueva justificación enviada</h2>
                        <p>Se ha recibido una nueva justificación del departamento de {$departamento['Departamentos']}.</p>
                        <p>Fecha de envío: " . date('d/m/Y H:i') . "</p>
                        <p>Por favor, ingrese al sistema para más detalles.</p>
                    </div>
                    <div class='footer'>
                        <p>Centro para la Sociedad Digital</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            enviarCorreo($destinatario, $asunto, $cuerpo);
        }

        echo json_encode(["success" => true, "message" => "Justificación guardada exitosamente"]);
        exit();

    } else {
        throw new Exception("Método de solicitud no válido");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => $e->getMessage(),
        "error_details" => $e->getTraceAsString()
    ]);
    exit();
}

mysqli_close($conexion);
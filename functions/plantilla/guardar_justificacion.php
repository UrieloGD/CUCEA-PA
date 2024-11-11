<?php
session_start();
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $justificacion = mysqli_real_escape_string($conexion, $_POST['justificacion']);
    $departamento_id = $_POST['departamento_id'];
    $codigo_usuario = $_POST['codigo_usuario'];

    // Verificar la longitud de la justificación sin contar espacios
    $justificacion_sin_espacios = preg_replace('/\s+/', '', $justificacion);
    if (strlen($justificacion_sin_espacios) < 60) {
        echo json_encode(["success" => false, "message" => "La justificación debe tener al menos 60 caracteres sin contar espacios."]);
        exit();
    }

    // Verificar si ya existe una justificación para este usuario y departamento
    $check_sql = "SELECT COUNT(*) as count FROM Justificaciones WHERE Departamento_ID = ? AND Codigo_Usuario = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("is", $departamento_id, $codigo_usuario);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(["success" => false, "message" => "Ya has enviado una justificación anteriormente."]);
        exit();
    }

    // Insertar justificación y marcarla como enviada
    $sql = "INSERT INTO Justificaciones (Departamento_ID, Codigo_Usuario, Justificacion, Justificacion_Enviada) 
            VALUES (?, ?, ?, 1)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iss", $departamento_id, $codigo_usuario, $justificacion);

    if ($stmt->execute()) {
        // La justificación se guardó correctamente

        // Obtener información del departamento
        $sql_departamento = "SELECT Departamentos FROM Departamentos WHERE Departamento_ID = ?";
        $stmt_departamento = $conexion->prepare($sql_departamento);
        $stmt_departamento->bind_param("i", $departamento_id);
        $stmt_departamento->execute();
        $result_departamento = $stmt_departamento->get_result();
        $departamento = $result_departamento->fetch_assoc();

        // Obtener correos de los usuarios de secretaría administrativa
        $sql_secretaria = "SELECT Correo FROM Usuarios WHERE Rol_ID = 2";
        $result_secretaria = $conexion->query($sql_secretaria);

        while ($secretaria = $result_secretaria->fetch_assoc()) {
            $destinatario = $secretaria['Correo'];
            $asunto = "Nueva justificación enviada";
            $cuerpo = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                    .header { text-align: center; padding-bottom: 20px; }
                    .header img { width: 300px; }
                    .content { padding: 20px; }
                    h2 { color: #2c3e50; }
                    p { line-height: 1.5; color: #333; }
                    .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
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
                        <p>Fecha de envío: " .date('d/m/Y H:i') . "</p>
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
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar la justificación: " . $stmt->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no válido"]);
}

mysqli_close($conexion);
exit();

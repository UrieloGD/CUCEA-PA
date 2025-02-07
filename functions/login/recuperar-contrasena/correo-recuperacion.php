<?php
require_once('./../../../config/db.php');
require_once('./../../../config/url_config.php');

// Establecer la codificación de la conexión a la base de datos
$conexion->set_charset('utf8');

// Código para filtrar y evitar inyecciones de código.
//$email = mysqli_real_escape_string($conexion, $_POST['Correo']);
//$emailclean = filter_var($email, FILTER_SANITIZE_EMAIL, FILTER_FLAG_STRIP_HIGH);

$email = $_POST['email'];

require './../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$query = "SELECT * FROM usuarios WHERE Correo = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows > 0) {
    $codigo = $row['Codigo'];
    
    // Generar URL dinámica
    $resetUrl = URLConfig::getFullURL('/functions/login/recuperar-contrasena/cambiar-contrasena.php?id=' . $codigo);

    // Crear una instancia; pasando `true` habilita excepciones
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'omarpruebas3621@gmail.com';
        $mail->Password = 'xwrw plzz kmvo jyws';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipientes
        $mail->setFrom('omarpruebas3621@gmail.com', 'Programación Académica');
        $mail->addAddress($email);

        // Configuración de la codificación
        $mail->CharSet = 'UTF-8';

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Solicitud de recuperación de contraseña';

        //Nuevo cuerpo del correo con estilo mejorado
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
                        <h2>Recuperación de Contraseñas</h2>
                        <p>!Hola, {$row['Nombre']} {$row['Apellido']}!</p>
                        <p>Hemos recibido una solicitud para recuperar tu contraseña. Si no has sido tú quien ha solicitado este cambio, por favor ignora este correo.</p>
                        <p>Para continuar con el proceso de recuperación de contraseña, por favor haz clic en el siguiente botón: </p>
                        <p style='text-align: center;'>
                            <a href='{$resetUrl}' class='button'>Recuperar Contraseña</a>
                        </p>
                        <p> Este enlace expirará en 24 horas.</p>
                    </div>
                    <div class='footer'>
                        <p>Centro para la Sociedad Digital</p>
                    </div>
                </div>
            </body>
        </html>
        ";
        $mail->Body = $cuerpo;
        $mail->AltBody = 'Este es un correo generado para solicitar la recuperación de tu contraseña, por favor, visita la página ' . $resetUrl;

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Correo de recuperación enviado']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "El mensaje no se pudo enviar. Error de Mailer: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "El correo no está registrado."]);
}

$stmt->close();
$conexion->close();
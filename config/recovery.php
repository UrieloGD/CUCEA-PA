<?php
require_once('./db.php');

// Establecer la codificación de la conexión a la base de datos
$conexion->set_charset('utf8');

// Código para filtrar y evitar inyecciones de código.
//$email = mysqli_real_escape_string($conexion, $_POST['Correo']);
//$emailclean = filter_var($email, FILTER_SANITIZE_EMAIL, FILTER_FLAG_STRIP_HIGH);

$email = $_POST['email'];

require './../vendor/autoload.php';

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
        $mail->Body = '¡Hola, ' . $row['Nombre'] . ' ' . $row['Apellido'] . '!<br> Este es un correo generado para solicitar la recuperación de tu contraseña, por favor, visita la página <a href="http://localhost/git/CUCEA-PA/cambiarContraseña.php?id=' . $codigo . '">Recuperación de contraseña</a>';
        $mail->AltBody = 'Este es un correo generado para solicitar la recuperación de tu contraseña, por favor, visita la página http://localhost/git/CUCEA-PA/cambiarContraseña.php?id=' . $codigo;

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

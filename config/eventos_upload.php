<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "La conexión ha fallado: " . $conn->connect_error]));
}

// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fechIn = $_POST['FechIn'];
    $fechFi = $_POST['FechFi'];
    $horIn = $_POST['HorIn'];
    $horFi = $_POST['HorFi'];
    $etiqueta = $_POST['etiqueta'];
    $participantes = isset($_POST['participantes']) ? implode(",", $_POST['participantes']) : '';
    $notif = $_POST['notificacion'];
    $horNotif = $_POST['HorNotif'];

    // Calculo de la notificación
    $fechaInicio = new DateTime($fechIn);
    $fechaNotificacion = null;

    switch ($notif) {
        case '1 hora antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-1 hour');
            break;
        case '2 horas antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-2 hours');
            break;
        case '1 día antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-1 day');
            break;
        case '1 semana antes':
            $fechaNotificacion = clone $fechaInicio;
            $fechaNotificacion->modify('-1 week');
            break;
    }

    // Insertar datos en la tabla eventos_admin
    $sql = "INSERT INTO eventos_admin (Nombre_Evento, Descripcion_Evento, Fecha_Inicio, Fecha_Fin, Hora_Inicio, Hora_Fin, Etiqueta, Participantes, notificaciones, Hora_Noti)
    VALUES ('$nombre', '$descripcion', '$fechIn', '$fechFi', '$horIn', '$horFi', '$etiqueta', '$participantes', '$notif', '$horNotif')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Nuevo registro creado con éxito']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no válido']);
}

// Cerrar la conexión
$conn->close();
?>
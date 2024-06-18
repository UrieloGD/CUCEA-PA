<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión ha fallado: " . $conn->connect_error);
}

// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario de eventos nuevos
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fechIn = $_POST['FechIn'];
    $fechFi = $_POST['FechFi'];
    $horIn = $_POST['HorIn'];
    $horFi = $_POST['HorFi'];
    $etiqueta = $_POST['etiqueta'];
    $participantes = isset($_POST['participantes']) ? implode(",", $_POST['participantes']) : ''; // Convertir el arreglo en una cadena separada por comas
    $notif = $_POST['notificacion'];
    $horNotif = $_POST['HorNotif'];

    // Calculo de la notificación
    // Convertir la fecha de inicio a un objeto DateTime
    $fechaInicio = new DateTime($fechIn);

    // Calcular la fecha de notificación basada en la opción seleccionada
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
        case 'Sin notificación':
            $fechaNotificacion = null;
            break;
        default:
            $fechaNotificacion = null;
            break;
    }

// Mostrar la fecha de notificación calculada
if ($fechaNotificacion !== null) {
    $fechaNotificacionFormato = $fechaNotificacion->format('d/m/Y H:i:s'); // Formato deseado
    echo "La notificación está programada para el día: " . $fechaNotificacionFormato . "<br>";
} else {
    echo "No se estableció fecha de notificación.<br>";
}

    // Insertar datos en la tabla eventos_admin
    $sql = "INSERT INTO eventos_admin (Nombre_Evento, Descripcion_Evento, Fecha_Inicio, Fecha_Fin, Hora_Inicio, Hora_Fin, Etiqueta, Participantes, notificaciones, Hora_Noti)
    VALUES ('$nombre', '$descripcion', '$fechIn', '$fechFi', '$horIn', '$horFi', '$etiqueta', '$participantes', '$notif', '$horNotif')";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    // Cerrar la conexión
    $conn->close();
}
?>
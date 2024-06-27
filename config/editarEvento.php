<?php
include './db.php';

// Verificar si se proporcionó un ID de evento
if (!isset($_GET['id'])) {
    die("No se proporcionó ID de evento");
}

$id = $_GET['id'];

// Obtener los datos del evento
$sql = "SELECT * FROM Eventos_Admin WHERE ID_Evento = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No se encontró el evento");
}

$evento = $result->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $etiqueta = $_POST['etiqueta'];
    $participantes = $_POST['participantes'];
    $notificaciones = $_POST['notificaciones'];
    $hora_noti = $_POST['hora_noti'];

    $sql = "UPDATE Eventos_Admin SET 
            Nombre_Evento = ?, 
            Descripcion_Evento = ?, 
            Fecha_Inicio = ?, 
            Fecha_Fin = ?, 
            Hora_Inicio = ?, 
            Hora_Fin = ?, 
            Etiqueta = ?, 
            Participantes = ?, 
            Notificaciones = ?, 
            Hora_Noti = ? 
            WHERE ID_Evento = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssssi", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $etiqueta, $participantes, $notificaciones, $hora_noti, $id);

    if ($stmt->execute()) {
        header("Location: ../admin-visual-eventos.php");
        exit();
    } else {
        echo "Error al actualizar el evento: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento</title>
    <!-- Aquí puedes incluir tus estilos CSS -->
</head>

<body>
    <h1>Editar Evento</h1>
    <form method="POST">
        <label for="nombre">Nombre del Evento:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($evento['Nombre_Evento']); ?>" required><br>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($evento['Descripcion_Evento']); ?></textarea><br>

        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $evento['Fecha_Inicio']; ?>" required><br>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $evento['Fecha_Fin']; ?>" required><br>

        <label for="hora_inicio">Hora de Inicio:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo $evento['Hora_Inicio']; ?>" required><br>

        <label for="hora_fin">Hora de Fin:</label>
        <input type="time" id="hora_fin" name="hora_fin" value="<?php echo $evento['Hora_Fin']; ?>" required><br>

        <label for="etiqueta">Etiqueta:</label>
        <input type="text" id="etiqueta" name="etiqueta" value="<?php echo htmlspecialchars($evento['Etiqueta']); ?>"><br>

        <label for="participantes">Participantes:</label>
        <input type="text" id="participantes" name="participantes" value="<?php echo htmlspecialchars($evento['Participantes']); ?>" required><br>

        <label for="notificaciones">Notificaciones:</label>
        <textarea id="notificaciones" name="notificaciones"><?php echo htmlspecialchars($evento['Notificaciones']); ?></textarea><br>

        <label for="hora_noti">Hora de Notificación:</label>
        <input type="time" id="hora_noti" name="hora_noti" value="<?php echo $evento['Hora_Noti']; ?>" required><br>

        <input type="submit" value="Guardar Cambios">
    </form>
</body>

</html>
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

// SQL para obtener los datos
$sql = "SELECT * FROM eventos_admin";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<tr>
            <th>ID</th>
            <th>Nombre Evento</th>
            <th>Descripción</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Etiqueta</th>
            <th>Participantes</th>
            <th>Notificaciones</th>
            <th>Hora Notificación</th>
          </tr>";
    // Mostrar los datos en una tabla HTML
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['ID_Evento']}</td>
                <td>{$row['Nombre_Evento']}</td>
                <td>{$row['Descripcion_Evento']}</td>
                <td>{$row['Fecha_Inicio']}</td>
                <td>{$row['Fecha_Fin']}</td>
                <td>{$row['Hora_Inicio']}</td>
                <td>{$row['Hora_Fin']}</td>
                <td>{$row['Etiqueta']}</td>
                <td>{$row['Participantes']}</td>
                <td>{$row['Notificaciones']}</td>
                <td>{$row['Hora_Noti']}</td>
              </tr>";
    }
} else {
    echo "No hay eventos registrados.";
}

// Cerrar la conexión
$conn->close();
?>
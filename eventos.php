<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario con Tabla de Resultados</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Formulario de Evento</h2>
    <form action=config/eventos_upload.php  method="post" id="eventoForm">
        <!--  Nombre del evento   -->
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre"><br><br>
        <!--  Descripción del evento   -->
        <label for="descripcion">Descripción:</label>
        <input type="text" id="descripcion" name="descripcion"><br><br>
        <!--  Fecha de inicio   -->
        <label for="FechIn">Fecha Inicio:</label>
        <input type="date" id="FechIn" name="FechIn"><br><br>
        <!--  Fecha de fin  -->
        <label for="FechFi">Fecha Fin:</label>
        <input type="date" id="FechFi" name="FechFi"><br><br>
        <!--  Hora de inicio   -->
        <label for="HorIn">Hora Inicio:</label>
        <input type="time" id="HorIn" name="HorIn"><br><br>
        <!--  Hora de fin   -->
        <label for="HorFi">Hora Fin:</label>
        <input type="time" id="HorFi" name="HorFi"><br><br>
        <!--  Etiqueta   -->
        <label for="etiqueta">Etiqueta:</label>
        <input type="text" id="etiqueta" name="etiqueta"><br><br>
        <!--  Participantes   -->
        <label for="participantes">Participantes:</label>
        <input type="text" id="participantes" name="participantes"><br><br>
        <!-- Notificación -->
        <label for="notificacion">Notificación:</label>
        <select id="notificacion" name="notificacion">
        <option value="1 hora antes">1 hora antes</option>
        <option value="2 horas antes">2 horas antes</option>
        <option value="1 día antes">1 día antes</option>
        <option value="1 semana antes">1 semana antes</option>
        <option value="Sin notificación">Sin notificación</option>
        </select>
        <br><br>
        <!--  Hora Notificación  -->
        <label for="HorNotif">Hora Notificación:</label>
        <input type="time" id="HorNotif" name="HorNotif"><br><br>
        <input type="submit" value="Enviar">
    </form>
    <h2>Eventos Registrados</h2>
    <table id="tablaEventos" border='1'>
        <tr>
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
        </tr>
    </table>

    <!-- Script para cargar los datos dinámicamente -->
    <script>
        // Función para cargar eventos desde PHP usando AJAX
        function cargarEventos() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tablaEventos").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "config/mostrar_eventos.php", true);
            xhttp.send();
        }

        // Llamar a la función al cargar la página
        cargarEventos();
    </script>
</body>
</html>
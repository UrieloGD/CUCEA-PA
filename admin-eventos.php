<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener usuarios de la base de datos
$sql = "SELECT Codigo, Nombre, Apellido, Correo FROM Usuarios";
$resultado = $conexion->query($sql);

// Generar opciones del dropdown
$opciones_usuarios = "";
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $codigo = $fila["Codigo"];
        $nombre = $fila["Nombre"] . " " . $fila["Apellido"];
        $correo = $fila["Correo"];
        $opciones_usuarios .= "<option value='$codigo'>$nombre ($correo)</option>";
    }
} else {
    $opciones_participantes = "<option value=''>No hay usuarios registrados</option>";
}

// Cerrar la conexión
$conexion->close();
?>

<title>Crear Evento</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Crear evento</h3>
        </div>
    </div>
    <div class="contenedor-formulario">

        <form action="config/eventos_upload.php" method="post" id="eventoForm">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del evento">
            </div>
            
            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <input type="date" id="FechIn" name="FechIn">
                    <input type="date" id="FechFi" name="FechFi">
                    <span>a las</span>
                    <input type="time" id="HorIn" name="HorIn">
                    <span> --> </span>
                    <input type="time" id="HorFin" name="HorFi">
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Notificaciones
                </label>
                <div class="notificaciones-group">
                    <select id="notificacion" name="notificacion">
                        <option value="1 hora antes">1 hora antes</option>
                        <option value="2 horas antes">2 horas antes</option>
                        <option value="1 día antes">1 día antes</option>
                        <option value="1 semana antes">1 semana antes</option>
                        <option value="Sin notificación">Sin notificación</option>
                    </select>
                    <span>a las</span>
                    <input type="time" id="HorNotif" name="HorNotif">
                    <label class="checkbox-label">
                        <input type="checkbox" name="correo_electronico">Correo electrónico
                    </label>
                </div>
            </div>

            <div class="botones_agregar">
                <button type="button" class="boton-agregar">+ Agregar notificación</button>
            </div>
            
            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <select id="participantes" name="participantes[]" multiple>
                        <option value="">Selecciona los participantes</option>
                        <?php echo $opciones_usuarios; ?>
                    </select>
                    <div id="participantes-seleccionados"></div>
                </div>
                <div class="split-item">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="etiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="1">Programación Académica</option>
                    </select>
                </div>
            </div>
            
            <!-- <div class="botones_agregar">    
                <button type="button" class="boton-agregar">+ Agregar participantes</button>
                <button type="button" class="boton-agregar">+ Agregar etiqueta</button>
            </div> -->
                            
            <div class="form-group">
                <label for="descripcion">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar">Cancelar</button>
            </div>
        </form>        
    </div>
    
    <script>
        // function cargarEventos() {
        //     var xhttp = new XMLHttpRequest();
        //     xhttp.onreadystatechange = function() {
        //         if (this.readyState == 4 && this.status == 200) {
        //             document.getElementById("tablaEventos").innerHTML = this.responseText;
        //         }
        //     };
        //     xhttp.open("GET", "config/mostrar_eventos.php", true);
        //     xhttp.send();
        // }

        // cargarEventos();

        // Obtener el elemento select y el contenedor de participantes seleccionados
   
        const selectParticipantes = document.getElementById('participantes');
        const contenedorParticipantes = document.getElementById('participantes-seleccionados');

        // Función para agregar un participante seleccionado
        function agregarParticipante() {
            const participanteSeleccionado = selectParticipantes.value;
            const nombreParticipante = selectParticipantes.options[selectParticipantes.selectedIndex].text;

            if (participanteSeleccionado) {
                const tarjetaParticipante = document.createElement('div');
                tarjetaParticipante.classList.add('participante-tarjeta');

                const nombreParticipanteElement = document.createElement('span');
                nombreParticipanteElement.textContent = nombreParticipante;
                tarjetaParticipante.appendChild(nombreParticipanteElement);

                const cerrarParticipante = document.createElement('span');
                cerrarParticipante.classList.add('cerrar');
                cerrarParticipante.textContent = '×';
                cerrarParticipante.addEventListener('click', eliminarParticipante);
                tarjetaParticipante.appendChild(cerrarParticipante);

                contenedorParticipantes.appendChild(tarjetaParticipante);
                selectParticipantes.selectedIndex = 0;
            }
        }

        function eliminarParticipante(evento) {
            const tarjetaParticipante = evento.target.parentNode;
            tarjetaParticipante.remove();
        }

        // Agregar evento al cambiar la selección del dropdown
        selectParticipantes.addEventListener('change', agregarParticipante);
    </script>

<?php include './template/footer.php' ?>

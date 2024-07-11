<?php
//<!-- Conexión a la base de datos -->
include './config/db.php';

// Verificar si se proporcionó un ID de evento
if (!isset($_GET['id'])) {
    die(json_encode(['status' => 'error', 'message' => 'No se proporcionó ID de evento']));
}

$id = $_GET['id'];

// Obtener los datos del evento
$sql = "SELECT * FROM Eventos_Admin WHERE ID_Evento = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(['status' => 'error', 'message' => 'No se encontró el evento']));
}

$evento = $result->fetch_assoc();


// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    error_reporting(0);
    ini_set('display_errors', 0);


    try {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fecha_inicio = $_POST['Evento_fecha_inicio'] ?? '';
        $fecha_fin = $_POST['Evento_echa_fin'] ?? '';
        $hora_inicio = $_POST['Evento_hora_inicio'] ?? '';
        $hora_fin = $_POST['Evento_hora_fin'] ?? '';
        $etiqueta = $_POST['etiqueta'] ?? '';
        $participantes = isset($_POST['participantes']) ? implode(',', $_POST['participantes']) : '';
        $notificaciones = $_POST['notificaciones'] ?? '';
        $hora_noti = $_POST['hora_noti'] ?? '';

        $sql = "UPDATE Eventos_Admin SET 
                Nombre_Evento = ?, 
                Descripcion_Evento = ?, 
                Evento_Fecha_Inicio = ?, 
                Evento_Fecha_Fin = ?, 
                Evento_Hora_Inicio = ?, 
                Evento_Hora_Fin = ?, 
                Etiqueta = ?, 
                Participantes = ?, 
                Notificaciones = ?, 
                Hora_Noti = ? 
                WHERE ID_Evento = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssssssi", $nombre, $descripcion, $evento_fecha_inicio, $evento_fecha_fin, $evento_hora_inicio, $evento_hora_fin, $etiqueta, $participantes, $notificaciones, $hora_noti, $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Cambios guardados correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>

<title>Editar Evento</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Editar evento</h3>
        </div>
    </div>
    <div class="contenedor-formulario">
        
        <form id="eventoForm" method="post">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($evento['Nombre_Evento']); ?>" required>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $evento['Evento_Fecha_Inicio']; ?>" required>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $evento['Evento_Fecha_Fin']; ?>" required>
                    <span>a las</span>
                    <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo $evento['Evento_Hora_Inicio']; ?>" required>
                    <span> --> </span>
                    <input type="time" id="hora_fin" name="hora_fin" value="<?php echo $evento['Evento_Hora_Fin']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Notificaciones
                </label>
                <div class="notificaciones-group">
                    <select id="notificaciones" name="notificaciones">
                        <option value="1 hora antes" <?php echo ($evento['Notificaciones'] == '1 hora antes') ? 'selected' : ''; ?>>1 hora antes</option>
                        <option value="2 horas antes" <?php echo ($evento['Notificaciones'] == '2 horas antes') ? 'selected' : ''; ?>>2 horas antes</option>
                        <option value="1 día antes" <?php echo ($evento['Notificaciones'] == '1 día antes') ? 'selected' : ''; ?>>1 día antes</option>
                        <option value="1 semana antes" <?php echo ($evento['Notificaciones'] == '1 semana antes') ? 'selected' : ''; ?>>1 semana antes</option>
                        <option value="Sin notificación" <?php echo ($evento['Notificaciones'] == 'Sin notificación') ? 'selected' : ''; ?>>Sin notificación</option>
                    </select>
                    <span>a las</span>
                    <input type="time" id="hora_noti" name="hora_noti" value="<?php echo $evento['Hora_Noti']; ?>" required>
                </div>
            </div>

            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <select id="participantes" name="participantes[]" multiple>
                        <option value="" selected disabled>Selecciona los participantes</option>
                        <?php
                        // Obtener usuarios de la base de datos
                        $sql = "SELECT Codigo, Nombre, Apellido, Correo FROM Usuarios";
                        $resultado = $conexion->query($sql);

                        // Generar opciones del dropdown
                        if ($resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                                $codigo = $fila["Codigo"];
                                $nombre = $fila["Nombre"] . " " . $fila["Apellido"];
                                $correo = $fila["Correo"];
                                $selected = in_array($codigo, explode(',', $evento['Participantes'])) ? 'selected' : '';
                                echo "<option value='$codigo' $selected>$nombre ($correo)</option>";
                            }
                        } else {
                            echo "<option value=''>No hay usuarios registrados</option>";
                        }
                        ?>
                    </select>
                    <div id="participantes-seleccionados"></div>
                    <div class="split-item">
                        <label for="etiqueta">
                            <i class="fas fa-tag"></i> Etiqueta
                        </label>
                        <select id="etiqueta" name="etiqueta">
                            <option value="">Elige una etiqueta</option>
                            <option value="Programación Académica" <?php echo ($evento['Etiqueta'] == 'Programación Académica') ? 'selected' : ''; ?>>Programación Académica</option>
                            <option value="Oferta Académica" <?php echo ($evento['Etiqueta'] == 'Oferta Académica') ? 'selected' : ''; ?>>Oferta Académica</option>
                            <option value="Administrativo" <?php echo ($evento['Etiqueta'] == 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-align-left"></i> Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($evento['Descripcion_Evento']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                    <a href="./admin-visual-eventos.php"><button type="button" class="btn-cancelar">Cancelar</button></a>
                </div>
        </form>
    </div>

    <script>
        document.getElementById('eventoForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas guardar los cambios en este evento?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(this);

                    fetch(window.location.href, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(text => {
                            console.log('Server response:', text); // Para depuración
                            try {
                                return JSON.parse(text);
                            } catch (error) {
                                console.error('Error parsing JSON:', text);
                                throw new Error('Invalid JSON response');
                            }
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire(
                                    '¡Guardado!',
                                    data.message || 'Los cambios han sido guardados.',
                                    'success'
                                ).then(() => {
                                    window.location.href = './admin-visual-eventos.php';
                                });
                            } else {
                                throw new Error(data.message || 'Error desconocido');
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error',
                                'Hubo un problema al procesar la respuesta del servidor: ' + error.message,
                                'error'
                            );
                        });
                }
            });
        });

        // Código para manejar la selección de participantes
        const selectParticipantes = document.getElementById('participantes');
        const contenedorParticipantes = document.getElementById('participantes-seleccionados');

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

        selectParticipantes.addEventListener('change', agregarParticipante);

        // Inicializar las tarjetas de participantes existentes
        window.addEventListener('load', function() {
            for (let option of selectParticipantes.options) {
                if (option.selected) {
                    const tarjetaParticipante = document.createElement('div');
                    tarjetaParticipante.classList.add('participante-tarjeta');

                    const nombreParticipanteElement = document.createElement('span');
                    nombreParticipanteElement.textContent = option.text;
                    tarjetaParticipante.appendChild(nombreParticipanteElement);

                    const cerrarParticipante = document.createElement('span');
                    cerrarParticipante.classList.add('cerrar');
                    cerrarParticipante.textContent = '×';
                    cerrarParticipante.addEventListener('click', eliminarParticipante);
                    tarjetaParticipante.appendChild(cerrarParticipante);

                    contenedorParticipantes.appendChild(tarjetaParticipante);
                }
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php include './template/footer.php' ?>
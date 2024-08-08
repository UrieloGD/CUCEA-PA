<?php
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
        $fecha_inicio = $_POST['FechIn'] ?? '';
        $fecha_fin = $_POST['FechFi'] ?? '';
        $hora_inicio = $_POST['HorIn'] ?? '';
        $hora_fin = $_POST['HorFi'] ?? '';
        $etiqueta = $_POST['etiqueta'] ?? '';
        $participantes = isset($_POST['participantes']) ? implode(',', $_POST['participantes']) : '';
        $notificaciones = $_POST['notificacion'] ?? '';
        $hora_noti = $_POST['HorNotif'] ?? '';

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
            echo json_encode(['status' => 'success', 'message' => 'Cambios guardados correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

// Obtener usuarios de la base de datos
$sql = "SELECT u.Codigo, u.Nombre, u.Apellido, u.Correo, r.Nombre_Rol, GROUP_CONCAT(d.Departamentos SEPARATOR ', ') AS Departamentos
        FROM Usuarios u
        LEFT JOIN Roles r ON u.Rol_ID = r.Rol_ID
        LEFT JOIN Usuarios_Departamentos ud ON u.Codigo = ud.Usuario_ID
        LEFT JOIN Departamentos d ON ud.Departamento_ID = d.Departamento_ID
        GROUP BY u.Codigo";

$resultado = $conexion->query($sql);

$filas_usuarios = "";
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $codigo = $fila["Codigo"];
        $nombre = $fila["Nombre"] . " " . $fila["Apellido"];
        $correo = $fila["Correo"];
        $rol = $fila["Nombre_Rol"];
        
        if(empty($fila["Departamentos"])){
            $rol_departamento = $rol;
        } else{
            $rol = $fila["Nombre_Rol"] . " - " . $fila["Departamentos"];
        }
    
        $checked = in_array($codigo, explode(',', $evento['Participantes'])) ? 'checked' : '';
        $filas_usuarios .= "<tr>
            <td><input type='checkbox' name='participantes[]' value='$codigo' class='checkbox-usuario' $checked></td>
            <td>$nombre</td>
            <td>$correo</td>
            <td>$rol</td>
        </tr>";
    }
} else {
    $filas_usuarios = "<tr><td colspan='4'>No hay usuarios registrados</td></tr>";
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>

<title>Editar Evento</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

<div class="cuadro-principal">
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
                    <input type="date" id="FechIn" name="FechIn" value="<?php echo $evento['Fecha_Inicio']; ?>" required min="<?php echo date('Y-m-d'); ?>">
                    <input type="date" id="FechFi" name="FechFi" value="<?php echo $evento['Fecha_Fin']; ?>" required min="<?php echo date('Y-m-d'); ?>">
                    <span>a las</span>
                    <input type="time" id="HorIn" name="HorIn" value="<?php echo $evento['Hora_Inicio']; ?>" required>
                    <span> --> </span>
                    <input type="time" id="HorFi" name="HorFi" value="<?php echo $evento['Hora_Fin']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Notificaciones
                </label>
                <div class="notificaciones-group">
                    <select id="notificacion" name="notificacion">
                        <option value="1 hora antes" <?php echo ($evento['Notificaciones'] == '1 hora antes') ? 'selected' : ''; ?>>1 hora antes</option>
                        <option value="2 horas antes" <?php echo ($evento['Notificaciones'] == '2 horas antes') ? 'selected' : ''; ?>>2 horas antes</option>
                        <option value="1 día antes" <?php echo ($evento['Notificaciones'] == '1 día antes') ? 'selected' : ''; ?>>1 día antes</option>
                        <option value="1 semana antes" <?php echo ($evento['Notificaciones'] == '1 semana antes') ? 'selected' : ''; ?>>1 semana antes</option>
                        <option value="Sin notificación" <?php echo ($evento['Notificaciones'] == 'Sin notificación') ? 'selected' : ''; ?>>Sin notificación</option>
                    </select>
                    <span>a las</span>
                    <input type="time" id="HorNotif" name="HorNotif" value="<?php echo $evento['Hora_Noti']; ?>" required>
                </div>
            </div>

            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <button class="boton-agregar-participantes" type="button" id="abrirModal">Añadir participantes</button>
                    <div id="participantes-seleccionados"></div>
                    <div id="input-participantes"></div>
                </div>
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
    
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Seleccionar Participantes</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $filas_usuarios; ?>
                </tbody>
            </table>
            <button class="btn-guardar" type="button" id="confirmarParticipantes">Confirmar selección</button>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('modal');
const abrirModalBtn = document.getElementById('abrirModal');
const cerrarModalBtn = document.querySelector('.close');
const confirmarParticipantesBtn = document.getElementById('confirmarParticipantes');
const participantesSeleccionados = document.getElementById('participantes-seleccionados');
const inputParticipantes = document.getElementById('input-participantes');

abrirModalBtn.onclick = function(e) {
    e.preventDefault();
    modal.style.display = "flex";
    ajustarTamañoModal();
}

cerrarModalBtn.onclick = function() {
    modal.style.display = "none";
}

confirmarParticipantesBtn.onclick = function() {
    actualizarParticipantesSeleccionados();
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function actualizarParticipantesSeleccionados() {
    participantesSeleccionados.innerHTML = '';
    inputParticipantes.innerHTML = '';
    const checkboxes = document.querySelectorAll('.checkbox-usuario');
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const nombre = checkbox.parentElement.nextElementSibling.textContent;
            const participanteDiv = document.createElement('div');
            participanteDiv.className = 'participante-tarjeta';
            participanteDiv.innerHTML = `
                <span class="nombre">${nombre}</span>
                <span class="eliminar" title="Eliminar">&times;</span>
            `;
            participantesSeleccionados.appendChild(participanteDiv);
            
            const inputOculto = document.createElement('input');
            inputOculto.type = 'hidden';
            inputOculto.name = 'participantes[]';
            inputOculto.value = checkbox.value;
            inputParticipantes.appendChild(inputOculto);

            participanteDiv.querySelector('.eliminar').addEventListener('click', function() {
                participanteDiv.remove();
                inputOculto.remove();
                checkbox.checked = false;
            });
        }
    });
}

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
            .then(response => response.json())
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

function ajustarTamañoModal() {
    const modalContent = document.querySelector('.modal-content');
    const tabla = modalContent.querySelector('table');
    const boton = modalContent.querySelector('#confirmarParticipantes');
    
    modalContent.style.maxHeight = '80vh';
    
    const alturaContenido = tabla.offsetHeight + boton.offsetHeight + 60;
    
    if (alturaContenido < window.innerHeight * 0.8) {
        modalContent.style.maxHeight = `${alturaContenido}px`;
    }
}

window.addEventListener('resize', ajustarTamañoModal);

// Inicializar los participantes seleccionados al cargar la página
window.addEventListener('load', function() {
    actualizarParticipantesSeleccionados();
});
</script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php include './template/footer.php' ?>
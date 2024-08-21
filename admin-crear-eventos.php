<?php
include './template/header.php';
include './template/navbar.php';
include './config/db.php';

// Verificar si se está editando un evento
$editing = false;
$evento = null;
if (isset($_GET['edit'])) {
    $editing = true;
    $id_evento = $_GET['edit'];
    
    // Obtener datos del evento
    $sql = "SELECT * FROM Eventos_Admin WHERE ID_Evento = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_evento);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $evento = $result->fetch_assoc();
    }
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
            $rol= $fila["Nombre_Rol"] . " - " . $fila["Departamentos"];
        }
    
        $checked = $editing && in_array($codigo, explode(',', $evento['Participantes'])) ? 'checked' : '';
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

<title><?php echo $editing ? 'Editar' : 'Crear'; ?> Evento</title>
<link rel="stylesheet" href="./CSS/admin-crear-eventos.css" />

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3><?php echo $editing ? 'Editar' : 'Crear'; ?> evento</h3>
        </div>
    </div>
    <div class="contenedor-formulario">
        <form id="eventoForm" method="post">
            <?php if ($editing): ?>
                <input type="hidden" name="id_evento" value="<?php echo $id_evento; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $editing ? htmlspecialchars($evento['Nombre_Evento']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <input type="date" id="FechIn" name="FechIn" value="<?php echo $editing ? $evento['Fecha_Inicio'] : ''; ?>" required min="<?php echo date('Y-m-d'); ?>">
                    <input type="date" id="FechFi" name="FechFi" value="<?php echo $editing ? $evento['Fecha_Fin'] : ''; ?>" required  min="<?php echo date('Y-m-d'); ?>">
                    <span>a las</span>
                    <input type="time" id="HorIn" name="HorIn" value="<?php echo $editing ? $evento['Hora_Inicio'] : ''; ?>" required>
                    <span> --> </span>
                    <input type="time" id="HorFin" name="HorFi" value="<?php echo $editing ? $evento['Hora_Fin'] : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Notificaciones
                </label>
                <div class="notificaciones-group">
                    <select id="notificacion" name="notificacion">
                        <option value="1 hora antes" <?php echo $editing && $evento['Notificaciones'] == '1 hora antes' ? 'selected' : ''; ?>>1 hora antes</option>
                        <option value="2 horas antes" <?php echo $editing && $evento['Notificaciones'] == '2 horas antes' ? 'selected' : ''; ?>>2 horas antes</option>
                        <option value="1 día antes" <?php echo $editing && $evento['Notificaciones'] == '1 día antes' ? 'selected' : ''; ?>>1 día antes</option>
                        <option value="1 semana antes" <?php echo $editing && $evento['Notificaciones'] == '1 semana antes' ? 'selected' : ''; ?>>1 semana antes</option>
                        <option value="Sin notificación" <?php echo $editing && $evento['Notificaciones'] == 'Sin notificación' ? 'selected' : ''; ?>>Sin notificación</option>
                    </select>
                    <span>a las</span>
                    <input type="time" id="HorNotif" name="HorNotif" value="<?php echo $editing ? $evento['Hora_Noti'] : ''; ?>" required>
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
                        <option value="Programación Académica" <?php echo $editing && $evento['Etiqueta'] == 'Programación Académica' ? 'selected' : ''; ?>>Programación Académica</option>
                        <option value="Oferta Académica" <?php echo $editing && $evento['Etiqueta'] == 'Oferta Académica' ? 'selected' : ''; ?>>Oferta Académica</option>
                        <option value="Administrativo" <?php echo $editing && $evento['Etiqueta'] == 'Administrativo' ? 'selected' : ''; ?>>Administrativo</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="descripcion">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea id="descripcion" name="descripcion" rows="4"><?php echo $editing ? htmlspecialchars($evento['Descripcion_Evento']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-guardar"><?php echo $editing ? 'Actualizar' : 'Guardar'; ?></button>
                <a href="./admin-eventos.php"><button type="button" class="btn-cancelar">Cancelar</button></a>
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

<script src="./JS/admin-eventos/admin-crear-eventos/modal-participantes.js"></script>
<script>
// participantes.js
const confirmarParticipantesBtn = document.getElementById('confirmarParticipantes');
const participantesSeleccionados = document.getElementById('participantes-seleccionados');
const inputParticipantes = document.getElementById('input-participantes');

confirmarParticipantesBtn.onclick = function() {
    actualizarParticipantesSeleccionados();
    modal.style.display = "none";
}

function actualizarParticipantesSeleccionados() {
    participantesSeleccionados.innerHTML = '';
    inputParticipantes.innerHTML = '';
    const checkboxes = document.querySelectorAll('.checkbox-usuario');
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const nombre = checkbox.parentElement.nextElementSibling.textContent;
            const correo = checkbox.parentElement.nextElementSibling.nextElementSibling.textContent;
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

// Inicializar los participantes seleccionados si estamos editando
<?php if ($editing): ?>
window.addEventListener('load', function() {
    actualizarParticipantesSeleccionados();
});
<?php endif; ?>
</script>

<?php include './template/footer.php'; ?>
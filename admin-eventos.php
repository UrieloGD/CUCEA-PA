<!--header -->
<?php include './template/header.php'; ?>
<!-- navbar -->
<?php include './template/navbar.php'; ?>
<!-- Conexión a la base de datos -->
<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener usuarios de la base de datos
$sql = "SELECT u.Codigo, u.Nombre, u.Apellido, u.Correo, r.Nombre_Rol, GROUP_CONCAT(d.Nombre_Departamento SEPARATOR ', ') AS Departamentos
        FROM Usuarios u
        LEFT JOIN Roles r ON u.Rol_ID = r.Rol_ID
        LEFT JOIN Usuarios_Departamentos ud ON u.Codigo = ud.Usuario_ID
        LEFT JOIN Departamentos d ON ud.Departamento_ID = d.Departamento_ID
        GROUP BY u.Codigo";

$resultado = $conexion->query($sql);

// Se despliegan los usuarios (Nombre y correo en checkboxes)
$filas_usuarios = "";
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $codigo = $fila["Codigo"];
        $nombre = $fila["Nombre"] . " " . $fila["Apellido"];
        $correo = $fila["Correo"];
        $rol = $fila["Nombre_Rol"];
        $departamentos = $fila["Departamentos"];
    
        $filas_usuarios .= "<tr>
            <td><input type='checkbox' name='participantes[]' value='$codigo' class='checkbox-usuario'></td>
            <td>$nombre</td>
            <td>$correo</td>
            <td>$rol</td>
            <td>$departamentos</td>
        </tr>";
    }
    
} else {
    $filas_usuarios = "<tr><td colspan='3'>No hay usuarios registrados</td></tr>";
}

// Cerrar la conexión
$conexion->close();
?>

<title>Crear Evento</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />
<style>
/* Estilos para el menú emergente de participantes  NOTA: No lo he añadido a la hoja de estilos de aquí*/
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-height: 70%;
    overflow-y: auto;
    border-radius: 8px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.boton-agregar-participantes {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.boton-agregar-participantes:hover {
    background-color: #0056b3;
}
</style>

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
                    <button type="button" id="abrirModal">Añadir participantes</button>
                    <div id="participantes-seleccionados"></div>
                    <label class="checkbox-label">
                        <input type="checkbox" id="select_all_rol_3" name="select_all_rol_3"> Seleccionar todos los usuarios con rol 3
                    </label>
                    <!-- Elementos de entrada ocultos para los participantes seleccionados -->
                    <div id="input-participantes"></div>
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
    
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Seleccionar Participantes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Departamentos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $filas_usuarios; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Obtener los elementos
        const modal = document.getElementById('modal');
        const abrirModalBtn = document.getElementById('abrirModal');
        const cerrarModalBtn = document.querySelector('.close');
        const participantesSeleccionados = document.getElementById('participantes-seleccionados');
        const selectAllRol3Checkbox = document.getElementById('select_all_rol_3');
        const inputParticipantes = document.getElementById('input-participantes');

        // Función para mostrar el modal
        abrirModalBtn.onclick = function() {
            modal.style.display = "block";
        }

        // Función para cerrar el modal
        cerrarModalBtn.onclick = function() {
            modal.style.display = "none";
            actualizarParticipantesSeleccionados();
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                actualizarParticipantesSeleccionados();
            }
        }

        // Actualizar la lista de participantes seleccionados
        function actualizarParticipantesSeleccionados() {
            participantesSeleccionados.innerHTML = '';
            inputParticipantes.innerHTML = '';
            const checkboxes = document.querySelectorAll('.checkbox-usuario');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const nombre = checkbox.parentElement.nextElementSibling.textContent;
                    const correo = checkbox.parentElement.nextElementSibling.nextElementSibling.textContent;
                    const participanteDiv = document.createElement('div');
                    participanteDiv.textContent = `${nombre} (${correo})`;
                    participantesSeleccionados.appendChild(participanteDiv);
                    
                    // Crear y agregar un input oculto al formulario
                    const inputOculto = document.createElement('input');
                    inputOculto.type = 'hidden';
                    inputOculto.name = 'participantes[]';
                    inputOculto.value = checkbox.value;
                    inputParticipantes.appendChild(inputOculto);
                }
            });
        }

        // Seleccionar o deseleccionar todos los usuarios con rol 3
        selectAllRol3Checkbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.checkbox-usuario');
            checkboxes.forEach(checkbox => {
                const rolID = checkbox.getAttribute('data-rol');
                if (rolID == 3) {
                    checkbox.checked = selectAllRol3Checkbox.checked;
                }
            });
        });
    </script>
</div>

<!--footer-->
<?php include './template/footer.php'; ?>

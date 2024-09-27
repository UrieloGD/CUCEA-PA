<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />
<link rel="stylesheet" href="./CSS/admin-crear-eventos.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Próximos Eventos</h3>
        </div>
    </div>

    <div class="section-title">Próximos eventos</div>

    <!-- Contenido -->
    <?php
    // Consulta modificada para obtener los nombres de los participantes
    $sql = "SELECT e.*, GROUP_CONCAT(CONCAT(u.Nombre, ' ', u.Apellido) SEPARATOR ', ') AS NombresParticipantes
            FROM Eventos_Admin e
            LEFT JOIN Usuarios u ON FIND_IN_SET(u.Codigo, e.Participantes)
            WHERE (e.Fecha_Fin >= CURDATE() OR (e.Fecha_Inicio <= CURDATE() AND e.Fecha_Fin >= CURDATE()))
            GROUP BY e.ID_Evento
            ORDER BY e.Fecha_Inicio, e.Hora_Inicio 
            LIMIT 5";

    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
            <div class="event-container">
                <div class="event-header">
                    <div class="event-day-container">
                        <div class="event-day"><?php echo date('d/m/Y', strtotime($row['Fecha_Inicio'])); ?></div>
                        <div class="event-time"><?php echo date('H:i', strtotime($row['Hora_Inicio'])); ?></div>
                    </div>
                </div>
                <div class="event-details">
                    <h3><?php echo htmlspecialchars($row['Nombre_Evento']); ?></h3>
                    <p><?php echo htmlspecialchars($row['Descripcion_Evento']); ?></p>
                    <p>Participantes: <?php echo htmlspecialchars($row['NombresParticipantes']); ?></p>
                    <div class="event-footer">
                        <span class="department"><?php echo htmlspecialchars($row['Etiqueta']); ?></span>
                    </div>
                </div>
                <div class="event-actions">
                    <button class="action-btn edit-btn" onclick="editEvent(<?php echo $row['ID_Evento']; ?>); return false;">
                        <img src="./Img/Icons/iconos-adminAU/editar2.png" alt="Editar">
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteEvent(<?php echo $row['ID_Evento']; ?>)">
                        <img src="./Img/Icons/iconos-adminAU/borrar2.png" alt="Borrar">
                    </button>
                </div>
            </div>
        <?php
        }
    } else {
        ?>
        <div class="event-container" style="text-align: center;">
            <h3>No hay próximos eventos registrados</h3>
        </div>
    <?php
    }
    ?>

    <div class="form-actions">
        <button type="button" class="btn" id="btnCrearEvento">Crear evento</button>
    </div>
</div>

<!-- Modal para crear evento -->
<div id="modalCrearEvento" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 style="margin-bottom: 0;">Crear nuevo evento</h2>
            <span class="close">&times;</span>
        </div>
        <hr style="border: 2px solid #0071b0; width: 99%;">
        <form id="formCrearEvento">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="nombre" name="nombre" value="" required>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <input type="date" id="FechIn" name="FechIn" value="" required min="<?php echo date('Y-m-d'); ?>">
                    <input type="date" id="FechFi" name="FechFi" value="" required  min="<?php echo date('Y-m-d'); ?>">
                    <span>a las</span>
                    <input type="time" id="HorIn" name="HorIn" value="" required>
                    <span> --> </span>
                    <input type="time" id="HorFin" name="HorFi" value="" required>
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
                    <input type="time" id="HorNotif" name="HorNotif" value="" required>
                </div>
            </div>

            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <button class="boton-agregar-participantes" type="button" id="abrirModalParticipantes">Añadir participantes</button>
                    <div id="participantes-seleccionados"></div>
                    <div id="input-participantes"></div>
                </div>
                <div class="split-item">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="etiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="Programación Académica">Programación Académica</option>
                        <option value="Oferta Académica">Oferta Académica</option>
                        <option value="Administrativo">Administrativo</option>
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
                <button type="button" class="btn-cancelar" id="btnCancelarCrearEvento">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para agregar participantes -->
<div id="modalParticipantes" class="modal">
    <div class="modal-content-participantes">
        <div class="modal-header">
            <h2 style="margin-bottom: 0;">Seleccionar Participantes</h2>
            <span class="close">&times;</span>
        </div>
        <hr style="border: 2px solid #0071b0; width: 99%;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody id="listaParticipantes">
                    <!-- Los participantes se cargarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>
        <div class="button-container">
            <button class="btn-guardar" type="button" id="confirmarParticipantes">Confirmar selección</button>
        </div>    
    </div>
</div>

<script src="./JS/admin-eventos/eliminar-evento.js"></script>
<script src="./JS/admin-eventos/editar-evento.js"></script>
<script src="./JS/admin-eventos/modal-creacion-y-participantes.js"></script>
<script>
    // Función para abrir el modal
function abrirModal(titulo = 'Crear nuevo evento') {
    document.querySelector('.modal-header h2').textContent = titulo;
    document.getElementById('modalCrearEvento').style.display = 'block';
    cargarParticipantesEnModal();
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('modalCrearEvento').style.display = 'none';
    document.getElementById('formCrearEvento').reset();
    document.getElementById('participantes-seleccionados').innerHTML = '';
    document.getElementById('input-participantes').innerHTML = '';
    // Eliminar el campo oculto de ID si existe
    const idInput = document.querySelector('input[name="id_evento"]');
    if (idInput) idInput.remove();
}

let participantesSeleccionadosGlobal = [];

function editEvent(id) {
    fetch(`./functions/admin-eventos/obtener-evento.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Llenar el formulario con los datos del evento
                document.getElementById('nombre').value = data.evento.Nombre_Evento;
                document.getElementById('descripcion').value = data.evento.Descripcion_Evento;
                document.getElementById('FechIn').value = data.evento.Fecha_Inicio;
                document.getElementById('FechFi').value = data.evento.Fecha_Fin;
                document.getElementById('HorIn').value = data.evento.Hora_Inicio;
                document.getElementById('HorFin').value = data.evento.Hora_Fin;
                document.getElementById('etiqueta').value = data.evento.Etiqueta;
                document.getElementById('notificacion').value = data.evento.Notificaciones;
                document.getElementById('HorNotif').value = data.evento.Hora_Noti;

                // Manejar los participantes
                participantesSeleccionadosGlobal = data.evento.Participantes.split(',').map(p => p.trim());
                console.log("Participantes cargados:", participantesSeleccionadosGlobal);

                // Actualizar la visualización de participantes seleccionados
                actualizarParticipantesSeleccionados();

                // Agregar el ID del evento al formulario
                let idInput = document.getElementById('id_evento');
                if (!idInput) {
                    idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.id = 'id_evento';
                    idInput.name = 'id_evento';
                    document.getElementById('formCrearEvento').appendChild(idInput);
                }
                idInput.value = id;

                // Cambiar el título del modal
                document.querySelector('.modal-header h2').textContent = 'Editar evento';

                // Abrir el modal
                document.getElementById('modalCrearEvento').style.display = 'block';

                // Cargar participantes en el modal
                cargarParticipantesEnModal();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un problema al cargar los datos del evento', 'error');
        });
}

function cargarParticipantesEnModal() {
    fetch('./functions/admin-eventos/obtener-participantes.php')
        .then(response => response.json())
        .then(data => {
            const listaParticipantes = document.getElementById('listaParticipantes');
            listaParticipantes.innerHTML = '';
            data.forEach(participante => {
                const isChecked = participantesSeleccionadosGlobal.includes(participante.Codigo.toString());
                const row = `
                    <tr>
                        <td><input type="checkbox" class="checkbox-usuario" value="${participante.Codigo}" ${isChecked ? 'checked' : ''}></td>
                        <td>${participante.Nombre} ${participante.Apellido}</td>
                        <td>${participante.Correo}</td>
                        <td>${participante.Rol}</td>
                    </tr>
                `;
                listaParticipantes.innerHTML += row;
            });

            // Añadir event listeners a los nuevos checkboxes
            document.querySelectorAll('.checkbox-usuario').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        if (!participantesSeleccionadosGlobal.includes(this.value)) {
                            participantesSeleccionadosGlobal.push(this.value);
                        }
                    } else {
                        const index = participantesSeleccionadosGlobal.indexOf(this.value);
                        if (index > -1) {
                            participantesSeleccionadosGlobal.splice(index, 1);
                        }
                    }
                    actualizarParticipantesSeleccionados();
                });
            });

            // Actualizar la visualización de participantes seleccionados
            actualizarParticipantesSeleccionados();
        })
        .catch(error => console.error('Error:', error));
}

function actualizarParticipantesSeleccionados() {
    const participantesSeleccionados = document.getElementById('participantes-seleccionados');
    const inputParticipantes = document.getElementById('input-participantes');
    participantesSeleccionados.innerHTML = '';
    inputParticipantes.innerHTML = '';

    participantesSeleccionadosGlobal.forEach(participanteId => {
        const checkbox = document.querySelector(`.checkbox-usuario[value="${participanteId}"]`);
        if (checkbox) {
            const nombre = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent;
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
            inputOculto.value = participanteId;
            inputParticipantes.appendChild(inputOculto);

            participanteDiv.querySelector('.eliminar').addEventListener('click', function() {
                participanteDiv.remove();
                inputOculto.remove();
                checkbox.checked = false;
                const index = participantesSeleccionadosGlobal.indexOf(participanteId);
                if (index > -1) {
                    participantesSeleccionadosGlobal.splice(index, 1);
                }
            });
        }
    });
}

// Agregar este event listener para el botón de confirmar participantes
document.getElementById('confirmarParticipantes').addEventListener('click', function() {
    actualizarParticipantesSeleccionados();
    document.getElementById('modalParticipantes').style.display = 'none';
});

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Abrir modal para crear evento
    document.getElementById('btnCrearEvento').addEventListener('click', function() {
        abrirModal();
    });

    // Cerrar modal
    document.querySelector('.close').addEventListener('click', cerrarModal);

    // Manejar envío del formulario
    document.getElementById('formCrearEvento').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const isEditing = formData.has('id_evento');

        Swal.fire({
            title: '¿Estás seguro?',
            text: isEditing ? "¿Deseas guardar los cambios en este evento?" : "¿Deseas crear este evento?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('guardar_evento.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire(
                            '¡Guardado!',
                            data.message,
                            'success'
                        ).then(() => {
                            window.location.reload();
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

    // Manejar selección de participantes
    document.getElementById('abrirModalParticipantes').addEventListener('click', function() {
        document.getElementById('modalParticipantes').style.display = 'block';
    });

    document.getElementById('confirmarParticipantes').addEventListener('click', function() {
        actualizarParticipantesSeleccionados();
        document.getElementById('modalParticipantes').style.display = 'none';
    });

    // Cerrar modal de participantes
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Cerrar modales al hacer clic fuera de ellos
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
});
</script>

<?php include './template/footer.php' ?>
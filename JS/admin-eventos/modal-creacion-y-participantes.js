document.addEventListener('DOMContentLoaded', function() {
    const modalCrearEvento = document.getElementById('modalCrearEvento');
    const modalParticipantes = document.getElementById('modalParticipantes');
    const btnCrearEvento = document.getElementById('btnCrearEvento');
    const btnCancelarCrearEvento = document.getElementById('btnCancelarCrearEvento');
    const btnAbrirModalParticipantes = document.getElementById('abrirModalParticipantes');
    const btnAbrirModalParticipantesEdicion = document.getElementById('abrirModalParticipantesEdicion');
    const btnConfirmarParticipantes = document.getElementById('confirmarParticipantes');
    const spans = document.getElementsByClassName('close');
    const formCrearEvento = document.getElementById('formCrearEvento');
    const listaParticipantes = document.getElementById('listaParticipantes');

    const modalEditarEvento = document.getElementById('modalEditarEvento');
    const formEditarEvento = document.getElementById('formEditarEvento');
    const btnCancelarEditarEvento = document.getElementById('btnCancelarEditarEvento');

    btnCrearEvento.onclick = function() {
        modalCrearEvento.style.display = 'block';
    }

    btnCancelarCrearEvento.onclick = function() {
        modalCrearEvento.style.display = 'none';
    }

     // Modificar el evento de abrir modal
     btnAbrirModalParticipantes.onclick = function(e) {
        e.preventDefault();
        cargarParticipantes();
        modalParticipantes.style.display = 'block';
    }

    if (btnAbrirModalParticipantes) {
        btnAbrirModalParticipantes.onclick = function(e) {
            e.preventDefault();
            cargarParticipantes();
            modalParticipantes.style.display = 'block';
        }
    }

    if (btnAbrirModalParticipantesEdicion) {
        btnAbrirModalParticipantesEdicion.onclick = function(e) {
            e.preventDefault();
            cargarParticipantes();
            modalParticipantes.style.display = 'block';
        }
    }

    // Función para restaurar participantes al abrir el modal
    function restaurarParticipantesSeleccionados() {
        const inputsOcultos = document.querySelectorAll('#input-participantes input[type="hidden"]');
        inputsOcultos.forEach(input => {
            participantesSeleccionados.add(input.value);
        });
    }

    // Función para abrir el modal de edición
    function editEvent(eventId) {
        console.log('Editando evento con ID:', eventId);
        fetch(`./functions/admin-eventos/obtener-evento.php?id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos del evento recibidos:', data);
                if (data.error) {
                    console.error('Error al obtener el evento:', data.error);
                    return;
                }
                
                // Función auxiliar para establecer el valor de forma segura
                const setValueSafely = (id, value) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.value = value || '';
                    } else {
                        console.warn(`Elemento con ID '${id}' no encontrado`);
                    }
                };
    
                // Rellenar el formulario con los datos del evento
                setValueSafely('editEventId', data.ID_Evento);
                setValueSafely('editNombre', data.Nombre_Evento);
                setValueSafely('editDescripcion', data.Descripcion_Evento);
                setValueSafely('editFechIn', data.Fecha_Inicio);
                setValueSafely('editFechFi', data.Fecha_Fin);
                setValueSafely('editHorIn', data.Hora_Inicio);
                setValueSafely('editHorFin', data.Hora_Fin);
                setValueSafely('editEtiqueta', data.Etiqueta);
                setValueSafely('editNotificacion', data.Notificaciones);
                setValueSafely('editHorNotif', data.Hora_Noti);
                setValueSafely('editDescripcion', data.Descripcion_Evento);

                // Cargar participantes
                cargarParticipantesEdicion(data.Participantes);


                // Llamar a una nueva función para manejar los participantes
                mostrarParticipantesEdicion(data.Participantes);
    
                // Mostrar el modal
                const modalEditarEvento = document.getElementById('modalEditarEvento');
                if (modalEditarEvento) {
                    modalEditarEvento.style.display = 'block';
                } else {
                    console.error('Modal de edición no encontrado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al cargar los datos del evento.', 'error');
            });
    }

    function mostrarParticipantesEdicion(participantes) {
        console.log('Mostrando participantes:', participantes);
        participantesSeleccionados.clear();
        
        if (participantes && typeof participantes === 'string' && participantes.trim() !== '') {
            const participantesArray = participantes.split(',');
            participantesArray.forEach(codigo => {
                if (codigo.trim() !== '') {
                    participantesSeleccionados.add(codigo.trim());
                }
            });
        }
        
        console.log('Participantes seleccionados:', Array.from(participantesSeleccionados));
    
        // Cargar los datos de los participantes
        fetch('./functions/admin-eventos/obtener-participantes.php')
            .then(response => response.json())
            .then(data => {
                console.log('Datos de todos los participantes:', data);
                participantesData = {}; // Reiniciar el objeto
                data.forEach(usuario => {
                    participantesData[usuario.Codigo] = usuario;
                });
                
                console.log('Datos de participantes procesados:', participantesData);
                
                // Actualizar las tarjetas de participantes
                actualizarParticipantesEdicion();
            })
            .catch(error => console.error('Error al cargar participantes:', error));
    }

    function cargarParticipantesEdicion(participantes) {
        participantesSeleccionados.clear();
        
        if (participantes && typeof participantes === 'string' && participantes.trim() !== '') {
            const participantesArray = participantes.split(',');
            participantesArray.forEach(codigo => {
                if (codigo.trim() !== '') {
                    participantesSeleccionados.add(codigo.trim());
                }
            });
        }
    
        // Cargar los datos de los participantes
        fetch('./functions/admin-eventos/obtener-participantes.php')
            .then(response => response.json())
            .then(data => {
                participantesData = {}; // Reiniciar el objeto
                data.forEach(usuario => {
                    participantesData[usuario.Codigo] = usuario;
                });
                
                // Actualizar las tarjetas de participantes
                actualizarParticipantesSeleccionados();
                
                // Marcar los checkboxes correspondientes en el modal de participantes
                const checkboxes = document.querySelectorAll('.checkbox-usuario');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = participantesSeleccionados.has(checkbox.value);
                });
            })
            .catch(error => console.error('Error al cargar participantes:', error));
    }

    formEditarEvento.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(formEditarEvento);

         // Agregar participantes al formData
        participantesSeleccionados.forEach(codigo => {
            formData.append('participantes[]', codigo);
        });
        
        console.log('Datos a enviar:', Object.fromEntries(formData)); // Para depuración
    
        fetch('./functions/admin-eventos/guardar-evento.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data); // Para depuración
            if (data.status === 'success') {
                Swal.fire(
                    '¡Actualizado!',
                    'El evento ha sido actualizado.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire(
                    'Error',
                    'Hubo un problema al actualizar el evento: ' + data.message,
                    'error'
                );
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            Swal.fire(
                'Error',
                'Hubo un problema al procesar la respuesta del servidor.',
                'error'
            );
        });
    }

    btnCancelarEditarEvento.onclick = function() {
        modalEditarEvento.style.display = 'none';
    }

    // Asignar la función editEvent a los botones de edición
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = function() {
            const eventId = this.getAttribute('data-event-id');
            console.log('Editando evento con ID:', eventId); // Para depuración
            editEvent(eventId);
        }
    });

    // Llamar a restaurarParticipantesSeleccionados cuando se carga la página
    restaurarParticipantesSeleccionados();

    for (let span of spans) {
        span.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    }

    // Modificar el cierre del modal al hacer clic fuera
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            if (event.target.id === 'modalParticipantes') {
                // Restaurar checkboxes según los participantes actuales
                const checkboxes = document.querySelectorAll('.checkbox-usuario');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = participantesSeleccionados.has(checkbox.value);
                });
            }
            event.target.style.display = 'none';
        }
    }

    // Agregar manejador para el botón cancelar del modal de participantes
    const closeButtons = document.querySelectorAll('.modal .close');
    closeButtons.forEach(button => {
        button.onclick = function() {
            const modal = this.closest('.modal');
            if (modal.id === 'modalParticipantes') {
                // Restaurar checkboxes según los participantes actuales
                const checkboxes = document.querySelectorAll('.checkbox-usuario');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = participantesSeleccionados.has(checkbox.value);
                });
            }
            modal.style.display = 'none';
        }
    });
    

     // Variable global para mantener los participantes seleccionados
     let participantesSeleccionados = new Set();

     function cargarParticipantes() {
        fetch('./functions/admin-eventos/obtener-participantes.php')
            .then(response => response.json())
            .then(data => {
                participantesData = {}; // Reiniciar el objeto
                listaParticipantes.innerHTML = data.map(usuario => {
                    participantesData[usuario.Codigo] = usuario; // Almacenar datos del participante
                    const isChecked = participantesSeleccionados.has(usuario.Codigo) ? 'checked' : '';
                    return `
                        <tr>
                            <td><input type="checkbox" name="participantes[]" value="${usuario.Codigo}" 
                                class="checkbox-usuario" ${isChecked}></td>
                            <td>${usuario.Nombre} ${usuario.Apellido}</td>
                            <td>${usuario.Correo}</td>
                            <td>${usuario.Nombre_Rol}</td>
                        </tr>
                    `;
                }).join('');
            })
            .catch(error => console.error('Error al cargar participantes:', error));
    }

    let participantesData = {};

    function actualizarParticipantesSeleccionados() {
        console.log('Actualizando participantes seleccionados');
        actualizarTarjetasParticipantes('participantes-seleccionados', 'input-participantes', Array.from(participantesSeleccionados));
    }

    function actualizarParticipantesEdicion() {
        console.log('Actualizando participantes en edición');
        actualizarTarjetasParticipantes('participantes-seleccionados-edicion', 'input-participantes-edicion', Array.from(participantesSeleccionados));
    }

    function actualizarTarjetasParticipantes(containerId, inputContainerId, participantes) {
        const participantesContainer = document.getElementById(containerId);
        const inputContainer = document.getElementById(inputContainerId);
        
        if (!participantesContainer || !inputContainer) {
            console.error('No se encontraron los elementos necesarios para mostrar los participantes');
            return;
        }
    
        // Limpiar contenedores existentes
        participantesContainer.innerHTML = '';
        inputContainer.innerHTML = '';
        
        participantes.forEach(codigo => {
            const participante = participantesData[codigo];
            if (participante) {
                const nombre = `${participante.Nombre} ${participante.Apellido}`;
                
                // Crear tarjeta visual
                const participanteDiv = document.createElement('div');
                participanteDiv.className = 'participante-tarjeta';
                participanteDiv.innerHTML = `
                    <span class="nombre">${nombre}</span>
                    <span class="eliminar" title="Eliminar">&times;</span>
                `;
                participantesContainer.appendChild(participanteDiv);
                
                // Crear input oculto
                const inputOculto = document.createElement('input');
                inputOculto.type = 'hidden';
                inputOculto.name = 'participantes[]';
                inputOculto.value = codigo;
                inputContainer.appendChild(inputOculto);
    
                // Manejador para eliminar participante
                participanteDiv.querySelector('.eliminar').addEventListener('click', function() {
                    participantesSeleccionados.delete(codigo);
                    participanteDiv.remove();
                    inputOculto.remove();
                });
            }
        });
    }

    // Modificar el evento de confirmar selección
    btnConfirmarParticipantes.onclick = function() {
        participantesSeleccionados.clear();
        document.querySelectorAll('.checkbox-usuario:checked').forEach(checkbox => {
            participantesSeleccionados.add(checkbox.value);
        });
        actualizarParticipantesSeleccionados();
        modalParticipantes.style.display = 'none';
    }

    document.getElementById('confirmarParticipantes').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.checkbox-usuario:checked');
        checkboxes.forEach(checkbox => {
            participantesSeleccionados.add(checkbox.value);
        });
        actualizarParticipantesEdicion();
        modalParticipantes.style.display = 'none';
    });

    formCrearEvento.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(formCrearEvento);
        
        // Verificar si hay participantes seleccionados
        const participantes = formData.getAll('participantes[]');
        console.log('Participantes seleccionados:', participantes); // Para depuración
    
        fetch('./functions/admin-eventos/guardar-evento.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data); // Para depuración
            if (data.status === 'success') {
                Swal.fire(
                    '¡Guardado!',
                    'El evento ha sido guardado.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire(
                    'Error',
                    'Hubo un problema al guardar el evento: ' + data.message,
                    'error'
                );
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            Swal.fire(
                'Error',
                'Hubo un problema al procesar la respuesta del servidor.',
                'error'
            );
        });
    }
});
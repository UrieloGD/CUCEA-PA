document.addEventListener('DOMContentLoaded', function() {
    const modalCrearEvento = document.getElementById('modalCrearEvento');
    const modalParticipantes = document.getElementById('modalParticipantes');
    const btnCrearEvento = document.getElementById('btnCrearEvento');
    const btnCancelarCrearEvento = document.getElementById('btnCancelarCrearEvento');
    const btnAbrirModalParticipantes = document.getElementById('abrirModalParticipantes');
    const btnConfirmarParticipantes = document.getElementById('confirmarParticipantes');
    const spans = document.getElementsByClassName('close');
    const formCrearEvento = document.getElementById('formCrearEvento');
    const listaParticipantes = document.getElementById('listaParticipantes');

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

    // Modificar el evento de confirmar selección
    btnConfirmarParticipantes.onclick = function() {
        actualizarParticipantesSeleccionados();
        modalParticipantes.style.display = 'none';
    }

    // Función para restaurar participantes al abrir el modal
    function restaurarParticipantesSeleccionados() {
        const inputsOcultos = document.querySelectorAll('#input-participantes input[type="hidden"]');
        inputsOcultos.forEach(input => {
            participantesSeleccionados.add(input.value);
        });
    }

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
                 listaParticipantes.innerHTML = data.map(usuario => {
                     // Verifica si el usuario ya estaba seleccionado
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
             .catch(error => console.error('Error:', error));
     }

     function actualizarParticipantesSeleccionados() {
        const participantesSeleccionadosDiv = document.getElementById('participantes-seleccionados');
        const inputParticipantes = document.getElementById('input-participantes');
        participantesSeleccionadosDiv.innerHTML = '';
        inputParticipantes.innerHTML = '';
        
        // Limpiar el Set antes de actualizarlo
        participantesSeleccionados.clear();
        
        const checkboxes = document.querySelectorAll('.checkbox-usuario:checked');
        checkboxes.forEach(checkbox => {
            const codigo = checkbox.value;
            const nombre = checkbox.parentElement.nextElementSibling.textContent;
            
            // Agregar al Set de participantes seleccionados
            participantesSeleccionados.add(codigo);
            
            // Crear tarjeta visual
            const participanteDiv = document.createElement('div');
            participanteDiv.className = 'participante-tarjeta';
            participanteDiv.innerHTML = `
                <span class="nombre">${nombre}</span>
                <span class="eliminar" title="Eliminar">&times;</span>
            `;
            participantesSeleccionadosDiv.appendChild(participanteDiv);
            
            // Crear input oculto
            const inputOculto = document.createElement('input');
            inputOculto.type = 'hidden';
            inputOculto.name = 'participantes[]';
            inputOculto.value = codigo;
            inputParticipantes.appendChild(inputOculto);

            // Manejador para eliminar participante
            participanteDiv.querySelector('.eliminar').addEventListener('click', function() {
                participantesSeleccionados.delete(codigo);
                participanteDiv.remove();
                inputOculto.remove();
                
                // Actualizar el checkbox en el modal si está abierto
                const checkbox = document.querySelector(`.checkbox-usuario[value="${codigo}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                }
            });
        });
    }

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
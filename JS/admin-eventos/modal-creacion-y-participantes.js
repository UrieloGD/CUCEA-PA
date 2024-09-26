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

    btnAbrirModalParticipantes.onclick = function(e) {
        e.preventDefault();
        cargarParticipantes();
        modalParticipantes.style.display = 'block';
    }

    btnConfirmarParticipantes.onclick = function() {
        actualizarParticipantesSeleccionados();
        modalParticipantes.style.display = 'none';
    }

    for (let span of spans) {
        span.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    function cargarParticipantes() {
        fetch('./functions/admin-eventos/obtener-participantes.php')
            .then(response => response.json())
            .then(data => {
                listaParticipantes.innerHTML = data.map(usuario => `
                    <tr>
                        <td><input type="checkbox" name="participantes[]" value="${usuario.Codigo}" class="checkbox-usuario"></td>
                        <td>${usuario.Nombre} ${usuario.Apellido}</td>
                        <td>${usuario.Correo}</td>
                        <td>${usuario.Nombre_Rol}</td>
                    </tr>
                `).join('');
            })
            .catch(error => console.error('Error:', error));
    }

    function actualizarParticipantesSeleccionados() {
        const participantesSeleccionados = document.getElementById('participantes-seleccionados');
        const inputParticipantes = document.getElementById('input-participantes');
        participantesSeleccionados.innerHTML = '';
        inputParticipantes.innerHTML = '';
        const checkboxes = document.querySelectorAll('.checkbox-usuario:checked');
        checkboxes.forEach(checkbox => {
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
        });
    }

    formCrearEvento.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(formCrearEvento);

        fetch('./functions/admin-eventos/guardar-evento.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
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
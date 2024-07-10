const modal = document.getElementById('modal');
    const abrirModalBtn = document.getElementById('abrirModal');
    const cerrarModalBtn = document.querySelector('.close');
    const confirmarParticipantesBtn = document.getElementById('confirmarParticipantes');
    const participantesSeleccionados = document.getElementById('participantes-seleccionados');
    const inputParticipantes = document.getElementById('input-participantes');

    abrirModalBtn.onclick = function(e) {
        e.preventDefault();
        modal.style.display = "block";
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

            // Añadir funcionalidad para eliminar el participante
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
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

    function ajustarTamañoModal() {
    const modalContent = document.querySelector('.modal-content');
    const tabla = modalContent.querySelector('table');
    const boton = modalContent.querySelector('#confirmarParticipantes');
    
    // Resetear la altura máxima
    modalContent.style.maxHeight = '80vh';
    
    // Calcular la altura del contenido
    const alturaContenido = tabla.offsetHeight + boton.offsetHeight + 60; // 60px para el padding
    
    // Si el contenido es menor que el 80% de la altura de la ventana, ajustar la altura del modal
    if (alturaContenido < window.innerHeight * 0.8) {
        modalContent.style.maxHeight = `${alturaContenido}px`;
    }
}

// Llamar a esta función cuando se abra el modal
abrirModalBtn.onclick = function(e) {
    e.preventDefault();
    modal.style.display = "flex";
    ajustarTamañoModal();
}

// También podrías querer llamarla si la ventana cambia de tamaño
window.addEventListener('resize', ajustarTamañoModal);
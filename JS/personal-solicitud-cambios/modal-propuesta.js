document.addEventListener('DOMContentLoaded', function() {
    const modalPropuesta = document.getElementById('solicitud-modal-propuesta-academica');
    const formPropuesta = document.getElementById('form-propuesta');

    // Configuración inicial del modal
    if (modalPropuesta) {
        modalPropuesta.style.display = 'none';
    }

    // Manejadores para cerrar el modal
    const closeButton = modalPropuesta?.querySelector('.close-button');
    const btnDescartar = modalPropuesta?.querySelector('#btn-descartar-propuesta');

    const cerrarModal = () => {
        if (modalPropuesta) {
            modalPropuesta.style.display = 'none';
            formPropuesta?.reset();
        }
    };

    // Event listeners para cerrar el modal
    if (closeButton) closeButton.addEventListener('click', cerrarModal);
    if (btnDescartar) btnDescartar.addEventListener('click', cerrarModal);

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (e) => {
        if (e.target === modalPropuesta) cerrarModal();
    });

    // Validaciones globales
    const setupValidations = () => {
        // Validación para campos de texto con caracteres especiales
        document.querySelectorAll('.texto-especial').forEach(input => {
            input.addEventListener('input', (e) => {
                // Permite letras, espacios, acentos y caracteres especiales comunes en nombres
                if (e.target.value) {
                    e.target.value = e.target.value.normalize("NFD").replace(/[^\w\s\u0300-\u036f]/g, "").normalize("NFC");
                }
            });
        });

        // Validación para campos numéricos
        document.querySelectorAll('input[type="number"], input[pattern*="[0-9]"]').forEach(input => {
            input.addEventListener('input', (e) => {
                if (e.target.type === 'number') {
                    let value = parseInt(e.target.value);
                    const max = e.target.hasAttribute('max') ? parseInt(e.target.getAttribute('max')) : 99999;
                    const min = e.target.hasAttribute('min') ? parseInt(e.target.getAttribute('min')) : 0;
                    
                    if (isNaN(value) || value < min) value = min;
                    if (value > max) value = max;
                    e.target.value = value;
                } else {
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                }
            });
        });

        // Validación de fechas
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.addEventListener('input', (e) => {
                const selectedDate = new Date(e.target.value);
                const today = new Date();
                const maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());
                
                if (selectedDate > maxDate) {
                    e.target.value = maxDate.toISOString().split('T')[0];
                }
            });
        });
    };

    setupValidations();

    // Manejo del formulario
    if (formPropuesta) {
        formPropuesta.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor, complete todos los campos requeridos correctamente',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const formData = new FormData(this);

            fetch('./functions/personal-solicitud-cambios/procesar_propuesta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Propuesta guardada exitosamente',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cerrarModal();
                                location.reload();
                            }
                        });
                    } else {
                        throw new Error(data.message);
                    }
                } catch (e) {
                    throw new Error(`Error al procesar la respuesta: ${text}`);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});
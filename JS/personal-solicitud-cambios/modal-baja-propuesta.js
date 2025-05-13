// modal-baja-propuesta.js
document.addEventListener('DOMContentLoaded', function() {
    const modalBajaPropuesta = document.getElementById('solicitud-modal-baja-propuesta');
    const formBajaPropuesta = document.getElementById('form-baja-propuesta');
    const closeButton = modalBajaPropuesta?.querySelector('.close-button');
    const btnDescartar = modalBajaPropuesta?.querySelector('#btn-descartar-baja-propuesta');

    // Configuración inicial del modal
    if (modalBajaPropuesta) {
        modalBajaPropuesta.style.display = 'none';
    }

    const cerrarModal = () => {
        if (modalBajaPropuesta) {
            modalBajaPropuesta.style.display = 'none';
            formBajaPropuesta?.reset();
        }
    };

    // Event listeners para cerrar el modal
    if (closeButton) {
        closeButton.addEventListener('click', () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se descartarán todos los cambios realizados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0071b0',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    cerrarModal();
                }
            });
        });
    }

    if (btnDescartar) {
        btnDescartar.addEventListener('click', () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se descartarán todos los cambios realizados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0071b0',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    cerrarModal();
                }
            });
        });
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (e) => {
        if (e.target === modalBajaPropuesta) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se descartarán todos los cambios realizados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0071b0',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    cerrarModal();
                }
            });
        }
    });

    // Validaciones de campos
    const setupValidations = () => {
        // Validación para campos de texto con caracteres especiales
        document.querySelectorAll('.text-especial').forEach(input => {
            input.addEventListener('input', (e) => {
                if (e.target.value) {
                    e.target.value = e.target.value
                        .normalize("NFD")
                        .replace(/[^\w\s\u0300-\u036f]/g, "")
                        .normalize("NFC");
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
    if (formBajaPropuesta) {
        formBajaPropuesta.addEventListener('submit', function(e) {
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

            fetch('./functions/personal-solicitud-cambios/procesar_baja_propuesta.php', {
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
                            text: 'Solicitud de baja-propuesta guardada exitosamente',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cerrarModal();
                                location.reload();
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Error al procesar la solicitud');
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

        // Validación de fechas relacionadas
        const fechaInicio = document.getElementById('periodo_desde_prop');
        const fechaFin = document.getElementById('periodo_hasta_prop');
        const fechaSinEfectos = document.getElementById('sin_efectos_baja');

        if (fechaInicio && fechaFin) {
            fechaInicio.addEventListener('change', () => {
                fechaFin.min = fechaInicio.value;
                if (fechaFin.value && fechaFin.value < fechaInicio.value) {
                    fechaFin.value = fechaInicio.value;
                }
            });

            fechaFin.addEventListener('change', () => {
                fechaInicio.max = fechaFin.value;
                if (fechaInicio.value && fechaInicio.value > fechaFin.value) {
                    fechaInicio.value = fechaFin.value;
                }
            });
        }

        if (fechaSinEfectos) {
            const today = new Date();
            fechaSinEfectos.min = today.toISOString().split('T')[0];
        }
    }

    // Autocompletar campos relacionados
    const setupAutoComplete = () => {
        const codigoProfBaja = document.getElementById('codigo_prof_baja');
        
        if (codigoProfBaja) {
            codigoProfBaja.addEventListener('blur', function() {
                // Aquí podrías agregar una llamada AJAX para obtener los datos del profesor
                // y autocompletar otros campos relacionados
            });
        }
    };

    setupAutoComplete();

    // Generar número de oficio automáticamente
    const setupOficioNum = () => {
        const oficioNum = document.getElementById('oficio_num_baja_prop');
        if (oficioNum) {
            // Aquí podrías agregar la lógica para generar el número de oficio
            // Por ejemplo, un contador automático o una llamada al servidor
        }
    };

    setupOficioNum();
});
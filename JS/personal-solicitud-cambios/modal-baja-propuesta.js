// modal-baja-propuesta.js
document.addEventListener('DOMContentLoaded', function() {
    const modalBajaPropuesta = document.getElementById('solicitud-modal-baja-propuesta');
    const formBajaPropuesta = document.getElementById('form-baja-propuesta');
    const closeButton = modalBajaPropuesta?.querySelector('.close-button-baja-propuesta');
    const btnDescartar = modalBajaPropuesta?.querySelector('#btn-descartar-baja-propuesta');
    let formData = new FormData();

    // Función para mayúsculas con acentos (igual que en modal-baja.js)
    const toUpperWithAccents = (str) => {
        return str.normalize('NFD')
            .toUpperCase()
            .replace(/¡/g, '¿') // Mantener símbolos en español
            .replace(/!/g, '?');
    };

    // Configuración inicial del modal
    if (modalBajaPropuesta) {
        modalBajaPropuesta.style.display = 'none';
    }

    // Guardar/Restaurar datos del formulario
    const guardarDatosFormulario = () => formData = new FormData(formBajaPropuesta);
    
    const restaurarDatosFormulario = () => {
        formData.forEach((valor, clave) => {
            const input = formBajaPropuesta.elements[clave];
            input && (input.value = valor);
        });
    };

    const cerrarModal = () => {
        if (modalBajaPropuesta) {
            modalBajaPropuesta.style.display = 'none';
            formBajaPropuesta?.reset();
            formData = new FormData();
        }
    };

    // Event listeners para cerrar el modal
    if (closeButton) {
        closeButton.addEventListener('click', () => {
            guardarDatosFormulario();
            modalBajaPropuesta.style.display = 'none';
        });
    }

    if (btnDescartar) {
        btnDescartar.addEventListener('click', () => {
            Swal.fire({
                title: '¿Descartar cambios?',
                text: "Se perderán todos los datos ingresados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
            guardarDatosFormulario();
            modalBajaPropuesta.style.display = 'none';
        }
    });

    // Convertir a mayúsculas con acentos al escribir (como en modal-baja.js)
    document.querySelectorAll('#form-baja-propuesta input[type="text"]').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = toUpperWithAccents(this.value);
        });
    });

    // Configuración detallada de validaciones de campos
    const setupValidations = () => {
        // Límites máximos para inputs (similar a modal-baja.js)
        const maxLengths = {
            'oficio_num_baja_prop': 15,
            'nombres_baja': 60, 'apellido_paterno_baja': 40, 'apellido_materno_baja': 40,
            'nombres_prop': 60, 'apellido_paterno_prop': 40, 'apellido_materno_prop': 40,
            'codigo_prof_baja': 10, 'codigo_prof_prop': 10,
            'nombre_materia_baja': 100,
            'carrera_baja': 50,
            'cve_materia_baja': 5,
            'tipo_asignacion_baja': 10, 'tipo_asignacion_prop': 10,
            'gdo_gpo_turno_baja': 20,
            'crn_baja': 7
        };

        // Aplicar límites
        Object.keys(maxLengths).forEach(field => {
            const input = document.getElementById(field);
            input && input.setAttribute('maxlength', maxLengths[field]);
        });
        
        // 1. Campos numéricos con longitud específica
        const camposNumericos = [
            {id: 'codigo_prof_baja', maxLength: 8},
            {id: 'codigo_prof_prop', maxLength: 8},
            {id: 'num_puesto_teoria_baja', maxLength: 8},
            {id: 'num_puesto_practica_baja', maxLength: 8},
            {id: 'num_puesto_teoria_prop', maxLength: 8},
            {id: 'num_puesto_practica_prop', maxLength: 8},
            {id: 'crn_baja', maxLength: 7}
        ];
        
        camposNumericos.forEach(campo => {
            const input = document.getElementById(campo.id);
            if (input) {
                input.addEventListener('input', (e) => {
                    // Solo permitir dígitos
                    e.target.value = e.target.value.replace(/\D/g, '');
                    // Controlar longitud máxima
                    if (e.target.value.length > campo.maxLength) {
                        e.target.value = e.target.value.slice(0, campo.maxLength);
                    }
                });
            }
        });

        const camposAlfabeticos = [
            'nombres_baja', 'apellido_paterno_baja', 'apellido_materno_baja',
            'nombre_materia_baja',
            'nombres_prop', 'apellido_paterno_prop', 'apellido_materno_prop'
        ];

        camposAlfabeticos.forEach(campo => {
            const input = document.getElementById(campo);
            if (input) {
                input.addEventListener('input', (e) => {
                    // Primero aplicamos toUpperWithAccents para mantener la conversión a mayúsculas
                    let valor = toUpperWithAccents(e.target.value);
                    
                    // Luego quitamos solo los números y algunos símbolos no deseados
                    // Esto preserva letras, espacios, acentos, ñ, etc.
                    valor = valor.replace(/[0-9]/g, '');
                    
                    e.target.value = valor;
                });
            }
        });

        const camposEstrictamenteNumericos = [
            'codigo_prof_baja', 'codigo_prof_prop', 'crn_baja'
        ];
        
        camposEstrictamenteNumericos.forEach(campo => {
            const input = document.getElementById(campo);
            if (input) {
                input.addEventListener('input', (e) => {
                    // Solo permitir dígitos
                    e.target.value = e.target.value.replace(/\D/g, '');
                    
                    // Aplicar la longitud máxima correspondiente
                    const maxLength = campo.includes('codigo') ? 8 : 7; // 8 para códigos, 7 para CRN
                    if (e.target.value.length > maxLength) {
                        e.target.value = e.target.value.slice(0, maxLength);
                    }
                });
            }
        });
        
        // 2. Campos de horas con validación numérica y rango
        const camposHoras = ['hrs_teoria_baja', 'hrs_practica_baja', 'hrs_teoria_prop', 'hrs_practica_prop'];
        
        camposHoras.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', (e) => {
                    // Validar que sea número
                    let value = e.target.value.replace(/\D/g, '');
                    
                    // Controlar máximo de 9999
                    if (value > 9999) value = '9999';
                    
                    e.target.value = value;
                });
            }
        });
        
        // 3. Validación de fechas
        const validarFechas = () => {
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
        };

        const motivoBaja = document.getElementById('motivo_baja');
        if (motivoBaja) {
            motivoBaja.addEventListener('input', (e) => {
                // Permitir letras, números, espacios y algunos caracteres especiales
                e.target.value = toUpperWithAccents(e.target.value);
                
                // Limitar a 50 caracteres
                if (e.target.value.length > 50) {
                    e.target.value = e.target.value.slice(0, 50);
                }
            });
        }
        
        validarFechas();
    };

    setupValidations();

    // Observer para detectar apertura del modal (como en modal-baja.js)
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && 
                modalBajaPropuesta.style.display === 'block') {
                // Actualizar número de oficio cuando se abre el modal
                actualizarNumeroOficio();
                bloquearCampoOficio(); // Agregar esta línea
                restaurarDatosFormulario();
            }
        });
    });

    observer.observe(modalBajaPropuesta, { attributes: true, attributeFilter: ['style'] });

    // Validar todo el formulario antes de enviar
    if (formBajaPropuesta) {
        formBajaPropuesta.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Verificar campos obligatorios personalizados
            let camposInvalidos = [];
            
            // Verificar campos numéricos
            document.querySelectorAll('input[pattern="[0-9]*"]').forEach(input => {
                if (input.required && (!input.value || isNaN(parseInt(input.value)))) {
                    camposInvalidos.push(input.previousElementSibling.textContent);
                }
            });
            
            // Verificar campos de fecha
            document.querySelectorAll('input[type="date"]').forEach(input => {
                if (input.required && !input.value) {
                    camposInvalidos.push(input.previousElementSibling.textContent);
                }
            });
            
            if (camposInvalidos.length > 0) {
                Swal.fire({
                    title: 'Error',
                    html: `Por favor, complete correctamente los siguientes campos:<br>${camposInvalidos.join('<br>')}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Si todos los campos son válidos, mostrar indicador de carga
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            // Continuar con el envío
            const formData = new FormData(this);

            fetch('./functions/personal-solicitud-cambios/procesar/procesar_baja_propuesta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.ok ? response.json() : Promise.reject(response))
            .then(data => {
                if (data.success) {
                    modalBajaPropuesta.style.display = 'none';
                    formBajaPropuesta.reset();
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Solicitud de baja-propuesta guardada exitosamente',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Error al procesar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Error en la comunicación',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
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

    // Función para asegurar que el campo oficio permanezca bloqueado
    const bloquearCampoOficio = () => {
        const oficioNum = document.getElementById('oficio_num_baja_prop');
        if (oficioNum) {
            // Hacer el campo readonly y disabled
            oficioNum.setAttribute('readonly', true);
            oficioNum.setAttribute('disabled', true);
            
            // Prevenir cualquier intento de modificación
            oficioNum.addEventListener('keydown', (e) => {
                e.preventDefault();
                return false;
            });
            
            oficioNum.addEventListener('keypress', (e) => {
                e.preventDefault();
                return false;
            });
            
            oficioNum.addEventListener('input', (e) => {
                e.preventDefault();
                return false;
            });
            
            oficioNum.addEventListener('paste', (e) => {
                e.preventDefault();
                return false;
            });
            
            // Prevenir cambios por JavaScript externo
            oficioNum.addEventListener('focus', () => {
                oficioNum.blur();
            });
        }
    };

    // Generar número de oficio automáticamente (versión mejorada)
    const setupOficioNum = () => {
        bloquearCampoOficio(); // Aplicar bloqueo inmediatamente
    };

    const actualizarNumeroOficio = () => {
        const oficioNum = document.getElementById('oficio_num_baja_prop');
        if (oficioNum) {
            // Mostrar un indicador de carga o texto temporal
            oficioNum.value = "Generando...";
            
            // Solicitar el próximo número de folio al servidor
            fetch('./functions/personal-solicitud-cambios/oficios/obtener_oficio_baja_prop.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        oficioNum.value = data.folio;
                    } else {
                        oficioNum.value = "";
                        console.error('Error al obtener el folio:', data.message);
                    }
                    // Asegurar que el campo permanezca bloqueado después de actualizar
                    bloquearCampoOficio();
                })
                .catch(error => {
                    oficioNum.value = "";
                    console.error('Error de comunicación:', error);
                    // Asegurar que el campo permanezca bloqueado incluso si hay error
                    bloquearCampoOficio();
                });
        }
    };

    setupOficioNum();
});
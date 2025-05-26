// ./JS/personal-solicitud-cambios/modal-baja.js
document.addEventListener('DOMContentLoaded', function() {
    const formBaja = document.getElementById('form-baja');
    const modalBaja = document.getElementById('solicitud-modal-baja-academica');
    let formData = new FormData();
    
    // Función para mayúsculas con acentos
    const toUpperWithAccents = (str) => {
        return str.normalize('NFD')
            .toUpperCase()
            .replace(/¡/g, '¿') // Mantener símbolos en español
            .replace(/!/g, '?');
    };

    // Límites máximos para inputs
    const maxLengths = {
        'oficio_num_baja': 15,
        'profesion': 15,
        'apellido_paterno': 40,
        'apellido_materno': 40,
        'nombres': 60,
        'codigo_prof': 10,
        'descripcion': 100,
        'crn': 7,
        'clasificacion': 15,
        'motivo': 50
    };

    // Campos alfabéticos - permitir letras, espacios y caracteres especiales del español
    const camposAlfabeticos = [
        'nombres', 'apellido_paterno', 'apellido_materno'
    ];

    camposAlfabeticos.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.addEventListener('input', (e) => {
                // Primero aplicamos toUpperWithAccents para mantener la conversión a mayúsculas
                let valor = toUpperWithAccents(e.target.value);
                
                // Luego quitamos solo los números
                // Esto preserva letras, espacios, acentos, ñ, etc.
                valor = valor.replace(/[0-9]/g, '');
                
                e.target.value = valor;
            });
        }
    });

    // Para campos estrictamente numéricos (CRN, CODIGO)
    const camposEstrictamenteNumericos = [
        'codigo_prof', 'crn'
    ];

    camposEstrictamenteNumericos.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.addEventListener('input', (e) => {
                // Solo permitir dígitos
                e.target.value = e.target.value.replace(/\D/g, '');
                
                // Aplicar la longitud máxima correspondiente
                const maxLength = campo === 'codigo_prof' ? 9 : 8; // 8 para código, 7 para CRN
                if (e.target.value.length > maxLength) {
                    e.target.value = e.target.value.slice(0, maxLength);
                }
            });
        }
    });

    // Aplicar límites
    Object.keys(maxLengths).forEach(field => {
        const input = document.getElementById(field);
        input && input.setAttribute('maxlength', maxLengths[field]);
    });

    // Función para establecer fecha actual en formato DD/MM/AAAA
    const establecerFechaActual = (forzar = false) => {
        const fechaInputDisplay = document.getElementById('fecha'); // Campo de visualización
        const fechaInputSQL = document.getElementById('fecha_sql'); // Campo oculto
        
        // No establecer fecha si el campo ya tiene un valor (excepto si se fuerza)
        if (!forzar && fechaInputDisplay && fechaInputDisplay.value && fechaInputDisplay.value.trim() !== '') {
            return; // Ya tiene un valor, no lo sobrescribir
        }
        
        if (fechaInputDisplay) {
            const fechaActual = new Date();
            
            // Formato para mostrar al usuario (DD/MM/YYYY)
            const dia = fechaActual.getDate().toString().padStart(2, '0');
            const mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
            const año = fechaActual.getFullYear();
            const fechaFormateada = `${dia}/${mes}/${año}`;
            
            // Formato SQL para enviar al servidor (YYYY-MM-DD)
            const fechaSQL = fechaActual.toISOString().split('T')[0];
            
            // Establecer la fecha en ambos campos
            fechaInputDisplay.value = fechaFormateada;
            if (fechaInputSQL) {
                fechaInputSQL.value = fechaSQL;
            }
        }
    };
    
    // Campos que deben ser readonly y no modificables  
    const camposReadonly = ['oficio_num_baja', 'fecha']; // Solo estos dos campos

    camposReadonly.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.setAttribute('readonly', true);
            input.classList.add('readonly-field');
            
            // Prevenir cualquier intento de modificación
            input.addEventListener('keydown', (e) => {
                e.preventDefault();
            });
            
            input.addEventListener('paste', (e) => {
                e.preventDefault();
            });
        }
    });

    // Actualizar número de oficio y fecha
    const actualizarNumeroOficio = () => {
        fetch('./functions/personal-solicitud-cambios/oficios/obtener_oficio_baja.php')
            .then(response => response.json())
            .then(data => {
                if (data.siguiente_numero) {
                    document.getElementById('oficio_num_baja').value = data.siguiente_numero;
                }
                // Establecer la fecha DESPUÉS de que se complete la carga del número de oficio
                setTimeout(() => {
                    establecerFechaActual();
                }, 100);
            })
            .catch(error => {
                console.error('Error al obtener número de oficio:', error);
                // Aún así establecer la fecha
                setTimeout(() => {
                    establecerFechaActual();
                }, 100);
            });
    };

    // Guardar/Restaurar datos del formulario
    const guardarDatosFormulario = () => formData = new FormData(formBaja);
    
    const restaurarDatosFormulario = () => {
        formData.forEach((valor, clave) => {
            const input = formBaja.elements[clave];
            input && (input.value = valor);
        });
    };

    // Observer para detectar apertura del modal
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && 
                modalBaja.style.display === 'block') {
                
                // Verificar si es modo visualización o nueva solicitud
                const esVisualizacion = modalBaja.querySelector('h2') && 
                                    modalBaja.querySelector('h2').textContent.includes('Solo visualización');
                
                if (!esVisualizacion) {
                    // Es una nueva solicitud
                    actualizarNumeroOficio();
                    restaurarDatosFormulario();
                    
                    // Asegurar que campos readonly estén configurados
                    setTimeout(() => {
                        const camposReadonly = ['oficio_num_baja', 'fecha'];
                        camposReadonly.forEach(campo => {
                            const input = document.getElementById(campo);
                            if (input) {
                                input.setAttribute('readonly', true);
                                input.classList.add('readonly-field');
                            }
                        });
                    }, 50);
                }
            }
        });
    });

    observer.observe(modalBaja, { attributes: true, attributeFilter: ['style'] });

    // Manejar envío del formulario
    formBaja.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
        
        fetch('./functions/personal-solicitud-cambios/procesar/procesar_baja.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            if (data.status === 'success') {
                modalBaja.style.display = 'none';
                formBaja.reset();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonColor: '#3085d6'
                }).then(() => window.location.reload());
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: error.message || 'Error en la comunicación',
                confirmButtonColor: '#d33'
            });
        });
    });

    // Botón descartar
    document.getElementById('btn-descartar').addEventListener('click', () => {
        Swal.fire({
            title: '¿Descartar cambios?',
            text: "Se perderán todos los datos ingresados",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                formBaja.reset();
                formData = new FormData();
                modalBaja.style.display = 'none';
            }
        });
    });

    // Cierre del modal
    document.querySelector('.close-button').addEventListener('click', () => {
        guardarDatosFormulario();
        modalBaja.style.display = 'none';
    });

    // Convertir a mayúsculas con acentos al escribir
    document.querySelectorAll('.modal-content input[type="text"]:not(.readonly-field):not([type="date"])').forEach(input => {
        // Excluir también campos específicos por ID
        if (input.id !== 'fecha_efectos' && input.id !== 'fecha') {
            input.addEventListener('input', function(e) {
                this.value = toUpperWithAccents(this.value);
            });
        }
    });

    // Manejar clic fuera del modal
    modalBaja.addEventListener('click', function(e) {
        if (e.target === modalBaja) {
            guardarDatosFormulario();
            modalBaja.style.display = 'none';
        }
    });

    // Exponer funciones globalmente para uso desde otros scripts
    window.actualizarNumeroOficioBaja = actualizarNumeroOficio;
    window.establecerFechaActualBaja = () => establecerFechaActual(true); // Forzar establecimiento

    // Función para reinicializar modal para nueva solicitud
    window.reinicializarModalBaja = () => {
        // Limpiar formData
        formData = new FormData();
        
        // Actualizar número de oficio y fecha
        actualizarNumeroOficio();
        
        // Asegurar que los campos readonly estén configurados correctamente
        const camposReadonly = ['oficio_num_baja', 'fecha'];
        camposReadonly.forEach(campo => {
            const input = document.getElementById(campo);
            if (input) {
                input.setAttribute('readonly', true);
                input.classList.add('readonly-field');
            }
        });
    };
});
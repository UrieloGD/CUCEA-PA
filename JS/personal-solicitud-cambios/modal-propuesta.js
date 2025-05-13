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

    if (closeButton) closeButton.addEventListener('click', cerrarModal);
    if (btnDescartar) btnDescartar.addEventListener('click', cerrarModal);

    window.addEventListener('click', (e) => {
        if (e.target === modalPropuesta) cerrarModal();
    });

    // NUEVAS FUNCIONES PARA LOS SELECTORES DE FECHA
    
    // Función para llenar los selectores de día, mes y año
    const llenarSelectoresFecha = () => {
        const diaSelect = document.getElementById('dia_p');
        const mesSelect = document.getElementById('mes_p');
        const anoSelect = document.getElementById('ano_p');
        
        // Llenar selector de día (1-31)
        if (diaSelect) {
            for (let i = 1; i <= 31; i++) {
                const option = document.createElement('option');
                // Asegurar que sea de dos dígitos (01, 02, etc.)
                option.value = i < 10 ? '0' + i : i.toString();
                option.textContent = i;
                diaSelect.appendChild(option);
            }
        }
        
        // Llenar selector de mes (1-12)
        if (mesSelect) {
            const meses = [
                {valor: '01', texto: '1 - Enero'},
                {valor: '02', texto: '2 - Febrero'},
                {valor: '03', texto: '3 - Marzo'},
                {valor: '04', texto: '4 - Abril'},
                {valor: '05', texto: '5 - Mayo'},
                {valor: '06', texto: '6 - Junio'},
                {valor: '07', texto: '7 - Julio'},
                {valor: '08', texto: '8 - Agosto'},
                {valor: '09', texto: '9 - Septiembre'},
                {valor: '10', texto: '10 - Octubre'},
                {valor: '11', texto: '11 - Noviembre'},
                {valor: '12', texto: '12 - Diciembre'}
            ];
            
            meses.forEach(mes => {
                const option     = document.createElement('option');
                option.value = mes.valor;
                option.textContent = mes.texto;
                mesSelect.appendChild(option);
            });
        }
        
        // Llenar selector de año (año actual - 2 hasta año actual + 3)
        if (anoSelect) {
            const añoActual = new Date().getFullYear();
            for (let i = añoActual - 5; i <= añoActual + 15; i++) {
                const option = document.createElement('option');
                option.value = i.toString();
                option.textContent = i;
                anoSelect.appendChild(option);
            }
            // Seleccionar el año actual por defecto
            anoSelect.value = añoActual.toString();
        }
    };

    // Llamar a la función para llenar los selectores al cargar la página
    llenarSelectoresFecha();

    const generarNumeroOficio = () => {
        const oficioInput = document.getElementById('oficio_num_prop');
        if (oficioInput) {
            const añoActual = new Date().getFullYear();
            const añoCorto = añoActual.toString().slice(-2);
            
            fetch('./functions/personal-solicitud-cambios/obtener_oficio_propuesta.php')
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        let numeroSecuencial = parseInt(data.ultimo_numero) + 1;
                        const numeroFormateado = numeroSecuencial.toString().padStart(4, '0');
                        oficioInput.value = `${numeroFormateado}/${añoCorto}`;
                        console.log('Número generado:', oficioInput.value); // Depuración
                    } else {
                        throw new Error(data.message || 'Error en el servidor');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Valor por defecto si falla la consulta
                    const numeroFormateado = '0001';
                    oficioInput.value = `${numeroFormateado}/${añoCorto}`;
                });
        }
    };

    // NUEVAS FUNCIONES DE VALIDACIÓN Y TRANSFORMACIÓN

    // Definir restricciones según el esquema de la base de datos
    const campoRestricciones = {
        // Campos de texto con sus longitudes máximas
        'profesion_p': { maxLength: 15, tipo: 'select' },
        'apellido_paterno_p': { maxLength: 40, tipo: 'texto' },
        'apellido_materno_p': { maxLength: 40, tipo: 'texto' },
        'nombres_p': { maxLength: 60, tipo: 'texto' },
        'descripcion_p': { maxLength: 100, tipo: 'texto' },
        'codigo_puesto_p': { maxLength: 10, tipo: 'alfanumerico' },
        'clasificacion_p': { maxLength: 15, tipo: 'alfanumerico' },
        'categoria': { maxLength: 20, tipo: 'alfanumerico' },
        'carrera': { maxLength: 50, tipo: 'texto' },
        'apellido_paterno_sust': { maxLength: 40, tipo: 'texto' },
        'apellido_materno_sust': { maxLength: 40, tipo: 'texto' },
        'nombres_sust': { maxLength: 60, tipo: 'texto' },
        'causa': { maxLength: 50, tipo: 'texto' },
        
        // Campos numéricos con sus valores máximos
        'codigo_prof_p': { maxLength: 10, tipo: 'numerico' },
        'dia_p': { maxLength: 2, tipo: 'select' },     // Ahora es un select
        'mes_p': { maxLength: 2, tipo: 'select' },     // Ahora es un select
        'ano_p': { maxLength: 4, tipo: 'select' },     // Ahora es un select
        'hrs_semanales': { maxLength: 5, tipo: 'numerico' },
        'crn_p': { maxLength: 7, tipo: 'numerico' },
        'num_puesto': { maxLength: 5, tipo: 'numerico' },
        'codigo_prof_sust': { maxLength: 10, tipo: 'numerico' }
    };

    // Función para convertir a mayúsculas y aplicar restricciones según el tipo de campo
    const aplicarRestricciones = (input) => {
        const id = input.id;
        const restricciones = campoRestricciones[id] || { maxLength: 255, tipo: 'texto' };
        
        // Si es un select, no aplicamos las mismas restricciones
        if (input.nodeName === 'SELECT') {
            return;
        }
        
        // Establecer el atributo maxlength
        input.setAttribute('maxlength', restricciones.maxLength);
        
        // Evento input para aplicar restricciones y convertir a mayúsculas
        input.addEventListener('input', function() {
            let valor = this.value;
            
            // Aplicar restricciones según el tipo
            switch (restricciones.tipo) {
                case 'numerico':
                    valor = valor.replace(/[^0-9]/g, '');
                    break;
                case 'alfanumerico':
                    // Permite letras (con o sin acentos), números y algunos caracteres especiales
                    // No filtramos caracteres aquí para mantener acentos y símbolos
                    break;
                case 'texto':
                    // No filtramos caracteres aquí para mantener acentos y símbolos
                    break;
            }
            
            // Convertir a mayúsculas después de aplicar restricciones
            this.value = valor.toUpperCase();
        });
        
        // Para inputs de tipo texto, validar en el evento blur
        if (restricciones.tipo !== 'select') {
            input.addEventListener('blur', function() {
                this.value = this.value.trim().toUpperCase();
            });
        }
    };

    // Aplicar restricciones a todos los campos del formulario
    if (formPropuesta) {
        const inputs = formPropuesta.querySelectorAll('input[type="text"], input[type="number"], select');
        inputs.forEach(aplicarRestricciones);
        
        // Especial para los campos de fecha que no deben convertirse a mayúsculas
        const fechas = formPropuesta.querySelectorAll('input[type="date"]');
        fechas.forEach(input => {
            input.addEventListener('input', (e) => {
                const selectedDate = new Date(e.target.value);
                const today = new Date();
                const maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());
                
                if (selectedDate > maxDate) {
                    e.target.value = maxDate.toISOString().split('T')[0];
                }
            });
        });
        
        // Validación específica para los campos numéricos
        const numericInputs = formPropuesta.querySelectorAll('.numeric-only');
        numericInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (!/[0-9]/.test(e.key) && e.key !== 'Enter' && e.key !== 'Backspace' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });
        });
    }

    // Mantener la lógica del botón descartar existente
    if (btnDescartar) {
        btnDescartar.addEventListener('click', () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se descartarán todos los cambios realizados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0071b0',
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

    // Función para abrir el modal y generar el número de oficio
    window.abrirModalPropuesta = function() {
        if (modalPropuesta) {
            modalPropuesta.style.display = 'block';
            generarNumeroOficio(); // Asegurar que esta línea está presente
            console.log("Modal abierto, número de oficio generado"); // Para depuración
        }
    };

    // Manejo del formulario (mantenemos la lógica existente)
    if (formPropuesta) {
        formPropuesta.addEventListener('submit', function(e) {
            e.preventDefault();

            // Convertir todos los campos a mayúsculas antes de enviar
            const textInputs = this.querySelectorAll('input[type="text"], textarea');
            textInputs.forEach(input => {
                input.value = input.value.toUpperCase();
            });

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
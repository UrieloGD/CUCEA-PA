document.addEventListener('DOMContentLoaded', function() {
    console.log('Script ver-detalles.js cargado');
    
    // Verificar si los botones existen
    const botones = document.querySelectorAll('.boton-ver-detalles');
    console.log('Botones encontrados:', botones.length);
    
    // Seleccionar todos los botones "Ver detalles"
    document.querySelectorAll('.boton-ver-detalles').forEach(btn => {
        btn.addEventListener('click', function() {
            const folio = this.dataset.folio;
            const tipo = this.dataset.tipo;
            
            console.log('Botón clickeado - Folio:', folio, 'Tipo:', tipo);
            
            // Cargar datos de la solicitud y abrir el modal correspondiente
            cargarDatosSolicitud(folio, tipo);
        });
    });
    
    // Función para cargar los datos de la solicitud desde el servidor
    function cargarDatosSolicitud(folio, tipo) {
        // Mostrar indicador de carga
        mostrarCargando();
        
        console.log('Cargando datos para folio:', folio, 'tipo:', tipo);
        
        // Realizar petición AJAX para obtener los datos
        fetch(`./functions/personal-solicitud-cambios/obtener_detalle_solicitud.php?folio=${folio}&tipo=${tipo}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los datos');
                }
                return response.json();
            })
            .then(data => {
                // Ocultar indicador de carga
                ocultarCargando();
                
                console.log('Datos recibidos:', data);
                
                // Comprobar si hay error en la respuesta
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Rellenar el modal con los datos
                rellenarModal(data, tipo);
                
                // Abrir el modal correspondiente
                abrirModal(tipo);
            })
            .catch(error => {
                console.error('Error:', error);
                ocultarCargando();
                alert('Hubo un error al cargar los datos: ' + error.message);
            });
    }
    
    // Función para mostrar indicador de carga
    function mostrarCargando() {
        // Crear overlay de carga si no existe
        if (!document.getElementById('cargando-overlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'cargando-overlay';
            overlay.innerHTML = '<div class="spinner"></div><p>Cargando datos...</p>';
            document.body.appendChild(overlay);
            
            // Estilos para el overlay de carga
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            overlay.style.display = 'flex';
            overlay.style.flexDirection = 'column';
            overlay.style.justifyContent = 'center';
            overlay.style.alignItems = 'center';
            overlay.style.zIndex = '9999';
            
            // Estilo para el spinner
            const spinnerStyle = document.createElement('style');
            spinnerStyle.textContent = `
                .spinner {
                    border: 5px solid #f3f3f3;
                    border-top: 5px solid #3498db;
                    border-radius: 50%;
                    width: 50px;
                    height: 50px;
                    animation: spin 2s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(spinnerStyle);
        } else {
            document.getElementById('cargando-overlay').style.display = 'flex';
        }
    }
    
    // Función para ocultar indicador de carga
    function ocultarCargando() {
        const overlay = document.getElementById('cargando-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
    
    // Función para abrir el modal según el tipo
    function abrirModal(tipo) {
        let modalId;
        
        switch (tipo) {
            case 'baja':
                modalId = 'solicitud-modal-baja-academica';
                break;
            case 'propuesta':
                modalId = 'solicitud-modal-propuesta-academica';
                break;
            case 'baja-propuesta':
                modalId = 'solicitud-modal-baja-propuesta';
                break;
            default:
                console.error('Tipo de modal no reconocido:', tipo);
                return;
        }
        
        console.log('Intentando abrir modal con ID:', modalId);
        
        const modal = document.getElementById(modalId);
        if (modal) {
            // Mostrar el modal con JavaScript puro
            modal.style.display = 'block';
            modal.classList.add('show');
            
            // Si el modal tiene un fondo oscuro (overlay), mostrarlo también
            const modalOverlay = document.querySelector('.modal-overlay');
            if (modalOverlay) {
                modalOverlay.style.display = 'block';
            }
            
            // Opcional: añadir un evento para cerrar el modal al hacer clic en botones de cierre
            const closeBtns = modal.querySelectorAll('.close-modal, .btn-close, [data-dismiss="modal"]');
            closeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    cerrarModal(modalId);
                });
            });
        } else {
            console.error('Modal no encontrado:', modalId);
        }
    }
    
    // Función para cerrar el modal
    function cerrarModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            
            // Si hay un overlay, ocultarlo también
            const modalOverlay = document.querySelector('.modal-overlay');
            if (modalOverlay) {
                modalOverlay.style.display = 'none';
            }
        }
    }
    
    // Función para llenar el modal con los datos correspondientes
    function rellenarModal(data, tipo) {
        console.log('Rellenando modal de tipo:', tipo);
        console.log('Datos a rellenar:', data);
        
        let modal;
        
        // Asegurarnos de que objetos anidados existan para evitar errores
        data.profesor_actual = data.profesor_actual || {};
        data.profesor_propuesto = data.profesor_propuesto || {};
        
        switch (tipo) {
            case 'baja':
                modal = document.getElementById('solicitud-modal-baja-academica');
                if (!modal) {
                    console.error('Modal de baja académica no encontrado');
                    return;
                }
                
                // Verificar cada selector antes de usarlo
                const setValueIfExists = (selector, value) => {
                    const elem = modal.querySelector(selector);
                    if (elem) {
                        elem.value = value || '';
                        console.log(`Elemento ${selector} actualizado con valor:`, value);
                    } else {
                        console.warn(`Elemento no encontrado: ${selector}`);
                    }
                };
                
                // Actualizar solo los campos que existen
                setValueIfExists('#CRN', data.crn);
                setValueIfExists('#materia-baja', data.materia); // Ajusta los nombres según tus HTML reales
                setValueIfExists('#puesto-baja', data.puesto);
                setValueIfExists('#clasificacion-baja', data.clasificacion_b);
                setValueIfExists('#efecto-baja', data.efecto);
                setValueIfExists('#apellido-paterno-baja', data.profesor_actual.paterno);
                setValueIfExists('#apellido-materno-baja', data.profesor_actual.materno);
                setValueIfExists('#nombres-baja', data.profesor_actual.nombres);
                setValueIfExists('#codigo-baja', data.profesor_actual.codigo);
                
                // Manejar el motivo de forma segura
                const motivoBaja = modal.querySelector('#motivo-baja');
                if (motivoBaja && data.motivo) {
                    // Verificar que motivoBaja.options existe antes de usarlo
                    if (motivoBaja.options && motivoBaja.options.length > 0) {
                        let motivoEncontrado = false;
                        for (let i = 0; i < motivoBaja.options.length; i++) {
                            if (motivoBaja.options[i].text === data.motivo) {
                                motivoBaja.selectedIndex = i;
                                motivoEncontrado = true;
                                break;
                            }
                        }
                        
                        // Si el motivo es "otro", mostrar y rellenar el campo de texto
                        if (data.motivo === "Otro") {
                            const otroMotivoContainer = modal.querySelector('#otro-motivo-container-baja');
                            if (otroMotivoContainer) {
                                otroMotivoContainer.style.display = 'block';
                                setValueIfExists('#otro-motivo-baja', data.otro_motivo);
                            }
                        }
                    } else {
                        // Si no tiene options, asignar directamente el texto
                        motivoBaja.value = data.motivo;
                    }
                }
                
                break;
                
            case 'propuesta':
                modal = document.getElementById('solicitud-modal-propuesta-academica');
                console.log('Modal de propuesta encontrado:', !!modal);
                if (!modal) {
                    console.error('Modal de propuesta académica no encontrado');
                    return;
                }
                
                // Imprimir contenido del modal para depuración
                console.log('Contenido del modal de propuesta:', modal.outerHTML);
                
                // Función auxiliar para establecer el valor de forma segura
                const setValueSafeP = (selector, value) => {
                    const elem = modal.querySelector(selector);
                    console.log(`Elemento ${selector}:`, !!elem, 'Valor:', value);
                    if (elem) {
                        elem.value = value || '';
                    } else {
                        console.error(`Elemento no encontrado: ${selector}`);
                    }
                };
                
                // Rellenar campos de forma segura
                setValueSafeP('#CRN-p', data.crn);
                setValueSafeP('#materia-p', data.materia);
                setValueSafeP('#puesto-p', data.puesto);
                setValueSafeP('#clasificacion-p', data.clasificacion_p);
                setValueSafeP('#horas-sem', data.horas_sem);
                setValueSafeP('#periodo-desde', data.periodo_desde);
                setValueSafeP('#periodo-hasta', data.periodo_hasta);
                
                // Profesor actual
                setValueSafeP('#apellido-paterno-actual', data.profesor_actual.paterno);
                setValueSafeP('#apellido-materno-actual', data.profesor_actual.materno);
                setValueSafeP('#nombres-actual', data.profesor_actual.nombres);
                setValueSafeP('#codigo-actual', data.profesor_actual.codigo);
                
                // Profesor propuesto
                setValueSafeP('#apellido-paterno-propuesto', data.profesor_propuesto.paterno);
                setValueSafeP('#apellido-materno-propuesto', data.profesor_propuesto.materno);
                setValueSafeP('#nombres-propuesto', data.profesor_propuesto.nombres);
                setValueSafeP('#codigo-propuesto', data.profesor_propuesto.codigo);
                
                // Seleccionar el motivo correcto
                const motivoPropuesta = modal.querySelector('#motivo-p');
                console.log('Elemento motivo:', !!motivoPropuesta, 'Valor motivo:', data.motivo);
                if (motivoPropuesta && data.motivo) {
                    let motivoEncontrado = false;
                    for (let i = 0; i < motivoPropuesta.options.length; i++) {
                        if (motivoPropuesta.options[i].text === data.motivo) {
                            motivoPropuesta.selectedIndex = i;
                            motivoEncontrado = true;
                            break;
                        }
                    }
                    
                    console.log('Motivo encontrado en opciones:', motivoEncontrado);
                    
                    // Si el motivo es "otro", mostrar y rellenar el campo de texto
                    if (data.motivo === "Otro") {
                        const otroMotivoContainer = modal.querySelector('#otro-motivo-container-p');
                        console.log('Contenedor otro motivo:', !!otroMotivoContainer);
                        if (otroMotivoContainer) {
                            otroMotivoContainer.style.display = 'block';
                            setValueSafeP('#otro-motivo-p', data.otro_motivo);
                        }
                    }
                }
                
                break;
                
            case 'baja-propuesta':
                modal = document.getElementById('solicitud-modal-baja-propuesta');
                console.log('Modal de baja-propuesta encontrado:', !!modal);
                if (!modal) {
                    console.error('Modal de baja-propuesta académica no encontrado');
                    return;
                }
                
                // Imprimir contenido del modal para depuración
                console.log('Contenido del modal de baja-propuesta:', modal.outerHTML);
                
                // Función auxiliar para establecer el valor de forma segura
                const setValueSafeBP = (selector, value) => {
                    const elem = modal.querySelector(selector);
                    console.log(`Elemento ${selector}:`, !!elem, 'Valor:', value);
                    if (elem) {
                        elem.value = value || '';
                    } else {
                        console.error(`Elemento no encontrado: ${selector}`);
                    }
                };
                
                // Rellenar campos de forma segura
                setValueSafeBP('#CRN-bp', data.crn);
                setValueSafeBP('#materia-bp', data.materia);
                setValueSafeBP('#clave-bp', data.clave);
                setValueSafeBP('#SEC-bp', data.sec);
                
                // Profesor actual
                setValueSafeBP('#apellido-paterno-actual-bp', data.profesor_actual.paterno);
                setValueSafeBP('#apellido-materno-actual-bp', data.profesor_actual.materno);
                setValueSafeBP('#nombres-actual-bp', data.profesor_actual.nombres);
                setValueSafeBP('#codigo-actual-bp', data.profesor_actual.codigo);
                
                // Profesor propuesto
                setValueSafeBP('#apellido-paterno-propuesto-bp', data.profesor_propuesto.paterno);
                setValueSafeBP('#apellido-materno-propuesto-bp', data.profesor_propuesto.materno);
                setValueSafeBP('#nombres-propuesto-bp', data.profesor_propuesto.nombres);
                setValueSafeBP('#codigo-propuesto-bp', data.profesor_propuesto.codigo);
                
                // Seleccionar el motivo correcto
                const motivoBajaPropuesta = modal.querySelector('#motivo-bp');
                console.log('Elemento motivo:', !!motivoBajaPropuesta, 'Valor motivo:', data.motivo);
                if (motivoBajaPropuesta && data.motivo) {
                    let motivoEncontrado = false;
                    for (let i = 0; i < motivoBajaPropuesta.options.length; i++) {
                        if (motivoBajaPropuesta.options[i].text === data.motivo) {
                            motivoBajaPropuesta.selectedIndex = i;
                            motivoEncontrado = true;
                            break;
                        }
                    }
                    
                    console.log('Motivo encontrado en opciones:', motivoEncontrado);
                    
                    // Si el motivo es "otro", mostrar y rellenar el campo de texto
                    if (data.motivo === "Otro") {
                        const otroMotivoContainer = modal.querySelector('#otro-motivo-container-bp');
                        console.log('Contenedor otro motivo:', !!otroMotivoContainer);
                        if (otroMotivoContainer) {
                            otroMotivoContainer.style.display = 'block';
                            setValueSafeBP('#otro-motivo-bp', data.otro_motivo);
                        }
                    }
                }
                
                break;
        }
        
        // Desactivar todos los campos para que sean de solo lectura
        if (modal) {
            modal.querySelectorAll('input, select, textarea').forEach(elem => {
                elem.setAttribute('readonly', 'readonly');
                if (elem.tagName === 'SELECT') {
                    elem.setAttribute('disabled', 'disabled');
                }
            });
            
            // Ocultar botones de envío
            const submitBtn = modal.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }
            
            // Añadir título de solo visualización
            const modalTitle = modal.querySelector('.modal-title');
            if (modalTitle) {
                modalTitle.innerHTML = 'Detalles de la solicitud (Solo visualización)';
            }
        }
    }
});
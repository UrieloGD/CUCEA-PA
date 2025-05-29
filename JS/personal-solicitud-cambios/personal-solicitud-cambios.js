// ./JS/personal-solicitud-cambios/personal-solicitud-cambios.js
document.addEventListener('DOMContentLoaded', function() {
    const esCoordinacionPersonal = rol_usuario === 3;
    
    // Cache de elementos para mejor rendimiento
    const elementosCacheados = {
        modalRechazo: null,
        botonesAceptar: null,
        botonesRechazar: null,
        nuevaSolicitudBtn: null,
        listaOpciones: null
    };
    
    // Función para mostrar/ocultar información (optimizada)
    window.mostrarInformacion = function(contenedorId, icono) {
        const contenedor = document.getElementById(contenedorId);
        
        if (contenedor) {
            const isActive = contenedor.classList.contains('active');
            
            if (!isActive) {
                contenedor.style.display = 'block';
                contenedor.classList.add('active');
                // Usar requestAnimationFrame para mejor rendimiento
                requestAnimationFrame(() => {
                    contenedor.style.maxHeight = contenedor.scrollHeight + "px";
                });
                icono.classList.add('rotated');
            } else {
                contenedor.classList.remove('active');
                contenedor.style.maxHeight = '0';
                icono.classList.remove('rotated');
                setTimeout(() => {
                    if (!contenedor.classList.contains('active')) {
                        contenedor.style.display = 'none';
                    }
                }, 200);
            }
        }
    };

    // Inicializar contenedores (optimizado)
    function inicializarContenedores() {
        const contenedores = document.querySelectorAll('.contenedor-informacion');
        contenedores.forEach(container => {
            container.style.display = 'none';
            container.style.maxHeight = '0';
        });
    }

    // Funcionalidad para usuarios NO coordinación personal (optimizada)
    function inicializarFuncionalidadNoCoordinacion() {
        // Cache de elementos
        const btn = elementosCacheados.nuevaSolicitudBtn || document.getElementById('nueva-solicitud-btn');
        const lista = elementosCacheados.listaOpciones || document.getElementById('lista-opciones');
        
        if (btn) elementosCacheados.nuevaSolicitudBtn = btn;
        if (lista) elementosCacheados.listaOpciones = lista;

        const modales = {
            'Solicitud de baja': document.getElementById('solicitud-modal-baja-academica'),
            'Solicitud de propuesta': document.getElementById('solicitud-modal-propuesta-academica'),
            'Solicitud de baja-propuesta': document.getElementById('solicitud-modal-baja-propuesta')
        };

        // Inicializar modales
        Object.values(modales).forEach(modal => {
            if (modal) modal.style.display = 'none';
        });

        if (btn && lista) {
            // Event listener para botón nueva solicitud
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                lista.classList.toggle('show');
            });

            // Event listener para opciones de lista
            lista.addEventListener('click', function(e) {
                e.preventDefault();
                const opcionSeleccionada = e.target.innerText;
                if (modales[opcionSeleccionada]) {
                    lista.classList.remove('show');
                    openModal(modales[opcionSeleccionada]);
                }
            });

            // Cerrar lista al hacer click fuera
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !lista.contains(e.target)) {
                    lista.classList.remove('show');
                }
            });
        }

        // Función para abrir modal (optimizada)
        function openModal(modal) {
            if (!modal) return;
            
            // Limpiar y habilitar el modal antes de abrirlo
            if (typeof window.limpiarYHabilitarModal === 'function') {
                window.limpiarYHabilitarModal(modal);
            }
            
            modal.style.display = 'block';
            const closeButton = modal.querySelector('.close-button');
            const modalContent = modal.querySelector('.modal-content-propuesta') || 
                               modal.querySelector('.modal-content-baja');
            
            // Event listener para botón cerrar
            if (closeButton) {
                const closeHandler = function() {
                    modal.style.display = 'none';
                    closeButton.removeEventListener('click', closeHandler);
                };
                closeButton.addEventListener('click', closeHandler);
            }
        
            // Prevenir cierre al hacer click en contenido
            if (modalContent) {
                modalContent.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        
            // Cerrar al hacer click fuera del modal
            const outsideClickHandler = function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    window.removeEventListener('click', outsideClickHandler);
                }
            };
            window.addEventListener('click', outsideClickHandler);
        }

        // Aplicar validaciones a campos de entrada (optimizada)
        aplicarValidacionesCampos();
    }

    // Validaciones de campos (optimizada con throttling)
    function aplicarValidacionesCampos() {
        const validaciones = {
            'texto-CRN': { pattern: /\D/g, maxLength: 6, type: 'numeric' },
            'texto-materia': { pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, type: 'text' },
            'texto-clave': { pattern: /[^a-zA-Z0-9]/g, maxLength: 5, type: 'alphanumeric' },
            'texto-SEC': { pattern: /[^a-zA-Z0-9]/g, maxLength: 3, type: 'alphanumeric' },
            'texto-folio': { pattern: /[^a-zA-Z0-9]/g, maxLength: 10, type: 'alphanumeric' },
            'texto-apellido-paterno': { pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, maxLength: 50, type: 'text' },
            'texto-apellido-materno': { pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, maxLength: 50, type: 'text' },
            'texto-nombres': { pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, maxLength: 50, type: 'text' },
            'texto-codigo': { pattern: /\D/g, maxLength: 10, type: 'numeric' },
            'texto-otro': { pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, maxLength: 120, type: 'text' }
        };

        Object.entries(validaciones).forEach(([className, rules]) => {
            document.querySelectorAll(`.${className}`).forEach(input => {
                let timeoutId;
                input.addEventListener('input', function(e) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        let value = e.target.value;
                        value = value.replace(rules.pattern, '');
                        if (rules.maxLength) value = value.slice(0, rules.maxLength);
                        e.target.value = value;
                    }, 100); // Throttling de 100ms
                });
            });
        });
    }

    // Función optimizada para cambiar estado de solicitud (con fecha)
    function cambiarEstadoSolicitud(folio, tipo, nuevoEstado, comentario = '') {
        // Mostrar loading inmediatamente sin delay
        const loadingAlert = Swal.fire({
            title: 'Procesando...',
            text: 'Actualizando estado de la solicitud',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const formData = new FormData();
        formData.append('folio', folio);
        formData.append('tipo', tipo);
        formData.append('estado', nuevoEstado);
        if (comentario) formData.append('comentario', comentario);
        
        fetch('./functions/personal-solicitud-cambios/cambiar_estado_solicitud.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Mensaje más detallado con la fecha
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    html: `
                        <div style="text-align: center;">
                            <p style="margin-bottom: 10px;">${data.message}</p>
                            ${data.fecha_modificacion ? 
                                `<p style="font-size: 0.9em; color: #666; margin-top: 10px;">
                                    <i class="fas fa-clock"></i> 
                                    Fecha de modificación: ${data.fecha_modificacion}
                                </p>` : ''
                            }
                        </div>
                    `,
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#28a745',
                    timer: 4000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'Error al procesar la solicitud. Inténtalo de nuevo.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
        });
    }

    // Modal de rechazo optimizado usando SweetAlert2 nativo
    function mostrarModalRechazo(folio, tipo) {
        Swal.fire({
            title: 'Motivo de rechazo',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px; color: #333;">Por favor, especifica el motivo por el cual se rechaza esta solicitud:</p>
                    <textarea id="swal-rechazo-textarea" 
                              placeholder="Escribe aquí el motivo del rechazo..." 
                              maxlength="500" 
                              rows="4"
                              style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; resize: vertical; font-family: inherit;"></textarea>
                    <div style="text-align: right; margin-top: 5px; font-size: 12px; color: #666;">
                        <span id="swal-contador">0</span>/500 caracteres
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirmar rechazo',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            focusConfirm: false,
            allowOutsideClick: false,
            width: '500px',
            preConfirm: () => {
                const comentario = document.getElementById('swal-rechazo-textarea').value.trim();
                
                if (!comentario) {
                    Swal.showValidationMessage('Por favor, especifica un motivo para el rechazo.');
                    return false;
                }
                
                if (comentario.length < 10) {
                    Swal.showValidationMessage('El motivo debe tener al menos 10 caracteres.');
                    return false;
                }
                
                return comentario;
            },
            didOpen: () => {
                const textarea = document.getElementById('swal-rechazo-textarea');
                const contador = document.getElementById('swal-contador');
                
                // Focus inmediato
                setTimeout(() => textarea.focus(), 50);
                
                // Contador con throttling optimizado
                let timeoutId;
                textarea.addEventListener('input', function(e) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        contador.textContent = e.target.value.length;
                    }, 50);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Confirmación final optimizada
                Swal.fire({
                    icon: 'question',
                    title: '¿Confirmar rechazo?',
                    text: '¿Está seguro de que desea rechazar esta solicitud?',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, rechazar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((confirmResult) => {
                    if (confirmResult.isConfirmed) {
                        cambiarEstadoSolicitud(folio, tipo, 'Rechazado', result.value);
                    }
                });
            }
        });
    }

    // Manejadores de botones PDF optimizados
    function handleGenerarPDF(btn) {
        const tipo = btn.dataset.tipo;
        const folio = btn.dataset.folio;
        
        // Prevenir múltiples clicks
        if (btn.classList.contains('processing')) return;
        btn.classList.add('processing');
        
        const funciones = {
            'baja': () => generarPDFBaja(folio),
            'propuesta': () => generarPDFPropuesta(folio),
            'baja-propuesta': () => generarPDFBajaPropuesta(folio)
        };
        
        if (funciones[tipo]) {
            funciones[tipo]();
        }
        
        // Remover clase después de un breve delay
        setTimeout(() => btn.classList.remove('processing'), 1000);
    }

    function handleDescargarPDF(btn) {
        const tipo = btn.dataset.tipo;
        const folio = btn.dataset.folio;
        
        // Prevenir múltiples clicks
        if (btn.classList.contains('processing')) return;
        btn.classList.add('processing');
        
        const urls = {
            'baja': `./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=${folio}`,
            'propuesta': `./functions/personal-solicitud-cambios/pdfs/descargar_pdf_propuesta.php?folio=${folio}`,
            'baja-propuesta': `./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja_propuesta.php?folio=${folio}`
        };
        
        if (urls[tipo]) {
            window.open(urls[tipo], '_blank');
        }
        
        // Remover clase después de un breve delay
        setTimeout(() => btn.classList.remove('processing'), 500);
    }

    function handleVerDetalles(btn) {
        const tipo = btn.dataset.tipo;
        const folio = btn.dataset.folio;
        
        // Aquí puedes implementar la lógica para ver detalles
        console.log('Ver detalles:', { tipo, folio });
    }

    // Sistema principal de eventos optimizado con delegación
    function inicializarEventosPrincipales() {
        // Usar delegación de eventos para máximo rendimiento
        document.body.addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;
            
            // Prevenir múltiples clicks simultáneos
            if (target.classList.contains('processing')) {
                e.preventDefault();
                return;
            }
            
            // Botones de aceptar solicitud
            if (target.classList.contains('boton-aceptar')) {
                e.preventDefault();
                target.classList.add('processing');
                
                const folio = target.dataset.folio;
                const tipo = target.dataset.tipo;
                
                // SweetAlert2 optimizado sin delays innecesarios
                Swal.fire({
                    icon: 'question',
                    title: '¿Confirmar aprobación?',
                    text: '¿Está seguro de que desea aceptar esta solicitud?',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, aceptar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    target.classList.remove('processing');
                    if (result.isConfirmed) {
                        cambiarEstadoSolicitud(folio, tipo, 'Aprobado');
                    }
                });
            }
            
            // Botones de rechazar solicitud
            else if (target.classList.contains('boton-rechazar')) {
                e.preventDefault();
                target.classList.add('processing');
                
                const folio = target.dataset.folio;
                const tipo = target.dataset.tipo;
                
                // Ejecutar inmediatamente sin delays
                target.classList.remove('processing');
                mostrarModalRechazo(folio, tipo);
            }
            
            // Botones de generar PDF
            else if (target.classList.contains('boton-generar')) {
                e.preventDefault();
                handleGenerarPDF(target);
            }
            
            // Botones de descargar PDF
            else if (target.classList.contains('boton-descargar')) {
                e.preventDefault();
                handleDescargarPDF(target);
            }
            
            // Botones de ver detalles
            else if (target.classList.contains('boton-ver-detalles')) {
                e.preventDefault();
                handleVerDetalles(target);
            }
        });
    }

    // Funciones de compatibilidad para PDF (mantenidas para compatibilidad)
    window.descargarPDFBaja = function(folio) {
        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=${folio}`, '_blank');
    };
    
    window.descargarPDFPropuesta = function(folio) {
        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_propuesta.php?folio=${folio}`, '_blank');
    };
    
    window.descargarPDFBajaPropuesta = function(folio) {
        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja_propuesta.php?folio=${folio}`, '_blank');
    };

    // Funciones para generar PDF (estas deben existir en otro archivo)
    // Si no existen, puedes crearlas o comentar las líneas que las llaman
    if (typeof generarPDFBaja === 'undefined') {
        window.generarPDFBaja = function(folio) {
            console.log('Función generarPDFBaja no definida para folio:', folio);
        };
    }
    
    if (typeof generarPDFPropuesta === 'undefined') {
        window.generarPDFPropuesta = function(folio) {
            console.log('Función generarPDFPropuesta no definida para folio:', folio);
        };
    }
    
    if (typeof generarPDFBajaPropuesta === 'undefined') {
        window.generarPDFBajaPropuesta = function(folio) {
            console.log('Función generarPDFBajaPropuesta no definida para folio:', folio);
        };
    }

    // Inicialización principal del sistema
    function inicializarSistema() {
        // Inicializar contenedores primero
        inicializarContenedores();
        
        // Inicializar eventos principales (siempre)
        inicializarEventosPrincipales();
        
        // Funcionalidad específica para usuarios no coordinación
        if (!esCoordinacionPersonal) {
            inicializarFuncionalidadNoCoordinacion();
        }
    }

    // Observador de mutaciones optimizado para contenido dinámico
    const observer = new MutationObserver(function(mutations) {
        let shouldReinitialize = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Solo reinicializar si se agregaron elementos relevantes
                for (let node of mutation.addedNodes) {
                    if (node.nodeType === 1 && // Es un elemento
                        (node.classList?.contains('boton-aceptar') ||
                         node.classList?.contains('boton-rechazar') ||
                         node.classList?.contains('boton-generar') ||
                         node.classList?.contains('boton-descargar') ||
                         node.querySelector?.('.boton-aceptar, .boton-rechazar, .boton-generar, .boton-descargar'))) {
                        shouldReinitialize = true;
                        break;
                    }
                }
            }
        });
        
        if (shouldReinitialize) {
            // Reinicializar solo los contenedores nuevos
            inicializarContenedores();
        }
    });
    
    // Observar cambios en el body
    observer.observe(document.body, { 
        childList: true, 
        subtree: true 
    });
    
    // Inicialización principal
    inicializarSistema();
});
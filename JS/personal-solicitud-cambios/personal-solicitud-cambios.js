// ./JS/personal-solicitud-cambios/personal-solicitud-cambios.js
document.addEventListener('DOMContentLoaded', function() {
    const esCoordinacionPersonal = rol_usuario === 3;
    
    // Función para mostrar/ocultar información
    window.mostrarInformacion = function(contenedorId, icono) {
        const contenedor = document.getElementById(contenedorId);
        
        if (contenedor) {
            contenedor.classList.toggle('active');
            
            if (contenedor.classList.contains('active')) {
                contenedor.style.display = 'block';
                setTimeout(() => {
                    contenedor.style.maxHeight = contenedor.scrollHeight + "px";
                }, 10);
                icono.classList.add('rotated');
            } else {
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

    // Inicializar contenedores
    document.querySelectorAll('.contenedor-informacion').forEach(container => {
        container.style.display = 'none';
        container.style.maxHeight = '0';
    });

    // Funcionalidad para no coordinación
    if (!esCoordinacionPersonal) {
        const btn = document.getElementById('nueva-solicitud-btn');
        const lista = document.getElementById('lista-opciones');
        const modales = {
            'Solicitud de baja': document.getElementById('solicitud-modal-baja-academica'),
            'Solicitud de propuesta': document.getElementById('solicitud-modal-propuesta-academica'),
            'Solicitud de baja-propuesta': document.getElementById('solicitud-modal-baja-propuesta')
        };

        Object.values(modales).forEach(modal => {
            if (modal) modal.style.display = 'none';
        });

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            lista.classList.toggle('show');
        });

        lista.addEventListener('click', function(e) {
            const opcionSeleccionada = e.target.innerText;
            if (modales[opcionSeleccionada]) {
                lista.classList.remove('show');
                openModal(modales[opcionSeleccionada]);
            }
        });

        document.addEventListener('click', function(e) {
            if (!btn.contains(e.target) && !lista.contains(e.target)) {
                lista.classList.remove('show');
            }
        });

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
            
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
        
            if (modalContent) {
                modalContent.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        
            window.addEventListener('click', function closeOutside(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    window.removeEventListener('click', closeOutside);
                }
            });
        }

        // Validaciones de campos
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
                input.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(rules.pattern, '');
                    if (rules.maxLength) value = value.slice(0, rules.maxLength);
                    e.target.value = value;
                });
            });
        });
    }

    // Sistema de inicialización de botones PDF
    function handlePDFButtons() {
        // Generar PDF (existente)
        document.querySelectorAll('.boton-generar').forEach(btn => {
            btn.addEventListener('click', function() {
                const tipo = this.dataset.tipo;
                const folio = this.dataset.folio;
                
                switch(tipo) {
                    case 'baja':
                        generarPDFBaja(folio);
                        break;
                    case 'propuesta':
                        generarPDFPropuesta(folio);
                        break;
                    case 'baja-propuesta':
                        generarPDFBajaPropuesta(folio);
                        break;
                }
            });
        });

        // Descargar PDF
        document.querySelectorAll('.boton-descargar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const tipo = this.dataset.tipo;
                const folio = this.dataset.folio;
                console.log('Descargando PDF tipo:', tipo, 'Folio:', folio); // Debug
                
                let url = '';
                switch(tipo) {
                    case 'baja':
                        url = `./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=${folio}`;
                        break;
                    case 'propuesta':
                        url = `./functions/personal-solicitud-cambios/pdfs/descargar_pdf_propuesta.php?folio=${folio}`;
                        break;
                    case 'baja-propuesta':
                        url = `./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja_propuesta.php?folio=${folio}`;
                        break;
                    default:
                        console.error('Tipo de PDF no reconocido:', tipo);
                        return;
                }
                
                window.open(url, '_blank');
            });
        });
    }

    function descargarPDFBaja(folio) {
        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=${folio}`, '_blank');
    }
    
    function descargarPDFPropuesta(folio) {
        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_propuesta.php?folio=${folio}`, '_blank');
    }
    
    // Si existe baja-propuesta
    function descargarPDFBajaPropuesta(folio) {
        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja_propuesta.php?folio=${folio}`, '_blank');
    }

    // Observador de cambios
    const observer = new MutationObserver(handlePDFButtons);
    observer.observe(document.body, { childList: true, subtree: true });
    handlePDFButtons();
});
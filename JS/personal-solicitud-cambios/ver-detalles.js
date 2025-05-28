document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar si los botones existen
    const botones = document.querySelectorAll('.boton-ver-detalles');
    
    // Seleccionar todos los botones "Ver detalles"
    document.querySelectorAll('.boton-ver-detalles').forEach(btn => {
        btn.addEventListener('click', function() {
            const folio = this.dataset.folio;
            const tipo = this.dataset.tipo;
            
            // Cargar datos de la solicitud y abrir el modal correspondiente
            cargarDatosSolicitud(folio, tipo);
        });
    });
    
    // Función para limpiar y habilitar un modal para nueva solicitud
    function limpiarYHabilitarModal(modal) {
        if (!modal) return;
        
        // Habilitar todos los campos
        modal.querySelectorAll('input, select, textarea').forEach(elem => {
            elem.removeAttribute('readonly');
            elem.removeAttribute('disabled');
            
            // Limpiar valores EXCEPTO los campos que deben auto-generarse
            if (elem.id !== 'oficio_num_baja' && elem.id !== 'fecha' && elem.id !== 'fecha_sql') {
                if (elem.tagName === 'SELECT') {
                    elem.selectedIndex = 0;
                } else if (elem.type === 'checkbox' || elem.type === 'radio') {
                    elem.checked = false;
                } else {
                    elem.value = '';
                }
            }
        });
        
        // Restaurar campos readonly específicos que deben mantenerse
        const camposReadonly = ['oficio_num_baja', 'fecha'];
        camposReadonly.forEach(campo => {
            const input = modal.querySelector(`#${campo}`);
            if (input) {
                input.setAttribute('readonly', true);
                input.classList.add('readonly-field');
            }
        });

        // **NUEVA FUNCIÓN: Limpiar secciones de archivo adjunto**
        function limpiarSeccionesArchivo(modal) {
            const nuevoArchivoSection = modal.querySelector('#nuevo-archivo-section');
            const existingArchivoSection = modal.querySelector('#existing-archivo-section');
            
            // Restaurar visibilidad original de las secciones
            if (nuevoArchivoSection) {
                nuevoArchivoSection.style.display = 'block'; // Mostrar sección de subida
            }
            
            if (existingArchivoSection) {
                existingArchivoSection.style.display = 'none'; // Ocultar sección de archivos existentes
            }
            
            // Limpiar contenido de archivo adjunto existente
            const archivoContenido = modal.querySelector('#archivo-adjunto-contenido');
            if (archivoContenido) {
                archivoContenido.innerHTML = '';
            }
            
            // Limpiar input de archivo si existe
            const inputArchivo = modal.querySelector('input[type="file"]');
            if (inputArchivo) {
                inputArchivo.value = '';
                // También limpiar cualquier preview o información del archivo
                const archivoPreview = modal.querySelector('.archivo-preview');
                if (archivoPreview) {
                    archivoPreview.innerHTML = '';
                }
            }
        }
        
        // **NUEVO: Limpiar y restaurar secciones de archivo adjunto**
        limpiarSeccionesArchivo(modal);
        
        // Mostrar botones de acción
        const botonesAccion = modal.querySelector('.contenedor-botones-baja') || 
                            modal.querySelector('.contenedor-botones-propuesta') ||
                            modal.querySelector('.contenedor-botones-baja-propuesta');
        if (botonesAccion) {
            botonesAccion.style.display = 'block';
        }
        
        // Restaurar título original
        const modalTitle = modal.querySelector('h2');
        if (modalTitle) {
            const originalTitles = {
                'solicitud-modal-baja-academica': 'Nueva solicitud de baja',
                'solicitud-modal-propuesta-academica': 'Nueva solicitud de propuesta',
                'solicitud-modal-baja-propuesta': 'Nueva solicitud de baja-propuesta'
            };
            modalTitle.innerHTML = originalTitles[modal.id] || 'Nueva solicitud';
        }
        
        // IMPORTANTE: Forzar la actualización de número de oficio y fecha para modal de baja
        if (modal.id === 'solicitud-modal-baja-academica') {
            // Llamar a las funciones del modal-baja.js si están disponibles
            setTimeout(() => {
                // Intentar ejecutar la función de actualización de oficio
                if (typeof window.actualizarNumeroOficioBaja === 'function') {
                    window.actualizarNumeroOficioBaja();
                } else {
                    // Si no está disponible, hacer la llamada directamente
                    fetch('./functions/personal-solicitud-cambios/oficios/obtener_oficio_baja.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.siguiente_numero) {
                                const oficioBaja = modal.querySelector('#oficio_num_baja');
                                if (oficioBaja) {
                                    oficioBaja.value = data.siguiente_numero;
                                }
                            }
                            // Establecer fecha actual
                            establecerFechaActualModal(modal);
                        })
                        .catch(error => {
                            establecerFechaActualModal(modal);
                        });
                }
            }, 100);
        }
    }

    function establecerFechaActualModal(modal) {
        const fechaInputDisplay = modal.querySelector('#fecha');
        const fechaInputSQL = modal.querySelector('#fecha_sql');
        
        if (fechaInputDisplay) {
            const fechaActual = new Date();
            
            // Formato para mostrar al usuario (DD/MM/YYYY)
            const dia = fechaActual.getDate().toString().padStart(2, '0');
            const mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
            const año = fechaActual.getFullYear();
            const fechaFormateada = `${dia}/${mes}/${año}`;
            
            // Formato SQL para enviar al servidor (YYYY-MM-DD)
            const fechaSQL = fechaActual.toISOString().split('T')[0];
            
            fechaInputDisplay.value = fechaFormateada;
            if (fechaInputSQL) {
                fechaInputSQL.value = fechaSQL;
            }
        }
    }

    // Exponer función globalmente para que pueda ser usada desde otros scripts
    window.limpiarYHabilitarModal = limpiarYHabilitarModal;
    
    // Función para cargar los datos de la solicitud desde el servidor
    function cargarDatosSolicitud(folio, tipo) {
        // Mostrar indicador de carga
        mostrarCargando();
        
        // Determinar qué archivo PHP usar según el tipo
        let url;
        switch (tipo) {
            case 'baja':
                url = './functions/personal-solicitud-cambios/mostrar-info/obtener_detalle_solicitud_baja.php';
                break;
            case 'propuesta':
                url = './functions/personal-solicitud-cambios/mostrar-info/obtener_detalle_solicitud_propuesta.php';
                break;
            case 'baja-propuesta':
                url = './functions/personal-solicitud-cambios/mostrar-info/obtener_detalle_solicitud_baja_propuesta.php';
                break;
            default:
                ocultarCargando();
                alert('Tipo de solicitud no reconocido');
                return;
        }
        
        // Realizar petición AJAX para obtener los datos
        fetch(`${url}?folio=${encodeURIComponent(folio)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error HTTP: ' + response.status);
                }
                
                // Verificar si la respuesta tiene contenido
                return response.text();
            })
            .then(text => {
                // Verificar si la respuesta está vacía
                if (!text || text.trim() === '') {
                    throw new Error('La respuesta del servidor está vacía');
                }
                
                // Intentar parsear como JSON
                try {
                    const data = JSON.parse(text);
                    return data;
                } catch (e) {
                    throw new Error('La respuesta no es JSON válido. Servidor devolvió: ' + text.substring(0, 100));
                }
            })
            .then(data => {
                // Ocultar indicador de carga
                ocultarCargando();
                
                // Comprobar si hay error en la respuesta
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Rellenar el modal con los datos
                try {
                    rellenarModal(data, tipo);
                    
                    // Abrir el modal correspondiente
                    abrirModal(tipo);
                } catch (error) {
                    alert('Error al mostrar los datos: ' + error.message);
                }
            })
            .catch(error => {
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
                return;
        }
        
        const modal = document.getElementById(modalId);
        if (modal) {
            // Mostrar el modal con JavaScript puro
            modal.style.display = 'block';
            
            // Si hay un botón para cerrar, añadir eventos
            const closeBtn = modal.querySelector('.close-button');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    cerrarModal(modalId);
                });
            }
            
            // Si hay un fondo de modal, mostrarlo también
            const modalOverlay = document.querySelector('.modal-overlay');
            if (modalOverlay) {
                modalOverlay.style.display = 'block';
            }
        }
    }
    
    function cerrarModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            
            // Si hay un fondo de modal, ocultarlo también
            const modalOverlay = document.querySelector('.modal-overlay');
            if (modalOverlay) {
                modalOverlay.style.display = 'none';
            }
        }
    }

    // Función para convertir fecha a formato DD/MM/AAAA para visualización
    function formatearFechaParaDD_MM_AAAA(fechaString) {
        if (!fechaString) return '';
        
        // Si ya viene en formato DD/MM/AAAA, devolverla tal como está
        if (fechaString.includes('/')) {
            return fechaString;
        }
        
        // Si viene en formato YYYY-MM-DD, convertir a DD/MM/AAAA
        if (fechaString.includes('-')) {
            const partes = fechaString.split('-');
            if (partes.length === 3) {
                return `${partes[2]}/${partes[1]}/${partes[0]}`;
            }
        }
        
        return fechaString;
    }

    // Función para convertir fecha a formato SQL (YYYY-MM-DD)
    function formatearFechaParaSQL(fechaString) {
        if (!fechaString) return '';
        
        // Si ya viene en formato YYYY-MM-DD, devolverla tal como está
        if (fechaString.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return fechaString;
        }
        
        // Si viene en formato DD/MM/AAAA, convertir a YYYY-MM-DD
        if (fechaString.includes('/')) {
            const partes = fechaString.split('/');
            if (partes.length === 3) {
                return `${partes[2]}-${partes[1]}-${partes[0]}`;
            }
        }
        
        return fechaString;
    }

    // Mantener la función original para campos de tipo date
    function formatearFechaParaHTML(fechaString) {
        // Si la fecha viene en formato dd/mm/yyyy, convertir a yyyy-mm-dd para input date
        if (fechaString && fechaString.includes('/')) {
            const partes = fechaString.split('/');
            if (partes.length === 3) {
                return `${partes[2]}-${partes[1]}-${partes[0]}`;
            }
        }
        return fechaString;
    }
    
    // Función para llenar el modal con los datos correspondientes
    function rellenarModal(data, tipo) {
        let modal;
        
        // Asegurarnos de que objetos anidados existan para evitar errores
        data.profesor_actual = data.profesor_actual || {};
        data.profesor_propuesto = data.profesor_propuesto || {};
        
        switch (tipo) {
            case 'baja':
                modal = document.getElementById('solicitud-modal-baja-academica');
                if (!modal) {
                    return;
                }
            
             // Función auxiliar para establecer el valor de forma segura
             const setValueSafe = (selector, value) => {
                const elem = modal.querySelector(selector);
                if (elem) {
                    elem.value = value || '';
                }
            };
            
            // Actualizar los campos con los IDs correctos según tu HTML
            setValueSafe('#crn', data.crn);
            setValueSafe('#descripcion', data.puesto);
            setValueSafe('#clasificacion', data.clasificacion_b);
            setValueSafe('#profesion', data.profesion);
            setValueSafe('#fecha_efectos', formatearFechaParaHTML(data.fecha_efectos || data.efecto));
            setValueSafe('#fecha', formatearFechaParaDD_MM_AAAA(data.fecha));
            setValueSafe('#fecha_sql', formatearFechaParaSQL(data.fecha));
            setValueSafe('#apellido_paterno', data.profesor_actual.paterno);
            setValueSafe('#apellido_materno', data.profesor_actual.materno);
            setValueSafe('#nombres', data.profesor_actual.nombres);
            setValueSafe('#codigo_prof', data.profesor_actual.codigo);
            setValueSafe('#motivo', data.motivo);
            setValueSafe('#oficio_num_baja', data.folio);
            
            // Establecer la profesión - Versión mejorada
            const profesionSelect = modal.querySelector('#profesion');
            const profesionValue = data.profesor_actual.profession || data.profession || '';

            if (profesionSelect && profesionValue) {
                // Primero intentamos buscar una coincidencia exacta
                for (let i = 0; i < profesionSelect.options.length; i++) {
                    if (profesionSelect.options[i].value === profesionValue) {
                        profesionSelect.selectedIndex = i;
                        break;
                    }
                }
                
                // Si no encontramos una coincidencia exacta, intentamos una parcial
                if (profesionSelect.selectedIndex === 0 || profesionSelect.selectedIndex === -1) {
                    for (let i = 0; i < profesionSelect.options.length; i++) {
                        if (profesionSelect.options[i].value && profesionValue &&
                            (profesionSelect.options[i].value.includes(profesionValue) || 
                            profesionValue.includes(profesionSelect.options[i].value))) {
                            profesionSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
            
            // NUEVO: Configurar archivo adjunto para visualización
            configurarArchivoAdjuntoVisualizacion(modal, data);
            
            break;
            
        case 'propuesta':
            modal = document.getElementById('solicitud-modal-propuesta-academica');
            if (!modal) {
                return;
            }
        
            const setValueSafeP = (selector, value) => {
                const elem = modal.querySelector(selector);
                if (elem) {
                    elem.value = value || '';
                }
            };
            
            // Función mejorada para seleccionar opción en desplegable
            const seleccionarOpcion = (selector, valor) => {
                const elem = modal.querySelector(selector);
                if (elem && valor !== undefined && valor !== null && valor !== '') {
                    // Convertir valor a string para comparaciones
                    const valorStr = valor.toString();
                    
                    // Buscar por valor exacto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].value === valorStr) {
                            elem.selectedIndex = i;
                            return;
                        }
                    }
                    
                    // Para meses, si viene un número, buscar también con cero adelante
                    if (selector === '#mes_p') {
                        const valorConCero = valorStr.padStart(2, '0'); // "8" -> "08"
                        for (let i = 0; i < elem.options.length; i++) {
                            if (elem.options[i].value === valorConCero) {
                                elem.selectedIndex = i;
                                return;
                            }
                        }
                        
                        // También buscar por nombre del mes
                        const meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        const nombreMes = meses[parseInt(valorStr)] || '';
                        
                        for (let i = 0; i < elem.options.length; i++) {
                            if (elem.options[i].text.includes(nombreMes) || elem.options[i].value.includes(nombreMes)) {
                                elem.selectedIndex = i;
                                return;
                            }
                        }
                    }
                    
                    // Buscar por texto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].text === valorStr) {
                            elem.selectedIndex = i;
                            return;
                        }
                    }
                }
            };
            
            // CAMPOS BÁSICOS
            setValueSafeP('#oficio_num_prop', data.folio);
            
            // Profesor propuesto
            setValueSafeP('#nombres_p', data.profesor_propuesto.nombres);
            setValueSafeP('#apellido_paterno_p', data.profesor_propuesto.paterno);
            setValueSafeP('#apellido_materno_p', data.profesor_propuesto.materno);
            setValueSafeP('#codigo_prof_p', data.profesor_propuesto.codigo);
        
            // CAMPOS DESPLEGABLES
            seleccionarOpcion('#dia_p', data.dia);
            seleccionarOpcion('#mes_p', data.mes);  // Ahora maneja mejor los números de mes
            
            // CAMPO CARGO ATC
            seleccionarOpcion('#cargo_atc', data.cargo);
            
            // CAMPOS NORMALES (inputs de texto) - CORREGIDO
            setValueSafeP('#crn_p', data.crn);
            setValueSafeP('#codigo_puesto_p', data.codigo_puesto);
            setValueSafeP('#descripcion_p', data.puesto);
            setValueSafeP('#clasificacion_p', data.clasificacion_p);
            setValueSafeP('#categoria', data.categoria);
            setValueSafeP('#carrera', data.carrera);
            setValueSafeP('#num_puesto', data.num_puesto);
            setValueSafeP('#hrs_semanales', data.horas_sem);
            setValueSafeP('#fecha_inicio', formatearFechaParaHTML(data.periodo_desde));
            setValueSafeP('#fecha_fin', formatearFechaParaHTML(data.periodo_hasta));
            setValueSafeP('#fecha', formatearFechaParaHTML(data.fecha));
            
            // Profesor Sustituto (actual)
            setValueSafeP('#nombres_sust', data.profesor_actual.nombres);
            setValueSafeP('#apellido_paterno_sust', data.profesor_actual.paterno);
            setValueSafeP('#apellido_materno_sust', data.profesor_actual.materno);
            setValueSafeP('#codigo_prof_sust', data.profesor_actual.codigo);
            setValueSafeP('#causa', data.motivo);
            
            // Seleccionar profesión para profesor propuesto
            const profesionPropuesto = modal.querySelector('#profesion_p');
            if (profesionPropuesto) {
                seleccionarOpcionProfesion(profesionPropuesto, data.profesor_propuesto.profession);
            }
            // Configurar archivo adjunto para visualización
            configurarArchivoAdjuntoVisualizacion(modal, data);
        break;
                
        case 'baja-propuesta':
            modal = document.getElementById('solicitud-modal-baja-propuesta');
            if (!modal) {
                return;
            }
            
            // Función auxiliar para establecer el valor de forma segura
            const setValueSafeBP = (selector, value) => {
                const elem = modal.querySelector(selector);
                if (elem) {
                    elem.value = value || '';
                }
            };
            
            // Función para seleccionar opción en desplegable
            const seleccionarOpcionBP = (selector, valor) => {
                const elem = modal.querySelector(selector);
                if (elem && valor !== undefined && valor !== null && valor !== '') {
                    // Buscar por valor exacto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].value === valor) {
                            elem.selectedIndex = i;
                            return;
                        }
                    }
                    
                    // Buscar por texto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].text === valor) {
                            elem.selectedIndex = i;
                            return;
                        }
                    }
                }
            };
            
            // DATOS GENERALES
            setValueSafeBP('#oficio_num_baja_prop', data.folio);
            setValueSafeBP('#fecha', formatearFechaParaHTML(data.fecha));
            
            // DATOS DE BAJA
            setValueSafeBP('#nombres_baja', data.profesor_actual.nombres);
            setValueSafeBP('#apellido_paterno_baja', data.profesor_actual.paterno);
            setValueSafeBP('#apellido_materno_baja', data.profesor_actual.materno);
            setValueSafeBP('#codigo_prof_baja', data.profesor_actual.codigo);
            
            // Profesión del profesor actual
            seleccionarOpcionBP('#profesion_baja', data.profesor_actual.profession);
            
            setValueSafeBP('#num_puesto_teoria_baja', data.num_puesto_teoria_baja);
            setValueSafeBP('#num_puesto_practica_baja', data.num_puesto_practica_baja);
            setValueSafeBP('#cve_materia_baja', data.clave);
            setValueSafeBP('#nombre_materia_baja', data.materia);
            setValueSafeBP('#crn_baja', data.crn);
            setValueSafeBP('#hrs_teoria_baja', data.hrs_teoria_baja);
            setValueSafeBP('#hrs_practica_baja', data.hrs_practica_baja);
            setValueSafeBP('#carrera_baja', data.carrera_baja);
            setValueSafeBP('#gdo_gpo_turno_baja', data.sec);
            setValueSafeBP('#tipo_asignacion_baja', data.tipo_asignacion_baja);
            setValueSafeBP('#sin_efectos_baja', formatearFechaParaHTML(data.fecha_efectos));
            setValueSafeBP('#motivo_baja', data.motivo);
            
            // DATOS DE PROPUESTA
            setValueSafeBP('#nombres_prop', data.profesor_propuesto.nombres);
            setValueSafeBP('#apellido_paterno_prop', data.profesor_propuesto.paterno);
            setValueSafeBP('#apellido_materno_prop', data.profesor_propuesto.materno);
            setValueSafeBP('#codigo_prof_prop', data.profesor_propuesto.codigo);
            setValueSafeBP('#hrs_teoria_prop', data.hrs_teoria_prop);
            setValueSafeBP('#hrs_practica_prop', data.hrs_practica_prop);
            setValueSafeBP('#num_puesto_teoria_prop', data.num_puesto_teoria_prop);
            setValueSafeBP('#num_puesto_practica_prop', data.num_puesto_practica_prop);
            
            // Interino/Temporal/Definitivo
            seleccionarOpcionBP('#inter_temp_def_prop', data.inter_temp_def);
            
            setValueSafeBP('#tipo_asignacion_prop', data.tipo_asignacion_prop);
            setValueSafeBP('#periodo_desde_prop', formatearFechaParaHTML(data.periodo_desde));
            setValueSafeBP('#periodo_hasta_prop', formatearFechaParaHTML(data.periodo_hasta));
            
            // Configurar archivo adjunto para visualización
            configurarArchivoAdjuntoVisualizacion(modal, data);
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
            
            // Ocultar botones de acción
            const botonesAccion = modal.querySelector('.contenedor-botones-baja') || 
                                modal.querySelector('.contenedor-botones-propuesta') ||
                                modal.querySelector('.contenedor-botones-baja-propuesta');
            if (botonesAccion) {
                botonesAccion.style.display = 'none';
            }
            
            // Añadir título de solo visualización
            const modalTitle = modal.querySelector('h2');
            if (modalTitle) {
                modalTitle.innerHTML = `Detalles de la solicitud de ${tipo} (Solo visualización)`;
            }
        }
    }

    function seleccionarOpcionProfesion(selectElement, profesionValue) {
        if (!selectElement || !profesionValue) return;
        
        // Primero buscar coincidencia exacta
        let encontrado = false;
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === profesionValue) {
                selectElement.selectedIndex = i;
                encontrado = true;
                break;
            }
        }
        
        // Si no se encuentra coincidencia exacta, buscar coincidencia parcial
        if (!encontrado) {
            for (let i = 0; i < selectElement.options.length; i++) {
                // Si la opción no está vacía y contiene parte del valor o viceversa
                if (selectElement.options[i].value && 
                    (selectElement.options[i].value.includes(profesionValue) || 
                    profesionValue.includes(selectElement.options[i].value))) {
                    selectElement.selectedIndex = i;
                    encontrado = true;
                    break;
                }
            }
        }
        
        // Si no se encuentra ninguna coincidencia, revisar por texto
        if (!encontrado) {
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].text === profesionValue) {
                    selectElement.selectedIndex = i;
                    break;
                }
            }
        }
    }

    // Función para generar HTML del archivo adjunto clickeable
    function generarHTMLArchivoAdjunto(nombreArchivo, rutaArchivo) {
        if (!nombreArchivo || !rutaArchivo) {
            return `
                <div class="archivo-no-disponible">
                    <i class="fa fa-exclamation-circle"></i>
                    <span>No se adjuntó ningún archivo a esta solicitud</span>
                </div>
            `;
        }
        
        // Determinar el tipo de archivo y su icono
        const extension = nombreArchivo.split('.').pop().toLowerCase();
        let icono = 'fa-file';
        let tipoArchivo = 'Archivo';
        
        switch (extension) {
            case 'pdf':
                icono = 'fa-file-pdf-o';
                tipoArchivo = 'PDF';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                icono = 'fa-file-image-o';
                tipoArchivo = 'Imagen';
                break;
            default:
                icono = 'fa-file';
                tipoArchivo = 'Archivo';
        }
        
        // Generar un ID único para este archivo
        const uniqueId = 'archivo-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        
        // Usar un ID único en lugar de onclick inline
        const html = `
            <div class="archivo-adjunto-preview" id="${uniqueId}" title="Clic para abrir/descargar" style="cursor: pointer;">
                <div class="archivo-icono">
                    <i class="fa ${icono}"></i>
                </div>
                <div class="archivo-info">
                    <div class="archivo-nombre">${nombreArchivo}</div>
                    <div class="archivo-tipo">${tipoArchivo}</div>
                </div>
                <div class="archivo-accion">
                    <i class="fa fa-external-link"></i>
                </div>
            </div>
        `;
        
        // Usar setTimeout para agregar el event listener después de que el HTML se inserte
        setTimeout(() => {
            const elemento = document.getElementById(uniqueId);
            if (elemento) {
                elemento.addEventListener('click', function() {
                    abrirArchivoAdjunto(rutaArchivo, nombreArchivo);
                });
            }
        }, 100);
        
        return html;
    }

    // Función para abrir/descargar archivo adjunto
    function abrirArchivoAdjunto(rutaArchivo, nombreArchivo) {
        try {
            // Crear un enlace temporal para descargar el archivo
            const enlace = document.createElement('a');
            enlace.href = rutaArchivo;
            enlace.target = '_blank'; // Abrir en nueva pestaña
            enlace.download = nombreArchivo; // Sugerir nombre de descarga
            
            // Agregar al DOM temporalmente y hacer clic
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
        } catch (error) {
            alert('Error al abrir el archivo. Por favor, inténtelo nuevamente.');
        }
    }

    // Modifica la función configurarArchivoAdjuntoVisualizacion en tu JavaScript
    function configurarArchivoAdjuntoVisualizacion(modal, data) {
        const nuevoArchivoSection = modal.querySelector('#nuevo-archivo-section');
        const existingArchivoSection = modal.querySelector('#existing-archivo-section');
        
        if (!nuevoArchivoSection || !existingArchivoSection) {
            return;
        }
        
        // Ocultar la sección de subida de archivos
        nuevoArchivoSection.style.display = 'none';
        
        // Mostrar la sección de archivos existentes
        existingArchivoSection.style.display = 'block';
        
        // Generar contenido del archivo adjunto
        const archivoContenido = modal.querySelector('#archivo-adjunto-contenido');
        if (archivoContenido) {
            // Usar los campos correctos que vienen del PHP
            const nombreArchivo = data.archivo_nombre;
            const rutaArchivo = data.archivo_ruta;
            const tieneArchivo = data.tiene_archivo;
            
            if (tieneArchivo && nombreArchivo && rutaArchivo) {
                archivoContenido.innerHTML = generarHTMLArchivoAdjunto(nombreArchivo, rutaArchivo);
            } else {
                archivoContenido.innerHTML = generarHTMLArchivoAdjunto(null, null);
            }
        }
    }

    // Función auxiliar para seleccionar una opción de motivo
    function seleccionarOpcionMotivo(selectElement, motivoValue) {
        if (!selectElement || !motivoValue) return;
        
        // Buscar coincidencia por valor y texto
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === motivoValue || 
                selectElement.options[i].text === motivoValue) {
                selectElement.selectedIndex = i;
                return;
            }
        }
        
        // Si no encuentra coincidencia exacta, buscar coincidencia parcial
        for (let i = 0; i < selectElement.options.length; i++) {
            if ((selectElement.options[i].value && selectElement.options[i].value.includes(motivoValue)) || 
                (selectElement.options[i].text && selectElement.options[i].text.includes(motivoValue))) {
                selectElement.selectedIndex = i;
                return;
            }
        }
    }

    // Función para convertir formatos de fecha
    function formatearFechaParaHTML(fechaString) {
        // Si la fecha viene en formato dd/mm/yyyy
        if (fechaString && fechaString.includes('/')) {
            const partes = fechaString.split('/');
            if (partes.length === 3) {
                return `${partes[2]}-${partes[1]}-${partes[0]}`; // Convierte a yyyy-mm-dd para input date
            }
        }
        return fechaString; // Si no está en el formato esperado, devuelve sin cambios
    }
});
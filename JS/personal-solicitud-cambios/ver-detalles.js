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
    
    // Función para limpiar y habilitar un modal para nueva solicitud
    function limpiarYHabilitarModal(modal) {
        if (!modal) return;
        
        console.log('Limpiando y habilitando modal para nueva solicitud');
        
        // Habilitar todos los campos
        modal.querySelectorAll('input, select, textarea').forEach(elem => {
            elem.removeAttribute('readonly');
            elem.removeAttribute('disabled');
            
            // Limpiar valores
            if (elem.tagName === 'SELECT') {
                elem.selectedIndex = 0;
            } else if (elem.type === 'checkbox' || elem.type === 'radio') {
                elem.checked = false;
            } else {
                elem.value = '';
            }
        });
        
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
    }
    
    // Exponer función globalmente para que pueda ser usada desde otros scripts
    window.limpiarYHabilitarModal = limpiarYHabilitarModal;
    
    // Función para cargar los datos de la solicitud desde el servidor
    function cargarDatosSolicitud(folio, tipo) {
        // Mostrar indicador de carga
        mostrarCargando();
        
        console.log('Cargando datos para folio:', folio, 'tipo:', tipo);
        
        // Determinar qué archivo PHP usar según el tipo
        let url;
        switch (tipo) {
            case 'baja':
                url = './functions/personal-solicitud-cambios/obtener_detalle_solicitud_baja.php';
                break;
            case 'propuesta':
                url = './functions/personal-solicitud-cambios/obtener_detalle_solicitud_propuesta.php';
                break;
            case 'baja-propuesta':
                url = './functions/personal-solicitud-cambios/obtener_detalle_solicitud_baja_propuesta.php';
                break;
            default:
                ocultarCargando();
                alert('Tipo de solicitud no reconocido');
                return;
        }
        
        // Realizar petición AJAX para obtener los datos
        fetch(`${url}?folio=${folio}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los datos: ' + response.status);
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
                try {
                    rellenarModal(data, tipo);
                    
                    // Abrir el modal correspondiente
                    abrirModal(tipo);
                } catch (error) {
                    console.error('Error al rellenar el modal:', error);
                    alert('Error al mostrar los datos: ' + error.message);
                }
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
        } else {
            console.error('Modal no encontrado:', modalId);
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
                console.log('Modal de baja encontrado:', !!modal);
                if (!modal) {
                    console.error('Modal de baja académica no encontrado');
                    return;
                }
            
            // Función auxiliar para establecer el valor de forma segura
            const setValueSafe = (selector, value) => {
                const elem = modal.querySelector(selector);
                console.log(`Elemento ${selector}:`, !!elem, 'Valor:', value);
                if (elem) {
                    elem.value = value || '';
                } else {
                    console.error(`Elemento no encontrado: ${selector}`);
                }
            };
            
            // Actualizar los campos con los IDs correctos según tu HTML
            setValueSafe('#crn', data.crn);
            setValueSafe('#descripcion', data.puesto);
            setValueSafe('#clasificacion', data.clasificacion_b);
            setValueSafe('#profesion', data.profesion);
            setValueSafe('#fecha_efectos', formatearFechaParaHTML(data.fecha));
            setValueSafe('#fecha', formatearFechaParaHTML(data.efecto));
            setValueSafe('#apellido_paterno', data.profesor_actual.paterno);
            setValueSafe('#apellido_materno', data.profesor_actual.materno);
            setValueSafe('#nombres', data.profesor_actual.nombres);
            setValueSafe('#codigo_prof', data.profesor_actual.codigo);
            setValueSafe('#motivo', data.motivo);
            setValueSafe('#oficio_num_baja', data.folio);
            
            // Establecer la profesión - Versión mejorada
            const profesionSelect = modal.querySelector('#profesion');
            const profesionValue = data.profesor_actual.profession || data.profession || '';

            console.log('Valor de profesión encontrado:', profesionValue);

            if (profesionSelect && profesionValue) {
                // Primero intentamos buscar una coincidencia exacta
                for (let i = 0; i < profesionSelect.options.length; i++) {
                    if (profesionSelect.options[i].value === profesionValue) {
                        profesionSelect.selectedIndex = i;
                        console.log('Profesión seleccionada (exacta):', profesionSelect.options[i].value);
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
                            console.log('Profesión seleccionada (parcial):', profesionSelect.options[i].value);
                            break;
                        }
                    }
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
        
            const setValueSafeP = (selector, value) => {
                const elem = modal.querySelector(selector);
                console.log(`Elemento ${selector}:`, !!elem, 'Valor:', value);
                if (elem) {
                    elem.value = value || '';
                } else {
                    console.error(`Elemento no encontrado: ${selector}`);
                }
            };
            
            // Función mejorada para seleccionar opción en desplegable
            const seleccionarOpcion = (selector, valor) => {
                const elem = modal.querySelector(selector);
                if (elem && valor !== undefined && valor !== null && valor !== '') {
                    console.log(`Intentando seleccionar ${selector} con valor:`, valor);
                    
                    // Convertir valor a string para comparaciones
                    const valorStr = valor.toString();
                    
                    // Buscar por valor exacto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].value === valorStr) {
                            elem.selectedIndex = i;
                            console.log(`${selector} seleccionado por valor:`, elem.options[i].text);
                            return;
                        }
                    }
                    
                    // Para meses, si viene un número, buscar también con cero adelante
                    if (selector === '#mes_p') {
                        const valorConCero = valorStr.padStart(2, '0'); // "8" -> "08"
                        for (let i = 0; i < elem.options.length; i++) {
                            if (elem.options[i].value === valorConCero) {
                                elem.selectedIndex = i;
                                console.log(`${selector} seleccionado con cero:`, elem.options[i].text);
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
                                console.log(`${selector} seleccionado por nombre:`, elem.options[i].text);
                                return;
                            }
                        }
                    }
                    
                    // Buscar por texto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].text === valorStr) {
                            elem.selectedIndex = i;
                            console.log(`${selector} seleccionado por texto:`, elem.options[i].text);
                            return;
                        }
                    }
                    
                    console.warn(`No se pudo seleccionar ${selector} con valor:`, valor);
                    console.log('Opciones disponibles:');
                    for (let i = 0; i < elem.options.length; i++) {
                        console.log(`  Opción ${i}: value="${elem.options[i].value}" text="${elem.options[i].text}"`);
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
            
        break;
                
        case 'baja-propuesta':
            modal = document.getElementById('solicitud-modal-baja-propuesta');
            console.log('Modal de baja-propuesta encontrado:', !!modal);
            if (!modal) {
                console.error('Modal de baja-propuesta no encontrado');
                return;
            }
            
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
            
            // Función para seleccionar opción en desplegable
            const seleccionarOpcionBP = (selector, valor) => {
                const elem = modal.querySelector(selector);
                if (elem && valor !== undefined && valor !== null && valor !== '') {
                    console.log(`Intentando seleccionar ${selector} con valor:`, valor);
                    
                    // Buscar por valor exacto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].value === valor) {
                            elem.selectedIndex = i;
                            console.log(`${selector} seleccionado:`, elem.options[i].text);
                            return;
                        }
                    }
                    
                    // Buscar por texto
                    for (let i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].text === valor) {
                            elem.selectedIndex = i;
                            console.log(`${selector} seleccionado por texto:`, elem.options[i].text);
                            return;
                        }
                    }
                    
                    console.warn(`No se pudo seleccionar ${selector} con valor:`, valor);
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
            
            console.log('=== DEBUG BAJA-PROPUESTA ===');
            console.log('Datos completos:', data);
            
            break;
    }
        
        // Desactivar todos los campos para que sean de solo lectura
        if (modal) {
            console.log('Desactivando campos del modal');
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
        
        console.log('Intentando seleccionar profesión:', profesionValue);
        
        // Primero buscar coincidencia exacta
        let encontrado = false;
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === profesionValue) {
                selectElement.selectedIndex = i;
                console.log('Profesión seleccionada (exacta):', selectElement.options[i].value);
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
                    console.log('Profesión seleccionada (parcial):', selectElement.options[i].value);
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
                    console.log('Profesión seleccionada (por texto):', selectElement.options[i].text);
                    break;
                }
            }
        }
    }
    
    // Función auxiliar para seleccionar una opción de motivo
    function seleccionarOpcionMotivo(selectElement, motivoValue) {
        if (!selectElement || !motivoValue) return;
        
        console.log('Intentando seleccionar motivo:', motivoValue);
        
        // Buscar coincidencia por valor y texto
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === motivoValue || 
                selectElement.options[i].text === motivoValue) {
                selectElement.selectedIndex = i;
                console.log('Motivo seleccionado:', selectElement.options[i].text);
                return;
            }
        }
        
        // Si no encuentra coincidencia exacta, buscar coincidencia parcial
        for (let i = 0; i < selectElement.options.length; i++) {
            if ((selectElement.options[i].value && selectElement.options[i].value.includes(motivoValue)) || 
                (selectElement.options[i].text && selectElement.options[i].text.includes(motivoValue))) {
                selectElement.selectedIndex = i;
                console.log('Motivo seleccionado (parcial):', selectElement.options[i].text);
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
// Sistema de filtros para solicitudes
class FiltrosSolicitudes {
    constructor() {
        this.inicializar();
        this.contadorTotal = 0;
        this.contadorVisible = 0;
    }

    inicializar() {
        // Obtener elementos DOM
        this.elementos = {
            filtroTipo: document.getElementById('filtro-tipo'),
            filtroFecha: document.getElementById('filtro-fecha'),
            filtroEstado: document.getElementById('filtro-estado'),
            filtroDepartamento: document.getElementById('filtro-departamento'),
            btnAplicar: document.getElementById('aplicar-filtros'),
            btnLimpiar: document.getElementById('limpiar-filtros'),
            contador: document.getElementById('contador-solicitudes'),
            contenedorSolicitudes: document.querySelector('.solicitud-contenedor-principal')
        };

        // Verificar que los elementos existan
        if (!this.elementos.contenedorSolicitudes) {
            console.warn('Contenedor de solicitudes no encontrado');
            return;
        }

        // Agregar event listeners
        this.agregarEventListeners();
        
        // Contar solicitudes iniciales
        this.actualizarContador();
    }

    agregarEventListeners() {
        // Botón aplicar filtros
        if (this.elementos.btnAplicar) {
            this.elementos.btnAplicar.addEventListener('click', () => this.aplicarFiltros());
        }

        // Botón limpiar filtros
        if (this.elementos.btnLimpiar) {
            this.elementos.btnLimpiar.addEventListener('click', () => this.limpiarFiltros());
        }

        // Aplicar filtros en tiempo real al cambiar selects
        Object.values(this.elementos).forEach(elemento => {
            if (elemento && elemento.tagName === 'SELECT') {
                elemento.addEventListener('change', () => this.aplicarFiltros());
            }
        });
    }

    debugDepartamento() {
        console.log('=== DEBUG DEPARTAMENTO ===');
        const solicitudes = this.obtenerTodasLasSolicitudes();
        
        solicitudes.forEach((solicitud, index) => {
            console.log(`Solicitud ${index}:`, {
                departamento: solicitud.departamento,
                departamentoId: solicitud.departamentoId,
                elementoHTML: solicitud.element.outerHTML.substring(0, 200) + '...'
            });
        });
        
        const select = this.elementos.filtroDepartamento;
        if (select) {
            console.log('Opciones del select:');
            Array.from(select.options).forEach(option => {
                console.log(`Valor: "${option.value}" - Texto: "${option.text}"`);
            });
        }
    }

    aplicarFiltros() {
        console.log('Aplicando filtros...');
        this.debugDepartamento(); // Agregar esta línea

        const filtros = this.obtenerFiltrosActivos();
        console.log('Filtros activos:', filtros);
        const solicitudes = this.obtenerTodasLasSolicitudes();
        
        let contadorVisible = 0;

        solicitudes.forEach(solicitud => {
            const esVisible = this.cumpleFiltros(solicitud, filtros);
            
            if (esVisible) {
                solicitud.element.classList.remove('solicitud-oculta');
                solicitud.element.classList.add('solicitud-visible');
                contadorVisible++;
            } else {
                solicitud.element.classList.add('solicitud-oculta');
                solicitud.element.classList.remove('solicitud-visible');
            }
        });

        this.contadorVisible = contadorVisible;
        this.actualizarContador();
        this.actualizarEstadoFiltros(filtros);
    }

    obtenerFiltrosActivos() {
        return {
            tipo: this.elementos.filtroTipo?.value || '',
            fecha: this.elementos.filtroFecha?.value || '',
            estado: this.elementos.filtroEstado?.value || '',
            departamento: this.elementos.filtroDepartamento?.value || ''
        };
    }

    obtenerTodasLasSolicitudes() {
        const elementos = this.elementos.contenedorSolicitudes.querySelectorAll('.solicitud-completa');
        const solicitudes = [];

        elementos.forEach(elemento => {
            const datos = this.extraerDatosSolicitud(elemento);
            if (datos) {
                solicitudes.push({
                    element: elemento,
                    ...datos
                });
            }
        });

        this.contadorTotal = solicitudes.length;
        return solicitudes;
    }

    extraerDatosSolicitud(elemento) {
        try {
            // Extraer tipo de solicitud
            const tipoElement = elemento.querySelector('.tipo-solicitud, .titulo-solicitud h4');
            const tipo = tipoElement ? tipoElement.textContent.trim() : '';
    
            // Extraer fecha
            const fechaElement = elemento.querySelector('.fecha-solicitud, .info-fecha');
            let fecha = '';
            if (fechaElement) {
                const fechaTexto = fechaElement.textContent;
                const fechaMatch = fechaTexto.match(/\d{4}-\d{2}-\d{2}|\d{2}\/\d{2}\/\d{4}/);
                fecha = fechaMatch ? fechaMatch[0] : '';
            }
    
            // Extraer estado
            const estadoElement = elemento.querySelector('.estado-solicitud, .badge-estado, [class*="estado"]');
            const estado = estadoElement ? estadoElement.textContent.trim() : '';
    
            // Extraer departamento
            const departamentoElement = elemento.querySelector('.departamento-solicitud, .info-departamento');
            const departamento = departamentoElement ? departamentoElement.textContent.trim() : '';
    
            // Extraer ID de departamento - MEJORADO
            let departamentoId = '';
            
            // Método 1: desde data-departamento-id del elemento principal
            if (elemento.hasAttribute('data-departamento-id')) {
                departamentoId = elemento.getAttribute('data-departamento-id');
            }
            
            // Método 2: buscar en elementos hijos
            if (!departamentoId) {
                const deptIdElement = elemento.querySelector('[data-departamento-id]');
                if (deptIdElement) {
                    departamentoId = deptIdElement.dataset.departamentoId;
                }
            }
    
            console.log('Datos extraídos:', {
                tipo,
                fecha,
                estado,
                departamento,
                departamentoId
            });
    
            return {
                tipo,
                fecha,
                estado,
                departamento,
                departamentoId
            };
        } catch (error) {
            console.warn('Error extrayendo datos de solicitud:', error);
            return null;
        }
    }

    cumpleFiltros(solicitud, filtros) {
        // Filtro por tipo - MANEJO ESPECÍFICO PARA CADA TIPO
        if (filtros.tipo && filtros.tipo !== '') {
            const tipoSolicitud = solicitud.tipo.trim();
            const tipoFiltro = filtros.tipo.trim();
            
            console.log('Comparando tipos específicos:', {
                solicitudTipo: `"${tipoSolicitud}"`,
                filtroTipo: `"${tipoFiltro}"`
            });
            
            let coincide = false;
            
            switch (tipoFiltro) {
                case 'Solicitud de baja':
                    coincide = (tipoSolicitud === 'Solicitud de baja');
                    break;
                case 'Solicitud de propuesta':
                    coincide = (tipoSolicitud === 'Solicitud de propuesta');
                    break;
                case 'Solicitud de baja-propuesta':
                    coincide = (tipoSolicitud === 'Solicitud de baja-propuesta');
                    break;
                default:
                    coincide = false;
            }
            
            if (!coincide) {
                return false;
            }
        }
    
        // Filtro por estado
        if (filtros.estado && solicitud.estado !== filtros.estado) {
            return false;
        }
    
        // Filtro por departamento
        if (filtros.departamento && filtros.departamento !== '') {
            const filtroStr = String(filtros.departamento);
            const solicitudIdStr = String(solicitud.departamentoId);
            
            if (filtroStr !== solicitudIdStr) {
                return false;
            }
        }
    
        // Filtro por fecha
        if (filtros.fecha && !this.cumpleFiltroFecha(solicitud.fecha, filtros.fecha)) {
            return false;
        }
    
        return true;
    }

    cumpleFiltroFecha(fechaSolicitud, filtroFecha) {
        if (!fechaSolicitud || !filtroFecha) return true;

        try {
            // Convertir fecha de solicitud a objeto Date
            let fecha;
            if (fechaSolicitud.includes('/')) {
                // Formato DD/MM/YYYY
                const partes = fechaSolicitud.split('/');
                fecha = new Date(partes[2], partes[1] - 1, partes[0]);
            } else {
                // Formato YYYY-MM-DD
                fecha = new Date(fechaSolicitud);
            }

            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            fecha.setHours(0, 0, 0, 0);

            const diferenciaDias = Math.floor((hoy - fecha) / (1000 * 60 * 60 * 24));

            switch (filtroFecha) {
                case 'hoy':
                    return diferenciaDias === 0;
                case '7dias':
                    return diferenciaDias >= 0 && diferenciaDias <= 7;
                case '1mes':
                    return diferenciaDias >= 0 && diferenciaDias <= 30;
                case '3meses':
                    return diferenciaDias >= 0 && diferenciaDias <= 90;
                default:
                    return true;
            }
        } catch (error) {
            console.warn('Error procesando fecha:', fechaSolicitud, error);
            return true;
        }
    }

    limpiarFiltros() {
        // Limpiar todos los selects
        Object.values(this.elementos).forEach(elemento => {
            if (elemento && elemento.tagName === 'SELECT') {
                elemento.value = '';
            }
        });

        // Mostrar todas las solicitudes
        const solicitudes = this.elementos.contenedorSolicitudes.querySelectorAll('.solicitud-completa');
        solicitudes.forEach(solicitud => {
            solicitud.classList.remove('solicitud-oculta');
            solicitud.classList.add('solicitud-visible');
        });

        this.contadorVisible = this.contadorTotal;
        this.actualizarContador();
        this.actualizarEstadoFiltros({});
    }

    actualizarContador() {
        if (!this.elementos.contador) return;

        const texto = this.contadorVisible === this.contadorTotal 
            ? `Mostrando todas las solicitudes (${this.contadorTotal})`
            : `Mostrando ${this.contadorVisible} de ${this.contadorTotal} solicitudes`;

        this.elementos.contador.textContent = texto;
    }

    actualizarEstadoFiltros(filtros) {
        const hayFiltrosActivos = Object.values(filtros).some(valor => valor !== '');
        const body = document.body;
        
        if (hayFiltrosActivos) {
            body.classList.add('filtros-activos');
        } else {
            body.classList.remove('filtros-activos');
        }
    }

    // Método público para reinicializar después de cargar nuevas solicitudes
    reinicializar() {
        this.actualizarContador();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.filtrosSolicitudes = new FiltrosSolicitudes();
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('Elementos encontrados:');
    console.log('Contenedor:', document.querySelector('.solicitud-contenedor-principal'));
    console.log('Solicitudes:', document.querySelectorAll('.solicitud-completa').length);
    console.log('Filtros:', {
        tipo: document.getElementById('filtro-tipo'),
        fecha: document.getElementById('filtro-fecha'),
        estado: document.getElementById('filtro-estado'),
        departamento: document.getElementById('filtro-departamento')
    });
});
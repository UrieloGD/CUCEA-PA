document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const checkList = document.getElementById('list1');
    const tabla = document.querySelector('.profesores-table');
    const filas = tabla.querySelectorAll('tbody tr');
    let departamentosSeleccionados = new Set();

    // Mapeo de departamentos (basado en los checkboxes del HTML original)
    const departamentoMapeo = {
        'Estudios Regionales': [
            'ESTUDIOS REGIONALES',
            'Estudios Regionales'],
        'Finanzas': [
            'FINANZAS',
            'Finanzas'
        ],
        'Ciencia Sociales': [
            'CERI/CIENCIAS SOCIALES',
            'CIENCIAS SOCIALES',
            'Ciencias Sociales', 
            'CIENCIAS SOCIALES/POLITICAS PUBLICAS'
        ],
        'Pale': [
            'ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA',
            'PALE',
            'Programa de Aprendizaje de Lengua Extranjera'
        ],
        'Economía': [
            'ECONOMIA',
            'Economía',
            'Economia'    
        ],
        'Recursos Humanos': [
            'RECURSOS HUMANOS',
            'Recursos Humanos',
            'RECURSOS_HUMANOS'
        ],
        'Métodos Cuantitativos': [
            'METODOS CUANTITATIVOS',
            'Métodos Cuantitativos',
            'Metodos Cuantitativos'
        ],
        'Políticas Públicas': [
            'POLITICAS PUBLICAS',
            'Políticas Públicas',
            'Politicas Publicas'
        ],
        'Administración': [
            'Administracion',
            'ADMINISTRACION',
            'Administración'
        ],
        'Auditoría': [
            'Auditoria',
            'AUDITORIA',
            'Auditoría',
            'SECRETARIA ADMINISTRATIVA/AUDITORIA'
        ],
        'Mercadotecnia': [
            'MERCADOTECNIA',
            'Mercadotecnia',
            'MERCADOTECNIA Y NEGOCIOS INTERNACIONALES'
        ],
        'Impuestos': [
            'IMPUESTOS',
            'Impuestos'
        ],
        'Sistemas de Información': [
            'SISTEMAS DE INFORMACION',
            'Sistemas de Información',
            'Sistemas de Informacion'
        ],
        'Turismo': [
            'TURISMO',
            'Turismo',
            'Turismo R. y S.'
        ],
        'Contabilidad': [
            'CONTABILIDAD',
            'Contabilidad'
        ]
    };

    // Función para verificar si un departamento coincide
    function departamentoCoincide(departamentoFila, departamentosSeleccionados) {
        for (let depSeleccionado of departamentosSeleccionados) {
            const mapeoOrigen = departamentoMapeo[depSeleccionado] || [depSeleccionado];
            if (mapeoOrigen.some(dep => departamentoFila.includes(dep))) {
                return true;
            }
        }
        return false;
    }

    // Función para actualizar la tabla según los departamentos seleccionados
    function actualizarTabla() {
        filas.forEach(fila => {
            const departamentoCell = fila.querySelector('td:nth-child(4)').textContent.trim();
            
            if (departamentosSeleccionados.size === 0) {
                // Si no hay departamentos seleccionados, mostrar todos
                fila.style.display = '';
            } else {
                // Mostrar solo si el departamento coincide
                fila.style.display = departamentoCoincide(departamentoCell, departamentosSeleccionados) ? '' : 'none';
            }
        });

        // Actualizar el mensaje si no hay resultados
        const filasVisibles = Array.from(filas).filter(fila => fila.style.display !== 'none');
        const tbody = tabla.querySelector('tbody');
        const mensajeNoResultados = tbody.querySelector('.no-resultados');
        
        if (filasVisibles.length === 0) {
            if (!mensajeNoResultados) {
                const tr = document.createElement('tr');
                tr.className = 'no-resultados';
                tr.innerHTML = '<td colspan="5">No se encontraron profesores para los departamentos seleccionados</td>';
                tbody.appendChild(tr);
            }
        } else if (mensajeNoResultados) {
            mensajeNoResultados.remove();
        }
    }

    // Manejar los checkboxes
    checkList.querySelectorAll('.items input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const departamento = this.parentElement.textContent.trim();
            
            if (this.checked) {
                departamentosSeleccionados.add(departamento);
            } else {
                departamentosSeleccionados.delete(departamento);
            }
            
            actualizarTabla();
        });
    });

    // Resto del código anterior de manejo de desplegable (igual que en la versión previa)
    checkList.querySelector('.anchor').addEventListener('click', function(e) {
        e.stopPropagation();
        checkList.classList.toggle('visible');
    });

    checkList.querySelector('.items').addEventListener('click', function(e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function(e) {
        if (!checkList.contains(e.target)) {
            checkList.classList.remove('visible');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            checkList.classList.remove('visible');
            departamentosSeleccionados.clear();
            actualizarTabla();
        }
    });

    // Agregar contador de seleccionados al título del desplegable
    function actualizarTituloDropdown() {
        const anchor = checkList.querySelector('.anchor');
        const texto = departamentosSeleccionados.size > 0 
            ? `Departamentos: (${departamentosSeleccionados.size} seleccionados)` 
            : 'Departamentos:';
        anchor.textContent = texto;
    }

    // Actualizar el título cuando cambie la selección
    checkList.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarTituloDropdown);
    });
});
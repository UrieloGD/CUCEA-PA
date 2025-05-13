document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando barra de búsqueda...');
    
    // Verificar que el elemento input-buscador exista
    const inputBuscador = document.getElementById('input-buscador');
    if (!inputBuscador) {
        console.error('Error: No se encontró el elemento con ID "input-buscador"');
        return;
    }
    
    // Verificar que la tabla global esté definida
    if (typeof window.tabulatorTable === 'undefined') {
        console.error('Error: La tabla Tabulator no está disponible globalmente como "tabulatorTable"');
        return;
    }
    
    // Función para realizar la búsqueda
    function realizarBusqueda() {
        const terminoBusqueda = inputBuscador.value.toLowerCase().trim();
        console.log('Realizando búsqueda con término:', terminoBusqueda);
        
        if (terminoBusqueda === '') {
            // Si el campo está vacío, limpiar todos los filtros
            window.tabulatorTable.clearFilter();
            return;
        }
        
        // Filtrar en todos los campos de texto
        window.tabulatorTable.setFilter(multiFieldFilter, {value: terminoBusqueda});
    }
    
    // Función de filtro personalizada que busca en todos los campos
    function multiFieldFilter(data, filterParams) {
        const value = filterParams.value.toLowerCase();
        
        // Recorrer todos los campos del registro
        for (let key in data) {
            // Solo buscar en campos de tipo string
            if (data[key] !== null && typeof data[key] === 'string') {
                if (data[key].toLowerCase().includes(value)) {
                    return true; // Devuelve true si encuentra coincidencia
                }
            }
            // Convertir números o booleanos a string para buscar también en ellos
            else if (data[key] !== null && typeof data[key] !== 'object') {
                if (String(data[key]).toLowerCase().includes(value)) {
                    return true;
                }
            }
        }
        return false; // Si no hay coincidencias
    }
    
    // Escuchar eventos de entrada en el buscador (con debounce para mejor rendimiento)
    let timeoutId;
    inputBuscador.addEventListener('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(realizarBusqueda, 300); // Esperar 300ms para no sobrecargarse con cada tecla
    });
    
    // Buscar al presionar Enter
    inputBuscador.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            realizarBusqueda();
        }
    });
    
    // Agregar funcionalidad al botón/icono de búsqueda
    const iconoBuscador = document.getElementById('icono-buscador');
    if (iconoBuscador) {
        iconoBuscador.addEventListener('click', function() {
            realizarBusqueda();
        });
    }
    
    // Función para limpiar la búsqueda (opcional - puedes agregar un botón X)
    window.limpiarBusqueda = function() {
        if (inputBuscador) {
            inputBuscador.value = '';
            window.tabulatorTable.clearFilter();
        }
    };
    
    console.log('Barra de búsqueda inicializada correctamente');
});
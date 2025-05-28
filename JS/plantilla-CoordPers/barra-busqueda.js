/**
 * Módulo para manejar la funcionalidad de búsqueda en la tabla Tabulator
 */
function setupTableSearch(table, searchInputId = 'input-buscador') {
    // Variables de estado
    let searchTerm = "";
    let searchTimeout = null;
    
    // Elementos DOM
    const searchInput = document.getElementById(searchInputId);
    
    if (!searchInput) {
        console.warn(`No se encontró el elemento de búsqueda con ID '${searchInputId}'`);
        return;
    }
    
    // Configurar eventos
    searchInput.addEventListener('input', handleSearchInput);
    
    // Manejar el evento de input
    function handleSearchInput(e) {
        searchTerm = e.target.value;
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            executeSearch();
        }, 500);
    }
    
    // Ejecutar la búsqueda
    function executeSearch() {
        const activeElement = document.activeElement;
        
        table.setPage(1)
            .then(() => table.replaceData())
            .then(() => {
                if (activeElement) {
                    activeElement.focus();
                    if (activeElement.setSelectionRange && activeElement.value) {
                        const len = activeElement.value.length;
                        activeElement.setSelectionRange(len, len);
                    }
                }
            });
    }
    
    // API pública
    return {
        getSearchTerm: () => searchTerm,
        setSearchTerm: (term) => {
            searchTerm = term;
            searchInput.value = term;
        },
        destroy: () => {
            searchInput.removeEventListener('input', handleSearchInput);
        }
    };
}

// Hacer disponible globalmente
window.setupTableSearch = setupTableSearch;
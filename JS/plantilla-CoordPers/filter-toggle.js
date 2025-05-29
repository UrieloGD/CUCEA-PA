// === FUNCIONALIDAD MEJORADA PARA MOSTRAR/OCULTAR FILTROS ===
function setupFilterToggle(table, initialVisibility = false) {
  console.log('[setupFilterToggle] Tabla recibida:', table);
  console.log('[setupFilterToggle] Tipo de tabla:', typeof table);
  console.log('[setupFilterToggle] Constructor:', table?.constructor?.name);
  
  const filterIcon = document.getElementById('icono-filtro');
  let filtersVisible = initialVisibility; // Los filtros están visibles por defecto
  
  console.log('[setupFilterToggle] Elemento icono-filtro encontrado:', filterIcon);
  console.log('[setupFilterToggle] Estado inicial filtersVisible:', filtersVisible);
  
  if (!filterIcon) {
    console.error('[setupFilterToggle] ❌ No se encontró el elemento con ID "icono-filtro"');
    return;
  }

  // Verificar que el DOM esté listo
  function waitForDOMReady(callback, maxAttempts = 10, currentAttempt = 0) {
    const headerFilters = document.querySelectorAll('.tabulator-header-filter');
    const headerCells = document.querySelectorAll('.tabulator-col');
    if (headerFilters.length > 0 && headerCells.length > 0) {
      console.log('[waitForDOMReady] ✅ DOM listo, ejecutando callback');
      callback();
    } else if (currentAttempt < maxAttempts) {
      console.log(`[waitForDOMReady] ⏳ DOM no listo, reintentando en 250ms...`);
      setTimeout(() => waitForDOMReady(callback, maxAttempts, currentAttempt + 1), 250);
    } else {
      console.error('[waitForDOMReady] ❌ Timeout: DOM no se cargó después de todos los intentos');
    }
  }

  // Función para ocultar todos los filtros de encabezado
  function hideHeaderFilters() {
    console.log('[hideHeaderFilters] ===== INICIANDO OCULTACIÓN =====');
    
    const headerFilters = document.querySelectorAll('.tabulator-header-filter');
    console.log('[hideHeaderFilters] Filtros encontrados:', headerFilters.length);
    
    if (headerFilters.length === 0) {
      console.warn('[hideHeaderFilters] ⚠️ No se encontraron filtros de encabezado');
      
      // Búsquedas alternativas para debugging
      const alternatives = [
        document.querySelectorAll('[tabulator-field] .tabulator-header-filter'),
        document.querySelectorAll('.tabulator-header .tabulator-header-filter'),
        document.querySelectorAll('.tabulator-col .tabulator-header-filter')
      ];
      
      alternatives.forEach((alt, index) => {
        console.log(`[hideHeaderFilters] Búsqueda alternativa ${index + 1}:`, alt.length);
      });
      
      return;
    }
    
    let hiddenCount = 0;
    headerFilters.forEach((filter, index) => {
      console.log(`[hideHeaderFilters] Procesando filtro ${index + 1}:`, filter);
      console.log(`[hideHeaderFilters] Estado previo del filtro ${index + 1}:`, {
        display: filter.style.display,
        visibility: filter.style.visibility,
        height: filter.style.height
      });
      
      filter.style.display = 'none';
      filter.style.visibility = 'hidden';
      filter.style.height = '0px';
      filter.style.overflow = 'hidden';
      hiddenCount++;
      
      console.log(`[hideHeaderFilters] ✅ Filtro ${index + 1} ocultado`);
    });
    
    console.log(`[hideHeaderFilters] Total filtros ocultados: ${hiddenCount}`);
    
    // Ajustar altura de las celdas de encabezado
    const headerCells = document.querySelectorAll('.tabulator-col');
    console.log('[hideHeaderFilters] Celdas de encabezado encontradas:', headerCells.length);
    
    let adjustedCells = 0;
    headerCells.forEach((cell, index) => {
      const filterElement = cell.querySelector('.tabulator-header-filter');
      if (filterElement) {
        const originalHeight = cell.style.height;
        cell.style.height = 'auto';
        cell.setAttribute('data-original-height', originalHeight);
        adjustedCells++;
      }
    });
    
    console.log(`[hideHeaderFilters] Celdas ajustadas: ${adjustedCells}`);
    
    filtersVisible = false;
    
    // Actualizar tooltip y icono
    filterIcon.setAttribute('data-tooltip', 'Mostrar filtros');
    filterIcon.classList.add('filters-hidden');
    
    console.log('[hideHeaderFilters] ✅ Estado actualizado - filtersVisible:', filtersVisible);
    console.log('[hideHeaderFilters] ===== OCULTACIÓN COMPLETADA =====');
  }

  // Función para mostrar todos los filtros de encabezado
  function showHeaderFilters() {
    console.log('[showHeaderFilters] ===== INICIANDO VISUALIZACIÓN =====');
    
    const headerFilters = document.querySelectorAll('.tabulator-header-filter');
    console.log('[showHeaderFilters] Filtros encontrados:', headerFilters.length);
    
    if (headerFilters.length === 0) {
      console.warn('[showHeaderFilters] ⚠️ No se encontraron filtros de encabezado');
      return;
    }
    
    let shownCount = 0;
    headerFilters.forEach((filter, index) => {
      console.log(`[showHeaderFilters] Procesando filtro ${index + 1}:`, filter);
      console.log(`[showHeaderFilters] Estado previo del filtro ${index + 1}:`, {
        display: filter.style.display,
        visibility: filter.style.visibility,
        height: filter.style.height
      });
      
      filter.style.display = 'block';
      filter.style.visibility = 'visible';
      filter.style.height = 'auto';
      filter.style.overflow = 'visible';
      shownCount++;
    });
    
    console.log(`[showHeaderFilters] Total filtros mostrados: ${shownCount}`);
    
    // Restaurar altura de las celdas de encabezado
    const headerCells = document.querySelectorAll('.tabulator-col');
    console.log('[showHeaderFilters] Celdas de encabezado encontradas:', headerCells.length);
    
    let restoredCells = 0;
    headerCells.forEach((cell, index) => {
      const filterElement = cell.querySelector('.tabulator-header-filter');
      if (filterElement) {
        const originalHeight = cell.getAttribute('data-original-height');
        if (originalHeight) {
          cell.style.height = originalHeight;
        } else {
          cell.style.height = 'auto';
        }
        restoredCells++;
      }
    });
    
    console.log(`[showHeaderFilters] Celdas restauradas: ${restoredCells}`);
    
    filtersVisible = true;
    
    // Actualizar tooltip e icono
    filterIcon.setAttribute('data-tooltip', 'Ocultar filtros');
    filterIcon.classList.remove('filters-hidden');
    
    console.log('[showHeaderFilters] ✅ Estado actualizado - filtersVisible:', filtersVisible);
    console.log('[showHeaderFilters] ===== VISUALIZACIÓN COMPLETADA =====');
  }

  // Función para alternar visibilidad de filtros
  function toggleHeaderFilters() {
    console.log('[toggleHeaderFilters] ===== ALTERNANDO FILTROS =====');
    console.log('[toggleHeaderFilters] Estado actual filtersVisible:', filtersVisible);
    
    if (filtersVisible) {
      console.log('[toggleHeaderFilters] 👁️ Ocultando filtros...');
      hideHeaderFilters();
    } else {
      console.log('[toggleHeaderFilters] 👁️ Mostrando filtros...');
      showHeaderFilters();
    }
    
    console.log('[toggleHeaderFilters] ===== ALTERNANCIA COMPLETADA =====');
  }

  // Event listener para el botón de filtro
  function setupEventListener() {
    console.log('[setupEventListener] Configurando event listener...');
    
    // Remover listeners previos si existen
    const oldListener = filterIcon.getAttribute('data-listener-attached');
    if (oldListener === 'true') {
      console.log('[setupEventListener] Removiendo listener previo...');
      filterIcon.removeEventListener('click', window.filterToggleHandler);
    }
    
    // Crear nuevo handler
    window.filterToggleHandler = function(e) {
      console.log('[Event Listener] ===== CLICK DETECTADO =====');
      console.log('[Event Listener] Event:', e);
      console.log('[Event Listener] Target:', e.target);
      console.log('[Event Listener] Current target:', e.currentTarget);
      
      e.preventDefault();
      e.stopPropagation();
      
      console.log('[Event Listener] Ejecutando toggle...');
      toggleHeaderFilters();
    };
    
    filterIcon.addEventListener('click', window.filterToggleHandler);
    filterIcon.setAttribute('data-listener-attached', 'true');
    
    console.log('[setupEventListener] ✅ Event listener configurado');
  }

  // Función para aplicar estilos CSS dinámicamente
  function addFilterToggleStyles() {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'filter-toggle-styles';
    styleSheet.textContent = `
      /* Estilos para el icono de filtro */
      #icono-filtro {
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
      }
      
      #icono-filtro:hover {
        transform: scale(1.1);
        opacity: 0.8;
      }
      
      #icono-filtro.filters-hidden {
        opacity: 0.6;
      }
      
      #icono-filtro.filters-hidden i {
        color: #999 !important;
      }
      
      /* Transiciones suaves para los filtros */
      .tabulator-header-filter {
        transition: all 0.3s ease-in-out;
      }
      
      /* Estilos para cuando los filtros están ocultos */
      .tabulator-header-filter[style*="display: none"] {
        opacity: 0;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden;
        border: none !important;
      }
      
      /* Mejorar la visualización del estado del botón */
      #icono-filtro.filters-hidden::after {
        font-size: 0.8em;
        color: #999;
      }
    `;
    document.head.appendChild(styleSheet);
  }

  // Función para manejar la reconstrucción de la tabla
  function handleTableEvents() {
    console.log('[handleTableEvents] Configurando eventos de tabla...');
    
    if (!table || typeof table.on !== 'function') {
      console.error('[handleTableEvents] ❌ Tabla no válida o no tiene método .on()');
      return;
    }
    
    // Eventos de renderizado
    const events = ['renderComplete', 'dataLoaded', 'pageLoaded'];
    
    events.forEach(eventName => {
      table.on(eventName, function() {
        console.log(`[Event ${eventName}] Tabla ${eventName.toLowerCase()}`);
        if (!filtersVisible) {
          console.log(`[Event ${eventName}] Filtros deben estar ocultos, aplicando ocultación...`);
          setTimeout(() => {
            console.log(`[Event ${eventName}] Ejecutando hideHeaderFilters después de timeout`);
            hideHeaderFilters();
          }, 100);
        }
      });
    });
    
    console.log('[handleTableEvents] ✅ Eventos configurados:', events);
  }

  // Función de debugging para inspeccionar el DOM
  function debugDOMState() {
    console.log('=== DEBUG DOM STATE ===');
    console.log('Elemento tabla:', document.getElementById('tabla-datos-tabulator'));
    console.log('Filtros de encabezado:', document.querySelectorAll('.tabulator-header-filter'));
    console.log('Estructura de tabla:', document.querySelector('.tabulator'));
    console.log('Icono filtro:', document.getElementById('icono-filtro'));
    console.log('========================');
  }

  // Función principal de inicialización
  function initializeFilterToggle() {
    console.log('[initializeFilterToggle] ===== INICIALIZANDO SISTEMA =====');
    
    // Debugging inicial
    debugDOMState();
    
    // Aplicar estilos
    addFilterToggleStyles();
    
    // Configurar eventos de tabla
    handleTableEvents();
    
    // Configurar event listener
    setupEventListener();
    
    // Asignar funciones globales
    console.log('[initializeFilterToggle] Asignando funciones globales...');
    
    window.toggleTableFilters = function() {
      console.log('[Global] toggleTableFilters llamado');
      toggleHeaderFilters();
    };
    
    window.hideTableFilters = function() {
      console.log('[Global] hideTableFilters llamado');
      hideHeaderFilters();
    };
    
    window.showTableFilters = function() {
      console.log('[Global] showTableFilters llamado');
      showHeaderFilters();
    };
    
    // Función de debugging accesible globalmente
    window.debugFilterToggle = function() {
      console.log('=== DEBUG FILTER TOGGLE STATE ===');
      console.log('filtersVisible:', filtersVisible);
      console.log('filterIcon:', filterIcon);
      console.log('table:', table);
      console.log('Funciones globales disponibles:', {
        toggleTableFilters: typeof window.toggleTableFilters,
        hideTableFilters: typeof window.hideTableFilters,
        showTableFilters: typeof window.showTableFilters
      });
      debugDOMState();
      console.log('==================================');
    };
    
    console.log('[initializeFilterToggle] ✅ Funciones globales asignadas');
    console.log('[initializeFilterToggle] Verificando asignación:');
    console.log('- toggleTableFilters:', typeof window.toggleTableFilters);
    console.log('- hideTableFilters:', typeof window.hideTableFilters);
    console.log('- showTableFilters:', typeof window.showTableFilters);
    console.log('- debugFilterToggle:', typeof window.debugFilterToggle);
    
    console.log('[setupFilterToggle] ✅ Sistema de toggle de filtros inicializado correctamente');
    console.log('[setupFilterToggle] ===== CONFIGURACIÓN COMPLETADA =====');
  }

  // Esperar a que el DOM esté listo antes de inicializar
  waitForDOMReady(initializeFilterToggle);
}
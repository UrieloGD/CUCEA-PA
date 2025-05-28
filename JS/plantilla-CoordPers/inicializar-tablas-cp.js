// === FUNCIONALIDAD MEJORADA PARA MOSTRAR/OCULTAR FILTROS ===
function setupFilterToggle(table) {
  console.log('[setupFilterToggle] ===== INICIANDO CONFIGURACIÓN =====');
  console.log('[setupFilterToggle] Tabla recibida:', table);
  console.log('[setupFilterToggle] Tipo de tabla:', typeof table);
  console.log('[setupFilterToggle] Constructor:', table?.constructor?.name);
  
  const filterIcon = document.getElementById('icono-filtro');
  let filtersVisible = true; // Los filtros están visibles por defecto
  
  console.log('[setupFilterToggle] Elemento icono-filtro encontrado:', filterIcon);
  console.log('[setupFilterToggle] Estado inicial filtersVisible:', filtersVisible);
  
  if (!filterIcon) {
    console.error('[setupFilterToggle] ❌ No se encontró el elemento con ID "icono-filtro"');
    return;
  }

  // Verificar que el DOM esté listo
  function waitForDOMReady(callback, maxAttempts = 10, currentAttempt = 0) {
    console.log(`[waitForDOMReady] Intento ${currentAttempt + 1}/${maxAttempts}`);
    
    const headerFilters = document.querySelectorAll('.tabulator-header-filter');
    const headerCells = document.querySelectorAll('.tabulator-col');
    
    console.log(`[waitForDOMReady] Filtros encontrados: ${headerFilters.length}`);
    console.log(`[waitForDOMReady] Celdas encontradas: ${headerCells.length}`);
    
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
        console.log(`[hideHeaderFilters] Ajustando altura de celda ${index + 1}`);
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
      
      console.log(`[showHeaderFilters] ✅ Filtro ${index + 1} mostrado`);
    });
    
    console.log(`[showHeaderFilters] Total filtros mostrados: ${shownCount}`);
    
    // Restaurar altura de las celdas de encabezado
    const headerCells = document.querySelectorAll('.tabulator-col');
    console.log('[showHeaderFilters] Celdas de encabezado encontradas:', headerCells.length);
    
    let restoredCells = 0;
    headerCells.forEach((cell, index) => {
      const filterElement = cell.querySelector('.tabulator-header-filter');
      if (filterElement) {
        console.log(`[showHeaderFilters] Restaurando altura de celda ${index + 1}`);
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
    
    // Actualizar tooltip y icono
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
    console.log('[addFilterToggleStyles] Aplicando estilos CSS...');
    
    // Verificar si ya existe el estilo
    if (document.getElementById('filter-toggle-styles')) {
      console.log('[addFilterToggleStyles] ℹ️ Estilos ya existen, omitiendo...');
      return;
    }

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
        content: " (ocultos)";
        font-size: 0.8em;
        color: #999;
      }
    `;
    
    document.head.appendChild(styleSheet);
    console.log('[addFilterToggleStyles] ✅ Estilos CSS aplicados correctamente');
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
    console.log('Celdas de encabezado:', document.querySelectorAll('.tabulator-col'));
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

// === FUNCIÓN DE TESTING MANUAL ===
function manualFilterToggle() {
  console.log('=== MANUAL TOGGLE TEST ===');
  
  const table = window.tabulatorTable || window.table;
  console.log('Tabla disponible:', table);
  
  const filterIcon = document.getElementById('icono-filtro');
  console.log('Icono filtro:', filterIcon);
  
  const headerFilters = document.querySelectorAll('.tabulator-header-filter');
  console.log('Filtros de encabezado:', headerFilters);
  
  console.log('Función setupFilterToggle:', typeof setupFilterToggle);
  
  if (table && filterIcon && headerFilters.length > 0) {
    console.log('✅ Condiciones cumplidas, ejecutando setupFilterToggle...');
    setupFilterToggle(table);
  } else {
    console.log('❌ Condiciones no cumplidas');
  }
  
  console.log('Funciones globales después del setup:');
  console.log('toggleTableFilters:', typeof window.toggleTableFilters);
  console.log('hideTableFilters:', typeof window.hideTableFilters);
  console.log('showTableFilters:', typeof window.showTableFilters);
  
  console.log('===========================');
}

// Hacer disponible la función de debug manual
window.manualFilterToggle = manualFilterToggle;


document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM completamente cargado');
  
  // === CONFIGURACIÓN INICIAL ===
  const saveIcon = document.getElementById("icono-guardar");
  const undoIcon = document.getElementById("icono-deshacer");
  const userRole = document.getElementById('user-role').value;
  const isEditable = userRole == 3 || userRole == 0;
  
  console.log('[InicializarTablas] Iconos en DOM:', { saveIcon, undoIcon });
  console.log('Rol del usuario:', userRole, '¿Es editable?', isEditable);

  // Función para actualizar el checkbox maestro
  function updateHeaderCheckbox() {
    const headerCheckbox = document.getElementById('header-checkbox');
    if (!headerCheckbox) return;
    
    const selectedRows = table.getSelectedRows();
    const totalRows = table.getRows().length;
    
    if (selectedRows.length === 0) {
      headerCheckbox.checked = false;
      headerCheckbox.indeterminate = false;
    } else if (selectedRows.length === totalRows) {
      headerCheckbox.checked = true;
      headerCheckbox.indeterminate = false;
    } else {
      headerCheckbox.checked = false;
      headerCheckbox.indeterminate = true;
    }
  }

  // Menú para cabeceras de columna (toggle de visibilidad)
  var headerMenu = function() {
  var menu = [];
  var columns = this.getColumns();

  for(let column of columns){
    const fieldName = column.getField();
    if(fieldName === "checkbox" || fieldName === "ID") continue; // Prohibido ocultar checkbox e ID

    // Crear icono usando Font Awesome
    let icon = document.createElement("i");
    icon.classList.add("fas");
    icon.classList.add(column.isVisible() ? "fa-check-square" : "fa-square");

    // Construir etiqueta
    let label = document.createElement("span");
    let title = document.createElement("span");

    title.textContent = " " + column.getDefinition().title;

    label.appendChild(icon);
    label.appendChild(title);

    // Crear elemento de menú
    menu.push({
      label: label,
      action: function(e) {
        e.stopPropagation(); // Prevenir cierre del menú
        column.toggle(); // Alternar visibilidad

        // Actualizar icono
        if(column.isVisible()) {
          icon.classList.remove("fa-square");
          icon.classList.add("fa-check-square");
        } else {
          icon.classList.remove("fa-check-square");
          icon.classList.add("fa-square");
        }
      }
    });
  }

  return menu;
};

  // Función para eliminar fila individual
  function eliminarFilaIndividual(row) {
    Swal.fire({
      title: "¿Eliminar registro?",
      text: "Esta acción no se puede deshacer",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar"
    }).then((result) => {
      if (result.isConfirmed) {
        const id = row.getData().ID;
        fetch("./functions/coord-personal-plantilla/eliminar-registros-coord.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: "ids=" + id
        })
        .then(response => response.text())
        .then(() => {
          row.delete();
          Swal.fire("Eliminado", "El registro ha sido eliminado", "success");
        })
        .catch(error => {
          console.error("Error:", error);
          Swal.fire("Error", "No se pudo eliminar el registro", "error");
        });
      }
    });
  }

  // === DEFINICIÓN DE COLUMNAS ===
  const columns = [
    {
      title: "",
      field: "checkbox",
      formatter: function(cell) {
        // Checkbox personalizado que refleja el estado real
        const row = cell.getRow();
        return `<input type="checkbox" ${row.isSelected() ? "checked" : ""}>`;
      },
      titleFormatter: function() {
        // Checkbox maestro personalizado
        return '<input type="checkbox" id="header-checkbox">';
      },
      hozAlign: "center",
      headerSort: false,
      width: 50,
      frozen: true,
      cellClick: function(e, cell) {
        // Activa/desactiva solo con click en el checkbox
        if (e.target.tagName === "INPUT") {
          const row = cell.getRow();
          
          // Sincronizar checkbox con selección de Tabulator
          if (e.target.checked) {
            row.select();
          } else {
            row.deselect();
          }
          
          // Actualizar checkbox maestro
          updateHeaderCheckbox();
          e.stopPropagation();
        }
      },
      headerClick: function(e, column) {
        // Maneja checkbox maestro
        if (e.target.tagName === "INPUT") {
          const checkbox = e.target;
          if (checkbox.checked) {
            table.selectRow(); // Selecciona todas las filas
          } else {
            table.deselectRow(); // Deselecciona todas las filas
          }
          // Los checkboxes individuales se actualizarán automáticamente
          // gracias al evento rowSelectionChanged
        }
      },
    },
    {
      title: "ID", 
      field: "ID", 
      editor: false,
      variableHeight: true, 
      frozen: true,
    },
    {title: "DATOS", field: "Datos", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar en Datos"},
    {title: "CODIGO", field: "Codigo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Código"},
    {title: "PATERNO", field: "Paterno", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Apellido Paterno"},
    {title: "MATERNO", field: "Materno", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Apellido Materno"},
    {title: "NOMBRES", field: "Nombres", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Nombres"},
    {title: "NOMBRE COMPLETO", field: "Nombre_completo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Nombre Completo"},
    {title: "DEPARTAMENTO", field: "Departamento", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Departamento"},
    {title: "CATEGORIA ACTUAL", field: "Categoria_actual", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Categoría Actual"},
    {title: "CATEGORIA ACTUAL", field: "Categoria_actual_dos", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Categoría Actual (2)"},
    {title: "HORAS FRENTE A GRUPO", field: "Horas_frente_grupo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Horas Frente a Grupo"},
    {title: "DIVISION", field: "Division", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar División"},
    {title: "TIPO DE PLAZA", field: "Tipo_plaza", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Tipo de Plaza"},
    {title: "CAT.ACT.", field: "Cat_act", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Cat. Actual"},
    {title: "CARGA HORARIA", field: "Carga_horaria", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Carga Horaria"},
    {title: "HORAS DEFINITIVAS", field: "Horas_definitivas", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Horas Definitivas"},
    {title: "UDG VIRTUAL CIT OTRO CENTRO", field: "Udg_virtual_CIT", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar UDG Virtual/CIT"},
    {title: "HORARIO", field: "Horario", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Horario"},
    {title: "TURNO", field: "Turno", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Turno"},
    {title: "INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION", field: "Investigacion_nombramiento_cambio_funcion", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Investigador"},
    {title: "S.N.I.", field: "SNI", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar SNI"},
    {title: "SNI DESDE", field: "SNI_desde", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar SNI Desde"},
    {title: "CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR", field: "Cambio_dedicacion", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Cambio de Dedicación"},
    {title: "TELEFONO PARTICULAR", field: "Telefono_particular", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Teléfono Particular"},
    {title: "TELEFONO OFICINA O CELULAR", field: "Telefono_oficina", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Teléfono Oficina/Celular"},
    {title: "DOMICILIO", field: "Domicilio", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Domicilio"},
    {title: "COLONIA", field: "Colonia", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Colonia"},
    {title: "C.P.", field: "CP", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Código Postal"},
    {title: "CIUDAD", field: "Ciudad", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Ciudad"},
    {title: "ESTADO", field: "Estado", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Estado"},
    {title: "CORREO ELECTRONICO", field: "Correo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Correo Electrónico"},
    {title: "CORREOS OFICIALES", field: "Correos_oficiales", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Correos Oficiales"},
    {title: "NO. AFIL. I.M.S.S.", field: "No_imss", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar N° IMSS"},
    {title: "C.U.R.P.", field: "CURP", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar CURP"},
    {title: "RFC", field: "RFC", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar RFC"},
    {title: "LUGAR DE NACIMIENTO", field: "Lugar_nacimiento", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Lugar de Nacimiento"},
    {title: "ESTADO CIVIL", field: "Estado_civil", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Estado Civil"},
    {title: "TIPO DE SANGRE", field: "Tipo_sangre", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Tipo de Sangre"},
    {title: "FECHA NAC.", field: "Fecha_nacimiento", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Fecha de Nacimiento"},
    {title: "EDAD", field: "Edad", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Edad"},
    {title: "NACIONALIDAD", field: "Nacionalidad", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Nacionalidad"},
    {title: "ULTIMO GRADO", field: "Ultimo_grado", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Último Grado"},
    {title: "PROGRAMA", field: "Programa", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Programa"},
    {title: "NIVEL", field: "Nivel", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Nivel"},
    {title: "INSTITUCION", field: "Institucion", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Institución"},
    {title: "ESTADO/PAIS", field: "Estado_pais", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Estado/País"},
    {title: "AÑO", field: "Año", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Año"},
    {title: "GDO EXP", field: "Gdo_exp", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Grado Expedido"},
    {title: "OTRO GRADO", field: "Otro_grado", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Grado"},
    {title: "PROGRAMA", field: "Otro_programa", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Programa"},
    {title: "NIVEL", field: "Otro_nivel", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Nivel"},
    {title: "INSTITUCION", field: "Otro_institucion", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otra Institución"},
    {title: "ESTADO/PAIS", field: "Otro_estado_pais", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Estado/País"},
    {title: "AÑO", field: "Otro_año", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Año"},
    {title: "GDO EXP", field: "Otro_gdo_exp", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Grado Expedido"},
    {title: "OTRO GRADO", field: "Otro_grado_alternativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Grado Alternativo"},
    {title: "PROGRAMA", field: "Otro_programa_alternativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Programa Alternativo"},
    {title: "NIVEL", field: "Otro_nivel_altenrativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Nivel Alternativo"},
    {title: "INSTITUCION", field: "Otro_institucion_alternativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otra Institución Alternativa"},
    {title: "ESTADO/PAIS", field: "Otro_estado_pais_alternativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Estado/País Alternativo"},
    {title: "AÑO", field: "Otro_año_alternativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Año Alternativo"},
    {title: "GDO EXP", field: "Otro_gdo_exp_alternativo", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Otro Grado Expedido Alternativo"},
    {title: "PROESDE 24-25", field: "Proesde_24_25", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar PROESDE 24-25"},
    {title: "A PARTIR DE", field: "A_partir_de", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar 'A Partir De'"},
    {title: "FECHA DE INGRESO", field: "Fecha_ingreso", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Fecha de Ingreso"},
    {title: "ANTIGÜEDAD", field: "Antiguedad", editor: isEditable ? "input" : false, variableHeight: true, headerMenu: headerMenu, headerFilter: "input", headerFilterPlaceholder: "Buscar Antigüedad"}
  ];
  
  // Función para generar URL AJAX con parámetros
  function generateAjaxURL(url, config, params) {
    let ajaxURL = url + "?";
    
    // Parámetros de paginación
    if (params.size === true) {
        ajaxURL += "all=true&";
    } else {
        ajaxURL += `page=${params.page}&size=${params.size}&`;
    }
    
    // Parámetros de ordenación
    if (params.sort && params.sort.length) {
        ajaxURL += `sort=${params.sort[0].field}&dir=${params.sort[0].dir}&`;
    }
    
    // Parámetro de búsqueda global (obtener del módulo de búsqueda si existe)
    const searchTerm = window.tableSearchInstance?.getSearchTerm();
    if (searchTerm) {
        ajaxURL += `search=${encodeURIComponent(searchTerm)}&`;
    }
    
    // Limpiar URL
    ajaxURL = ajaxURL.replace(/[&?]$/, "");
    console.log("URL generada para AJAX:", ajaxURL);
    return ajaxURL;
}

  // Función para aplicar clases de Bulma
  function applyBulmaClasses() {
    // Botones de paginación
    document.querySelectorAll('.tabulator-paginator button').forEach(button => {
      button.classList.add('button', 'is-small');
    });
    
    // Selector de tamaño de página
    const pageSizeSelector = document.querySelector('.tabulator-page-size');
    if (pageSizeSelector && !pageSizeSelector.parentElement.classList.contains('select')) {
      const wrapper = document.createElement('div');
      wrapper.classList.add('select', 'is-small');
      pageSizeSelector.parentNode.insertBefore(wrapper, pageSizeSelector);
      wrapper.appendChild(pageSizeSelector);
    }
    
    // Inputs de filtro
    document.querySelectorAll('.tabulator-header-filter input').forEach(input => {
      input.classList.add('input', 'is-small');
    });
  }

  // Función para ajustar anchos de columna
  function adjustColumnWidths() {
    if (!table || !table.getColumns().length) return;
    
    console.log('Ajustando anchos de columnas basados en contenido...');
    
    table.columnManager.columns.forEach(column => {
      if (column.getField() === "checkbox") return;
      
      const field = column.getField();
      const title = column.getDefinition().title;
      const headerLength = title ? title.length : 0;
      
      let maxContentLength = 0;
      const visibleData = table.getData();
      
      visibleData.forEach(row => {
        const cellValue = row[field];
        if (cellValue) {
          maxContentLength = Math.max(maxContentLength, String(cellValue).length);
        }
      });
      
      // Calcular ancho (8px por carácter + padding)
      const charWidth = 8;
      const padding = 20;
      let calculatedWidth = Math.max(headerLength, maxContentLength) * charWidth + padding;
      
      // Establecer límites
      calculatedWidth = Math.max(calculatedWidth, 80);
      calculatedWidth = Math.min(calculatedWidth, 300);
      
      column.setWidth(calculatedWidth);
    });
  }

  // === MOSTRAR LOADER ===
  Swal.fire({
    title: 'Cargando datos...',
    html: 'Por favor espere mientras se procesan los datos',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => Swal.showLoading()
  });

  // === VERIFICAR ELEMENTO DE LA TABLA ===
  const tableElement = document.getElementById('tabla-datos-tabulator');
  if (!tableElement) {
    console.error('Error: No se encontró el elemento con ID "tabla-datos-tabulator"');
    Swal.fire('Error', 'No se encontró el contenedor de la tabla', 'error');
    return;
  }

  // === INICIALIZACIÓN DE TABULATOR ===
  const table = new Tabulator("#tabla-datos-tabulator", {
    // Configuración AJAX
    ajaxURL: './functions/coord-personal-plantilla/datos-tabla-cp.php',
    ajaxConfig: "GET",
    ajaxParams: {},
    ajaxURLGenerator: generateAjaxURL,
    
    // Configuración de columnas y layout
    columns: columns,
    layout: "fitData",
    autoColumns: false,
    responsiveLayout: false,
    
    history: true,

    // Configuración de paginación
    pagination: true,
    paginationMode: "remote",
    paginationSize: 50,
    paginationSizeSelector: [20, 50, 100, 300, true],
    paginationCounter: "rows",
    paginationDataReceived: {
      "last_page": "last_page",
      "data": "data",
      "total": "total"
    },
    paginationDataSent: {
      "page": "page",
      "size": "size"
    },
    
    // Configuración general
    sortMode: "remote",
    movableColumns: true,
    height: "620px",
    placeholder: "No hay datos disponibles",
    
    // ====== CONFIGURACIÓN DE SELECCIÓN MEJORADA ======
    // Habilitar selección de filas (para checkboxes)
    selectable: true,
    
    // Configuración del portapapeles y selección de celdas
    selectableRange: true, // ¡Importante! Habilitar selección de rangos
    selectableRangeColumns: true, // Permitir selección por columnas
    selectableRangeRows: true, // Permitir selección por filas
    selectableRangeClearCells: true, // Permitir limpiar celdas
    
    // Configuración del portapapeles (Ctrl+C, Ctrl+V)
    clipboard: true,
    clipboardCopyStyled: false,
    clipboardCopyConfig: {
      rowHeaders: false,
      columnHeaders: false,
    },
    clipboardCopyRowRange: "range",
    clipboardPasteParser: "range",
    clipboardPasteAction: "range",
    
    // Configuración de edición
    editTriggerEvent: "dblclick",
    
    // Configuración de scroll y columnas
    horizontalScroll: true,
    resizableColumns: true,
    columnMinWidth: 80,
    columnCalcLayout: "fitData",
    
    // Localización
    langs: {
      "es": {
        "pagination": {
          "first": "Primero",
          "first_title": "Primera página",
          "last": "Último",
          "last_title": "Última página",
          "prev": "Anterior",
          "prev_title": "Página anterior", 
          "next": "Siguiente",
          "next_title": "Página siguiente",
          "all": "Todo",
          "page_size": "Registros por página"
        },
        "headerFilters": {
          "default": "Filtrar columna...",
          "columns": {}
        }
      }
    },
    locale: "es",
    theme: "bulma",
    virtualDom: true,
    renderVertical: "virtual",
    
    // ====== EVENTOS MEJORADOS ======
    rowSelectionChanged: function(data, rows) {
      console.log("Filas seleccionadas:", rows.length);
      
      // Actualizar checkboxes individuales
      table.getRows().forEach(row => {
        const checkbox = row.getElement().querySelector('input[type="checkbox"]');
        if (checkbox) {
          checkbox.checked = row.isSelected();
        }
      });
      
      // Actualizar checkbox maestro
      updateHeaderCheckbox();
    },
    
    // Controlar el comportamiento del click en filas
    rowClick: function(e, row) {
      // Solo permitir selección si se hace click en el checkbox
      // Para el resto de la fila, permitir selección de celdas
      const isCheckboxColumn = e.target.closest('[tabulator-field="checkbox"]');
      
      if (isCheckboxColumn) {
        // Si es la columna checkbox, no hacer nada aquí
        // (se maneja en cellClick)
        return false;
      }
      
      // Para otras columnas, permitir comportamiento normal
      // (selección de celdas, edición, etc.)
      return true;
    },
    
    // Evento para manejar selección de celdas
    cellClick: function(e, cell) {
      const column = cell.getColumn();
      
      // Si es la columna checkbox, manejar selección de fila
      if (column.getField() === "checkbox") {
        // Ya se maneja en la definición de la columna
        return;
      }
      
      // Para otras columnas, permitir selección normal de celdas
      // No interferir con la funcionalidad de clipboard/range selection
    },
    
    dataLoaded: function(data) {
      console.log('Datos cargados correctamente:', data);
      console.log('[dataLoaded] Cantidad de registros:', data ? data.length : 0);

      Swal.close();
      adjustColumnWidths();
      
      // Configurar scroll horizontal
      const tableHolder = document.querySelector('.tabulator-tableHolder');
      if (tableHolder) {
        tableHolder.style.overflowX = 'auto';
      }
      
      applyBulmaClasses();
      
      // Respaldar datos originales
      if (window.editManager) {
        window.editManager.backupOriginalData();
      }

      // === LLAMAR A setupFilterToggle AQUÍ ===
      console.log('[dataLoaded] Iniciando configuración de filtros...');
      console.log('[dataLoaded] Tabla disponible:', this);
      
      // Usar setTimeout para asegurar que el DOM esté completamente renderizado
      setupFilterToggle
    },
    
    ajaxError: function(xhr, textStatus, errorThrown) {
      console.error('Error en la carga AJAX:', { xhr, textStatus, errorThrown });
      Swal.fire('Error', 'No se pudieron cargar los datos. Detalles: ' + errorThrown, 'error');
    },
    
    tableBuilt: function() {
      console.log('Tabla construida');
      applyBulmaClasses();
    },
    
    renderStarted: function() {
      console.log('Renderizado iniciado');
    },
    
    renderComplete: function() {
      console.log('Renderizado completado');
      adjustColumnWidths();
      applyBulmaClasses();
    },
    
    columnResized: function(column) {
      console.log(`Columna ${column.getField()} redimensionada a ${column.getWidth()}px`);
    },
    
    pageLoaded: function(pageno) {
      console.log(`Página ${pageno} cargada correctamente`);
    },
    
    pageSizeChanged: function(size) {
      console.log(`Tamaño de página cambiado a: ${size}`);
    }
  });

  console.log('Tabulator inicializado:', table);

  setupUndoRedoEvents(table);

  // === VARIABLES GLOBALES ===
  window.tabulatorTable = table;
  window.table = table;
  tableSearchInstance = setupTableSearch(table);
  window.tableSearchInstance = setupTableSearch(table); // Instancia global para búsqueda
  return table;
});

// Función para configurar los eventos de Undo/Redo
function setupUndoRedoEvents(table) {
  // Ocultar los botones de undo/redo
  const undoButton = document.getElementById("history-undo");
  const redoButton = document.getElementById("history-redo");
  if (undoButton) undoButton.style.display = "none";
  if (redoButton) redoButton.style.display = "none";

  // Enfocar tabla después de editar y en eventos clave
  function focusTable() {
    setTimeout(() => {
      const tableElement = table.getElement();
      if (document.activeElement !== tableElement) {
        tableElement.focus({ preventScroll: true });
      }
    }, 10);
  }

  // Eventos de edición y clicks
  table.on("cellEdited", focusTable);
  table.on("rowClick", focusTable);
  table.on("cellClick", focusTable);

  // Manejador de teclado robusto
  document.addEventListener("keydown", function(e) {
    if (e.ctrlKey && !e.altKey && !e.metaKey) {
      const key = e.key?.toLowerCase(); // Manejo seguro para navegadores antiguos
      
      // Undo: Ctrl + Z (sin Shift)
      if (key === "z" && !e.shiftKey) {
        e.preventDefault();
        console.log("Undo accionado");
        table.undo();
        return;
      }
      
      // Redo: Ctrl + Shift + Z o Ctrl + Y
      if ((key === "y") || (key === "z" && e.shiftKey)) {
        e.preventDefault();
        console.log("Redo accionado");
        table.redo();
        return;
      }
    }
  });

  // Feedback de historial en consola
  table.on("historyUndo", () => console.log("Undo realizado - Historial:", table.getHistoryUndoSize()));
  table.on("historyRedo", () => console.log("Redo realizado - Historial:", table.getHistoryRedoSize()));
}

// === FUNCIÓN DE ELIMINACIÓN DE REGISTROS MEJORADA ===
function eliminarRegistrosSeleccionados() {
  // Obtener referencia a la tabla Tabulator
  const table = window.tabulatorTable;
  
  if (!table) {
    console.error('No se encontró la referencia a la tabla Tabulator');
    Swal.fire({
      title: "Error",
      text: "No se pudo acceder a la tabla. Recarga la página e intenta de nuevo.",
      icon: "error",
    });
    return;
  }
  
  // Obtener las filas seleccionadas usando la API de Tabulator
  const selectedRows = table.getSelectedRows();
  const ids = selectedRows.map(row => row.getData().ID);
  
  console.log("Filas seleccionadas:", selectedRows.length);
  console.log("IDs a eliminar:", ids);
  
  if (ids.length === 0) {
    Swal.fire({
      title: "Advertencia",
      text: "No hay registros seleccionados. Seleccione al menos un registro para eliminar.",
      icon: "warning",
    });
    return;
  }

  // Mensaje dinámico basado en la cantidad
  const mensaje = ids.length === 1 
    ? "Se eliminará 1 registro" 
    : `Se eliminarán ${ids.length} registros`;

  Swal.fire({
    title: "¿Desea continuar?",
    text: mensaje,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loading mientras se procesa
      Swal.fire({
        title: 'Eliminando registros...',
        html: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
      });

      var xhr = new XMLHttpRequest();
      xhr.open(
        "POST",
        "./functions/coord-personal-plantilla/eliminar-registros-coord.php",
        true
      );
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            try {
              // Intentar parsear respuesta JSON si es aplicable
              const response = xhr.responseText;
              console.log('Respuesta del servidor:', response);
              
              const mensajeExito = ids.length === 1 
                ? "1 registro eliminado correctamente." 
                : `${ids.length} registros eliminados correctamente.`;

              Swal.fire({
                title: "¡Éxito!",
                text: mensajeExito,
                icon: "success",
              }).then(() => {
                // Limpiar selecciones
                table.deselectRow();
                
                // Recargar los datos en la tabla sin recargar toda la página
                table.setPage(1).then(() => table.replaceData());
              });
            } catch (error) {
              console.error('Error al procesar respuesta:', error);
              Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la respuesta del servidor.",
                icon: "error",
              });
            }
          } else {
            console.error('Error HTTP:', xhr.status, xhr.statusText);
            Swal.fire({
              title: "Error",
              text: `Error del servidor: ${xhr.status} - ${xhr.statusText}`,
              icon: "error",
            });
          }
        }
      };

      xhr.onerror = function() {
        console.error('Error de red al eliminar registros');
        Swal.fire({
          title: "Error de conexión",
          text: "No se pudo conectar con el servidor. Verifica tu conexión a internet.",
          icon: "error",
        });
      };

      xhr.send("ids=" + encodeURIComponent(ids.join(",")));
    }
  });
}
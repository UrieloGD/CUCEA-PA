/**
 * Tabulator Drag-Fill Extension
 * Implementa la funcionalidad de arrastrar y rellenar (drag-fill) similar a Excel en tablas Tabulator
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, configurando inicialización para drag-fill...');
    
    // Esperar a que la tabla Tabulator esté completamente inicializada
    // Utilizar un enfoque más seguro para detectar cuando la tabla está lista
    const checkInterval = setInterval(function() {
      const tabulatorEl = document.getElementById('tabla-datos-tabulator');
      if (tabulatorEl && (tabulatorEl._tabulator || window.tabulatorTable)) {
        console.log('Tabla Tabulator encontrada, inicializando drag-fill...');
        window.tabulatorTable = tabulatorEl._tabulator || window.tabulatorTable;
        clearInterval(checkInterval);
        initDragFill();
      }
    }, 500); // Verificar cada medio segundo
  
    // Establecer un tiempo máximo de espera (15 segundos)
    setTimeout(function() {
      clearInterval(checkInterval);
      console.log('Tiempo de espera agotado para inicializar drag-fill');
    }, 15000);
  });
  
  function initDragFill() {
    // Referencia a la tabla Tabulator
    const table = window.tabulatorTable;
    if (!table) {
      console.error('Tabla Tabulator no encontrada');
      return;
    }
  
    console.log('Inicializando drag-fill extension para Tabulator...');
    
    try {
      // Verificar la versión de Tabulator si está disponible
      const version = typeof Tabulator !== 'undefined' ? 
                     (Tabulator.prototype.version || "desconocida") : 
                     "desconocida";
      console.log('Versión de Tabulator detectada:', version);
    } catch (e) {
      console.log('No se pudo determinar la versión de Tabulator');
    }
  
    // Variables para el seguimiento del estado
    let isDragging = false;
    let startCell = null;
    let startCellValue = null;
    let currentHoveredCell = null;
    let fillHandle = null;
    let selectedCells = [];
    let userRole = document.getElementById('user-role')?.value;
    const isEditable = userRole == 3 || userRole == 0;
  
    // Si el usuario no tiene permisos de edición, no inicializar la funcionalidad
    if (!isEditable) {
      console.log('Usuario sin permisos de edición, funcionalidad de drag-fill desactivada');
      return;
    }
  
    // Crear el indicador de arrastre (fill handle)
    function createFillHandle() {
      const handle = document.createElement('div');
      handle.className = 'tabulator-fill-handle';
      handle.style.position = 'absolute';
      handle.style.width = '10px';
      handle.style.height = '10px';
      handle.style.backgroundColor = '#1665C0';
      handle.style.border = '1px solid #fff';
      handle.style.cursor = 'crosshair';
      handle.style.zIndex = '1000';
      handle.style.borderRadius = '2px';
      handle.style.pointerEvents = 'auto'; // Asegura que reciba eventos de mouse
      return handle;
    }
  
    // Agregar estilos necesarios
    function addStyles() {
      // Eliminar estilos anteriores si existen
      const oldStyle = document.getElementById('tabulator-drag-fill-styles');
      if (oldStyle) oldStyle.remove();
      
      const styleElement = document.createElement('style');
      styleElement.id = 'tabulator-drag-fill-styles';
      styleElement.textContent = `
        .tabulator-cell.selected-for-fill {
          background-color: rgba(22, 101, 192, 0.2) !important;
        }
        .tabulator-cell.drag-origin {
          background-color: rgba(22, 101, 192, 0.3) !important;
        }
        .tabulator-cell {
          position: relative;
        }
        .tabulator-fill-handle {
          position: absolute !important;
          bottom: 0 !important;
          right: 0 !important;
          transform: translate(5px, 5px);
          transition: background-color 0.2s ease;
        }
        .tabulator-fill-handle:hover, .tabulator-fill-handle:active {
          background-color: #2196F3;
          transform: translate(5px, 5px) scale(1.2);
        }
      `;
      document.head.appendChild(styleElement);
    }
  
    // Posicionar el indicador de arrastre en la esquina inferior derecha de la celda
    function positionFillHandle(cell) {
      if (!cell || !fillHandle) return;
      
      const cellElement = cell.getElement();
      if (!cellElement) {
        console.warn('No se pudo obtener el elemento de celda para posicionar el fill handle');
        return;
      }
      
      try {
        // Remover el handle si ya está en el DOM
        if (fillHandle.parentNode) {
          fillHandle.parentNode.removeChild(fillHandle);
        }
        
        // Adjuntar directamente al elemento de la celda para posicionamiento relativo
        cellElement.style.position = 'relative';
        cellElement.appendChild(fillHandle);
        
        // Asegurar que el handle es visible
        fillHandle.style.display = 'block';
        fillHandle.style.position = 'absolute';
        fillHandle.style.bottom = '0';
        fillHandle.style.right = '0';
        fillHandle.style.transform = 'translate(5px, 5px)';
        
        // Añadir clase para identificar la celda activa
        cellElement.classList.add('tabulator-cell-with-handle');
      } catch (error) {
        console.error('Error al posicionar el fill handle:', error);
      }
    }
  
    // Ocultar el indicador de arrastre
    function hideFillHandle() {
      if (fillHandle) {
        if (fillHandle.parentNode) {
          fillHandle.parentNode.removeChild(fillHandle);
        }
        
        // Remover cualquier clase de celda con handle
        document.querySelectorAll('.tabulator-cell-with-handle').forEach(cell => {
          cell.classList.remove('tabulator-cell-with-handle');
        });
      }
    }
  
    // Obtener la celda en la posición actual del mouse
    function getCellFromEvent(e) {
      try {
        const elements = document.elementsFromPoint(e.clientX, e.clientY);
        for (const element of elements) {
          if (element.classList.contains('tabulator-cell')) {
            const rowElement = element.closest('.tabulator-row');
            if (!rowElement) continue;
            
            const rowIndex = rowElement.getAttribute('data-index');
            if (rowIndex === null) continue;
            
            const columnField = element.getAttribute('data-field');
            if (!columnField) continue;
            
            try {
              // Intentar obtener la fila y columna usando los métodos de Tabulator
              let row, column;
              
              // Método 1: Intento directo con getRow
              try {
                row = table.getRow(rowIndex);
              } catch (e) {
                // Método 2: Buscar en todas las filas visibles
                const visibleRows = table.getRows();
                row = visibleRows.find(r => r.getIndex() == rowIndex);
              }
              
              // Obtener la columna
              try {
                column = table.getColumn(columnField);
              } catch (e) {
                // Alternativa: obtener todas las columnas y encontrar la que coincide
                const columns = table.getColumns();
                column = columns.find(c => c.getField() === columnField);
              }
              
              if (row && column) {
                return { row, column, element };
              }
            } catch (rowColError) {
              console.warn('Error al obtener fila o columna:', rowColError);
            }
          }
        }
      } catch (error) {
        console.error('Error en getCellFromEvent:', error);
      }
      return null;
    }
  
    // Marcar celdas como seleccionadas para rellenar
    function markCellsForFill(startCell, endCell) {
      // Limpiar selección anterior
      clearCellSelection();
      
      if (!startCell || !endCell) return;
      
      try {
        // Determinar el rango de selección
        const startRowIndex = table.getRows().indexOf(startCell.row);
        const endRowIndex = table.getRows().indexOf(endCell.row);
        
        const startColIndex = table.getColumns().indexOf(startCell.column);
        const endColIndex = table.getColumns().indexOf(endCell.column);
        
        // Asegurarse de que tenemos índices válidos
        if (startRowIndex === -1 || endRowIndex === -1 || 
            startColIndex === -1 || endColIndex === -1) {
          console.warn('Índices de fila o columna no válidos');
          return;
        }
        
        // Determinar el rango (min y max para cada dimensión)
        const minRow = Math.min(startRowIndex, endRowIndex);
        const maxRow = Math.max(startRowIndex, endRowIndex);
        const minCol = Math.min(startColIndex, endColIndex);
        const maxCol = Math.max(startColIndex, endColIndex);
        
        // Guardar las celdas seleccionadas
        selectedCells = [];
        
        // Obtener todas las filas y columnas visibles
        const rows = table.getRows();
        const columns = table.getColumns();
        
        // Marcar todas las celdas en el rango
        for (let rowIndex = minRow; rowIndex <= maxRow; rowIndex++) {
          for (let colIndex = minCol; colIndex <= maxCol; colIndex++) {
            try {
              if (rowIndex >= 0 && rowIndex < rows.length && 
                  colIndex >= 0 && colIndex < columns.length) {
                
                const row = rows[rowIndex];
                const column = columns[colIndex];
                
                // Obtener la celda utilizando la API correcta de Tabulator
                if (row && column) {
                  const cellElement = row.getCell(column.getField());
                  
                  if (cellElement) {
                    const element = cellElement.getElement();
                    // No seleccionar la celda de origen
                    if (!(rowIndex === startRowIndex && colIndex === startColIndex) && element) {
                      element.classList.add('selected-for-fill');
                      selectedCells.push({
                        row: row,
                        column: column,
                        element: element
                      });
                    }
                  }
                }
              }
            } catch (cellError) {
              console.warn(`Error al marcar celda en posición [${rowIndex}, ${colIndex}]:`, cellError);
            }
          }
        }
        
        // Marcar la celda de origen con una clase especial
        try {
          const originCell = startCell.row.getCell(startCell.column.getField());
          if (originCell) {
            const element = originCell.getElement();
            if (element) {
              element.classList.add('drag-origin');
            }
          }
        } catch (originCellError) {
          console.warn('Error al marcar celda de origen:', originCellError);
        }
      } catch (error) {
        console.error('Error al marcar celdas para rellenar:', error);
      }
    }
  
    // Limpiar la selección de celdas
    function clearCellSelection() {
      document.querySelectorAll('.tabulator-cell.selected-for-fill, .tabulator-cell.drag-origin').forEach(cell => {
        cell.classList.remove('selected-for-fill', 'drag-origin');
      });
      selectedCells = [];
    }
  
    // Aplicar el valor a las celdas seleccionadas
    function applyValueToCells(value) {
      if (!selectedCells.length) return;
      
      // Almacenar cambios para la función de deshacer
      const changes = [];
      
      selectedCells.forEach(cell => {
        const row = cell.row;
        const column = cell.column;
        const field = column.getField();
        
        // Solo aplicar a columnas editables
        const columnDef = column.getDefinition();
        if (columnDef.editor) {
          // Guardar el valor anterior para la función de deshacer
          const oldValue = row.getData()[field];
          changes.push({
            row: row,
            field: field,
            oldValue: oldValue,
            newValue: value
          });
          
          // Actualizar los datos
          const rowData = row.getData();
          rowData[field] = value;
          row.update(rowData);
          
          // Marcar la celda como modificada (opcional)
          cell.element.classList.add('tabulator-cell-modified');
        }
      });
      
      // Guardar los cambios en el historial si se implementa undo/redo
      if (typeof window.saveChangesToHistory === 'function') {
        window.saveChangesToHistory(changes);
      }
    }
  
    // Limpiar todo el estado
    function reset() {
      isDragging = false;
      startCell = null;
      startCellValue = null;
      currentHoveredCell = null;
      hideFillHandle();
      clearCellSelection();
    }
  
    // Inicialización del indicador de arrastre
    fillHandle = createFillHandle();
    addStyles();
  
    // Manejar evento de click en una celda
    table.on("cellClick", function(e, cell) {
      // Limpiar cualquier handle existente primero
      hideFillHandle();
      
      // Solo mostrar el indicador si la celda es editable
      const column = cell.getColumn();
      const columnDef = column.getDefinition();
      
      if (columnDef.editor) {
        startCell = {
          row: cell.getRow(),
          column: column,
          element: cell.getElement()
        };
        startCellValue = cell.getValue();
        
        // Crear un objeto de celda compatible con la función positionFillHandle
        const cellForPositioning = {
          getElement: function() { return cell.getElement(); }
        };
        
        // Posicionar el indicador de arrastre
        positionFillHandle(cellForPositioning);
        
        // Registrar la celda actual para debugging
        console.log('Celda seleccionada:', {
          field: column.getField(),
          value: startCellValue,
          rowData: cell.getRow().getData()
        });
      } else {
        startCell = null;
      }
    });
  
    // Eventos para el indicador de arrastre
    document.addEventListener('mousedown', function(e) {
      if (fillHandle && (e.target === fillHandle || fillHandle.contains(e.target))) {
        e.preventDefault();
        e.stopPropagation();
        isDragging = true;
        document.body.style.cursor = 'crosshair';
        
        // Evitar que la tabla cambie la selección
        document.addEventListener('selectstart', preventDefaultHandler, { capture: true });
        
        console.log('Inicio de drag-fill detectado');
      }
    }, true);
  
    // Prevenir comportamiento predeterminado
    function preventDefaultHandler(e) {
      e.preventDefault();
      return false;
    }
  
    // Eventos del documento para seguir el arrastre
    document.addEventListener('mousemove', function(e) {
      if (!isDragging || !startCell) return;
      
      // Obtener la celda bajo el cursor
      const cell = getCellFromEvent(e);
      
      if (cell) {
        currentHoveredCell = cell;
        markCellsForFill(startCell, cell);
        
        // Mostrar un efecto visual mientras se arrastra
        document.body.style.cursor = 'crosshair';
      }
    });
  
    document.addEventListener('mouseup', function(e) {
      if (isDragging && startCell && currentHoveredCell && startCellValue !== null) {
        console.log('Finalizando drag-fill, aplicando valor:', startCellValue);
        console.log('Desde celda:', startCell.column.getField(), 'hasta celda:', currentHoveredCell.column.getField());
        console.log('Celdas seleccionadas:', selectedCells.length);
        
        // Aplicar el valor a todas las celdas seleccionadas
        applyValueToCells(startCellValue);
      }
      
      // Limpiar el estado
      document.body.style.cursor = '';
      document.removeEventListener('selectstart', preventDefaultHandler, { capture: true });
      
      // Si estábamos arrastrando, realizar un reset completo
      if (isDragging) {
        reset();
      } else {
        // Si no estábamos arrastrando, mantener el fill handle visible
        isDragging = false;
      }
    });
  
    // Limpiar cuando el mouse sale de la tabla
    const tabulatorElement = document.querySelector('.tabulator');
    if (tabulatorElement) {
      tabulatorElement.addEventListener('mouseleave', function() {
        if (!isDragging) {
          hideFillHandle();
        }
      });
    } else {
      console.warn('No se encontró el elemento de la tabla (.tabulator)');
    }
  
    // Reposicionar el indicador cuando la tabla se desplaza
    const tableHolder = document.querySelector('.tabulator-tableHolder');
    if (tableHolder) {
      tableHolder.addEventListener('scroll', function() {
        if (startCell && !isDragging) {
          // Al desplazar, el enfoque es mantener visible el handle en la celda seleccionada
          // En lugar de reposicionar, nos aseguramos que la celda tenga el handle (ya que está adjunto a la celda)
          const selectedCellElements = document.querySelectorAll('.tabulator-cell-with-handle');
          if (selectedCellElements.length === 0 && startCell.element) {
            // Si no encontramos ninguna celda con handle, intentamos reposicionar
            try {
              const cellForPositioning = {
                getElement: function() { return startCell.element; }
              };
              positionFillHandle(cellForPositioning);
            } catch (error) {
              console.error('Error al reposicionar el fill handle durante scroll:', error);
              hideFillHandle();
            }
          }
        }
      });
    } else {
      console.warn('No se encontró el contenedor de la tabla (.tabulator-tableHolder)');
    }
  
    // Registrar eventos adicionales si la tabla se redibuja
    try {
      // Solo intentar usar estos eventos si están disponibles en la versión de Tabulator
      if (typeof table.on === 'function') {
        // Intentar registrar eventos comunes
        table.on("tableBuilt", reset);
        table.on("dataLoaded", reset);
        table.on("pageLoaded", reset);
      }
    } catch (error) {
      console.warn('No se pudieron registrar algunos eventos de Tabulator:', error);
    }
  
    console.log('Inicializada funcionalidad de Drag-Fill para Tabulator');
  }
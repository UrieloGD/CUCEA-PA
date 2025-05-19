/**
 * Tabulator Drag-Fill Extension - Versión Corregida
 */

document.addEventListener('DOMContentLoaded', function() {
  console.log('[DragFill] Inicializando extensión...');
  
  const checkTable = setInterval(() => {
    const table = document.getElementById('tabla-datos-tabulator')?._tabulator || window.tabulatorTable;
    if (table && typeof table.getRows === 'function') {
      clearInterval(checkTable);
      initDragFill(table);
    }
  }, 100);
});

function initDragFill(table) {
  console.log('[DragFill] Iniciando con tabla:', table);
  
  // Variables de estado
  let isDragging = false;
  let startCell = null;
  let startCellValue = null;
  let selectedCells = [];
  let fillHandle = null;

  // 1. Crear el handle de arrastre
  function createFillHandle() {
    const handle = document.createElement('div');
    handle.id = 'tabulator-fill-handle';
    handle.style.cssText = `
      position: fixed;
      width: 16px;
      height: 16px;
      background: #1a73e8;
      border: 2px solid white;
      border-radius: 50%;
      cursor: crosshair;
      z-index: 10000;
      pointer-events: auto;
      display: none;
      transform: translate(-50%, -50%);
    `;
    
    handle.addEventListener('mousedown', function(e) {
      e.preventDefault();
      e.stopPropagation();
      isDragging = true;
      document.addEventListener('mousemove', handleDragMove);
      document.addEventListener('mouseup', handleDragEnd, {once: true});
    });
    
    document.body.appendChild(handle);
    return handle;
  }

  // 2. Posicionar el handle
  function positionFillHandle(cell) {
    if (!cell || !fillHandle) return;
    
    try {
      const cellEl = cell.getElement();
      const rect = cellEl.getBoundingClientRect();
      
      fillHandle.style.display = 'block';
      fillHandle.style.left = `${rect.left + rect.width / 2}px`;
      fillHandle.style.top = `${rect.top + rect.height / 2}px`;
    } catch (error) {
      console.error('[DragFill] Error posicionando handle:', error);
    }
  }

  // 3. Obtener celda desde evento - VERSIÓN CORREGIDA
  function getCellFromEvent(e) {
    try {
      // Obtener todos los elementos en la posición del mouse
      const elements = document.elementsFromPoint(e.clientX, e.clientY);
      
      for (const element of elements) {
        // Verificar si es una celda Tabulator
        if (element.classList.contains('tabulator-cell')) {
          // Encontrar la fila usando el método más confiable
          let rowElement = element;
          while (rowElement && !rowElement.classList.contains('tabulator-row')) {
            rowElement = rowElement.parentElement;
          }
          
          if (!rowElement) continue;
          
          // Obtener la fila usando la API de Tabulator
          const row = table.getRow(rowElement);
          if (!row) continue;
          
          // Obtener la columna usando el campo del elemento
          const columnField = element.getAttribute('tabulator-field') || 
                            element.getAttribute('data-field');
          if (!columnField) continue;
          
          const column = table.getColumn(columnField);
          if (!column) continue;
          
          return {
            row: row,
            column: column,
            element: element
          };
        }
      }
    } catch (error) {
      console.error('[DragFill] Error en getCellFromEvent:', error);
    }
    return null;
  }

  // 4. Manejar el arrastre
  function handleDragMove(e) {
    if (!isDragging || !startCell) return;
    
    e.preventDefault();
    
    const cell = getCellFromEvent(e);
    if (cell) {
      markCellsForFill(startCell, cell);
    }
  }

  // 5. Finalizar arrastre
  function handleDragEnd() {
    if (!isDragging) return;
    
    if (startCellValue !== null && selectedCells.length > 0) {
      table.blockRedraw();
      try {
        selectedCells.forEach(cell => {
          try {
            const field = cell.column.getField();
            cell.row.update({ [field]: startCellValue });
          } catch (error) {
            console.error('[DragFill] Error actualizando celda:', error);
          }
        });
      } finally {
        table.restoreRedraw();
      }
    }
    
    // Limpiar
    document.removeEventListener('mousemove', handleDragMove);
    isDragging = false;
    clearSelection();
    
    // Reposicionar handle
    if (startCell) {
      try {
        const cell = startCell.row.getCell(startCell.column.getField());
        positionFillHandle(cell);
      } catch (e) {
        hideFillHandle();
        startCell = null;
      }
    }
  }

  // 6. Marcar celdas para rellenar
  function markCellsForFill(start, end) {
    clearSelection();
    if (!start || !end) return;
    
    try {
      const rows = table.getRows();
      const cols = table.getColumns();
      
      const startRowIdx = rows.indexOf(start.row);
      const endRowIdx = rows.indexOf(end.row);
      const startColIdx = cols.indexOf(start.column);
      const endColIdx = cols.indexOf(end.column);
      
      if ([startRowIdx, endRowIdx, startColIdx, endColIdx].some(idx => idx === -1)) return;
      
      const minRow = Math.min(startRowIdx, endRowIdx);
      const maxRow = Math.max(startRowIdx, endRowIdx);
      const minCol = Math.min(startColIdx, endColIdx);
      const maxCol = Math.max(startColIdx, endColIdx);
      
      selectedCells = [];
      
      for (let r = minRow; r <= maxRow; r++) {
        for (let c = minCol; c <= maxCol; c++) {
          if (r === startRowIdx && c === startColIdx) continue;
          
          const row = rows[r];
          const col = cols[c];
          
          if (row && col && col.getDefinition().editor) {
            try {
              const cell = row.getCell(col.getField());
              if (cell) {
                const cellEl = cell.getElement();
                cellEl.style.backgroundColor = 'rgba(26, 115, 232, 0.2)';
                selectedCells.push({ row, column: col });
              }
            } catch (error) {
              console.warn('[DragFill] Error marcando celda:', error);
            }
          }
        }
      }
      
      // Resaltar celda de origen
      try {
        const originCell = start.row.getCell(start.column.getField());
        originCell.getElement().style.backgroundColor = 'rgba(26, 115, 232, 0.3)';
      } catch (error) {
        console.warn('[DragFill] Error resaltando celda origen:', error);
      }
    } catch (error) {
      console.error('[DragFill] Error en markCellsForFill:', error);
    }
  }

  // 7. Limpiar selección
  function clearSelection() {
    selectedCells = [];
    
    try {
      const rows = table.getRows();
      const cols = table.getColumns();
      
      for (let r = 0; r < rows.length; r++) {
        for (let c = 0; c < cols.length; c++) {
          try {
            const cell = rows[r].getCell(cols[c].getField());
            if (cell) {
              cell.getElement().style.backgroundColor = '';
            }
          } catch (error) {
            // Ignorar errores de celdas no accesibles
          }
        }
      }
    } catch (error) {
      console.error('[DragFill] Error limpiando selección:', error);
    }
  }

  // 8. Ocultar handle
  function hideFillHandle() {
    if (fillHandle) {
      fillHandle.style.display = 'none';
    }
  }

  // 9. Configurar eventos de la tabla
  table.on('cellClick', function(e, cell) {
    if (cell.getColumn().getDefinition().editor) {
      startCell = { 
        row: cell.getRow(), 
        column: cell.getColumn() 
      };
      startCellValue = cell.getValue();
      
      if (!fillHandle) {
        fillHandle = createFillHandle();
      }
      positionFillHandle(cell);
    }
  });

  // 10. Manejar scroll y resize
  window.addEventListener('resize', function() {
    if (startCell && !isDragging) {
      try {
        const cell = startCell.row.getCell(startCell.column.getField());
        positionFillHandle(cell);
      } catch (e) {
        hideFillHandle();
      }
    }
  });

  table.on('scroll', function() {
    if (startCell && !isDragging) {
      try {
        const cell = startCell.row.getCell(startCell.column.getField());
        positionFillHandle(cell);
      } catch (e) {
        hideFillHandle();
      }
    }
  });

  console.log('[DragFill] Extensión lista');
}
// Funcionalidad para navegación con teclado en tablas editables
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si es una tabla editable
    const userRole = document.getElementById("user-role");
    if (!userRole || userRole.value !== "1") {
        return; // Solo aplicar para usuarios con rol 1
    }

    const table = document.getElementById("tabla-datos");
    if (!table) {
        return;
    }

    // Aplicar eventos de teclado a todas las celdas editables
    function applyKeyboardNavigation() {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td[contenteditable="true"]');
            cells.forEach((cell, cellIndex) => {
                cell.addEventListener('keydown', function(e) {
                    handleKeyNavigation(e, cell, rowIndex, cellIndex, rows);
                });
            });
        });
    }

    // Manejar la navegación con teclado
    function handleKeyNavigation(event, currentCell, rowIndex, cellIndex, rows) {
        let nextCell = null;

        switch (event.key) {
            case 'ArrowUp':
                event.preventDefault();
                // Moverse a la misma celda en la fila anterior
                if (rowIndex > 0) {
                    // Obtener el mismo índice de columna en la fila anterior
                    const sameColumnIndex = currentCell.cellIndex;
                    const prevRow = rows[rowIndex - 1];
                    if (prevRow && prevRow.cells[sameColumnIndex]) {
                        nextCell = prevRow.cells[sameColumnIndex];
                        // Verificar que la celda sea editable
                        if (nextCell.getAttribute('contenteditable') !== 'true') {
                            nextCell = null;
                        }
                    }
                }
                break;

            case 'ArrowDown':
                event.preventDefault();
                // Moverse a la misma celda en la fila siguiente
                if (rowIndex < rows.length - 1) {
                    // Obtener el mismo índice de columna en la fila siguiente
                    const sameColumnIndex = currentCell.cellIndex;
                    const nextRow = rows[rowIndex + 1];
                    if (nextRow && nextRow.cells[sameColumnIndex]) {
                        nextCell = nextRow.cells[sameColumnIndex];
                        // Verificar que la celda sea editable
                        if (nextCell.getAttribute('contenteditable') !== 'true') {
                            nextCell = null;
                        }
                    }
                }
                break;

            case 'ArrowLeft':
                event.preventDefault();
                // Moverse a la celda anterior en la misma fila
                const currentRowCells = Array.from(rows[rowIndex].querySelectorAll('td[contenteditable="true"]'));
                const currentCellIndex = currentRowCells.indexOf(currentCell);
                if (currentCellIndex > 0) {
                    nextCell = currentRowCells[currentCellIndex - 1];
                }
                break;

            case 'ArrowRight':
                event.preventDefault();
                // Moverse a la celda siguiente en la misma fila
                const rowCells = Array.from(rows[rowIndex].querySelectorAll('td[contenteditable="true"]'));
                const cellIdx = rowCells.indexOf(currentCell);
                if (cellIdx < rowCells.length - 1) {
                    nextCell = rowCells[cellIdx + 1];
                }
                break;

            case 'Enter':
                if (!event.shiftKey) {
                    event.preventDefault();
                    // Simular presionar tecla abajo al presionar Enter
                    if (rowIndex < rows.length - 1) {
                        const sameColumnIndex = currentCell.cellIndex;
                        const nextRow = rows[rowIndex + 1];
                        if (nextRow && nextRow.cells[sameColumnIndex]) {
                            nextCell = nextRow.cells[sameColumnIndex];
                            // Verificar que la celda sea editable
                            if (nextCell.getAttribute('contenteditable') !== 'true') {
                                nextCell = null;
                            }
                        }
                    }
                } else {
                    // Shift+Enter: moverse a la celda de arriba
                    event.preventDefault();
                    if (rowIndex > 0) {
                        const sameColumnIndex = currentCell.cellIndex;
                        const prevRow = rows[rowIndex - 1];
                        if (prevRow && prevRow.cells[sameColumnIndex]) {
                            nextCell = prevRow.cells[sameColumnIndex];
                            // Verificar que la celda sea editable
                            if (nextCell.getAttribute('contenteditable') !== 'true') {
                                nextCell = null;
                            }
                        }
                    }
                }
                break;

            case 'Tab':
                event.preventDefault();
                // Moverse a la siguiente celda editable (horizontal, luego vertical)
                const allCells = Array.from(table.querySelectorAll('td[contenteditable="true"]'));
                const currentIndex = allCells.indexOf(currentCell);
                
                if (!event.shiftKey) {
                    // Tab normal - moverse a la siguiente celda
                    if (currentIndex < allCells.length - 1) {
                        nextCell = allCells[currentIndex + 1];
                    }
                } else {
                    // Shift+Tab - moverse a la celda anterior
                    if (currentIndex > 0) {
                        nextCell = allCells[currentIndex - 1];
                    }
                }
                break;
        }

        // Si encontramos una celda a la que movernos, enfocamos en ella
        if (nextCell) {
            focusCell(nextCell);
        }
    }

    // Enfocar en una celda y posicionar el cursor al final del texto
    function focusCell(cell) {
        cell.focus();
        
        // Posicionar el cursor al final del contenido
        const range = document.createRange();
        const selection = window.getSelection();
        
        if (cell.childNodes.length > 0) {
            const lastNode = cell.childNodes[cell.childNodes.length - 1];
            const lastNodeLength = lastNode.nodeType === 3 ? lastNode.length : 0;
            range.setStart(lastNode, lastNodeLength);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            // Si la celda está vacía, simplemente enfocamos
            const textNode = document.createTextNode('');
            cell.appendChild(textNode);
            range.setStart(textNode, 0);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    // Escuchamos cambios en el DOM para aplicar navegación a celdas que se hagan editables
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && 
                mutation.attributeName === 'contenteditable') {
                applyKeyboardNavigation();
            }
        });
    });

    // Observar cambios en atributos contenteditable
    observer.observe(table, {
        attributes: true,
        attributeFilter: ['contenteditable'],
        subtree: true
    });

    // Inicializar la navegación una vez que la tabla sea editable
    // Usar un pequeño retraso para asegurarse que makeEditable() ya se ejecutó
    setTimeout(applyKeyboardNavigation, 500);
});
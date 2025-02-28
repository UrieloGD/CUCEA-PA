let changedCells = new Set();

const columnMap = {
  ID: "ID_Plantilla",
  CICLO: "CICLO",
  CRN: "CRN",
  MATERIA: "MATERIA",
  "CVE MATERIA": "CVE_MATERIA",
  SECCIÓN: "SECCION",
  NIVEL: "NIVEL",
  "NIVEL TIPO": "NIVEL_TIPO",
  TIPO: "TIPO",
  "C. MIN": "C_MIN",
  "H. TOTALES": "H_TOTALES",
  STATUS: "ESTATUS",
  "TIPO CONTRATO": "TIPO_CONTRATO",
  CÓDIGO: "CODIGO_PROFESOR",
  "NOMBRE PROFESOR": "NOMBRE_PROFESOR",
  CATEGORIA: "CATEGORIA",
  DESCARGA: "DESCARGA",
  "CÓDIGO DESCARGA": "CODIGO_DESCARGA",
  "NOMBRE DESCARGA": "NOMBRE_DESCARGA",
  "NOMBRE DEFINITIVO": "NOMBRE_DEFINITIVO",
  TITULAR: "TITULAR",
  HORAS: "HORAS",
  "CÓDIGO DEPENDENCIA": "CODIGO_DEPENDENCIA",
  L: "L",
  M: "M",
  I: "I",
  J: "J",
  V: "V",
  S: "S",
  D: "D",
  "DÍA PRESENCIAL": "DIA_PRESENCIAL",
  "DÍA VIRTUAL": "DIA_VIRTUAL",
  MODALIDAD: "MODALIDAD",
  "FECHA INICIAL": "FECHA_INICIAL",
  "FECHA FINAL": "FECHA_FINAL",
  "HORA INICIAL": "HORA_INICIAL",
  "HORA FINAL": "HORA_FINAL",
  MÓDULO: "MODULO",
  AULA: "AULA",
  CUPO: "CUPO",
  OBSERVACIONES: "OBSERVACIONES",
  EXTRAORDINARIO: "EXAMEN_EXTRAORDINARIO",
};

const maxLengths = {
  CICLO: 10,
  CRN: 15,
  MATERIA: 80,
  CVE_MATERIA: 5,
  SECCION: 5,
  NIVEL: 25,
  NIVEL_TIPO: 25,
  TIPO: 1,
  C_MIN: 2,
  H_TOTALES: 2,
  ESTATUS: 10,
  TIPO_CONTRATO: 30,
  CODIGO_PROFESOR: 9,
  NOMBRE_PROFESOR: 60,
  CATEGORIA: 40,
  DESCARGA: 2,
  CODIGO_DESCARGA: 9,
  NOMBRE_DESCARGA: 60,
  NOMBRE_DEFINITIVO: 60,
  TITULAR: 2,
  HORAS: 1,
  CODIGO_DEPENDENCIA: 4,
  L: 5,
  M: 5,
  I: 5,
  J: 5,
  V: 5,
  S: 5,
  D: 5,
  DIA_PRESENCIAL: 10,
  DIA_VIRTUAL: 10,
  MODALIDAD: 10,
  FECHA_INICIAL: 10,
  FECHA_FINAL: 10,
  HORA_INICIAL: 10,
  HORA_FINAL: 10,
  MODULO: 10,
  AULA: 10,
  CUPO: 3,
  OBSERVACIONES: 150,
  EXAMEN_EXTRAORDINARIO: 2,
};

// Variables globales para control de estado
let activeCell = null;
let editMode = false;

// Función principal para hacer editable la tabla
function makeEditable() {
  // Añadir estilos CSS para las celdas seleccionadas
  addExcelStyleCSS();

  // Verificar el rol del usuario antes de hacer la tabla editable
  const table = document.getElementById("tabla-datos");
  if (!table) return;

  // Verificar si la tabla tiene el atributo data-editable="false"
  if (table.getAttribute("data-editable") === "false") {
    return;
  }

  const userRole = document.getElementById("user-role");
  if (!userRole || userRole.value !== "1") {
    return;
  }

  const rows = table.querySelectorAll("tbody tr");

  // Procesar cada celda de la tabla
  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    cells.forEach((cell, cellIndex) => {
      // Omitir la segunda columna (índice 1) que generalmente es la de ID
      // También omitir celdas que contienen inputs para borrar filas
      if (cellIndex !== 1 && !cell.querySelector('input')) {
        // Guardar el valor original
        cell.setAttribute("data-original-value", cell.textContent.trim());
        
        // Añadir clase para marcar como procesada
        cell.classList.add("excel-behavior-applied");
        
        // Añadir eventos
        cell.addEventListener("click", handleCellClick);
        cell.addEventListener("dblclick", handleCellDblClick);
        cell.addEventListener("keydown", handleKeyNavigation);
        cell.addEventListener("input", function() {
          updateCell(this);
        });
      }
    });
  });

  // Emitir evento de tabla editable
  const editableEvent = new CustomEvent('tableNowEditable', {
    detail: { table: table }
  });
  document.dispatchEvent(editableEvent);

  // Añadir listener para cuando se haga clic fuera de la tabla
  document.addEventListener("click", function(e) {
    if (!table.contains(e.target) && activeCell) {
      exitEditMode();
      if (activeCell) {
        activeCell.classList.remove('selected-cell');
        activeCell = null;
      }
    }
  });
}

// Manejo de selección y edición de celdas
function handleCellClick(event) {
  // Si ya hay una celda activa en modo edición, salir del modo edición
  if (activeCell && activeCell !== this && editMode) {
    exitEditMode();
  }
  
  // Seleccionar esta celda
  selectCell(this);
  
  // Importante: Asegurar que la celda reciba el foco
  this.focus();
  
  event.stopPropagation();
}

function handleCellDblClick(event) {
  // Activar modo edición en doble clic
  enterEditMode(this);
  event.stopPropagation();
}

function selectCell(cell) {
  // Deseleccionar la celda activa anterior si existe
  if (activeCell) {
    activeCell.classList.remove("selected-cell");
  }

  // Marcar esta celda como seleccionada
  activeCell = cell;
  cell.classList.add("selected-cell");
  editMode = false;
  
  // Asegurarse de que la celda no sea editable en modo selección
  cell.setAttribute("contenteditable", "false");
  
  // Importante: Hacer que la celda sea "focusable"
  cell.setAttribute("tabindex", "0");
  
  // Dar foco a la celda
  cell.focus();
}

function enterEditMode(cell) {
  // Activar la edición de la celda
  cell.setAttribute("contenteditable", "true");
  editMode = true;
  
  // Posicionar el cursor al final del texto
  placeCursorAtEnd(cell);
}

function exitEditMode() {
  if (activeCell && editMode) {
    // Desactivar la edición de la celda
    activeCell.setAttribute("contenteditable", "false");
    editMode = false;
    
    // Verificar si ha habido cambios
    const originalValue = activeCell.getAttribute('data-original-value');
    const currentValue = activeCell.textContent.trim();
    
    if (originalValue !== currentValue) {
      activeCell.style.backgroundColor = "#FFFACD"; // Color amarillo claro para indicar cambio
      changedCells.add(activeCell);
      showSaveButton();
      showEditIcons();
    }
  }
}

function placeCursorAtEnd(cell) {
  cell.focus();
  
  // Crear un rango al final del contenido
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
    // Si la celda está vacía, crear un nodo de texto
    const textNode = document.createTextNode("");
    cell.appendChild(textNode);
    range.setStart(textNode, 0);
    range.collapse(true);
    selection.removeAllRanges();
    selection.addRange(range);
  }
}

// Manejo de navegación con teclado
function handleKeyNavigation(event) {
  if (!activeCell) return;
  
  // Teclas especiales
  switch (event.key) {
    case "F2":
      event.preventDefault();
      if (!editMode) {
        enterEditMode(activeCell);
      }
      return;
      
    case "Escape":
      event.preventDefault();
      if (editMode) {
        // Restaurar valor original
        activeCell.textContent = activeCell.getAttribute('data-original-value') || '';
        exitEditMode();
      } else {
        // Deseleccionar celda
        if (activeCell) {
          activeCell.classList.remove("selected-cell");
          activeCell = null;
        }
      }
      return;
      
    case "Enter":
      event.preventDefault();
      if (editMode) {
        exitEditMode();
      }
      navigateToCell("down");
      return;
  }
  
  // Si estamos en modo edición, permitir que las teclas de flecha muevan el cursor
  if (editMode) {
    // No interceptar las teclas de flecha en modo edición
    return;
  }
  
  // Si no estamos en modo edición, usar las flechas para navegar entre celdas
  switch (event.key) {
    case "ArrowUp":
      event.preventDefault();
      navigateToCell("up");
      break;
    case "ArrowDown":
      event.preventDefault();
      navigateToCell("down");
      break;
    case "ArrowLeft":
      event.preventDefault();
      navigateToCell("left");
      break;
    case "ArrowRight":
      event.preventDefault();
      navigateToCell("right");
      break;
    case "Tab":
      event.preventDefault();
      navigateToCell(event.shiftKey ? "left" : "right");
      break;
  }
}

function navigateToCell(direction) {
  if (!activeCell) return;
  
  const table = document.getElementById("tabla-datos");
  const rows = table.querySelectorAll('tbody tr');
  const rowArray = Array.from(rows);
  const currentRow = activeCell.parentElement;
  const rowIndex = rowArray.indexOf(currentRow);
  
  // Obtener todas las celdas editables (excluyendo la columna de ID)
  const allEditableCells = Array.from(table.querySelectorAll('td.excel-behavior-applied'));
  
  let nextCell = null;
  
  switch (direction) {
    case "up":
      if (rowIndex > 0) {
        // Buscar la celda en la misma posición en la fila anterior
        const cellIndex = Array.from(currentRow.cells).indexOf(activeCell);
        const upperRow = rowArray[rowIndex - 1];
        if (upperRow && upperRow.cells[cellIndex]) {
          const targetCell = upperRow.cells[cellIndex];
          if (targetCell.classList.contains('excel-behavior-applied')) {
            nextCell = targetCell;
          }
        }
      }
      break;
      
    case "down":
      if (rowIndex < rowArray.length - 1) {
        // Buscar la celda en la misma posición en la fila siguiente
        const cellIndex = Array.from(currentRow.cells).indexOf(activeCell);
        const lowerRow = rowArray[rowIndex + 1];
        if (lowerRow && lowerRow.cells[cellIndex]) {
          const targetCell = lowerRow.cells[cellIndex];
          if (targetCell.classList.contains('excel-behavior-applied')) {
            nextCell = targetCell;
          }
        }
      }
      break;
      
    case "left":
      // Buscar la celda anterior editable en la misma fila
      const currentCells = Array.from(currentRow.cells).filter(cell => 
        cell.classList.contains('excel-behavior-applied')
      );
      const currentIndex = currentCells.indexOf(activeCell);
      if (currentIndex > 0) {
        nextCell = currentCells[currentIndex - 1];
      }
      break;
      
    case "right":
      // Buscar la celda siguiente editable en la misma fila
      const rowCells = Array.from(currentRow.cells).filter(cell => 
        cell.classList.contains('excel-behavior-applied')
      );
      const cellIdx = rowCells.indexOf(activeCell);
      if (cellIdx < rowCells.length - 1) {
        nextCell = rowCells[cellIdx + 1];
      }
      break;
  }
  
  // Si encontramos una celda a la que navegar, la seleccionamos
  if (nextCell) {
    selectCell(nextCell);
  }
}

// Actualización de celdas
function updateCell(cell) {
  if (!cell.hasAttribute("data-original-value")) {
    cell.setAttribute("data-original-value", cell.textContent.trim());
  }

  const columnName = getColumnName(cell);
  const maxLength = maxLengths[columnName] || 60;

  // Obtener la posición del cursor
  const selection = window.getSelection();
  const range = selection.getRangeAt(0);
  const cursorPosition = range.startOffset;

  let newText = cell.textContent;
  const originalLength = newText.length;

  // Validar el tipo de datos por columna
  switch (columnName) {
    case "CICLO":
    case "CRN":
    case "C_MIN":
    case "H_TOTALES":
    case "CODIGO_PROFESOR":
    case "CODIGO_DESCARGA":
    case "HORAS":
    case "CODIGO_DEPENDENCIA":
    case "HORA_INICIAL":
    case "HORA_FINAL":
    case "CUPO":
      newText = newText.replace(/\D/g, ""); // Permitir solo dígitos
      break;
    case "FECHA_INICIAL":
    case "FECHA_FINAL":
      // No modificar el texto, se manejará con un calendario
      break;
    default:
      // Convertir a mayúsculas
      newText = newText.toUpperCase();
      break;
  }

  // Limitar la longitud
  if (newText.length > maxLength) {
    newText = newText.slice(0, maxLength);
  }

  // Solo actualizar el texto si es diferente, para evitar perder la posición del cursor
  if (cell.textContent !== newText) {
    cell.textContent = newText;
    
    // Ajustar la posición del cursor en función del cambio de longitud
    let newCursorPos = cursorPosition;
    if (newText.length !== originalLength) {
      // Si el texto se acortó, ajustar la posición del cursor
      newCursorPos = Math.min(cursorPosition, newText.length);
    }
    
    // Restaurar la posición del cursor
    if (cell.firstChild) {
      range.setStart(cell.firstChild, newCursorPos);
      range.setEnd(cell.firstChild, newCursorPos);
      selection.removeAllRanges();
      selection.addRange(range);
    }
  }

  cell.style.backgroundColor = "#FFFACD";
  changedCells.add(cell);
  showSaveButton();
  showEditIcons();
}

// Funciones auxiliares
function getColumnName(cell) {
  const headerRow = document.querySelector("#tabla-datos tr");
  let columnName = headerRow.cells[cell.cellIndex].textContent.trim();

  // Eliminar emojis, caracteres especiales y símbolos como ▾
  columnName = columnName
    .replace(
      /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F1E0}-\u{1F1FF}▾]/gu,
      ""
    )
    .trim();

  // Para depuración
  // console.log("Column name from header:", columnName);
  let mappedName = columnMap[columnName] || columnName;
  // console.log("Mapped column name:", mappedName);
  return mappedName;
}

// Añadir estilos CSS para las celdas seleccionadas
function addExcelStyleCSS() {
  if (document.getElementById('excel-style-css')) return;
  
  const style = document.createElement('style');
  style.id = 'excel-style-css';
  style.textContent = `
    .selected-cell {
      outline: 2px solid #217346 !important; /* Color verde Excel */
      background-color: rgba(33, 115, 70, 0.1) !important;
    }
    #tabla-datos td.excel-behavior-applied {
      position: relative;
      cursor: cell;
    }
    #tabla-datos td[contenteditable="true"] {
      cursor: text;
    }
    #tabla-datos td.excel-behavior-applied::selection {
      background-color: transparent;
    }
    #tabla-datos td[contenteditable="true"]::selection {
      background-color: #b5d7ff;
    }
    .char-count {
      position: absolute;
      bottom: 0;
      right: 0;
      font-size: 10px;
      color: #888;
    }
  `;
  document.head.appendChild(style);
}

// Funciones para los botones de guardar y deshacer
function showSaveButton() {
  const saveIcon = document.getElementById("icono-guardar");
  if (saveIcon) {
    saveIcon.style.visibility = "visible";
    saveIcon.style.opacity = "1";
  }
}

function hideSaveButton() {
  const saveIcon = document.getElementById("icono-guardar");
  if (saveIcon) {
    saveIcon.style.visibility = "hidden";
    saveIcon.style.opacity = "0";
  }
}

function showEditIcons() {
  const saveIcon = document.getElementById("icono-guardar");
  const undoIcon = document.getElementById("icono-deshacer");
  if (saveIcon) {
    saveIcon.style.visibility = "visible";
    saveIcon.style.opacity = "1";
  }
  if (undoIcon) {
    undoIcon.style.visibility = "visible";
    undoIcon.style.opacity = "1";
  }
}

function hideEditIcons() {
  const saveIcon = document.getElementById("icono-guardar");
  const undoIcon = document.getElementById("icono-deshacer");
  if (saveIcon) {
    saveIcon.style.visibility = "hidden";
    saveIcon.style.opacity = "0";
  }
  if (undoIcon) {
    undoIcon.style.visibility = "hidden";
    undoIcon.style.opacity = "0";
  }
}

function undoAllChanges() {
  if (changedCells.size > 0) {
    // Obtener la última celda modificada
    const lastCell = Array.from(changedCells).pop();

    // Comprobar si la celda contiene un input, en ese caso ignorarla
    if (!lastCell.querySelector('input')) {
      // Revertir los cambios de la última celda
      const originalValue = lastCell.getAttribute("data-original-value");
      if (originalValue !== null) {
        lastCell.textContent = originalValue;
      }
      lastCell.style.backgroundColor = "";
    }
    
    // Eliminar la última celda de la lista de celdas modificadas
    changedCells.delete(lastCell);
  }

  // Si ya no quedan celdas modificadas, ocultar los iconos de edición
  if (changedCells.size === 0) {
    hideEditIcons();
  }
}

function saveAllChanges() {
  // Añadir console.log para verificar el rol del usuario
  const userRole = document.getElementById("user-role").value;
  console.log("User Role:", userRole);

  const promises = Array.from(changedCells).map((cell) => {
    const id = cell.parentNode.cells[1].textContent;
    const column = getColumnName(cell);
    let value = cell.textContent;

    // Limpieza básica de datos
    value = value.replace(/[^\x00-\x7F]/g, "");

    // Añadir más información de depuración
    console.log("Saving - ID:", id, "Column:", column, "Value:", value);

    return fetch("./functions/basesdedatos/actualizar-celda.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        id,
        column,
        value,
        user_role: userRole, // Pasar el rol del usuario
      }),
    })
      .then((response) => {
        console.log("Response status:", response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Server response:", data);
        if (data.error) {
          throw new Error(data.error);
        }
        return { cell, data };
      });
  });

  Promise.all(promises)
    .then((results) => {
      results.forEach(({ cell, data }) => {
        if (data.success) {
          // Actualizar el valor original
          cell.setAttribute("data-original-value", cell.textContent.trim());
          
          // Mostrar confirmación visual
          cell.style.backgroundColor = "#90EE90";
          setTimeout(() => {
            cell.style.transition = "background-color 0.5s ease";
            cell.style.backgroundColor = "";
          }, 2000);
        }
      });

      console.log("Todos los cambios guardados:", results);
      changedCells.clear();
      hideEditIcons();
    })
    .catch((error) => {
      console.error("Error saving changes:", error);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: "error",
          title: "Error",
          text:
            "No tienes los permisos necesarios para realizar cambios en la base de datos. Error: " +
            error.message,
          confirmButtonText: "Entendido",
        });
      } else {
        alert("Error al guardar los cambios: " + error.message);
      }
    });
}

// Inicialización al cargar el documento
document.addEventListener("DOMContentLoaded", function() {
  makeEditable();
});

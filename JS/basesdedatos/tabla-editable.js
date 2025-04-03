// ./JS/basesdedatos/tabla-editable.js
let changedCells = new Set();
let dragStartCell = null;
let dragInProgress = false;
let cellsToFill = [];
let fillHandleVisible = false;

// Variables para portapapeles y el historias de acciones
let clipboard = null;
let undoStack = [];
const MAX_UNDO_STACK = 50;

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
  HORAS: 3,
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
  if (
    !userRole ||
    (userRole.value !== "1" &&
      userRole.value !== "4" &&
      userRole.value !== "0") ||
    !puedeEditar
  ) {
    hideEditIcons();
    return;
  }

  const rows = table.querySelectorAll("tbody tr");

  // Procesar cada celda de la tabla
  rows.forEach((row) => {
    const cells = row.querySelectorAll("td");
    cells.forEach((cell, cellIndex) => {
      // Omitir la segunda columna (índice 1) que generalmente es la de ID
      // También omitir celdas que contienen inputs para borrar filas
      if (cellIndex !== 1 && !cell.querySelector("input")) {
        // Guardar el valor original
        cell.setAttribute("data-original-value", cell.textContent.trim());

        // Añadir clase para marcar como procesada
        cell.classList.add("excel-behavior-applied");

        // Solo agregar eventos si puedeEditar es true
        if (puedeEditar) {
          cell.addEventListener("click", handleCellClick);
          cell.addEventListener("dblclick", handleCellDblClick);
          cell.addEventListener("keydown", handleKeyNavigation);
          cell.addEventListener("input", function () {
            updateCell(this);
          });
        }
      }
    });
  });

  // Emitir evento de tabla editable solo si puedeEditar es true
  if (puedeEditar) {
    const editableEvent = new CustomEvent("tableNowEditable", {
      detail: { table: table },
    });
    document.dispatchEvent(editableEvent);

    // Añadir listener para cuando se haga clic fuera de la tabla
    document.addEventListener("click", function (e) {
      if (!table.contains(e.target) && activeCell) {
        exitEditMode();
        if (activeCell) {
          activeCell.classList.remove("selected-cell");
          activeCell = null;
        }
      }
    });
  }
}

// Función de reconocimiento de eventos
function setupKeyboardShortcuts() {
  document.addEventListener("keydown", function (event) {
    // Solo procesa si tenemos una celda activa
    if (!activeCell) return;

    // Ctrl + C (Copiar)
    if (event.ctrlKey && event.key === "c") {
      event.preventDefault();
      copySelectedCell();
      return;
    }

    // Ctrl + X (Cortar)
    if (event.ctrlKey && event.key === "x") {
      event.preventDefault();
      cutSelectedCell();
      return;
    }

    // Ctrl + V (Pegar)
    if (event.ctrlKey && event.key === "v") {
      event.preventDefault();
      pasteSelectedCell();
      return;
    }

    // Ctrl + Z (Deshacer)
    if (event.ctrlKey && event.key === "z") {
      event.preventDefault();
      undoLastAction();
      return;
    }
  });
}

// Función para copiar el contenido de una celda seleccionada
function copySelectedCell() {
  if (!activeCell) return;

  clipboard = {
    text: activeCell.textContent,
    sourceCell: activeCell,
  };

  // Muestra indicador visual de copiado
  showFeedbackAnimation(activeCell, "copied");
}

// Función para cortar el contenido de la celda seleccionada
function cutSelectedCell() {
  if (!activeCell) return;

  // Guarda el valor actual para deshacer
  saveActionForUndo({
    type: "edit",
    cell: activeCell,
    previousValue: activeCell.textContent,
    newValue: "",
  });

  // Guarda en el portapapeles
  clipboard = {
    text: activeCell.textContent,
    sourceCell: activeCell,
  };

  //Limpia la celda

  const originalValue = activeCell.getAttribute("data-original-value");
  activeCell.textContent = "";

  // Marca como cambiada
  if (activeCell.textContent !== originalValue) {
    activeCell.style.backgroundColor = "#FFFACD";
    changedCells.add(activeCell);
    showSaveButton();
    showEditIcons();
  }

  // Muestra un indicador visual
  showFeedbackAnimation(activeCell, "cut");
}

function pasteSelectedCell() {
  if (!activeCell || !clipboard || !puedeEditar) {
    if (!puedeEditar) {
      showFeedbackMessage(
        "No puedes editar fuera de las fechas de Programación Académica."
      );
    }
    return;
  }

  // No se puede hacer la acción de pegar si se esta en modo edición
  if (editMode) {
    exitEditMode();
  }

  // Guardar el valor actual para deshacer
  saveActionForUndo({
    type: "edit",
    cell: activeCell,
    previousValue: activeCell.textContent,
    newValue: clipboard.text,
  });

  // Obtiene el nombre de la columna para validación
  const columnName = getColumnName(activeCell);

  // Guarda el texto original antes de validarlo
  let originalText = clipboard.text;
  let newText = originalText;

  // Aplica validaciones según el tipo de columna
  const maxLength = maxLengths[columnName] || 60;

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
    case "HORA_FIANL":
    case "CUPO":
      newText = newText.replace(/\D/g, ""); // Permitir solo dígitos
      break;
    default:
      // Convertir a mayúsculas
      newText = newText.toUpperCase();
      break;
  }

  // Limita la longitud
  if (newText.length > maxLength) {
    newText = newText.slice(0, maxLength);
  }

  // Asigna el valor validado
  activeCell.textContent = newText;

  // Marcar como cambiada
  const originalValue = activeCell.getAttribute("data-original-value");
  if (activeCell.textContent !== originalValue) {
    activeCell.style.backgroundColor = "#FFFACD";
    changedCells.add(activeCell);
    showSaveButton();
    showEditIcons();
  }

  // Muestra un indicador visual
  showFeedbackAnimation(activeCell, "pasted");
}

// Función para guardar una acccón en el historial de deshacer
function saveActionForUndo(action) {
  // Añadir la acción al inicio del array
  undoStack.unshift(action);

  // Limitar el tamaño del historial
  if (undoStack.length > MAX_UNDO_STACK) {
    undoStack.pop();
  }
}

// Función para deshacer la última acción
function undoLastAction() {
  if (undoStack.length === 0) return;

  // Obtener la última acción
  const action = undoStack.shift();

  if (action.type === "edit") {
    // Restaurar el valor anterior
    action.cell.textContent = action.previousValue;

    // Verificar si volvimos al valor original
    const originalValue = action.cell.getAttribute("data-original-value");
    if (action.cell.textContent === originalValue) {
      action.cell.style.backgroundColor = "";
      changedCells.delete(action.cell);
      if (changedCells.size === 0) {
        hideEditIcons();
      }
    } else {
      action.cell.style.backgroundColor = "#FFFACD";
      changedCells.add(action.cell);
      showSaveButton();
      showEditIcons();
    }

    // Muestra un indicador visual para deshacer
    showFeedbackAnimation(action.cell, "undone");
  }
}

// Función para mostrar una animación de feedback
function showFeedbackAnimation(cell, action) {
  if (!cell) return;

  // Crear un div para la animación
  const feedback = document.createElement("div");
  feedback.className = "action-feedback";

  // Configurar el mensaje según la acción
  let message = "";
  switch (action) {
    case "copied":
      message = "Copiado";
      break;
    case "cut":
      message = "Cortado";
      break;
    case "pasted":
      message = "Pegado";
      break;
    case "undone":
      message = "Deshecho";
      break;
  }

  feedback.textContent = message;

  // Establece estilos
  feedback.style.position = "absolute";
  feedback.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
  feedback.style.color = "#fff";
  feedback.style.padding = "4px 8px";
  feedback.style.borderRadius = "3px";
  feedback.style.fontSize = "12px";
  feedback.style.zIndex = "1000";
  feedback.style.pointerEvents = "none";

  // Calcular la posición (encima de la celda)
  const cellRect = cell.getBoundingClientRect();
  const tableRect = document
    .getElementById("tabla-datos")
    .getBoundingClientRect();

  feedback.style.top = cellRect.top - 25 + "px";
  feedback.style.left = cellRect.left + cellRect.width / 2 - 40 + "px";

  // Añade al documento
  document.body.appendChild(feedback);

  // Configurar animación
  feedback.style.opacity = "0";
  feedback.style.transform = "translateY(10px)";
  feedback.style.transition = "opacity 0.2s, transfom 0.2s";

  // Activar la animación después de un pequeño retraso
  setTimeout(() => {
    feedback.style.opacity = "1";
    feedback.style.transform = "translateY(0)";
  }, 10);

  // Eliminar después de un tiempo
  setTimeout(() => {
    feedback.style.opacity = "0";
    feedback.style.transform = "translateY(-10px)";

    setTimeout(() => {
      if (feedback.parentNode) {
        document.body.removeChild(feedback);
      }
    }, 200);
  }, 1000);
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

// Función de selección de celda
function selectCell(cell) {
  // Deseleccionar la celda activa anterior si existe
  if (activeCell) {
    activeCell.classList.remove("selected-cell");
    // Quitar el manejador de relleno si existe
    removeFillHandle(activeCell);
  }

  // Marcar esta celda como seleccionada
  activeCell = cell;
  cell.classList.add("selected-cell");
  editMode = false;

  // Asegurarse de que la celda no sea editable en modo selección
  cell.setAttribute("contenteditable", "false");

  // Importante: Hacer que la celda sea "focusable"
  cell.setAttribute("tabindex", "0");

  // Añadir el manejador de relleno
  addFillHandle(cell);

  // Dar foco a la celda
  cell.focus();
}

function removeFillHandle(cell) {
  if (!cell) return;

  // Eliminar el manejador de esta celda específica
  const existingHandle = cell.querySelector(".fill-handle");
  if (existingHandle) {
    cell.removeChild(existingHandle);
  }

  // Como medida de seguridad, eliminar cualquier otro manejador que pudiera existir
  document.querySelectorAll(".fill-handle").forEach((handle) => {
    if (handle.parentNode) {
      handle.parentNode.removeChild(handle);
    }
  });

  // Actualizar el estado
  fillHandleVisible = false;

  // También limpiar cualquier estado de arrastre
  if (dragInProgress) {
    dragInProgress = false;
    dragStartCell = null;
    document.removeEventListener("mousemove", handleFillDrag);
    document.removeEventListener("mouseup", endFillDrag);
  }

  // Limpiar las marcas de celdas objetivo
  document.querySelectorAll(".fill-target").forEach((cell) => {
    cell.classList.remove("fill-target");
  });
}

// Función para añadir el manejador de relleno a una celda
function addFillHandle(cell) {
  // Eliminar primero cualquier manejador existente, tanto en esta celda como en otras
  removeFillHandle(cell);

  // Si la celda es válida, añadir el manejador
  if (cell && cell.classList.contains("excel-behavior-applied")) {
    // Crear el nuevo manejador
    const fillHandle = document.createElement("div");
    fillHandle.className = "fill-handle";
    fillHandle.addEventListener("mousedown", startFillDrag);

    // Asegurar que la celda tenga posición relativa
    cell.style.position = "relative";

    // Añadir el manejador
    cell.appendChild(fillHandle);
    fillHandleVisible = true;
  }
}

// Función para iniciar el arrastre de relleno
function startFillDrag(event) {
  event.preventDefault();
  event.stopPropagation();

  if (!activeCell) return;

  dragStartCell = activeCell;
  dragInProgress = true;

  // Añadir listeners para el arrastre
  document.addEventListener("mousemove", handleFillDrag);
  document.addEventListener("mouseup", endFillDrag);
}

// Función para manejar el arrastre durante el relleno
function handleFillDrag(event) {
  if (!dragInProgress || !dragStartCell) return;

  // Obtener la posición del mouse
  const mouseX = event.clientX;
  const mouseY = event.clientY;

  // Obtener la tabla
  const table = document.getElementById("tabla-datos");
  if (!table) return;

  // Limpiar las marcas previas
  document.querySelectorAll(".fill-target").forEach((cell) => {
    cell.classList.remove("fill-target");
  });

  // Vaciar el array de celdas a rellenar
  cellsToFill = [];

  // Detectar qué celdas están bajo el cursor
  detectCellsInPath(mouseX, mouseY);
}

// Función para detectar las celdas en el camino del arrastre
function detectCellsInPath(mouseX, mouseY) {
  if (!dragStartCell) return;

  const table = document.getElementById("tabla-datos");
  const rows = Array.from(table.querySelectorAll("tbody tr"));

  // Obtener la posición de inicio (celda original)
  const startCell = dragStartCell;
  const startRow = startCell.parentElement;
  const startRowIndex = rows.indexOf(startRow);
  const startCellIndex = Array.from(startRow.cells).indexOf(startCell);

  // Encontrar la celda bajo el cursor
  let targetCell = null;
  let targetRow = null;

  document
    .querySelectorAll("#tabla-datos td.excel-behavior-applied")
    .forEach((cell) => {
      const rect = cell.getBoundingClientRect();
      if (
        mouseX >= rect.left &&
        mouseX <= rect.right &&
        mouseY >= rect.top &&
        mouseY <= rect.bottom
      ) {
        targetCell = cell;
        targetRow = cell.parentElement;
      }
    });

  if (!targetCell) return;

  const targetRowIndex = rows.indexOf(targetRow);
  const targetCellIndex = Array.from(targetRow.cells).indexOf(targetCell);

  // Determinar la dirección del arrastre (vertical u horizontal)
  const isVertical = startCellIndex === targetCellIndex;

  if (isVertical) {
    // Arrastre vertical
    const startIdx = Math.min(startRowIndex, targetRowIndex);
    const endIdx = Math.max(startRowIndex, targetRowIndex);

    for (let i = startIdx; i <= endIdx; i++) {
      if (i === startRowIndex) continue; // Ignorar la celda de inicio

      const row = rows[i];
      const cell = row.cells[startCellIndex];

      if (cell && cell.classList.contains("excel-behavior-applied")) {
        cell.classList.add("fill-target");
        cellsToFill.push(cell);
      }
    }
  } else {
    // Arrastre horizontal (misma fila)
    if (startRowIndex === targetRowIndex) {
      const cells = Array.from(startRow.cells).filter((c) =>
        c.classList.contains("excel-behavior-applied")
      );

      const startIdx = cells.indexOf(startCell);
      const targetIdx = cells.indexOf(targetCell);

      const minIdx = Math.min(startIdx, targetIdx);
      const maxIdx = Math.max(startIdx, targetIdx);

      for (let i = minIdx; i <= maxIdx; i++) {
        if (i === startIdx) continue; // Ignorar la celda de inicio

        const cell = cells[i];
        cell.classList.add("fill-target");
        cellsToFill.push(cell);
      }
    }
  }
}

// Función para finalizar el arrastre y aplicar el valor
function endFillDrag(event) {
  if (!dragInProgress) return;

  // Aplicar el valor a todas las celdas marcadas
  if (dragStartCell && cellsToFill.length > 0) {
    const valueToFill = dragStartCell.textContent.trim();

    cellsToFill.forEach((cell) => {
      // Guardar el valor original para posible reversión
      if (!cell.hasAttribute("data-original-value")) {
        cell.setAttribute("data-original-value", cell.textContent.trim());
      }

      cell.textContent = valueToFill;
      cell.style.backgroundColor = "#FFFACD"; // Color amarillo claro para indicar cambio
      changedCells.add(cell);

      // Limpiar la marca de objetivo de relleno
      cell.classList.remove("fill-target");
    });

    showSaveButton();
    showEditIcons();
  }

  // Limpiar el estado
  dragInProgress = false;
  dragStartCell = null;
  cellsToFill = [];

  // Quitar los listeners
  document.removeEventListener("mousemove", handleFillDrag);
  document.removeEventListener("mouseup", endFillDrag);
}

function enterEditMode(cell) {
  if (!cell) return;

  // Primero eliminamos el manejador de relleno para evitar interferencias
  removeFillHandle(cell);

  // Activar la edición de la celda
  cell.setAttribute("contenteditable", "true");
  editMode = true;

  // Posicionar el cursor al final del texto usando la función mejorada
  placeCursorAtEnd(cell);

  // Asegurarnos de que la celda tenga el foco
  cell.focus();
}

originalEnterEditMode = enterEditMode;
enterEditMode = function (cell) {
  if (
    !cell ||
    (!puedeEditar && userRole.value !== "0" && userRole.value !== "4")
  ) {
    if (!puedeEditar) {
      showFeedbackMessage(
        "No puedes editar fuera de las fechas de Programación Académica."
      );
    }
    return;
  }
  // Guarda el valor antes de entrar en edición
  const previousValue = cell.textContent;

  // Llamar a la función original
  originalEnterEditMode.call(this, cell);

  // Guarda un punto de referencia para deshacer
  saveActionForUndo({
    type: "edit",
    cell: cell,
    previousValue: previousValue,
    newValue: previousValue, // Mismo valor, solo marca un punto en el historial
  });
};

function exitEditMode() {
  if (activeCell && editMode) {
    // Desactivar la edición de la celda
    activeCell.setAttribute("contenteditable", "false");
    editMode = false;

    // Verificar si ha habido cambios
    const originalValue = activeCell.getAttribute("data-original-value");
    const currentValue = activeCell.textContent.trim();

    if (originalValue !== currentValue) {
      activeCell.style.backgroundColor = "#FFFACD"; // Color amarillo claro para indicar cambio
      changedCells.add(activeCell);
      showSaveButton();
      showEditIcons();
    }

    // Mostrar nuevamente el manejador de relleno
    addFillHandle(activeCell);
  }
}

function placeCursorAtEnd(cell) {
  // Asegurarnos de que la celda tenga el foco
  cell.focus();

  // En lugar de usar el método complejo de selección de rango,
  // usaremos una técnica más fiable para posicionar el cursor

  // Primero, guardamos el contenido actual
  const content = cell.textContent;

  // Vaciamos la celda
  cell.textContent = "";

  // Creamos un nuevo nodo de texto con el contenido original
  const textNode = document.createTextNode(content);

  // Añadimos el nodo de texto a la celda
  cell.appendChild(textNode);

  // Creamos un rango en la posición correcta (al final del texto)
  const range = document.createRange();
  const selection = window.getSelection();

  // Establecemos el rango al final del nodo de texto
  range.setStart(textNode, content.length);
  range.setEnd(textNode, content.length);

  // Aplicamos la selección
  selection.removeAllRanges();
  selection.addRange(range);
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
        activeCell.textContent =
          activeCell.getAttribute("data-original-value") || "";
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

// Eliminar el manejador al navegar
const originalHandleKeyNavigation = handleKeyNavigation;
handleKeyNavigation = function (event) {
  // Si es uno de nuestros atajos, no procesamos la navegación normal
  if (event.ctrlKey && ["c", "x", "v", "z"].includes(event.key)) {
    return;
  }

  // Guardar la celda activa actual antes de la navegación
  const prevActiveCell = activeCell;

  // Llamar a la función original
  originalHandleKeyNavigation.call(this, event);

  // Si la celda activa ha cambiado, remover el manejador de la celda anterior
  if (prevActiveCell && prevActiveCell !== activeCell) {
    removeFillHandle(prevActiveCell);
  }
};

function navigateToCell(direction) {
  if (!activeCell) return;

  const table = document.getElementById("tabla-datos");
  const rows = table.querySelectorAll("tbody tr");
  const rowArray = Array.from(rows);
  const currentRow = activeCell.parentElement;
  const rowIndex = rowArray.indexOf(currentRow);

  // Obtener todas las celdas editables (excluyendo la columna de ID)
  const allEditableCells = Array.from(
    table.querySelectorAll("td.excel-behavior-applied")
  );

  let nextCell = null;

  switch (direction) {
    case "up":
      if (rowIndex > 0) {
        // Buscar la celda en la misma posición en la fila anterior
        const cellIndex = Array.from(currentRow.cells).indexOf(activeCell);
        const upperRow = rowArray[rowIndex - 1];
        if (upperRow && upperRow.cells[cellIndex]) {
          const targetCell = upperRow.cells[cellIndex];
          if (targetCell.classList.contains("excel-behavior-applied")) {
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
          if (targetCell.classList.contains("excel-behavior-applied")) {
            nextCell = targetCell;
          }
        }
      }
      break;

    case "left":
      // Buscar la celda anterior editable en la misma fila
      const currentCells = Array.from(currentRow.cells).filter((cell) =>
        cell.classList.contains("excel-behavior-applied")
      );
      const currentIndex = currentCells.indexOf(activeCell);
      if (currentIndex > 0) {
        nextCell = currentCells[currentIndex - 1];
      }
      break;

    case "right":
      // Buscar la celda siguiente editable en la misma fila
      const rowCells = Array.from(currentRow.cells).filter((cell) =>
        cell.classList.contains("excel-behavior-applied")
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
      // Permitir 4 dígitos + letra opcional
      newText = newText
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, "")
        .slice(0, 6);
      break;
    case "CRN":
    case "C_MIN":
    case "H_TOTALES":
      newText = newText.replace(/\D/g, "").slice(0, 2);
      break;
    case "CODIGO_PROFESOR":
    case "CODIGO_DESCARGA":
      newText = newText.replace(/\D/g, "").slice(0, 9);
      break;
    case "HORAS":
      // Permitir: vacío, números, un solo punto decimal
      newText = newText
        .replace(/[^0-9.]/g, "") // Permite números y puntos
        .replace(/(\..*)\./g, "$1") // Elimina puntos adicionales
        .replace(/^\./, "0."); // Si empieza con punto, añade 0

      // Limitar a un decimal y 4 caracteres máximo (ej: 99.9)
      if (newText.includes(".")) {
        const partes = newText.split(".");
        newText =
          partes[0].slice(0, 2) +
          "." +
          (partes[1] ? partes[1].slice(0, 1) : "");
      } else {
        newText = newText.slice(0, 3);
      }
      break;
    case "CODIGO_DEPENDENCIA":
    case "HORA_INICIAL":
    case "HORA_FINAL":
      // Permitir solo 4 dígitos numéricos
      newText = newText
        .replace(/\D/g, "") // Solo números
        .slice(0, 4); // Limitar a 4 dígitos
      break;
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

// Agrega la funcionalidad para guardad en el historias de deshacer a la función updateCell
const originalUpdateCell = updateCell;
updateCell = function (cell) {
  if (!puedeEditar) {
    showFeedbackMessage(
      "No puedes editar fuera de las fechas de Programación Académica."
    );
    return;
  }
  // Guarda el valor antes de modificarlo
  const previousValue = cell.textContent;

  // Llamar a la función original
  originalUpdateCell.call(this, cell);

  // Si el valor cambió, guarda la acción
  if (previousValue !== cell.textContent) {
    saveActionForUndo({
      type: "edit",
      cell: cell,
      previousValue: previousValue,
      newValue: cell.textContent,
    });
  }
};

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

// Estilos CSS para las celdas seleccionadas
function addExcelStyleCSS() {
  if (document.getElementById("excel-style-css")) return;

  const style = document.createElement("style");
  style.id = "excel-style-css";
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
    /* Estilos para el manejador de relleno */
    .fill-handle {
      position: absolute;
      bottom: -4px;
      right: -4px;
      width: 8px;
      height: 8px;
      background-color: #217346;
      border: 1px solid white;
      cursor: crosshair;
      z-index: 100;
    }
    .fill-target {
      background-color: rgba(33, 115, 70, 0.2) !important;
    }
  `;
  document.head.appendChild(style);
}

// Añade estilos para las animaciones a la función addExcelStyleCSS
const originalAddExcelStyleCSS = addExcelStyleCSS;
addExcelStyleCSS = function () {
  originalAddExcelStyleCSS();

  if (document.getElementById("excel-shortcuts-css")) return;

  const style = document.createElement("style");
  style.id = "excel-shortcuts.css";
  style.textContent = `
  .action-feedback {
    position: absolute;
    background-color: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    z-index: 1000;
    pointer-events: none;
  }
  `;
  document.head.appendChild(style);
};

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
    if (!lastCell.querySelector("input")) {
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
  if (!puedeEditar) {
    showFeedbackMessage(
      "No puedes guardar cambios fuera de las fechas de Programación Académica."
    );
    return;
  }
  const userRole = document.getElementById("user-role").value;
  if (
    (userRole !== "0" && !puedeEditar) ||
    (userRole === "0" && !puedeEditar)
  ) {
    hideEditIcons();
    return;
  }
  const departmentId = document.getElementById("departamento_id").value;
  console.log("User Role:", userRole, "Department ID:", departmentId);

  // Para tracking de cambios múltiples
  const totalChanges = changedCells.size;
  let completedChanges = 0;

  const promises = Array.from(changedCells).map((cell) => {
    const id = cell.parentNode.cells[1].textContent;
    const column = getColumnName(cell);
    let value = cell.textContent;
    // Limpieza básica de datos
    value = value.replace(/[^\x00-\x7F]/g, "");
    // Añadir info para debug
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
        user_role: userRole,
        department_id: departmentId, // Enviar el departamento_id actual
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
        completedChanges++;
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

      if (typeof actualizarNotificaciones === "function") {
        actualizarNotificaciones();
      }

      console.log("Todos los cambios guardados:", results);
      changedCells.clear();
      hideEditIcons();
    })
    .catch((error) => {
      console.error("Error saving changes:", error);
      if (typeof Swal !== "undefined") {
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
// Listener de documento para eliminar el manejador cuando se hace clic fuera de la tabla
document.addEventListener("DOMContentLoaded", function () {
  makeEditable();
  // Si no puede editar, ocultar íconos por defecto
  if (!puedeEditar) {
    hideEditIcons();
  }

  document.addEventListener("click", function (e) {
    const table = document.getElementById("tabla-datos");
    if (!table) return;

    // Verificar si el clic fue fuera de la tabla o en un elemento que no es una celda editable
    if (
      !table.contains(e.target) ||
      !e.target.closest("td.excel-behavior-applied")
    ) {
      // Clic fuera de la tabla o en un elemento no editable

      // Primero salir del modo edición si estamos en él
      if (editMode && activeCell) {
        exitEditMode();
      }

      // Eliminar explícitamente todos los manejadores de relleno que puedan existir
      document.querySelectorAll(".fill-handle").forEach((handle) => {
        handle.parentNode.removeChild(handle);
      });

      // También limpiar cualquier estado de arrastre
      dragInProgress = false;
      dragStartCell = null;
      document.removeEventListener("mousemove", handleFillDrag);
      document.removeEventListener("mouseup", endFillDrag);

      // Limpiar las marcas de celdas objetivo
      document.querySelectorAll(".fill-target").forEach((cell) => {
        cell.classList.remove("fill-target");
      });

      // Deseleccionar la celda activa
      if (activeCell) {
        activeCell.classList.remove("selected-cell");
        activeCell = null;
      }

      // Actualizar el estado del manejador
      fillHandleVisible = false;
    }
  });
});

// Modificación de la inicialización para incluir las nuevas funciones
const originalDOMContentLoaded = document.addEventListener;
document.addEventListener = function (event, callback) {
  if (event === "DOMContentLoaded") {
    const originalCallback = callback;
    callback = function () {
      originalCallback();
      setupKeyboardShortcuts();
    };
  }

  return originalDOMContentLoaded.call(this, event, callback);
};

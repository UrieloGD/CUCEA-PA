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
  CRN: 10,
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
      // Permitir hasta 10 dígitos para CRN
      newText = newText.replace(/\D/g, "").slice(0, 10);
      break;
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

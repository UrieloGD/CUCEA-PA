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
  ID: "ID",
  CODIGO: "Codigo",
  PATERNO: "Paterno",
  MATERNO: "Materno",
  NOMBRES: "Nombres",
  "NOMBRE COMPLETO": "Nombre_completo",
  SEXO: "Sexo",
  DEPARTAMENTO: "Departamento",
  "CATEGORIA ACTUAL": "Categoria_actual",
  "CATEGORIA ACTUAL": "Categoria_actual_dos",
  "HORAS FRENTE A GRUPO": "Horas_frente_grupo",
  DIVISION: "Division",
  "TIPO DE PLAZA": "Tipo_plaza",
  "CAT.ACT.": "Cat_act",
  "CARGA HORARIA": "Carga_horaria",
  "HORAS DEFINITIVAS": "Horas_definitivas",
  HORARIO: "Horario",
  TURNO: "Turno",
  "INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION":
    "Investigacion_nombramiento_cambio_funcion",
  "S.N.I.": "SNI",
  "SIN DESDE": "SIN_desde",
  "CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR": "Cambio_dedicacion",
  INICIO: "Inicio",
  FIN: "Fin",
  "2024A": "2024A",
  "TELEFONO PARTICULAR": "Telefono_particular",
  "TELEFONO OFICINA O CELULAR": "Telefono_oficina",
  DOMICILIO: "Domicilio",
  COLONIA: "Colonia",
  "C.P.": "CP",
  CIUDAD: "Ciudad",
  ESTADO: "Estado",
  "NO. AFIL. I.M.S.S.": "No_imss",
  "C.U.R.P.": "CURP",
  RFC: "RFC",
  "LUGAR DE NACIMIENTO": "Lugar_nacimiento",
  "ESTADO CIVIL": "Estado_civil",
  "TIPO DE SANGRE": "Tipo_sangre",
  "FECHA NAC.": "Fecha_nacimiento",
  EDAD: "Edad",
  NACIONALIDAD: "Nacionalidad",
  "CORREO ELECTRONICO": "Correo",
  "CORREOS OFICIALES": "Correos_oficiales",
  "ULTIMO GRADO": "Ultimo_grado",
  PROGRAMA: "Programa",
  NIVEL: "Nivel",
  INSTITUCION: "Institucion",
  "ESTADO/PAIS": "Estado_pais",
  AÑO: "Año",
  "GDO EXP": "Gdo_exp",
  "OTRO GRADO": "Otro_grado",
  PROGRAMA: "Otro_programa",
  NIVEL: "Otro_nivel",
  INSTITUCION: "Otro_institucion",
  "ESTADO/PAIS": "Otro_estado_pais",
  AÑO: "Otro_año",
  "GDO EXP": "Otro_gdo_exp",
  "OTRO GRADO": "Otro_grado_alternativo",
  PROGRAMA: "Otro_programa_alternativo",
  NIVEL: "Otro_nivel_altenrativo",
  INSTITUCION: "Otro_institucion_alternativo",
  "ESTADO/PAIS": "Otro_estado_pais_alternativo",
  AÑO: "Otro_año_alternativo",
  "GDO EXP": "Otro_gdo_exp_alternativo",
  "PROESDE 24-25": "Proesde_24_25",
  "A PARTIR DE": "A_partir_de",
  "FECHA DE INGRESO": "Fecha_ingreso",
  ANTIGÜEDAD: "Antiguedad",
};

const maxLengths = {
  Codigo: 12,
  Paterno: 35,
  Materno: 35,
  Nombres: 60,
  Nombre_completo: 60,
  Sexo: 10,
  Departamento: 60,
  Categoria_actual: 60,
  Categoria_actual_dos: 60,
  Horas_frente_grupo: 8,
  Division: 60,
  Tipo_plaza: 60,
  Cat_act: 60,
  Carga_horaria: 60,
  Horas_definitivas: 60,
  Horario: 60,
  Turno: 15,
  Investigacion_nombramiento_cambio_funcion: 30,
  SNI: 15,
  Cambio_dedicacion: 30,
  Telefono_particular: 15,
  Telefono_oficina: 15,
  Domicilio: 60,
  Colonia: 60,
  CP: 10,
  Ciudad: 30,
  Estado: 30,
  No_imss: 30,
  CURP: 30,
  RFC: 30,
  Lugar_nacimiento: 50,
  Estado_civil: 15,
  Tipo_sangre: 5,
  Fecha_nacimiento: 15,
  Edad: 5,
  Nacionalidad: 20,
  Correo: 60,
  Correos_oficiales: 60,
  Ultimo_grado: 5,
  Programa: 70,
  Nivel: 10,
  Institucion: 40,
  Estado_pais: 25,
  Año: 8,
  Gdo_exp: 15,
  Otro_grado: 5,
  Otro_programa: 70,
  Otro_nivel: 10,
  Otro_institucion: 30,
  Otro_estado_pais: 25,
  Otro_año: 8,
  Otro_gdo_exp: 15,
  Otro_grado_alternativo: 5,
  Otro_programa_alternativo: 70,
  Otro_nivel_altenrativo: 10,
  Otro_institucion_alternativo: 30,
  Otro_estado_pais_alternativo: 25,
  Otro_año_alternativo: 8,
  Otro_gdo_exp_alternativo: 15,
  Proesde_24_25: 15,
  Antiguedad: 25,
};

// Variables globales para control de estado
let activeCell = null;
let editMode = false;

function makeEditable() {

  // Add at the beginning of makeEditable()
console.log("makeEditable called");
console.log("Table found:", !!document.getElementById("tabla-datos"));
console.log("User role:", document.getElementById("user-role")?.value);

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
  if (!userRole || userRole.value !== "3") {
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

// función para detectar los comandos shortcut
function setupKeyboardShortcuts(){
  document.addEventListener('keydown', function(event) {
    // Solo procesa si tenemos una celda activa
    if(!activeCell) return;

    // Ctrl + C (Copiar)
    if (event.ctrlKey && event.key === 'c') {
      event.preventDefault();
      copySelectedCell();
      return;
    }

    // Ctrl + X (Cortar)
    if(event.ctrlKey && event.key === 'x'){
      event.preventDefault();
      cutSelectedCell();
      return;
    }

    // Ctrl + V (Pegar)
    if(event.ctrlKey && event.key === 'v'){
      event.preventDefault();
      pasteToSelectedCell();
      return;
    }

    // Ctrl + Z (Deshacer)
    if(event.ctrlKey && event.key === 'z'){
      event.preventDefault();
      undoLastAction();
      return;
    }
  });
}

// Función para copiar el contenido de la celda seleccionada
function copySelectedCell() {
  if(!activeCell) return;

  clipboard = {
    text: activeCell.textContent,
    sourceCell: activeCell
  };

  // Muestra el indicador visual de copiado
  showFeedbackAnimation(activeCell, 'copied');
}

// Función para cortar el contenido de la celda seleccionada
function cutSelectedCell() {
  if(!activeCell) return;

  // Guarda el valor actual para deshacer
  saveActionForUndo({
    type: 'edit',
    cell: activeCell,
    previousValue: activeCell.textContent,
    newValue: ''
  });

  // guardar en el portapapeles
  clipboard = {
    text: activeCell.textContent,
    sourceCell: activeCell
  };

  // Limpiar la celda
  const originalValue = activeCell.getAttribute('data-original-value');
  activeCell.textContent = '';

  // Marca como cambiada
  if(activeCell.textContent !== originalValue){
    activeCell.style.backgroundColor = "#FFFACD";
    changedCells.add(activeCell);
    showSaveButton();
    showEditIcons();
  }

  // Mostrar indicador visual
  showFeedbackAnimation(activeCell, 'cut');
}

// Función para pegar el contenido en la celda seleccionada
function pasteToSelectedCell() {
  if(!activeCell || !clipboard) return;

  // No se puede pegar si se ingresa en modo edición
  if (editMode) {
    exitEditMode();
  }

  // Guarda el valor actual para deshacer
  saveActionForUndo({
    type: 'edit',
    cell: activeCell, 
    previousValue: activeCell.textContent,
    newValue: clipboard.text
  });

  // Obtiene el nombre de la columna para validación
  const columnName = getColumnName(activeCell);
  
  // Guarda el texto original antes de validarlo
  let originalText = clipboard.text;
  let newText = originalText;

  // Aplica validaciones según el tipo de columna
  const maxLength = maxLengths[columnName] || 60;

  switch (columnName) {
    case "CODIGO":
    case "HORAS_FRENTE_GRUPO":
    case "HORAS_DEFINITIVAS":
    case "TELEFONO_PARTICULAR":
    case "TELEFONO_OFICINA":
    case "CP":
    case "NO_IMSS":
    case "AÑO":
    case "OTRO_AÑO":
    case "OTRO_AÑO_ALTERNATIVO":
    case "PROESDE_24_25":
      newText = newText.replace(/\D/g, ""); // Permitir solo dígitos
      break;
    case "INICIO":
    case "FIN":
    case "FECHA_NACIMIENTO":
      // No modificar el texto, se manejará con un calendario
      break;
    default:
      // Convertir a mayúsculas
      newText = newText.toUpperCase();
      break;
  }

  // Limitar la longitud
  if(newText.length > maxLength){
    newText = newText.slice(0, maxLength);
  }

  // Asigna el valor validado
  activeCell.textContent = newText;

  // Marcar como cambiaba
  const originalValue = activeCell.getAttribute('data-original-value');
  if (activeCell.textContent !== originalValue) {
    activeCell.style.backgroundColor = "#FFFACD";
    changedCells.add(activeCell);
    showSaveButton();
    showEditIcons();
  }

  // Mostrar indicador visual
  showFeedbackAnimation(activeCell, 'pasted');
}

function saveActionForUndo(action) {
  // Añade la acción al inicio del array
  undoStack.unshift(action);

  // Limita el tamaño del historial
  if (undoStack.length > MAX_UNDO_STACK) {
    undoStack.pop();
  }
}

// Función para deshacer la última acción
function undoLastAction() {
  if(undoStack.length === 0) return;

  // Obtiene la última acción
  const action = undoStack.shift();

  if (action.type === 'edit') {
    // Restaurar el valor interior
    action.cell.textContent = action.previousValue;

    // Verifica si vuelve al valor original
    const originalValue = action.cell.getAttribute('data-original-value');
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

    // Muestra el indicador visual de deshacer
    showFeedbackAnimation(action.cell, 'undone');
  }
}

function showFeedbackAnimation(cell, action) {
  if(!cell) return;

  // Crear un div para la animación
  const feedback = document.createElement('div');
  feedback.className = 'action-feedback';

  // Configurar el mensaje según la acción
  let message = '';
  switch (action) {
    case 'copied': message = 'Copiado'; break;
    case 'cut': message = 'Cortado'; break;
    case 'pasted': message = 'Pegado'; break;
    case 'undone': message = 'Deshecho'; break;
  }

  feedback.textContent = message;

  // Establece estilos
  feedback.style.position = 'absolute';
  feedback.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
  feedback.style.color = '#fff';
  feedback.style.padding = '4px 8px';
  feedback.style.borderRadius = '3px';
  feedback.style.fontSize = '12px';
  feedback.style.zIndex = '1000';
  feedback.style.pointerEvents = 'none';

  // Calcula la posición (encima de la celda)
  const cellRect = cell.getBoundingClientRect();
  const tableRect = document.getElementById('tabla-datos').getBoundingClientRect();

  feedback.style.top = (cellRect.top - 25) + 'px';
  feedback.style.left = (cellRect.left + (cellRect.width / 2) - 40) + 'px';

  // Añadir el documento
  document.body.appendChild(feedback);

  // Configura la animación
  feedback.style.opacity = '0';
  feedback.style.transform = 'translateY(10px)';
  feedback.style.transition = 'opacity 0.2s, transform 0.2s';

  // Activa la animación después de un pequeño retraso
  setTimeout(() => {
    feedback.style.opacity = '1';
    feedback.style.transform = 'translateY(0)';
  }, 10);

  // Se elimina despues de un tiempo
  setTimeout(() => {
    feedback.style.opacity = '0';
    feedback.style.transform = 'translateY(-10px)';

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
  const existingHandle = cell.querySelector('.fill-handle');
  if (existingHandle) {
    cell.removeChild(existingHandle);
  }
  
  // Como medida de seguridad, eliminar cualquier otro manejador que pudiera existir
  document.querySelectorAll('.fill-handle').forEach(handle => {
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
    document.removeEventListener('mousemove', handleFillDrag);
    document.removeEventListener('mouseup', endFillDrag);
  }
  
  // Limpiar las marcas de celdas objetivo
  document.querySelectorAll('.fill-target').forEach(cell => {
    cell.classList.remove('fill-target');
  });
}

// Función para añadir el manejador de relleno a una celda
function addFillHandle(cell) {
  // Eliminar primero cualquier manejador existente, tanto en esta celda como en otras
  removeFillHandle(cell);
  
  // Si la celda es válida, añadir el manejador
  if (cell && cell.classList.contains('excel-behavior-applied')) {
    // Crear el nuevo manejador
    const fillHandle = document.createElement('div');
    fillHandle.className = 'fill-handle';
    fillHandle.addEventListener('mousedown', startFillDrag);
    
    // Asegurar que la celda tenga posición relativa
    cell.style.position = 'relative';
    
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
  document.addEventListener('mousemove', handleFillDrag);
  document.addEventListener('mouseup', endFillDrag);
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
  document.querySelectorAll('.fill-target').forEach(cell => {
    cell.classList.remove('fill-target');
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
  const rows = Array.from(table.querySelectorAll('tbody tr'));
  
  // Obtener la posición de inicio (celda original)
  const startCell = dragStartCell;
  const startRow = startCell.parentElement;
  const startRowIndex = rows.indexOf(startRow);
  const startCellIndex = Array.from(startRow.cells).indexOf(startCell);
  
  // Encontrar la celda bajo el cursor
  let targetCell = null;
  let targetRow = null;
  
  document.querySelectorAll('#tabla-datos td.excel-behavior-applied').forEach(cell => {
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
      
      if (cell && cell.classList.contains('excel-behavior-applied')) {
        cell.classList.add('fill-target');
        cellsToFill.push(cell);
      }
    }
  } else {
    // Arrastre horizontal (misma fila)
    if (startRowIndex === targetRowIndex) {
      const cells = Array.from(startRow.cells).filter(c => 
        c.classList.contains('excel-behavior-applied')
      );
      
      const startIdx = cells.indexOf(startCell);
      const targetIdx = cells.indexOf(targetCell);
      
      const minIdx = Math.min(startIdx, targetIdx);
      const maxIdx = Math.max(startIdx, targetIdx);
      
      for (let i = minIdx; i <= maxIdx; i++) {
        if (i === startIdx) continue; // Ignorar la celda de inicio
        
        const cell = cells[i];
        cell.classList.add('fill-target');
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
    
    cellsToFill.forEach(cell => {
      // Guardar el valor original para posible reversión
      if (!cell.hasAttribute('data-original-value')) {
        cell.setAttribute('data-original-value', cell.textContent.trim());
      }
      
      cell.textContent = valueToFill;
      cell.style.backgroundColor = "#FFFACD"; // Color amarillo claro para indicar cambio
      changedCells.add(cell);
      
      // Limpiar la marca de objetivo de relleno
      cell.classList.remove('fill-target');
    });
    
    showSaveButton();
    showEditIcons();
  }
  
  // Limpiar el estado
  dragInProgress = false;
  dragStartCell = null;
  cellsToFill = [];
  
  // Quitar los listeners
  document.removeEventListener('mousemove', handleFillDrag);
  document.removeEventListener('mouseup', endFillDrag);
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

// Modificación para guardar el estado antes de entrar en edición la función 'enterEditMode'

const originalEnterEditMode = enterEditMode;
enterEditMode = function(cell) {
  if(!cell) return;

  // Guarda el valor antes de entrar en edición
  const previousValue = cell.textContent;

  // Llamar a la función original
  originalEnterEditMode.call(this, cell);

  // Guarda un punto de referencia para deshacer
  saveActionForUndo({
    type: 'edit',
    cell: cell,
    previousValue: previousValue,
    newValue: previousValue // Mismo valor, solo marca un punto en el historial
  });
};

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
  cell.textContent = '';
  
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

// Eliminar el manejador al navegar
const originalHandleKeyNavigation = handleKeyNavigation;
handleKeyNavigation = function(event) {
  // Si es uno de los atajos, no se procesa la navegación normal
  if (event.ctrlKey && ['c', 'x', 'v', 'z'].includes(event.key)) {
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
    case "CODIGO":
    case "HORAS_FRENTE_GRUPO":
    case "HORAS_DEFINITIVAS":
    case "TELEFONO_PARTICULAR":
    case "TELEFONO_OFICINA":
    case "CP":
    case "NO_IMSS":
    case "AÑO":
    case "OTRO_AÑO":
    case "OTRO_AÑO_ALTERNATIVO":
    case "PROESDE_24_25":
      newText = newText.replace(/\D/g, ""); // Permitir solo dígitos
      break;
    case "INICIO":
    case "FIN":
    case "FECHA_NACIMIENTO":
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

// Modificación para guarda el historial de deshacer para la función updateCell
const originalUpdateCell = updateCell;
updateCell = function(cell) {
  // Guarda el valor antes de modificarlo
  const previousValue = cell.textContent

  // Llamar a la función original
  originalUpdateCell.call(this, cell);

  // Si el valor cambió, se guarda la acción
  if (previousValue !== cell.textContent) {
    saveActionForUndo({
      type: 'edit',
      cell: cell,
      previousValue: previousValue,
      newValue: cell.textContent
    });
  }
};

// Estilos CSS para las celdas seleccionadas
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

// Modificación para añadir estilos para las animaciones a la funcion de 'addExcelStyleCSS'
const originalAddExcelStyleCSS = addExcelStyleCSS;
addExcelStyleCSS = function(){
  originalAddExcelStyleCSS();

  if(document.getElementById('excel-shortcuts-css')) return;

  const style = document.createElement('style');
  style.id = 'excel-shortcuts-css';
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

function showCharacterCount(cell) {
  const columnName = getColumnName(cell);
  const maxLength = maxLengths[columnName] || 60;
  const countSpan = document.createElement("span");
  countSpan.className = "char-count";
  countSpan.style.position = "absolute";
  countSpan.style.bottom = "0";
  countSpan.style.right = "0";
  countSpan.style.fontSize = "10px";
  countSpan.style.color = "#888";
  cell.style.position = "relative";
  cell.appendChild(countSpan);
  updateCharacterCount(cell);
}

function hideCharacterCount(cell) {
  const countSpan = cell.querySelector(".char-count");
  if (countSpan) {
    cell.removeChild(countSpan);
  }
}

function updateCharacterCount(cell) {
  const columnName = getColumnName(cell);
  const maxLength = maxLengths[columnName] || 60;
  const remainingChars = maxLength - cell.textContent.length;
  const countSpan = cell.querySelector(".char-count");
  if (countSpan) {
    countSpan.textContent = remainingChars;
  }
}

function getColumnName(cell) {
  const headerRow = document.querySelector("#tabla-datos tr");
  let columnName = headerRow.cells[cell.cellIndex].textContent.trim();
  console.log("Column name from header:", columnName); // Para depuración
  let mappedName = columnMap[columnName] || columnName;
  console.log("Mapped column name:", mappedName); // Para depuración
  return mappedName;
}

function showSaveButton() {
  const saveIcon = document.getElementById("icono-guardar");
  saveIcon.style.visibility = "visible";
  saveIcon.style.opacity = "1";
}

function hideSaveButton() {
  const saveIcon = document.getElementById("icono-guardar");
  saveIcon.style.visibility = "hidden";
  saveIcon.style.opacity = "0";
}

function showEditIcons() {
  const saveIcon = document.getElementById("icono-guardar");
  const undoIcon = document.getElementById("icono-deshacer");
  saveIcon.style.visibility = "visible";
  saveIcon.style.opacity = "1";
  undoIcon.style.visibility = "visible";
  undoIcon.style.opacity = "1";
}

function hideEditIcons() {
  const saveIcon = document.getElementById("icono-guardar");
  const undoIcon = document.getElementById("icono-deshacer");
  saveIcon.style.visibility = "hidden";
  saveIcon.style.opacity = "0";
  undoIcon.style.visibility = "hidden";
  undoIcon.style.opacity = "0";
}

function undoAllChanges() {
  if (changedCells.size > 0) {
    // Obtener la última celda modificada
    const lastCell = Array.from(changedCells).pop();

    // Revertir los cambios de la última celda
    const originalValue = lastCell.getAttribute("data-original-value");
    if (originalValue !== null) {
      lastCell.textContent = originalValue;
    }
    lastCell.style.backgroundColor = "";
    lastCell.removeAttribute("data-original-value");

    // Eliminar la última celda de la lista de celdas modificadas
    changedCells.delete(lastCell);
  }

  // Si ya no quedan celdas modificadas, ocultar los iconos de edición
  if (changedCells.size === 0) {
    hideEditIcons();
  }
}

function saveAllChanges() {
  const promises = [];
  changedCells.forEach((cell) => {
    const row = cell.closest("tr");
    const id = row.getAttribute("data-id");
    const columnName = getColumnName(cell);
    const newValue = cell.textContent;

    console.log(
      "Guardando cambios - ID:",
      id,
      "Columna:",
      columnName,
      "Valor:",
      newValue
    );

    if (!id) {
      console.error("No se pudo obtener el ID para la fila:", row);
      return;
    }

    promises.push(
      fetch("./functions/coord-personal-plantilla/actualizar-celda-coord.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `id=${encodeURIComponent(id)}&column=${encodeURIComponent(
          columnName
        )}&value=${encodeURIComponent(newValue)}`,
      }).then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
    );
  });

  Promise.all(promises)
    .then((results) => {
      results.forEach((data, index) => {
        const cell = Array.from(changedCells)[index];
        if (data.success) {
          cell.style.backgroundColor = "#90EE90";
          cell.removeAttribute("data-original-value");
          setTimeout(() => {
            cell.style.backgroundColor = "";
          }, 2000);
        } else {
          cell.style.backgroundColor = "#FFB6C1";
          if (data.oldValue !== undefined) {
            cell.textContent = data.oldValue;
          }
          console.error("Error al actualizar:", data.error);
          alert(`Error al actualizar: ${data.error}`);
        }
      });
      changedCells.clear();
      hideEditIcons();
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(`Error de red o del servidor: ${error.message}`);
    });
}

// Inicialización al cargar el documento
// Listener de documento para eliminar el manejador cuando se hace clic fuera de la tabla
document.addEventListener("DOMContentLoaded", function() {
  makeEditable();
  
  document.addEventListener("click", function(e) {
    const table = document.getElementById("tabla-datos");
    if (!table) return;
    
    // Verificar si el clic fue fuera de la tabla o en un elemento que no es una celda editable
    if (!table.contains(e.target) || !e.target.closest('td.excel-behavior-applied')) {
      // Clic fuera de la tabla o en un elemento no editable
      
      // Primero salir del modo edición si estamos en él
      if (editMode && activeCell) {
        exitEditMode();
      }
      
      // Eliminar explícitamente todos los manejadores de relleno que puedan existir
      document.querySelectorAll('.fill-handle').forEach(handle => {
        handle.parentNode.removeChild(handle);
      });
      
      // También limpiar cualquier estado de arrastre
      dragInProgress = false;
      dragStartCell = null;
      document.removeEventListener('mousemove', handleFillDrag);
      document.removeEventListener('mouseup', endFillDrag);
      
      // Limpiar las marcas de celdas objetivo
      document.querySelectorAll('.fill-target').forEach(cell => {
        cell.classList.remove('fill-target');
      });
      
      // Deseleccionar la celda activa
      if (activeCell) {
        activeCell.classList.remove('selected-cell');
        activeCell = null;
      }
      
      // Actualizar el estado del manejador
      fillHandleVisible = false;
    }
  });
});

// Modificación de la inicialización para incluir las nuevas funciones
const originalDOMContentLoaded = document.addEventListener;
document.addEventListener = function(event, callback) {
  if (event === 'DOMContentLoaded') {
    const originalCallback = callback;
    callback = function() {
      originalCallback();
      setupKeyboardShortcuts();
    };
  }

  return originalDOMContentLoaded.call(this, event, callback);
};
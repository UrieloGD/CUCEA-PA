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

function makeEditable() {
  const table = document.getElementById("tabla-datos");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName("td");
    for (let j = 1; j < cells.length; j++) {
      if (j !== 1) {
        cells[j].setAttribute(
          "data-original-value",
          cells[j].textContent.trim()
        );
        cells[j].setAttribute("contenteditable", "true");
        cells[j].addEventListener("input", function () {
          updateCell(this);
        });
      }
    }
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

  // Actualizar el contenido de la celda
  cell.textContent = newText;

  // Restaurar la posición del cursor
  range.setStart(cell.firstChild, Math.min(cursorPosition, newText.length));
  range.setEnd(cell.firstChild, Math.min(cursorPosition, newText.length));
  selection.removeAllRanges();
  selection.addRange(range);

  cell.style.backgroundColor = "#FFFACD";
  changedCells.add(cell);
  showSaveButton();
  showEditIcons();
}

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

  // Eliminar emojis y otros caracteres especiales
  columnName = columnName
    .replace(
      /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F1E0}-\u{1F1FF}]/gu,
      ""
    )
    .trim();

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
  const promises = Array.from(changedCells).map((cell) => {
    const id = cell.parentNode.cells[1].textContent;
    const column = getColumnName(cell);
    let value = cell.textContent;

    // Limpieza básica de datos
    value = value.replace(/[^\x00-\x7F]/g, "");
    // value = encodeURIComponent(value);

    return fetch("./functions/basesdedatos/actualizar-celda.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id, column, value }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        if (data.error) {
          throw new Error(data.error);
        }
        // Almacenar la celda junto con la respuesta para usarla en el siguiente then
        return { cell, data };
      });
  });

  Promise.all(promises)
    .then((results) => {
      results.forEach(({ cell, data }) => {
        if (data.success) {
          // Cambiar el color de fondo a verde suave
          cell.style.backgroundColor = "#90EE90"; // Light green

          // Opcional: Hacer una transición suave después de 2 segundos
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
      console.error("Error al guardar los cambios:", error);
      alert(
        "Hubo un error al guardar los cambios. Por favor, inténtelo de nuevo."
      );

      // Opcional: Mostrar color rojo en caso de error
      changedCells.forEach((cell) => {
        cell.style.backgroundColor = "#fe726c";
      });
    });
}

document.addEventListener("DOMContentLoaded", makeEditable);

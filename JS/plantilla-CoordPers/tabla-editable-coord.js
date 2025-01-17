let changedCells = new Set();

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
  Institucion: 30,
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

function makeEditable() {
  const table = document.getElementById("tabla-datos");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName("td");
    for (let j = 1; j < cells.length; j++) {
      // Excluir la columna de estado y la columna de ID
      if (j !== 1 && !cells[j].classList.contains("estado-cell")) {
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
  const row = cell.closest("tr");
  const id = row.getAttribute("data-id");

  if (!id) {
    console.error("No se pudo obtener el ID para la fila:", row);
    return;
  }

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
    case "Horas_frente_grupo":
    case "Horas_definitivas":
    case "Telefono_particular":
    case "Telefono_oficina":
    case "CP":
    case "No_imss":
    case "Edad":
    case "Año":
    case "Otro_año":
    case "Otro_año_alternativo":
      newText = newText.replace(/\D/g, ""); // Permitir solo dígitos
      break;
    case "SIN_desde":
    case "Inicio":
    case "Fin":
    case "Fecha_nacimiento":
    case "A_partir_de":
    case "Fecha_ingreso":
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

document.addEventListener("DOMContentLoaded", makeEditable);

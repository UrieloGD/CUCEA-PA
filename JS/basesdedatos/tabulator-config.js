// tabulator-config.js
// Configuración e inicialización de Tabulator

// Custom Date Editor
var dateEditor = function (cell, onRendered, success, cancel) {
  //cell - the cell component for the editable cell
  //onRendered - function to call when the editor has been rendered
  //success - function to call to pass the successfully updated value to Tabulator
  //cancel - function to call to abort the edit and return to a normal cell

  //create and style input
  var cellValue = luxon.DateTime.fromFormat(
      cell.getValue(),
      "dd/MM/yyyy"
    ).toFormat("yyyy-MM-dd"),
    input = document.createElement("input");

  input.setAttribute("type", "date");

  input.style.padding = "4px";
  input.style.width = "100%";
  input.style.boxSizing = "border-box";

  input.value = cellValue;

  onRendered(function () {
    input.focus();
    input.style.height = "100%";
  });

  function onChange() {
    if (input.value != cellValue) {
      success(
        luxon.DateTime.fromFormat(input.value, "yyyy-MM-dd").toFormat(
          "dd/MM/yyyy"
        )
      );
    } else {
      cancel();
    }
  }

  //submit new value on blur or change
  input.addEventListener("blur", onChange);

  //submit new value on enter
  input.addEventListener("keydown", function (e) {
    if (e.keyCode == 13) {
      onChange();
    }

    if (e.keyCode == 27) {
      cancel();
    }
  });

  return input;
};

//define row context menu contents
var rowMenu = [
  {
    label: "<i class='fas fa-user'></i> Change Name",
    action: function (e, row) {
      row.update({ name: "Steve Bobberson" });
    },
  },
  {
    label: "<i class='fas fa-check-square'></i> Select Row",
    action: function (e, row) {
      row.select();
    },
  },
  {
    separator: true,
  },
  {
    label: "Admin Functions",
    menu: [
      {
        label: "<i class='fas fa-trash'></i> Delete Row",
        action: function (e, row) {
          row.delete();
        },
      },
      {
        label: "<i class='fas fa-ban'></i> Disabled Option",
        disabled: true,
      },
    ],
  },
];

//define column header menu as column visibility toggle
var headerMenu = function () {
  var menu = [];
  var columns = this.getColumns();

  for (let column of columns) {
    // NUEVO: Excluir las columnas checkbox e ID_Plantilla del menú
    const field = column.getField();
    if (field === "checkbox" || field === "ID_Plantilla") {
      continue; // Saltar estas columnas
    }

    //create checkbox element using font awesome icons
    let icon = document.createElement("i");
    icon.classList.add("fas");
    icon.classList.add(column.isVisible() ? "fa-check-square" : "fa-square");

    //build label
    let label = document.createElement("span");
    let title = document.createElement("span");

    title.textContent = " " + column.getDefinition().title;

    label.appendChild(icon);
    label.appendChild(title);

    //create menu item
    menu.push({
      label: label,
      action: function (e) {
        //prevent menu closing
        e.stopPropagation();

        //toggle current column visibility
        column.toggle();

        //change menu item icon
        if (column.isVisible()) {
          icon.classList.remove("fa-square");
          icon.classList.add("fa-check-square");
        } else {
          icon.classList.remove("fa-check-square");
          icon.classList.add("fa-square");
        }

        // NUEVO: Guardar el estado de visibilidad en el almacenamiento local
        saveColumnVisibility();
      },
    });
  }

  return menu;
};

// NUEVA FUNCIÓN: Guardar la visibilidad de las columnas
function saveColumnVisibility() {
  if (window.table) {
    const columnVisibility = {};
    const columns = window.table.getColumns();

    columns.forEach((column) => {
      const field = column.getField();
      if (field && field !== "checkbox") {
        // Excluir la columna de checkbox
        columnVisibility[field] = column.isVisible();
      }
    });

    localStorage.setItem(
      "tabulator_column_visibility",
      JSON.stringify(columnVisibility)
    );
    console.log("Visibilidad de columnas guardada:", columnVisibility);
  }
}

// NUEVA FUNCIÓN: Restaurar la visibilidad de las columnas
function restoreColumnVisibility(table) {
  try {
    const savedVisibility = localStorage.getItem("tabulator_column_visibility");
    if (savedVisibility) {
      const columnVisibility = JSON.parse(savedVisibility);

      // Aplicar la visibilidad guardada a cada columna
      Object.entries(columnVisibility).forEach(([field, isVisible]) => {
        const column = table.getColumn(field);
        if (column) {
          if (isVisible && !column.isVisible()) {
            column.show();
          } else if (!isVisible && column.isVisible()) {
            column.hide();
          }
        }
      });

      console.log("Visibilidad de columnas restaurada:", columnVisibility);
    }
  } catch (error) {
    console.error("Error al restaurar la visibilidad de columnas:", error);
  }
}

// Definir las columnas de tabulator
const columns = [
  {
    // Columna de selección de filas
    title: "",
    field: "checkbox",
    formatter: "rowSelection",
    titleFormatter: "rowSelection",
    hozAlign: "center",
    headerSort: false,
    width: 50,
    frozen: true, // Fijar la columna de selección
    editor: false, // Explícitamente no editable
  },
  {
    title: "ID",
    field: "ID_Plantilla",
    // headerMenu: headerMenu,
    sorter: "number",
    //headerFilter: true,
    width: 80,
    frozen: true,
  },
  {
    title: "CICLO",
    field: "CICLO",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CRN",
    field: "CRN",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "MATERIA",
    field: "MATERIA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true,
    width: 250,
    editor: "input",
  },
  {
    title: "CVE MATERIA",
    field: "CVE_MATERIA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "SECCIÓN",
    field: "SECCION",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NIVEL",
    field: "NIVEL",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NIVEL TIPO",
    field: "NIVEL_TIPO",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "TIPO",
    field: "TIPO",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "C. MIN",
    field: "C_MIN",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
    validator: ["minLength:1", "maxLength:5", "integer"],
  },
  {
    title: "H. TOTALES",
    field: "H_TOTALES",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "STATUS",
    field: "ESTATUS",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "TIPO CONTRATO",
    field: "TIPO_CONTRATO",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CÓDIGO",
    field: "CODIGO_PROFESOR",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NOMBRE PROFESOR",
    field: "NOMBRE_PROFESOR",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true,
    width: 200,
    editor: "input",
  },
  {
    title: "CATEGORIA",
    field: "CATEGORIA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "DESCARGA",
    field: "DESCARGA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CÓDIGO DESCARGA",
    field: "CODIGO_DESCARGA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NOMBRE DESCARGA",
    field: "NOMBRE_DESCARGA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NOMBRE DEFINITIVO",
    field: "NOMBRE_DEFINITIVO",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "TITULAR",
    field: "TITULAR",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "HORAS",
    field: "HORAS",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CÓDIGO DEPENDENCIA",
    field: "CODIGO_DEPENDENCIA",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "L",
    field: "L",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "M",
    field: "M",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "I",
    field: "I",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "J",
    field: "J",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "V",
    field: "V",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "S",
    field: "S",
    headerMenu: headerMenu,
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "D",
    field: "D",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "DÍA PRESENCIAL",
    field: "DIA_PRESENCIAL",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "DÍA VIRTUAL",
    field: "DIA_VIRTUAL",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "MODALIDAD",
    field: "MODALIDAD",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "FECHA INICIAL",
    field: "FECHA_INICIAL",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "FECHA FINAL",
    field: "FECHA_FINAL",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "HORA INICIAL",
    field: "HORA_INICIAL",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "HORA FINAL",
    field: "HORA_FINAL",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "MÓDULO",
    field: "MODULO",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "AULA",
    field: "AULA",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "CUPO",
    field: "CUPO",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "OBSERVACIONES",
    field: "OBSERVACIONES",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "EXTRAORDINARIO",
    field: "EXAMEN_EXTRAORDINARIO",
    headerMenu: headerMenu,
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
];

// Función para inicializar Tabulator
function initializeTabulator(data) {
  // Initialize Tabulator with the dataset from PHP
  var table = new Tabulator("#tabla-datos", {
    data: data,

    // Añadir persistencia
    persistence: {
      sort: true,
      filter: true,
      columns: true,
    },
    persistenceID: "examplePerststance",

    rowContextMenu: rowMenu,

    // Habilitar el historial de cambios
    history: true,

    // Habilitar la seección por rango
    selectableRange: 1,
    selectableRangeColumns: true,
    selectableRangeRows: true,
    selectableRangeClearCells: true,

    // Hacer que la selección se haga con doble click
    editTriggerEvent: "dblclick",

    // Configuraciones para habilitar copy-paste
    clipboard: true,
    clipboardCopyStyled: false,
    clipboardCopyConfig: {
      rowHeaders: false,
      columnHeaders: false,
    },
    clipboardCopyRowRange: "range",
    clipboardPasteParser: "range",
    clipboardPasteAction: "range",

    // rowHeader: {
    //   resizable: false,
    //   frozen: true,
    //   width: 40,
    //   hozAlign: "center",
    //   formatter: "rownum",
    //   cssClass: "range-header-col",
    //   editor: false,
    // },

    // Configurar celdas para que funcionen como spreadsheets
    columnDefaults: {
      headerSort: false,
      headerHozAlign: "left",
      editor: "input",
      resizable: "header",
      width: 100,
    },
    columns: columns,
    layout: "fitDataFill",
    pagination: "local",
    paginationSize: 25,
    paginationSizeSelector: [15, 25, 50, 100],
    movableColumns: true,
    resizableColumns: true,
    selectable: true,
    selectableRangeMode: "click",
    height: "700px",
    placeholder: "No hay datos disponibles",
    printAsHtml: true,
    printStyled: true,
    headerFilterLiveFilterDelay: 300,

    // Importante: hacer la tabla editable
    cellEditable: function (cell) {
      // Verificar permisos de edición
      const userRole = document.getElementById("user-role")?.value;
      const puedeEditar = window.puedeEditar !== false;

      // Solo permitir edición a roles autorizados y si puedeEditar es true
      return (
        puedeEditar &&
        (userRole === "0" || userRole === "1" || userRole === "4")
      );
    },
  });

  // Hacer la tabla accesible globalmente
  window.table = table;

  return table;
}

function initializeTabulator(data) {
  // Initialize Tabulator with the dataset from PHP
  var table = new Tabulator("#tabla-datos", {
    data: data,

    // Añadir persistencia
    persistence: {
      sort: true,
      filter: true,
      columns: true,
    },
    persistenceID: "examplePerststance",

    rowContextMenu: rowMenu,

    // Habilitar el historial de cambios
    history: true,

    // Habilitar la seección por rango
    selectableRange: 1,
    selectableRangeColumns: true,
    selectableRangeRows: true,
    selectableRangeClearCells: true,

    // Hacer que la selección se haga con doble click
    editTriggerEvent: "dblclick",

    // Configuraciones para habilitar copy-paste
    clipboard: true,
    clipboardCopyStyled: false,
    clipboardCopyConfig: {
      rowHeaders: false,
      columnHeaders: false,
    },
    clipboardCopyRowRange: "range",
    clipboardPasteParser: "range",
    clipboardPasteAction: "range",

    // Configurar celdas para que funcionen como spreadsheets
    columnDefaults: {
      headerSort: false,
      headerHozAlign: "left",
      editor: "input",
      resizable: "header",
      width: 100,
    },
    columns: columns,
    layout: "fitDataFill",
    pagination: "local",
    paginationSize: 25,
    paginationSizeSelector: [15, 25, 50, 100],
    movableColumns: true,
    resizableColumns: true,
    selectable: true,
    selectableRangeMode: "click",
    height: "700px",
    placeholder: "No hay datos disponibles",
    printAsHtml: true,
    printStyled: true,
    headerFilterLiveFilterDelay: 300,

    // Importante: hacer la tabla editable
    cellEditable: function (cell) {
      // Verificar permisos de edición
      const userRole = document.getElementById("user-role")?.value;
      const puedeEditar = window.puedeEditar !== false;

      // Solo permitir edición a roles autorizados y si puedeEditar es true
      return (
        puedeEditar &&
        (userRole === "0" || userRole === "1" || userRole === "4")
      );
    },
  });

  // NUEVO: Restaurar la visibilidad de columnas después de que la tabla esté completamente construida
  table.on("tableBuilt", function () {
    // Esperar un poco para asegurar que la tabla esté completamente renderizada
    setTimeout(() => {
      restoreColumnVisibility(table);
    }, 100);
  });

  // NUEVO: Guardar cambios de visibilidad cuando se use el toggle nativo de Tabulator
  table.on("columnVisibilityChanged", function (column, visible) {
    setTimeout(saveColumnVisibility, 50); // Pequeño delay para asegurar que el cambio se haya aplicado
  });

  // Hacer la tabla accesible globalmente
  window.table = table;

  return table;
}

// NUEVA FUNCIÓN: Limpiar la persistencia de columnas (útil para debugging o reset)
function clearColumnVisibilityStorage() {
  localStorage.removeItem("tabulator_column_visibility");
  console.log("Persistencia de visibilidad de columnas limpiada");
}

// NUEVA FUNCIÓN: Obtener el estado actual de visibilidad (útil para debugging)
function getCurrentColumnVisibility() {
  if (window.table) {
    const columns = window.table.getColumns();
    const visibility = {};
    columns.forEach((column) => {
      const field = column.getField();
      if (field && field !== "checkbox") {
        visibility[field] = column.isVisible();
      }
    });
    console.log("Estado actual de visibilidad:", visibility);
    return visibility;
  }
  return null;
}

// Función para configurar los eventos de Undo/Redo
function setupUndoRedoEvents(table) {
  // Ocultar los botones de undo/redo si existen
  const undoButton = document.getElementById("history-undo");
  const redoButton = document.getElementById("history-redo");

  if (undoButton) {
    undoButton.style.display = "none";
  }
  if (redoButton) {
    redoButton.style.display = "none";
  }

  // Atajos de teclado para Undo/Redo
  document.addEventListener("keydown", function (e) {
    // Ctrl+Z para Undo
    if (e.ctrlKey && e.key === "z" && !e.shiftKey) {
      e.preventDefault();
      table.undo();
    }
    // Ctrl+Y o Ctrl+Shift+Z para Redo
    if (
      (e.ctrlKey && e.key === "y") ||
      (e.ctrlKey && e.shiftKey && e.key === "Z")
    ) {
      e.preventDefault();
      table.redo();
    }
  });

  // Eventos de historial para feedback en consola
  table.on("historyUndo", function () {
    console.log("Undo realizado");
  });

  table.on("historyRedo", function () {
    console.log("Redo realizado");
  });
}

// Función para configurar la búsqueda global
function setupGlobalSearch(table) {
  const searchInput = document.getElementById("input-buscador");
  const searchIcon = document.getElementById("icono-buscador");

  if (searchInput) {
    // Configurar el evento de búsqueda con debounce
    let searchTimeout;

    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        const searchTerm = this.value.trim();

        if (searchTerm === "") {
          // Si no hay término de búsqueda, limpiar filtros
          table.clearFilter();
        } else {
          // Aplicar múltiples filtros para buscar en las columnas principales
          const searchFilters = [
            { field: "Departamento_ID", type: "like", value: searchTerm },
            { field: "CICLO", type: "like", value: searchTerm },
            { field: "CRN", type: "like", value: searchTerm },
            { field: "MATERIA", type: "like", value: searchTerm },
            { field: "CVE_MATERIA", type: "like", value: searchTerm },
            { field: "SECCION", type: "like", value: searchTerm },
            { field: "NIVEL", type: "like", value: searchTerm },
            { field: "NIVEL_TIPO", type: "like", value: searchTerm },
            { field: "TIPO", type: "like", value: searchTerm },
            { field: "C_MIN", type: "like", value: searchTerm },
            { field: "H_TOTALES", type: "like", value: searchTerm },
            { field: "ESTATUS", type: "like", value: searchTerm },
            { field: "TIPO_CONTRATO", type: "like", value: searchTerm },
            { field: "CODIGO_PROFESOR", type: "like", value: searchTerm },
            { field: "NOMBRE_PROFESOR", type: "like", value: searchTerm },
            { field: "CATEGORIA", type: "like", value: searchTerm },
            { field: "DESCARGA", type: "like", value: searchTerm },
            { field: "CODIGO_DESCARGA", type: "like", value: searchTerm },
            { field: "NOMBRE_DESCARGA", type: "like", value: searchTerm },
            { field: "NOMBRE_DEFINITIVO", type: "like", value: searchTerm },
            { field: "TITULAR", type: "like", value: searchTerm },
            { field: "HORAS", type: "like", value: searchTerm },
            { field: "CODIGO_DEPENDENCIA", type: "like", value: searchTerm },
            { field: "L", type: "like", value: searchTerm },
            { field: "M", type: "like", value: searchTerm },
            { field: "I", type: "like", value: searchTerm },
            { field: "J", type: "like", value: searchTerm },
            { field: "V", type: "like", value: searchTerm },
            { field: "S", type: "like", value: searchTerm },
            { field: "D", type: "like", value: searchTerm },
            { field: "DIA_PRESENCIAL", type: "like", value: searchTerm },
            { field: "DIA_VIRTUAL", type: "like", value: searchTerm },
            { field: "MODALIDAD", type: "like", value: searchTerm },
            { field: "FECHA_INICIAL", type: "like", value: searchTerm },
            { field: "FECHA_FINAL", type: "like", value: searchTerm },
            { field: "HORA_INICIAL", type: "like", value: searchTerm },
            { field: "HORA_FINAL", type: "like", value: searchTerm },
            { field: "MODULO", type: "like", value: searchTerm },
            { field: "AULA", type: "like", value: searchTerm },
            { field: "CUPO", type: "like", value: searchTerm },
            { field: "OBSERVACIONES", type: "like", value: searchTerm },
            { field: "EXAMEN_EXTRAORDINARIO", type: "like", value: searchTerm },
          ];

          table.setFilter([searchFilters]);
        }
      }, 300); // Esperar 300ms después de que el usuario deje de escribir
    });

    // // Limpiar búsqueda al hacer clic en el icono de buscar
    // searchIcon.addEventListener("click", function () {
    //   searchInput.value = "";
    //   table.clearFilter();
    //   searchInput.focus();
    // });

    // Limpiar búsqueda con ESC
    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        this.value = "";
        table.clearFilter();
      }
    });
  }
}

function setupTableEvents(table) {
  // Configurar la búsqueda global PRIMERO
  setupGlobalSearch(table);

  // Configurar eventos de Undo/Redo
  setupUndoRedoEvents(table);

  // Export table data to Excel function
  document
    .getElementById("icono-descargar")
    .addEventListener("click", function () {
      const departmentName =
        document
          .querySelector(".encabezado-centro h3")
          ?.textContent.replace("Data - ", "") || "datos";
      table.download("xlsx", `data_${departmentName}.xlsx`, {
        sheetName: "Data Departamento",
      });
    });

  // Toggle header filters
  document
    .getElementById("icono-filtro")
    .addEventListener("click", function () {
      table.toggleHeaderFilter();
    });
}

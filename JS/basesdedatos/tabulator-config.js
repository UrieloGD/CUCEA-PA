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
    sorter: "number",
    //headerFilter: true,
    width: 80,
    frozen: true,
  },
  {
    title: "CICLO",
    field: "CICLO",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CRN",
    field: "CRN",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "MATERIA",
    field: "MATERIA",
    sorter: "string",
    //headerFilter: true,
    width: 250,
    editor: "input",
  },
  {
    title: "CVE MATERIA",
    field: "CVE_MATERIA",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "SECCIÓN",
    field: "SECCION",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NIVEL",
    field: "NIVEL",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NIVEL TIPO",
    field: "NIVEL_TIPO",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "TIPO",
    field: "TIPO",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "C. MIN",
    field: "C_MIN",
    sorter: "string",
    //headerFilter: true
    editor: "input",
    validator: ["minLength:1", "maxLength:5", "integer"],
  },
  {
    title: "H. TOTALES",
    field: "H_TOTALES",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "STATUS",
    field: "ESTATUS",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "TIPO CONTRATO",
    field: "TIPO_CONTRATO",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CÓDIGO",
    field: "CODIGO_PROFESOR",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NOMBRE PROFESOR",
    field: "NOMBRE_PROFESOR",
    sorter: "string",
    //headerFilter: true,
    width: 200,
    editor: "input",
  },
  {
    title: "CATEGORIA",
    field: "CATEGORIA",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "DESCARGA",
    field: "DESCARGA",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CÓDIGO DESCARGA",
    field: "CODIGO_DESCARGA",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NOMBRE DESCARGA",
    field: "NOMBRE_DESCARGA",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "NOMBRE DEFINITIVO",
    field: "NOMBRE_DEFINITIVO",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "TITULAR",
    field: "TITULAR",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "HORAS",
    field: "HORAS",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "CÓDIGO DEPENDENCIA",
    field: "CODIGO_DEPENDENCIA",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "L",
    field: "L",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "M",
    field: "M",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "I",
    field: "I",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "J",
    field: "J",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "V",
    field: "V",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "S",
    field: "S",
    sorter: "string",
    //headerFilter: true
    editor: "input",
  },
  {
    title: "D",
    field: "D",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "DÍA PRESENCIAL",
    field: "DIA_PRESENCIAL",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "DÍA VIRTUAL",
    field: "DIA_VIRTUAL",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "MODALIDAD",
    field: "MODALIDAD",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "FECHA INICIAL",
    field: "FECHA_INICIAL",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "FECHA FINAL",
    field: "FECHA_FINAL",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "HORA INICIAL",
    field: "HORA_INICIAL",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "HORA FINAL",
    field: "HORA_FINAL",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "MÓDULO",
    field: "MODULO",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "AULA",
    field: "AULA",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "CUPO",
    field: "CUPO",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "OBSERVACIONES",
    field: "OBSERVACIONES",
    sorter: "string",
    // headerFilter: true
    editor: "input",
  },
  {
    title: "EXTRAORDINARIO",
    field: "EXAMEN_EXTRAORDINARIO",
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

    // HABILITAR EL HISTORIAL - ESTO ES LO IMPORTANTE
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
      headerHozAlign: "center",
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

    // Limpiar búsqueda al hacer clic en el icono
    searchIcon.addEventListener("click", function () {
      searchInput.value = "";
      table.clearFilter();
      searchInput.focus();
    });

    // Limpiar búsqueda con ESC
    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        this.value = "";
        table.clearFilter();
      }
    });
  }
}

// REEMPLAZAR COMPLETAMENTE la función setupTableEvents existente con esta:
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

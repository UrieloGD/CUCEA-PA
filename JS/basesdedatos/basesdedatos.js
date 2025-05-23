// Archivo JavaScript para la funcionalidad de Tabulator en basesdedatos.php

// Esperar a que el documento esté cargado
document.addEventListener("DOMContentLoaded", function () {
  // Definir columnas para Tabulator basadas en la estructura de la base de datos
  const columns = [
    {
      title: "ID",
      field: "ID_Plantilla",
      sorter: "number",
      headerFilter: true,
      width: 80,
      frozen: true,
    },
    { title: "CICLO", field: "CICLO", sorter: "string", headerFilter: true },
    { title: "CRN", field: "CRN", sorter: "string", headerFilter: true },
    {
      title: "MATERIA",
      field: "MATERIA",
      sorter: "string",
      headerFilter: true,
      width: 250,
    },
    {
      title: "CVE MATERIA",
      field: "CVE_MATERIA",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "SECCIÓN",
      field: "SECCION",
      sorter: "string",
      headerFilter: true,
    },
    { title: "NIVEL", field: "NIVEL", sorter: "string", headerFilter: true },
    {
      title: "NIVEL TIPO",
      field: "NIVEL_TIPO",
      sorter: "string",
      headerFilter: true,
    },
    { title: "TIPO", field: "TIPO", sorter: "string", headerFilter: true },
    { title: "C. MIN", field: "C_MIN", sorter: "string", headerFilter: true },
    {
      title: "H. TOTALES",
      field: "H_TOTALES",
      sorter: "string",
      headerFilter: true,
    },
    { title: "STATUS", field: "ESTATUS", sorter: "string", headerFilter: true },
    {
      title: "TIPO CONTRATO",
      field: "TIPO_CONTRATO",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "CÓDIGO",
      field: "CODIGO_PROFESOR",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "NOMBRE PROFESOR",
      field: "NOMBRE_PROFESOR",
      sorter: "string",
      headerFilter: true,
      width: 200,
    },
    {
      title: "CATEGORIA",
      field: "CATEGORIA",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "DESCARGA",
      field: "DESCARGA",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "CÓDIGO DESCARGA",
      field: "CODIGO_DESCARGA",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "NOMBRE DESCARGA",
      field: "NOMBRE_DESCARGA",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "NOMBRE DEFINITIVO",
      field: "NOMBRE_DEFINITIVO",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "TITULAR",
      field: "TITULAR",
      sorter: "string",
      headerFilter: true,
    },
    { title: "HORAS", field: "HORAS", sorter: "string", headerFilter: true },
    {
      title: "CÓDIGO DEPENDENCIA",
      field: "CODIGO_DEPENDENCIA",
      sorter: "string",
      headerFilter: true,
    },
    { title: "L", field: "L", sorter: "string", headerFilter: true, width: 60 },
    { title: "M", field: "M", sorter: "string", headerFilter: true, width: 60 },
    { title: "I", field: "I", sorter: "string", headerFilter: true, width: 60 },
    { title: "J", field: "J", sorter: "string", headerFilter: true, width: 60 },
    { title: "V", field: "V", sorter: "string", headerFilter: true, width: 60 },
    { title: "S", field: "S", sorter: "string", headerFilter: true, width: 60 },
    { title: "D", field: "D", sorter: "string", headerFilter: true, width: 60 },
    {
      title: "DÍA PRESENCIAL",
      field: "DIA_PRESENCIAL",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "DÍA VIRTUAL",
      field: "DIA_VIRTUAL",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "MODALIDAD",
      field: "MODALIDAD",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "FECHA INICIAL",
      field: "FECHA_INICIAL",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "FECHA FINAL",
      field: "FECHA_FINAL",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "HORA INICIAL",
      field: "HORA_INICIAL",
      sorter: "string",
      headerFilter: true,
    },
    {
      title: "HORA FINAL",
      field: "HORA_FINAL",
      sorter: "string",
      headerFilter: true,
    },
    { title: "MÓDULO", field: "MODULO", sorter: "string", headerFilter: true },
    { title: "AULA", field: "AULA", sorter: "string", headerFilter: true },
    {
      title: "CUPO",
      field: "CUPO",
      sorter: "string",
      headerFilter: true,
      width: 80,
    },
    {
      title: "OBSERVACIONES",
      field: "OBSERVACIONES",
      sorter: "string",
      headerFilter: true,
      width: 200,
    },
    {
      title: "EXTRAORDINARIO",
      field: "EXAMEN_EXTRAORDINARIO",
      sorter: "string",
      headerFilter: true,
    },
  ];

  // Iniciamos Tabulator con datos precargados si están disponibles
  var tabulatorData = window.tabulatorData || [];

  // Inicializar Tabulator
  var table = new Tabulator("#tabla-datos", {
    data: tabulatorData,
    columns: columns,
    layout: "fitDataFill",
    pagination: "local",
    paginationSize: 25,
    paginationSizeSelector: [15, 25, 50, 100, true],
    movableColumns: true,
    resizableColumns: true,
    selectable: true,
    height: "700px",
    placeholder: "No hay datos disponibles",
    headerFilterLiveFilterDelay: 300,
    initialSort: [{ column: "ID_Plantilla", dir: "asc" }],
    persistence: {
      sort: true,
      filter: true,
      columns: true,
    },
    persistenceID: "baseDeDatosTable",
    persistenceMode: "local",
    columnDefaults: {
      headerFilter: true,
      headerFilterPlaceholder: "Filtrar...",
      resizable: true,
      tooltip: true,
    },
    // Función para ajustar la altura de la tabla según el contenedor
    responsiveLayout: "hide",
    responsiveLayoutCollapseStartOpen: false,
  });

  // Función para actualizar la tabla con nuevos datos
  window.actualizarTabla = function (nuevosRegistros) {
    table.replaceData(nuevosRegistros);
  };

  // Botón para exportar a Excel
  document
    .getElementById("icono-exportar")
    .addEventListener("click", function () {
      // Obtener el nombre del departamento actual para el nombre del archivo
      const departamentoNombre = document
        .querySelector(".encabezado-centro h3")
        .textContent.replace("Data - ", "");

      table.download("xlsx", `data_${departamentoNombre}.xlsx`, {
        sheetName: "Data Departamento",
        documentProcessing: function (workbook) {
          // Personalización adicional del archivo Excel si es necesario
          return workbook;
        },
      });
    });

  // Añadir tooltips a los iconos
  const iconos = document.querySelectorAll(".icono-buscador");
  iconos.forEach((icono) => {
    if (icono.id === "icono-exportar") {
      icono.setAttribute("data-tooltip", "Exportar a Excel");
    } else if (icono.id === "icono-filtro") {
      icono.setAttribute("data-tooltip", "Mostrar/ocultar filtros");
    }
  });

  // Mostrar/ocultar filtros de encabezado
  document
    .getElementById("icono-filtro")
    .addEventListener("click", function () {
      table.toggleHeaderFilter();
    });

  // Función para redimensionar la tabla cuando cambia el tamaño de la ventana
  window.addEventListener("resize", function () {
    table.redraw(true);
  });

  // Configuración para guardar preferencias del usuario (columnas visibles, filtros, etc.)
  // cuando se cierre la página
  window.addEventListener("beforeunload", function () {
    table.persistToStorage("baseDeDatosTabulator");
  });

  // Ajustar altura de la tabla en caso de cambios en el contenedor
  function ajustarAlturaTabla() {
    const contenedor = document.querySelector(".cuadro-principal");
    if (contenedor) {
      const posicionY = contenedor.getBoundingClientRect().top;
      const alturaVentana = window.innerHeight;
      const margenInferior = 40; // Margen inferior para mejor apariencia
      const alturaDisponible = alturaVentana - posicionY - margenInferior;

      // Establecer la altura mínima
      const alturaMinima = 400;
      const alturaFinal = Math.max(alturaDisponible, alturaMinima);

      table.setHeight(alturaFinal + "px");
    }
  }

  // Ejecutar ajuste de altura inicial y en cada redimensionamiento
  ajustarAlturaTabla();
  window.addEventListener("resize", ajustarAlturaTabla);

  // Si hay un elemento de carga, ocultarlo cuando la tabla esté completamente inicializada
  table.on("tableBuilt", function () {
    const loadingElement = document.getElementById("loading-spinner");
    if (loadingElement) {
      loadingElement.style.display = "none";
    }
  });
});

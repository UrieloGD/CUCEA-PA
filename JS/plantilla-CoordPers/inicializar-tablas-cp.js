document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM completamente cargado');
  
  // Obtener el rol del usuario
  const userRole = document.getElementById('user-role').value;
  console.log('Rol del usuario:', userRole);
  
  const isEditable = userRole == 3 || userRole == 0;
  console.log('¿Es editable?', isEditable);

  // Definir todas las columnas para Tabulator
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
    },
    {title: "ID", field: "ID", editor: isEditable ? "input" : false, variableHeight: true, frozen: true},
    {title: "DATOS", field: "Datos", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CODIGO", field: "Codigo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "PATERNO", field: "Paterno", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "MATERNO", field: "Materno", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "NOMBRES", field: "Nombres", editor: isEditable ? "input" : false, variableHeight: true},
    {
      title: "NOMBRE COMPLETO", 
      field: "Nombre_completo", 
      editor: isEditable ? "input" : false, 
      variableHeight: true,
      headerFilter: "input",
      headerFilterPlaceholder: "Buscar nombre..."
  },
    {title: "DEPARTAMENTO", field: "Departamento", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CATEGORIA ACTUAL", field: "Categoria_actual", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CATEGORIA ACTUAL", field: "Categoria_actual_dos", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "HORAS FRENTE A GRUPO", field: "Horas_frente_grupo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "DIVISION", field: "Division", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "TIPO DE PLAZA", field: "Tipo_plaza", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CAT.ACT.", field: "Cat_act", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CARGA HORARIA", field: "Carga_horaria", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "HORAS DEFINITIVAS", field: "Horas_definitivas", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "UDG VIRTUAL CIT OTRO CENTRO", field: "Udg_virtual_CIT", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "HORARIO", field: "Horario", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "TURNO", field: "Turno", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION", field: "Investigacion_nombramiento_cambio_funcion", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "S.N.I.", field: "SNI", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "SNI DESDE", field: "SNI_desde", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR", field: "Cambio_dedicacion", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "TELEFONO PARTICULAR", field: "Telefono_particular", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "TELEFONO OFICINA O CELULAR", field: "Telefono_oficina", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "DOMICILIO", field: "Domicilio", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "COLONIA", field: "Colonia", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "C.P.", field: "CP", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CIUDAD", field: "Ciudad", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ESTADO", field: "Estado", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "NO. AFIL. I.M.S.S.", field: "No_imss", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "C.U.R.P.", field: "CURP", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "RFC", field: "RFC", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "LUGAR DE NACIMIENTO", field: "Lugar_nacimiento", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ESTADO CIVIL", field: "Estado_civil", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "TIPO DE SANGRE", field: "Tipo_sangre", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "FECHA NAC.", field: "Fecha_nacimiento", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "EDAD", field: "Edad", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "NACIONALIDAD", field: "Nacionalidad", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CORREO ELECTRONICO", field: "Correo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "CORREOS OFICIALES", field: "Correos_oficiales", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ULTIMO GRADO", field: "Ultimo_grado", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "PROGRAMA", field: "Programa", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "NIVEL", field: "Nivel", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "INSTITUCION", field: "Institucion", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ESTADO/PAIS", field: "Estado_pais", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "AÑO", field: "Año", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "GDO EXP", field: "Gdo_exp", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "OTRO GRADO", field: "Otro_grado", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "PROGRAMA", field: "Otro_programa", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "NIVEL", field: "Otro_nivel", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "INSTITUCION", field: "Otro_institucion", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ESTADO/PAIS", field: "Otro_estado_pais", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "AÑO", field: "Otro_año", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "GDO EXP", field: "Otro_gdo_exp", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "OTRO GRADO", field: "Otro_grado_alternativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "PROGRAMA", field: "Otro_programa_alternativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "NIVEL", field: "Otro_nivel_altenrativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "INSTITUCION", field: "Otro_institucion_alternativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ESTADO/PAIS", field: "Otro_estado_pais_alternativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "AÑO", field: "Otro_año_alternativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "GDO EXP", field: "Otro_gdo_exp_alternativo", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "PROESDE 24-25", field: "Proesde_24_25", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "A PARTIR DE", field: "A_partir_de", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "FECHA DE INGRESO", field: "Fecha_ingreso", editor: isEditable ? "input" : false, variableHeight: true},
    {title: "ANTIGÜEDAD", field: "Antiguedad", editor: isEditable ? "input" : false, variableHeight: true}
  ];

  // Mostrar las columnas en la consola para depuración
  console.log('Columnas configuradas:', columns);

  // Mostrar loader
  console.log('Mostrando loader...');
  Swal.fire({
    title: 'Cargando datos...',
    html: 'Por favor espere mientras se procesan los datos',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Verificar si el elemento existe
  const tableElement = document.getElementById('tabla-datos-tabulator');
  if (!tableElement) {
    console.error('Error: No se encontró el elemento con ID "tabla-datos-tabulator"');
    Swal.fire('Error', 'No se encontró el contenedor de la tabla', 'error');
    return;
  }

  console.log('Inicializando Tabulator...');
  
  // Inicializar Tabulator con configuración de paginación del lado del servidor
  const table = new Tabulator("#tabla-datos-tabulator", {
    ajaxURL: 'http://localhost/CUCEA-PA/functions/coord-personal-plantilla/get_data.php',
    ajaxConfig: "GET",
    ajaxParams: {},
    // Desactivar el loader interno de Tabulator
    // ajaxLoader: false,
      ajaxLoader: false,
      ajaxLoaderLoading: "", 
    
    // IMPORTANTE: Configuración de paginación para comunicación con el servidor
    ajaxURLGenerator: function(url, config, params) {
      // Agregar parámetros de paginación a la URL
      let ajaxURL = url + "?";
      
      // Si usa paginación, enviar la página actual y tamaño de página
      if (this.options.pagination) {
        // Verificar si se están mostrando todos los registros
        if (params.size === true) {
          ajaxURL += "all=true";
        } else {
          ajaxURL += "page=" + params.page + "&size=" + params.size;
        }
      }
      
      console.log("URL generada para AJAX:", ajaxURL);
      return ajaxURL;
    },
    
    columns: columns,
    layout: "fitData", // Cambiamos a fitData para ajustar al contenido
    autoColumns: false, // Desactivamos autoColumns para usar nuestra definición
    responsiveLayout: false, // Desactivar el responsive layout para mantener todas las columnas
    frozenRowsField: "id",
    
    // IMPORTANTE: Configuración de paginación corregida
    pagination: true,
    paginationMode: "remote", // Modo de paginación remota
    paginationSize: 50, // Tamaño de página predeterminado
    paginationSizeSelector: [20, 50, 100, 300, true], // Opciones de tamaño de página
    paginationCounter: "rows", // Mostrar contador de filas
    paginationDataReceived: {
      "last_page": "last_page",
      "data": "data",
      "total": "total"
    },
    paginationDataSent: {
      "page": "page",
      "size": "size"
    },
    
    movableColumns: true,
    height: "620px",
    placeholder: "No hay datos disponibles",
    
    // Configuración para la selección de rangos y portapapeles
    selectable: true,
    selectableRange: true, // Habilitar selección de rangos
    selectableRangeColumns: true, // Permitir selección de columnas completas
    selectableRangeRows: true, // Permitir selección de filas completas
    selectableRangeClearCells: true, // Permitir borrado de celdas seleccionadas
    
    // Configuración del portapapeles
    clipboard: true, // Habilitar funcionalidad de portapapeles
    clipboardCopyStyled: false, // Copiar solo datos sin estilos
    clipboardCopyConfig: {
        rowHeaders: false,
        columnHeaders: false,
    },
    clipboardCopyRowRange: "range", // Copiar rangos de celdas
    clipboardPasteParser: "range", // Analizar datos pegados como rangos
    clipboardPasteAction: "range", // Pegar datos como rangos
    
    // Cambiar el modo de activación de edición para mejor navegación
    editTriggerEvent: "dblclick", // Solo editar al hacer doble click
    
    // Configuración para habilitar desplazamiento horizontal
    horizontalScroll: true,
    columnHeaderVertAlign: "middle",
    
    // Opciones para el ajuste automático de columnas
    resizableColumns: true,
    columnMinWidth: 80,
    
    // Recalcular ancho basado en contenido
    columnCalcLayout: "fitData",
    
    langs: {
      "es": {
        "pagination": {
          "first": "Primero",
          "first_title": "Primera página",
          "last": "Último",
          "last_title": "Última página",
          "prev": "Anterior",
          "prev_title": "Página anterior", 
          "next": "Siguiente",
          "next_title": "Página siguiente",
          "all": "Todo",
          "page_size": "Registros por página"
        },
        "headerFilters": {
          "default": "Filtrar columna...",
          "columns": {}
        }
      }
    },
    locale: "es",
    // Aplicar el tema Bulma
    theme: "bulma",
    // Configuración para mejorar rendimiento
    virtualDom: true,
    renderVertical: "virtual",
    
    // Callbacks
    dataLoading: function(data) {
      console.log('Cargando datos...', data);
    },
    dataLoaded: function(data) {
      console.log('Datos cargados correctamente:', data);
      Swal.close();
      
      // Calcular y ajustar los anchos de columna basados en el contenido
      adjustColumnWidths();
      
      // Asegurar que el scroll horizontal esté disponible
      const tableHolder = document.querySelector('.tabulator-tableHolder');
      if (tableHolder) {
        tableHolder.style.overflowX = 'auto';
      }
      
      // Aplicar clases de Bulma específicas tras cargar los datos
      applyBulmaClasses();
    },
    ajaxError: function(xhr, textStatus, errorThrown) {
      console.error('Error en la carga AJAX:', {
        xhr: xhr,
        textStatus: textStatus,
        errorThrown: errorThrown
      });
      Swal.fire('Error', 'No se pudieron cargar los datos. Detalles: ' + errorThrown, 'error');
    },
    tableBuilt: function() {
      console.log('Tabla construida');
      
      // Aplicar clases de Bulma al construir la tabla
      applyBulmaClasses();
    },
    renderStarted: function() {
      console.log('Renderizado iniciado');
    },
    renderComplete: function() {
      console.log('Renderizado completado');
      
      // Ajustar columnas después del renderizado
      adjustColumnWidths();
      
      // Asegurar que las clases de Bulma se aplican después del renderizado
      applyBulmaClasses();
    },
    columnResized: function(column) {
      // Guardar el ancho personalizado si es necesario
      console.log(`Columna ${column.getField()} redimensionada a ${column.getWidth()}px`);
    },
    pageLoaded: function(pageno) {
      console.log(`Página ${pageno} cargada correctamente`);
    },
    pageSizeChanged: function(size) {
      console.log(`Tamaño de página cambiado a: ${size}`);
    }
  });

  console.log('Tabulator inicializado:', table);

  // Función para ajustar los anchos de columna basados en el contenido
  function adjustColumnWidths() {
    // Solo ejecutar si la tabla y los datos están disponibles
    if (!table || !table.getColumns().length) return;
    
    console.log('Ajustando anchos de columnas basados en contenido...');
    
    // Calcular el ancho necesario para cada columna basado en su contenido
    table.columnManager.columns.forEach(column => {
      // Ignorar la columna de checkbox que ya tiene un ancho fijo
      if (column.getField() === "checkbox") return;
      
      let field = column.getField();
      let title = column.getDefinition().title;
      
      // Calcular ancho necesario basado en el título (header) y el contenido más largo
      let headerLength = title ? title.length : 0;
      let maxContentLength = 0;
      
      // Iterar sobre los datos visibles para encontrar el contenido más largo
      let visibleData = table.getData();
      visibleData.forEach(row => {
        let cellValue = row[field];
        if (cellValue) {
          let valueLength = String(cellValue).length;
          maxContentLength = Math.max(maxContentLength, valueLength);
        }
      });
      
      // Determinar el ancho necesario (aproximadamente 8px por carácter más un padding)
      let charWidth = 8; // aproximado para la mayoría de fuentes
      let padding = 20; // padding adicional
      let calculatedWidth = Math.max(headerLength, maxContentLength) * charWidth + padding;
      
      // Establecer un ancho mínimo y máximo razonable
      calculatedWidth = Math.max(calculatedWidth, 80); // mínimo 80px
      calculatedWidth = Math.min(calculatedWidth, 300); // máximo 300px
      
      // Aplicar el ancho calculado
      column.setWidth(calculatedWidth);
    });
  }

  // Función para aplicar clases de Bulma a elementos específicos
  function applyBulmaClasses() {
    // Aplicar clases de Bulma a los botones de paginación
    document.querySelectorAll('.tabulator-paginator button').forEach(button => {
      button.classList.add('button', 'is-small');
    });
    
    // Mejorar el aspecto del selector de tamaño de página
    const pageSizeSelector = document.querySelector('.tabulator-page-size');
    if (pageSizeSelector) {
      pageSizeSelector.classList.add('select', 'is-small');
      
      // Envolver el select con un div para aplicar estilo Bulma adecuado
      if (!pageSizeSelector.parentElement.classList.contains('select')) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('select', 'is-small');
        pageSizeSelector.parentNode.insertBefore(wrapper, pageSizeSelector);
        wrapper.appendChild(pageSizeSelector);
      }
    }
    
    // Aplicar estilo a los inputs de filtro
    document.querySelectorAll('.tabulator-header-filter input').forEach(input => {
      input.classList.add('input', 'is-small');
    });
  }

  // Hacer la tabla accesible globalmente para depuración
  window.tabulatorTable = table;

  // Inicializar componentes
  if (typeof initializeCustomTooltips === 'function') {
    initializeCustomTooltips();
  }
  
  if (typeof setupEventHandlers === 'function') {
    setupEventHandlers();
  }
  
  console.log('Inicialización completada');
});
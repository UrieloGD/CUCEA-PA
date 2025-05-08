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
      title: "",
      field: "checkbox",
      formatter: "rowSelection",
      titleFormatter: "rowSelection",
      hozAlign: "center",
      headerSort: false,
      frozen: true,
      width: 40
    },
    {title: "ID", field: "ID", width: 80, frozen: true, editor: isEditable ? "input" : false},
    {title: "DATOS", field: "Datos", editor: isEditable ? "input" : false},
    {title: "CODIGO", field: "Codigo", editor: isEditable ? "input" : false},
    {title: "PATERNO", field: "Paterno", editor: isEditable ? "input" : false},
    {title: "MATERNO", field: "Materno", editor: isEditable ? "input" : false},
    {title: "NOMBRES", field: "Nombres", editor: isEditable ? "input" : false},
    {title: "NOMBRE COMPLETO", field: "Nombre_completo", editor: isEditable ? "input" : false},
    {title: "DEPARTAMENTO", field: "Departamento", editor: isEditable ? "input" : false},
    {title: "CATEGORIA ACTUAL", field: "Categoria_actual", editor: isEditable ? "input" : false},
    {title: "CATEGORIA ACTUAL", field: "Categoria_actual_dos", editor: isEditable ? "input" : false},
    {title: "HORAS FRENTE A GRUPO", field: "Horas_frente_grupo", editor: isEditable ? "input" : false},
    {title: "DIVISION", field: "Division", editor: isEditable ? "input" : false},
    {title: "TIPO DE PLAZA", field: "Tipo_plaza", editor: isEditable ? "input" : false},
    {title: "CAT.ACT.", field: "Cat_act", editor: isEditable ? "input" : false},
    {title: "CARGA HORARIA", field: "Carga_horaria", editor: isEditable ? "input" : false},
    {title: "HORAS DEFINITIVAS", field: "Horas_definitivas", editor: isEditable ? "input" : false},
    {title: "UDG VIRTUAL CIT OTRO CENTRO", field: "Udg_virtual_CIT", editor: isEditable ? "input" : false},
    {title: "HORARIO", field: "Horario", editor: isEditable ? "input" : false},
    {title: "TURNO", field: "Turno", editor: isEditable ? "input" : false},
    {title: "INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION", field: "Investigacion_nombramiento_cambio_funcion", editor: isEditable ? "input" : false},
    {title: "S.N.I.", field: "SNI", editor: isEditable ? "input" : false},
    {title: "SNI DESDE", field: "SNI_desde", editor: isEditable ? "input" : false},
    {title: "CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR", field: "Cambio_dedicacion", editor: isEditable ? "input" : false},
    {title: "TELEFONO PARTICULAR", field: "Telefono_particular", editor: isEditable ? "input" : false},
    {title: "TELEFONO OFICINA O CELULAR", field: "Telefono_oficina", editor: isEditable ? "input" : false},
    {title: "DOMICILIO", field: "Domicilio", editor: isEditable ? "input" : false},
    {title: "COLONIA", field: "Colonia", editor: isEditable ? "input" : false},
    {title: "C.P.", field: "CP", editor: isEditable ? "input" : false},
    {title: "CIUDAD", field: "Ciudad", editor: isEditable ? "input" : false},
    {title: "ESTADO", field: "Estado", editor: isEditable ? "input" : false},
    {title: "NO. AFIL. I.M.S.S.", field: "No_imss", editor: isEditable ? "input" : false},
    {title: "C.U.R.P.", field: "CURP", editor: isEditable ? "input" : false},
    {title: "RFC", field: "RFC", editor: isEditable ? "input" : false},
    {title: "LUGAR DE NACIMIENTO", field: "Lugar_nacimiento", editor: isEditable ? "input" : false},
    {title: "ESTADO CIVIL", field: "Estado_civil", editor: isEditable ? "input" : false},
    {title: "TIPO DE SANGRE", field: "Tipo_sangre", editor: isEditable ? "input" : false},
    {title: "FECHA NAC.", field: "Fecha_nacimiento", editor: isEditable ? "input" : false},
    {title: "EDAD", field: "Edad", editor: isEditable ? "input" : false},
    {title: "NACIONALIDAD", field: "Nacionalidad", editor: isEditable ? "input" : false},
    {title: "CORREO ELECTRONICO", field: "Correo", editor: isEditable ? "input" : false},
    {title: "CORREOS OFICIALES", field: "Correos_oficiales", editor: isEditable ? "input" : false},
    {title: "ULTIMO GRADO", field: "Ultimo_grado", editor: isEditable ? "input" : false},
    {title: "PROGRAMA", field: "Programa", editor: isEditable ? "input" : false},
    {title: "NIVEL", field: "Nivel", editor: isEditable ? "input" : false},
    {title: "INSTITUCION", field: "Institucion", editor: isEditable ? "input" : false},
    {title: "ESTADO/PAIS", field: "Estado_pais", editor: isEditable ? "input" : false},
    {title: "AÑO", field: "Año", editor: isEditable ? "input" : false},
    {title: "GDO EXP", field: "Gdo_exp", editor: isEditable ? "input" : false},
    {title: "OTRO GRADO", field: "Otro_grado", editor: isEditable ? "input" : false},
    {title: "PROGRAMA", field: "Otro_programa", editor: isEditable ? "input" : false},
    {title: "NIVEL", field: "Otro_nivel", editor: isEditable ? "input" : false},
    {title: "INSTITUCION", field: "Otro_institucion", editor: isEditable ? "input" : false},
    {title: "ESTADO/PAIS", field: "Otro_estado_pais", editor: isEditable ? "input" : false},
    {title: "AÑO", field: "Otro_año", editor: isEditable ? "input" : false},
    {title: "GDO EXP", field: "Otro_gdo_exp", editor: isEditable ? "input" : false},
    {title: "OTRO GRADO", field: "Otro_grado_alternativo", editor: isEditable ? "input" : false},
    {title: "PROGRAMA", field: "Otro_programa_alternativo", editor: isEditable ? "input" : false},
    {title: "NIVEL", field: "Otro_nivel_altenrativo", editor: isEditable ? "input" : false},
    {title: "INSTITUCION", field: "Otro_institucion_alternativo", editor: isEditable ? "input" : false},
    {title: "ESTADO/PAIS", field: "Otro_estado_pais_alternativo", editor: isEditable ? "input" : false},
    {title: "AÑO", field: "Otro_año_alternativo", editor: isEditable ? "input" : false},
    {title: "GDO EXP", field: "Otro_gdo_exp_alternativo", editor: isEditable ? "input" : false},
    {title: "PROESDE 24-25", field: "Proesde_24_25", editor: isEditable ? "input" : false},
    {title: "A PARTIR DE", field: "A_partir_de", editor: isEditable ? "input" : false},
    {title: "FECHA DE INGRESO", field: "Fecha_ingreso", editor: isEditable ? "input" : false},
    {title: "ANTIGÜEDAD", field: "Antiguedad", editor: isEditable ? "input" : false}
  ];

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
  // Inicializar Tabulator
  const table = new Tabulator("#tabla-datos-tabulator", {
    // data: [], // No necesitamos esto si usamos ajaxURL
    ajaxURL: 'http://localhost/CUCEA-PA/functions/coord-personal-plantilla/get_data.php',
    columns: columns,
    layout: "fitDataStretch",
    responsiveLayout: "hide",
    pagination: "local",
    paginationSize: 15,
    paginationSizeSelector: [15, 25, 50, 100],
    movableColumns: true,
    height: "620px",
    placeholder: "No hay datos disponibles",
    selectable: true,
    selectableRangeMode: "click",
    langs: {
      "es": {
        "pagination": {
          "first": "Primera",
          "first_title": "Primera página",
          "last": "Última",
          "last_title": "Última página",
          "prev": "Anterior",
          "prev_title": "Página anterior",
          "next": "Siguiente",
          "next_title": "Página siguiente"
        },
        "headerFilters": {
          "default": "Filtrar columna...",
          "columns": {}
        }
      }
    },
    locale: "es",
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
    },
    renderStarted: function() {
      console.log('Renderizado iniciado');
    },
    renderComplete: function() {
      console.log('Renderizado completado');
    }
  });

  console.log('Tabulator inicializado:', table);

  // Hacer la tabla accesible globalmente para depuración
  window.tabulatorTable = table;

  // Función para cargar datos (alternativa si no funciona ajaxURL)
  window.loadData = function() {
    console.log('Ejecutando loadData()');
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
    
    fetch('http://localhost/CUCEA-PA/functions/coord-personal-plantilla/get_data.php')
      .then(response => {
        console.log('Respuesta recibida del servidor:', response);
        if (!response.ok) {
          throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log("Datos recibidos del servidor:", data);
        table.setData(data)
          .then(() => {
            console.log('Datos establecidos en la tabla');
            Swal.close();
          })
          .catch(error => {
            console.error('Error al establecer datos:', error);
            Swal.fire('Error', 'Error al mostrar los datos en la tabla', 'error');
          });
      })
      .catch(error => {
        console.error('Error al cargar datos:', error);
        Swal.fire('Error', 'No se pudieron cargar los datos. Detalles: ' + error.message, 'error');
      });
  };

  // Inicializar tooltips personalizados
  function initializeCustomTooltips() {
    console.log('Inicializando tooltips...');
    document.querySelectorAll('[data-tooltip]').forEach(element => {
      element.addEventListener('mouseenter', function() {
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = this.getAttribute('data-tooltip');
        
        document.body.appendChild(tooltip);
        
        const rect = this.getBoundingClientRect();
        tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
        tooltip.style.left = (rect.left + (rect.width - tooltip.offsetWidth) / 2) + 'px';
        
        this.addEventListener('mouseleave', function() {
          document.querySelectorAll('.custom-tooltip').forEach(t => t.remove());
        }, { once: true });
      });
    });
  }

  // Configurar eventos para botones de filtro, visibilidad, etc.
  function setupEventHandlers() {
    console.log('Configurando event handlers...');
    
    // Botón de filtro
    const filterBtn = document.getElementById('icono-filtro');
    if (filterBtn) {
      filterBtn.addEventListener('click', function() {
        console.log('Botón de filtro clickeado');
        table.toggleHeaderFilter();
      });
    } else {
      console.warn('No se encontró el botón de filtro');
    }
    
    // Botón de visibilidad
    const visibilityBtn = document.getElementById('icono-visibilidad');
    if (visibilityBtn) {
      visibilityBtn.addEventListener('click', function() {
        console.log('Botón de visibilidad clickeado');
        // Implementar lógica para mostrar/ocultar columnas
      });
    } else {
      console.warn('No se encontró el botón de visibilidad');
    }
    
    // Botón de guardar cambios
    const saveBtn = document.getElementById('icono-guardar');
    if (saveBtn) {
      saveBtn.addEventListener('click', function() {
        console.log('Botón de guardar clickeado');
        saveAllChanges();
      });
    } else {
      console.warn('No se encontró el botón de guardar');
    }
    
    // Botón de deshacer cambios
    const undoBtn = document.getElementById('icono-deshacer');
    if (undoBtn) {
      undoBtn.addEventListener('click', function() {
        console.log('Botón de deshacer clickeado');
        undoAllChanges();
      });
    } else {
      console.warn('No se encontró el botón de deshacer');
    }
  }

  // Función para guardar cambios
  window.saveAllChanges = function() {
    console.log('Ejecutando saveAllChanges()');
    const editedData = table.getEditedCells();
    console.log('Celdas editadas:', editedData);
    
    if (editedData.length === 0) {
      console.log('No hay cambios para guardar');
      Swal.fire('Información', 'No hay cambios para guardar', 'info');
      return;
    }
    
    console.log('Preparando para guardar cambios...');
    // Implementar lógica para guardar cambios
    // Aquí deberías implementar una petición AJAX para guardar los cambios
  };
  
  // Función para deshacer cambios
  window.undoAllChanges = function() {
    console.log('Ejecutando undoAllChanges()');
    table.clearCellEdited();
    console.log('Cambios deshechos');
    // Recargar datos originales
    table.setData('http://localhost/CUCEA-PA/functions/coord-personal-plantilla/get_data.php')
      .then(() => console.log('Datos recargados'))
      .catch(error => console.error('Error al recargar datos:', error));
  };
  
  // Función para eliminar registros seleccionados
  window.eliminarRegistrosSeleccionados = function() {
    console.log('Ejecutando eliminarRegistrosSeleccionados()');
    const selectedRows = table.getSelectedRows();
    console.log('Filas seleccionadas:', selectedRows);
    
    if (selectedRows.length === 0) {
      console.log('No hay registros seleccionados');
      Swal.fire('Información', 'No hay registros seleccionados para eliminar', 'info');
      return;
    }
    
    console.log('Mostrando confirmación para eliminar');
    Swal.fire({
      title: '¿Está seguro?',
      text: `¿Desea eliminar ${selectedRows.length} registro(s)?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        console.log('Usuario confirmó eliminación');
        // Implementar lógica para eliminar registros
        console.log('Registros a eliminar:', selectedRows.map(row => row.getData()));
        
        // Aquí deberías implementar una petición AJAX para eliminar los registros
      } else {
        console.log('Usuario canceló la eliminación');
      }
    });
  };

  // Inicializar componentes
  initializeCustomTooltips();
  setupEventHandlers();
  
  // Forzar carga de datos si es necesario (comentar si usas ajaxURL)
  // loadData();

  console.log('Inicialización completada');
});
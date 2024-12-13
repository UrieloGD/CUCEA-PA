/**
* Inicializa y configura la tabla con DataTables aplicando múltiples opciones personalizadas.
*/

$(document).ready(function () {
  localStorage.removeItem('DataTables_tabla-datos');
  var table = $("#tabla-datos").DataTable({
    dom: '<"top"<"custom-search-container">f>rt<"bottom"lip>',
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
      search: "_INPUT_",
      searchPlaceholder: "Buscar...",
    },
    initComplete: function() {
      // Mostrar la tabla después de la inicialización
      $("#tabla-datos").css ("display", "table");

      // Mover la barra de búsqueda al contenedor personalizado
      $('.dataTables_filter').appendTo('.custom-search-container');
      $('.dataTables_filter input').addClass('custom-search-input');
 
      // Configuración de filtros para cada columna
      this.api().columns().every(function(index) {
        var column = this;
        var header = $(column.header());
        var filterIcon = header.find('.filter-icon');
    
        if (filterIcon.length) {
          var filterMenu = $('<div class="filter-menu"></div>');
          var filterContainer = $('<div class="filter-container"></div>');
          filterContainer.append(filterMenu);
          $('.datatable-container').append(filterContainer);
          
          // Agregar barra de búsqueda al filter-menu
          filterMenu.prepend(
          $('<div class="filter-search-container">').append($('<input type="text" class="filter-search-input" placeholder="Buscar...">'))
      );

      filterMenu.find('.filter-search-input').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        filterMenu.find('label').each(function() {
          var label = $(this).text().toLowerCase();
          $(this).toggle(label.includes(searchTerm));
        });
      });

          var uniqueValues = column.data().unique().sort().toArray();
    
          uniqueValues.forEach(function(value) {
            filterMenu.append(
              $('<label>')
                .append($('<input type="checkbox">').val(value))
                .append(' ' + value)
            );
          });
    
          filterMenu.append(
            $('<div class="filter-buttons">')
              .append($('<button class="apply-filter">Aplicar</button>'))
              .append($('<button class="clear-filter">Limpiar</button>'))
          );
    
          filterIcon.on('click', function(e) {
            e.stopPropagation();
            $('.filter-menu').not(filterMenu).hide();
            filterMenu.toggle();
          });
    
          filterMenu.find('.apply-filter').on('click', function() {
            var selectedValues = filterMenu.find('input:checked')
              .map(function() {
                return '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$';
              }).get().join('|');
    
            column.search(selectedValues, true, false).draw();
            filterMenu.hide();
          });
    
          filterMenu.find('.clear-filter').on('click', function() {
            filterMenu.find('input').prop('checked', false);
            column.search('').draw();
            filterMenu.hide();
          });
        }
      });
    
      // Cerrar menús de filtro al hacer clic fuera
      $(document).on('click', function(e) {
        if (!$(e.target).closest('.filter-menu, .filter-icon').length) {
          $('.filter-menu').hide();
        }
      });
    },
    pageLength: 15,
    // Definir opciones de longitud de página
    lengthMenu: [
      [15, 25, 50, -1],
      [15, 25, 50, "Todos"],
    ],
    responsive: true,
    
    stateSave: true,
    stateDuration: -1,
    stateSaveCallback: function(settings, data) {
      localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data));
    },
    
    stateLoadCallback: function(settings) {
      return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance));
    },
    ordering: true,
    info: true,
    scrollX: true,
    scrollCollapse: true,
    fixedHeader: true,
    columnDefs: [
      { targets: "_all", defaultContent: "" },
      { orderable: false, targets: 0 },
      { reorderable: false, targets: 0 },
      { orderable: false, targets: -1 },
      // Nuevo columnDefs para manejar choques
      {
        targets: [1, 36, 37, 38, 39], // Añadir índice de la columna de aula
        createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
          var $row = $(cell).closest('tr');
          var choques = $row.data('choques');
          
          if (choques && choques.length > 0) {
              $(cell).addClass('celda-choque');
              
              choques.forEach(function(choque) {
                  // Marcar solo el primer departamento en azul
                  if (choque.Primer_Departamento === choque.Departamento) {
                      $(cell).addClass('choque-primero');
                  } else {
                      $(cell).addClass('choque-segundo');
                  }
              });
              
              // Tooltip con ID de choque y nombre de departamento
              var tooltipContent = choques.map(function(choque) {
                  return "Choque con ID (#" + choque.ID_Choque + ") del Departamento (" + choque.Departamento + ")";
              }).join('<br>');
              
              $(cell).attr('title', tooltipContent);
              $row.addClass('fila-choque');
          }
        }
      }
    ],
    order: [[1, "asc"]],
    colReorder: {
      fixedColumnsLeft: 1,
      fixedColumnsRight: 0,
    },
    buttons: [
      {
        extend: "colvis",
        text: '<i class="fa fa-eye"></i>',
        titleAttr: "Column visibility",
        collectionLayout: "fixed columns",
        columns: ":not(:first-child)",
      },
    ],
  });
  
  $("#icono-visibilidad").on("click", function () {
    table.button(".buttons-colvis").trigger();
  });
 
  new $.fn.dataTable.FixedColumns(table, {
    leftColumns: 2,
    rightColumns: 0,
  });
 });

// Ocultar los iconos de filtro al inicializar la tabla
$('.filter-icon').hide();

// Función para alternar la visibilidad de los iconos de filtro
function toggleFilterIcons() {
  $('.filter-icon').toggle();
  $('#icono-filtro').toggleClass('active');
}

// Asignar evento de clic al botón de filtro
$('#icono-filtro').on('click', toggleFilterIcons);
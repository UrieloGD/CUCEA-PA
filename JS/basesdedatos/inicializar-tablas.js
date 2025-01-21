/**
 * Inicializa y configura la tabla con DataTables aplicando múltiples opciones personalizadas.
 */

$(document).ready(function () {
  localStorage.removeItem("DataTables_tabla-datos");
  var table = $("#tabla-datos").DataTable({
    dom: '<"top"<"custom-search-container">f>rt<"bottom"lip>',
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
      search: "_INPUT_",
      searchPlaceholder: "Buscar...",
    },
    initComplete: function () {
      // Cerrar Sweet Alert cuando la tabla esté completamente cargada
      if (typeof Swal !== "undefined") {
        Swal.close();
      }

      // Mostrar la tabla después de la inicialización
      $("#tabla-datos").css("display", "table");

      // Mover la barra de búsqueda al contenedor personalizado
      $(".dataTables_filter").appendTo(".custom-search-container");
      $(".dataTables_filter input").addClass("custom-search-input");

      // Configuración de filtros para cada columna
      this.api()
        .columns()
        .every(function (index) {
          var column = this;
          var header = $(column.header());
          var filterIcon = header.find(".filter-icon");

          if (filterIcon.length) {
            var filterMenu = $('<div class="filter-menu"></div>');
            var filterContainer = $('<div class="filter-container"></div>');
            filterContainer.append(filterMenu);
            $(".datatable-container").append(filterContainer);

            // Agregar barra de búsqueda al filter-menu
            filterMenu.prepend(
              $('<div class="filter-search-container">').append(
                $(
                  '<input type="text" class="filter-search-input" placeholder="Buscar...">'
                )
              )
            );

            filterMenu.find(".filter-search-input").on("input", function () {
              var searchTerm = $(this).val().toLowerCase();
              filterMenu.find("label").each(function () {
                var label = $(this).text().toLowerCase();
                $(this).toggle(label.includes(searchTerm));
              });
            });

            var uniqueValues = column.data().unique().sort().toArray();

            uniqueValues.forEach(function (value) {
              filterMenu.append(
                $("<label>")
                  .append($('<input type="checkbox">').val(value))
                  .append(" " + value)
              );
            });

            filterMenu.append(
              $('<div class="filter-buttons">')
                .append($('<button class="apply-filter">Aplicar</button>'))
                .append($('<button class="clear-filter">Limpiar</button>'))
            );

            filterIcon.on("click", function (e) {
              e.stopPropagation();
              $(".filter-menu").not(filterMenu).hide();
              filterMenu.toggle();
            });

            filterMenu.find(".apply-filter").on("click", function () {
              var selectedValues = filterMenu
                .find("input:checked")
                .map(function () {
                  return (
                    "^" + $.fn.dataTable.util.escapeRegex(this.value) + "$"
                  );
                })
                .get()
                .join("|");

              column.search(selectedValues, true, false).draw();
              filterMenu.hide();
            });

            filterMenu.find(".clear-filter").on("click", function () {
              filterMenu.find("input").prop("checked", false);
              column.search("").draw();
              filterMenu.hide();
            });
          }
        });

      // Cerrar menús de filtro al hacer clic fuera
      $(document).on("click", function (e) {
        if (!$(e.target).closest(".filter-menu, .filter-icon").length) {
          $(".filter-menu").hide();
        }
      });
    },
    pageLength: 15,
    lengthMenu: [
      [15, 25, 50, -1],
      [15, 25, 50, "Todos"],
    ],
    responsive: true,
    stateSave: true,
    stateDuration: -1,
    stateSaveCallback: function (settings, data) {
      localStorage.setItem(
        "DataTables_" + settings.sInstance,
        JSON.stringify(data)
      );
    },
    stateLoadCallback: function (settings) {
      return JSON.parse(
        localStorage.getItem("DataTables_" + settings.sInstance)
      );
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
      {
        targets: [1, 36, 37, 38, 39],
        createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
          var $row = $(cell).closest('tr');
          var choques = $row.data('choques');
          
          if (choques && choques.length > 0) {
              $(cell).addClass('celda-choque');
              
              choques.forEach(function(choque) {
                  if (choque.Primer_Departamento === choque.Nombre_Departamento) {
                      $(cell).addClass('choque-primero');
                  } else {
                      $(cell).addClass('choque-segundo');
                  }
              });
              
              var tooltipContent = choques.map(function(choque) {
                  return "Choque con ID #" + choque.ID_Choque + " del departamento " + choque.Departamento;
              }).join('\n');
              
              // Usar data-attribute en lugar de title
              $(cell).attr('data-tooltip', tooltipContent);
          }
        },
      },
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
$(".filter-icon").hide();

// Función para alternar la visibilidad de los iconos de filtro
function toggleFilterIcons() {
  $(".filter-icon").toggle();
  $("#icono-filtro").toggleClass("active");
}

// Añadir esta función después de la inicialización de la tabla
function añadirFila() {
  var table = $('#tabla-datos').DataTable();
  
  // Crear una fila con inputs editables
  var rowNode = table.row.add([
      // ... Añadir el resto de campos según necesites
      '<div class="btn-group">' +
          '<button class="btn btn-success btn-sm btn-guardar"><i class="fas fa-save"></i></button>' +
          '<button class="btn btn-danger btn-sm btn-cancelar"><i class="fas fa-times"></i></button>' +
      '</div>',
      '<input type="checkbox">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">', 
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      // '<select class="form-control input-sm">' + obtenerOpcionesDepartamentos() + '</select>', // DEPARTAMENTO
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
      '<input type="text" class="form-control input-sm" placeholder="">',
  ]).draw(false).node();

  // Estilos de los inputs al agregar fila
  $(rowNode).find('input, select').css({
      'width': '100%',
      'padding': '3px',
      'box-sizing': 'border-box'
  });

  // Manejar el guardado
  $(rowNode).find('.btn-guardar').on('click', function() {
      var datos = {};
      $(rowNode).find('input, select').each(function(index) {
          var columnName = table.column(index).header().textContent.trim();
          datos[columnName] = $(this).val();
      });

      // Realizar la petición AJAX para guardar
      $.ajax({
          url: 'guardar_registro.php',
          method: 'POST',
          data: datos,
          success: function(response) {
              if(response.success) {
                  // Convertir inputs a texto
                  $(rowNode).find('input, select').each(function() {
                      var valor = $(this).val();
                      $(this).parent().html(valor);
                  });
                  
                  // Reemplazar botones con opciones de edición
                  $(rowNode).find('.btn-group').html(
                      '<button class="btn btn-primary btn-sm btn-editar"><i class="fas fa-edit"></i></button>' +
                      '<button class="btn btn-danger btn-sm btn-eliminar"><i class="fas fa-trash"></i></button>'
                  );
                  
                  Swal.fire({
                      icon: 'success',
                      title: 'Registro guardado correctamente',
                      showConfirmButton: false,
                      timer: 1500
                  });
              }
          },
          error: function() {
              Swal.fire({
                  icon: 'error',
                  title: 'Error al guardar el registro',
                  text: 'Por favor, intente nuevamente'
              });
          }
      });
  });

  // Manejar la cancelación
  $(rowNode).find('.btn-cancelar').on('click', function() {
      table.row($(rowNode)).remove().draw();
  });
}

// Agregar el evento al botón de añadir
$('#icono-añadir').on('click', function() {
  agregarNuevaFila();
});

// Asignar evento de clic al botón de filtro
$("#icono-filtro").on("click", toggleFilterIcons);

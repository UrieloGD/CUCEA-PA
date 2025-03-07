var table;

// Inicializar tooltips personalizados
function initializeCustomTooltips() {
  const tooltip = document.createElement("div");
  tooltip.className = "custom-tooltip";
  document.body.appendChild(tooltip);

  function positionTooltip(element, tooltipElement, content) {
    const rect = element.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft =
      window.pageXOffset || document.documentElement.scrollLeft;

    tooltipElement.innerHTML = content;
    tooltipElement.classList.add("show");

    let top = rect.top + scrollTop - tooltipElement.offsetHeight - 10;
    const left =
      rect.left + scrollLeft + rect.width / 2 - tooltipElement.offsetWidth / 2;

    if (top < scrollTop) {
      top = rect.bottom + scrollTop + 10;
      tooltipElement.setAttribute("data-position", "bottom");
    } else {
      tooltipElement.setAttribute("data-position", "top");
    }

    const rightEdge = window.innerWidth + scrollLeft;
    if (left + tooltipElement.offsetWidth > rightEdge) {
      tooltipElement.style.left =
        rightEdge - tooltipElement.offsetWidth - 5 + "px";
    } else if (left < scrollLeft) {
      tooltipElement.style.left = scrollLeft + 5 + "px";
    } else {
      tooltipElement.style.left = left + "px";
    }

    tooltipElement.style.top = top + "px";
  }

  document.addEventListener("mouseover", function (e) {
    const target = e.target.closest("[data-tooltip]");
    if (target) {
      const content = target.getAttribute("data-tooltip");
      if (target.classList.contains("celda-choque")) {
        tooltip.classList.add("tooltip-choque");
      } else {
        tooltip.classList.remove("tooltip-choque");
      }
      positionTooltip(target, tooltip, content);
    }
  });

  document.addEventListener("mouseout", function (e) {
    const target = e.target.closest("[data-tooltip]");
    if (target) {
      tooltip.classList.remove("show");
    }
  });

  document.addEventListener("scroll", function () {
    const activeTooltip = document.querySelector("[data-tooltip]:hover");
    if (activeTooltip) {
      const content = activeTooltip.getAttribute("data-tooltip");
      positionTooltip(activeTooltip, tooltip, content);
    }
  });
}

// Mostrar el loader al inicio
Swal.fire({
  title: "Cargando datos...",
  html: "Por favor espere mientras se procesan los datos",
  allowOutsideClick: false,
  allowEscapeKey: false,
  showConfirmButton: false,
  didOpen: () => {
    Swal.showLoading();
  },
});


$(document).ready(function () {

  localStorage.removeItem("DataTables_tabla-datos");
  table = $("#tabla-datos").DataTable({
    scrollY: '620px',     // Altura fija para el cuerpo de la tabla
    scrollCollapse: true, // Permite que la tabla se colapse cuando hay poco contenido
    scrollX: true,        // Scroll horizontal si es necesario
    fixedHeader: {
        header: true,     // Mantiene el encabezado fijo durante el scroll
        headerOffset: 0, // Ajusta este valor si tienes una barra de navegación fija en la parte superior
        footer: false
    },
    dom: '<"top"<"custom-search-container">f>rt<"bottom"lip>',
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
      search: "_INPUT_",
      searchPlaceholder: "Buscar...",
    },
    initComplete: function () {
      // Cerrar Sweet Alert
      if (typeof Swal !== "undefined") {
        Swal.close();
      }

      // Mostrar la tabla
      $("#tabla-datos").css("display", "table");

      // Mover la barra de búsqueda
      $(".dataTables_filter").appendTo(".custom-search-container");
      $(".dataTables_filter input").addClass("custom-search-input");

      // Configurar tooltips para encabezados
      this.api()
        .columns()
        .every(function () {
          const headerCell = $(this.header());
          const headerText = headerCell.text().trim();
          headerCell.attr("data-tooltip", headerText);
        });

      // Configurar filtros para columnas
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

      // Inicializar tooltips personalizados
      initializeCustomTooltips();
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

    //////////////////////////////////////////////////////////////////////////////////////////////////////////// Errores with 
    ordering: true,
    info: true,
    scrollX: true,
    scrollCollapse: true,
    columnDefs: [
      { targets: "_all", defaultContent: "" },
      { orderable: false, targets: 0 },
      { reorderable: false, targets: 0 },
      {
        targets: [1, 24, 25, 26, 27, 28, 29, 30, 36, 37, 38, 39],
        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
          var $row = $(cell).closest("tr");
          var choquesStr = $row.attr("data-choques");

          if (choquesStr && choquesStr !== "null" && choquesStr !== "[]") {
            try {
              var choquesData = JSON.parse(choquesStr);

              if (choquesData && choquesData.length > 0) {
                $(cell).addClass("celda-choque");

                var tooltipMessages = [];

                choquesData.forEach(function (choqueInfo) {
                  if (choqueInfo.Es_Primero) {
                    $(cell)
                      .addClass("choque-primero")
                      .removeClass("choque-segundo");
                  } else {
                    $(cell)
                      .addClass("choque-segundo")
                      .removeClass("choque-primero");
                  }

                  tooltipMessages.push(
                    "Choque con ID #" +
                      choqueInfo.ID_Choque +
                      " del departamento " +
                      choqueInfo.Departamento
                  );
                });

                // Configurar tooltip personalizado
                $(cell).attr("data-tooltip", tooltipMessages.join("<br>"));

                // Configurar hover
                $(cell).hover(
                  function () {
                    var currentCell = $(this);
                    choquesData.forEach(function (choqueInfo) {
                      $("tr").each(function () {
                        var rowId = $(this).find("td:eq(1)").text().trim();
                        if (rowId === String(choqueInfo.ID_Choque)) {
                          $(this)
                            .find("td")
                            .each(function () {
                              var $td = $(this);
                              $td.addClass("celda-choque-hover");
                              if (choqueInfo.Es_Primero) {
                                $td
                                  .addClass("choque-primero")
                                  .removeClass("choque-segundo");
                              } else {
                                $td
                                  .addClass("choque-segundo")
                                  .removeClass("choque-primero");
                              }
                            });
                        }
                      });
                    });
                  },
                  function () {
                    $(".celda-choque-hover")
                      .removeClass("celda-choque-hover")
                      .removeClass("choque-primero")
                      .removeClass("choque-segundo");
                  }
                );
              }
            } catch (e) {
              console.error("Error al procesar choques:", e);
              console.error("Data choques:", choquesStr);
            }
          }
        },
      },
    ],
    order: [[1, "asc"]],
    colReorder: {
      columns: ":gt(1)",
      fixedColumnsLeft: 2,
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

  // Mantener fijos los controles de paginación
  $(window).scroll(function() {
    var tableBottom = $('.datatable-container').offset().top + $('.datatable-container').height();
    var scrollPosition = $(window).scrollTop() + $(window).height();
    
    if (scrollPosition < tableBottom) {
      // Fijar los controles en la parte inferior
      $('.dataTables_paginate, .dataTables_info, .dataTables_length')
        .css('position', 'fixed')
        .css('bottom', '0')
        .css('background', '#fff')
        .css('width', $('.datatable-container').width())
        .css('z-index', '10')
        .css('padding', '8px 0')
        .css('box-shadow', '0 -2px 5px rgba(0,0,0,0.1)');
    } else {
      // Volver a la posición normal
      $('.dataTables_paginate, .dataTables_info, .dataTables_length')
        .css('position', 'static')
        .css('box-shadow', 'none');
    }
  });

  // Redibujar tabla - mueve esto dentro de document.ready
  table.columns.adjust().draw();

  // Redibujar tabla
  setTimeout(function () {
    table.columns.adjust().draw();
  }, 200);

  $("#icono-visibilidad").on("click", function () {
    table.button(".buttons-colvis").trigger();
  });

  new $.fn.dataTable.FixedColumns(table, {
    leftColumns: 2,
    rightColumns: 0,
  });
});

// Ajusta las columnas después de la carga completa
$(window).on('load', function() {
  if (table && table.columns) {
      setTimeout(function() {
          table.columns.adjust().draw();
      }, 500); // Un pequeño retraso puede ayudar
  }
});

// Ajusta las columnas si el tamaño de la ventana cambia
$(window).on('resize', function() {
  if (table) {
      table.columns.adjust().draw();
  }
});

// Ocultar los iconos de filtro
$(".filter-icon").hide();

// Función para alternar la visibilidad de los iconos de filtro
function toggleFilterIcons() {
  $(".filter-icon").toggle();
  $("#icono-filtro").toggleClass("active");
}

// Evento de clic para el botón de filtro
$("#icono-filtro").on("click", toggleFilterIcons);

// Cerrar menús de filtro al hacer clic fuera
$(document).on("click", function (e) {
  if (!$(e.target).closest(".filter-menu, .filter-icon").length) {
    $(".filter-menu").hide();
  }
});

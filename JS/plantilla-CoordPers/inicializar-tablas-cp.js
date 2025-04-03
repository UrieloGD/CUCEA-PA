// ./JS/basesdedatos/inicializar-tablas.js
var table;

// Inicializar tooltips personalizados
function initializeCustomTooltips() {
  if ($("[data-tooltip]").data("tooltip-initialized")) return;
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
  $("[data-tooltip]").data("tooltip-initialized", true);
}

$(document).ready(function () {
  // Aplicar estilos CSS para columnas fijas
  function applyFixedColumnsCSS() {
    const style = `
      .DTFC_LeftBodyWrapper table tbody tr td:first-child,
      .DTFC_LeftBodyWrapper table tbody tr td:nth-child(2) {
        position: sticky;
        left: 0;
        z-index: 1;
        background-color: white;
      }
      .DTFC_LeftBodyWrapper table tbody tr td:nth-child(2) {
        left: var(--first-column-width, 50px);
      }
      .DTFC_LeftHeadWrapper table thead tr th:first-child,
      .DTFC_LeftHeadWrapper table thead tr th:nth-child(2) {
        position: sticky;
        left: 0;
        z-index: 3;
        background-color: white;
      }
      .DTFC_LeftHeadWrapper table thead tr th:nth-child(2) {
        left: var(--first-column-width, 50px);
      }
      .dataTables_scrollBody {
        overflow-x: auto !important;
      }
    `;
    $('<style>').text(style).appendTo('head');
    
    // Calcular y establecer el ancho de la primera columna
    const firstColWidth = $('#tabla-datos thead th:first').outerWidth();
    document.documentElement.style.setProperty('--first-column-width', `${firstColWidth}px`);
  }

  table = $("#tabla-datos").DataTable({
    scrollY: "620px",
    scrollCollapse: true,
    scrollX: true,
    fixedHeader: {
      header: true,
      headerOffset: 0,
      footer: false,
    },
    dom: '<"top"<"custom-search-container">fB>rt<"bottom"lip>',
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
      search: "_INPUT_",
      searchPlaceholder: "Buscar...",
    },
    initComplete: function () {
      if (typeof Swal !== "undefined") {
        Swal.close();
      }

      $("#tabla-datos").css("display", "table");
      $(".dataTables_filter").appendTo(".custom-search-container");
      $(".dataTables_filter input").addClass("custom-search-input");

      // Aplicar estilos CSS para columnas fijas
      applyFixedColumnsCSS();

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

      initializeCustomTooltips();

      setTimeout(function () {
        $(".dt-buttons").css("display", "none");

        var customColvisMenu = $(
          '<div class="custom-colvis-menu" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; box-shadow:0 2px 4px rgba(0,0,0,0.2); padding:10px; z-index:1000; max-height:400px; overflow-y:auto;"></div>'
        );
        $("body").append(customColvisMenu);

        var customButton = $(
          '<div class="icono-buscador" id="btn-colvis-custom" data-tooltip="Mostrar/ocultar columnas"><i class="fa fa-eye"></i></div>'
        );

        customButton.insertBefore("#icono-filtro");
        $("#icono-visibilidad, #btn-colvis").remove();

        customButton.on("click", function (e) {
          e.preventDefault();
          e.stopPropagation();

          if (customColvisMenu.is(":visible")) {
            customColvisMenu.hide();
            return;
          }

          var buttonPos = $(this).offset();
          customColvisMenu.css({
            top: buttonPos.top + $(this).outerHeight() + 5,
            left: buttonPos.left,
          });

          customColvisMenu.empty();
          customColvisMenu.append(
            '<div style="font-weight:bold; margin-bottom:10px; padding-bottom:5px; border-bottom:1px solid #eee;">Mostrar/Ocultar columnas</div>'
          );

          table.columns().every(function (index) {
            if (index > 0) {
              var column = this;
              var isVisible = column.visible();
              var header = $(column.header()).text().trim();

              var checkbox = $(
                '<div style="margin:5px 0;">' +
                  '<label style="display:flex; align-items:center;">' +
                  '<input type="checkbox" ' +
                  (isVisible ? "checked" : "") +
                  "> " +
                  '<span style="margin-left:5px;">' +
                  header +
                  "</span>" +
                  "</label></div>"
              );

              checkbox.find("input").data("column-index", index);
              checkbox.find("input").on("change", function (e) {
                e.stopPropagation();
                var colIdx = $(this).data("column-index");
                var visible = $(this).prop("checked");
                table.column(colIdx).visible(visible);
                table.state.save();
                table.columns.adjust().draw();
              });

              customColvisMenu.append(checkbox);
            }
          });

          customColvisMenu.show();
          if (typeof initializeCustomTooltips === "function") {
            initializeCustomTooltips();
          }
        });

        $(document).on("click", function (e) {
          if (
            !$(e.target).closest(".custom-colvis-menu, #btn-colvis-custom")
              .length
          ) {
            customColvisMenu.hide();
          }
        });

        $(document).on("keydown", function (e) {
          if (e.key === "Escape") {
            customColvisMenu.hide();
          }
        });
      }, 300);
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
    stateLoadParams: function(settings, data) {
      return true;
    },
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

                $(cell).attr("data-tooltip", tooltipMessages.join("<br>"));

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
      fixedColumnsLeft: 0, // Desactivado ya que usamos CSS
      fixedColumnsRight: 0,
      reorderCallback: function() {
        table.state.save();
      }
    },
    buttons: [
      {
        extend: "colvis",
        text: '<i class="fa fa-eye"></i>',
        collectionLayout: "fixed columns",
        columns: ":not(:first-child)",
        className: "icono-buscador",
        attr: {
          "data-tooltip": "Mostrar/ocultar columnas",
          id: "btn-colvis",
        },
        init: function (api, node, config) {
          $(node).removeClass("dt-button");
          $(node).find(".dt-down-arrow").remove();
        },
      },
    ],
  });

  // Mantener fijos los controles de paginación
  $(window).scroll(function () {
    var tableBottom =
      $(".datatable-container").offset().top +
      $(".datatable-container").height();
    var scrollPosition = $(window).scrollTop() + $(window).height();

    if (scrollPosition < tableBottom) {
      $(".dataTables_paginate, .dataTables_info, .dataTables_length")
        .css("position", "fixed")
        .css("bottom", "0")
        .css("background", "#fff")
        .css("width", $(".datatable-container").width())
        .css("z-index", "10")
        .css("padding", "8px 0")
        .css("box-shadow", "0 -2px 5px rgba(0,0,0,0.1)");
    } else {
      $(".dataTables_paginate, .dataTables_info, .dataTables_length")
        .css("position", "static")
        .css("box-shadow", "none");
    }
  });

  table.columns.adjust().draw();

  setTimeout(function () {
    table.columns.adjust().draw();
  }, 200);

  $(document).on("click", function (e) {
    if (
      !$(e.target).closest(".custom-columns-menu, #icono-visibilidad").length
    ) {
      $(".custom-columns-menu").remove();
    }
  });
});

$(window).on("load", function () {
  if (table && table.columns) {
    setTimeout(function () {
      table.columns.adjust().draw();
    }, 500);
  }
});

$(window).on("resize", function () {
  if (table) {
    table.columns.adjust().draw();
  }
});

$(".filter-icon").hide();

function toggleFilterIcons() {
  $(".filter-icon").toggle();
  $("#icono-filtro").toggleClass("active");
}

$("#icono-filtro").on("click", toggleFilterIcons);

$(document).on("click", function (e) {
  if (!$(e.target).closest(".filter-menu, .filter-icon").length) {
    $(".filter-menu").hide();
  }
});

$(document).on('order.dt length.dt', function(e, settings) {
  if (table) {
    table.state.save();
  }
});
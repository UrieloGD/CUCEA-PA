// ./JS/basesdedatos/inicializar-tablas.js
var table;

// Observar cambios en el DOM y eliminar los elementos en cuanto aparezcan
(function() {
  // Función para eliminar los elementos
  function removeTargets() {
    var targets = document.querySelectorAll("#icono-visibilidad, #btn-colvis");
    targets.forEach(function(el) {
      if (el && el.parentNode) {
        el.parentNode.removeChild(el);
        console.log("Elemento eliminado:", el.id);
      }
    });
  }
  
  // Configurar MutationObserver para vigilar cambios en el DOM
  var observer = new MutationObserver(function(mutations) {
    removeTargets();
  });
  
  // Comenzar observación tan pronto como sea posible
  observer.observe(document.documentElement || document.body, {
    childList: true,
    subtree: true
  });
  
  // Intentar eliminar inmediatamente y periódicamente
  removeTargets();
  setInterval(removeTargets, 50);
  
  // También cuando el DOM esté listo
  document.addEventListener("DOMContentLoaded", removeTargets);
  
  // Y cuando todo esté cargado
  window.addEventListener("load", removeTargets);
})();

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
  // Verificar si la tabla ya existe y conservarla en lugar de destruirla
  if ($.fn.dataTable.isDataTable('#tabla-datos')) {
    table = $('#tabla-datos').DataTable();
    console.log("Tabla existente recuperada"); // Depuración
    
    // Esperar y asegurarse de que los componentes personalizados estén presentes
    setTimeout(function() {
      initializeCustomTooltips();
      setupCustomColvisButton();
    }, 500);
    
    return; // Importante: salir aquí para evitar reinicializar
  }

  // Si llegamos aquí, inicializamos la tabla por primera vez
  try {
    table = $("#tabla-datos").DataTable({
      scrollY: "620px", // Altura fija para el cuerpo de la tabla
      scrollCollapse: true, // Permite que la tabla se colapse cuando hay poco contenido
      scrollX: true, // Scroll horizontal si es necesario
      fixedHeader: {
        header: true, // Mantiene el encabezado fijo durante el scroll
        headerOffset: 0, // Ajusta este valor si tienes una barra de navegación fija en la parte superior
        footer: false,
      },
      fixedColumns: {
        leftColumns: 2, // Fija las dos primeras columnas
      },
      dom: '<"top"<"custom-search-container">fB>rt<"bottom"lip>',
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

        // Configurar el botón personalizado para mostrar/ocultar columnas
        setupCustomColvisButton();
      },
      pageLength: 15,
      lengthMenu: [
        [15, 25, 50, -1],
        [15, 25, 50, "Todos"],
      ],
      responsive: true,
      stateSave: true,
      stateDuration: -1, // -1 significa que el estado se guardará indefinidamente
      stateSaveCallback: function (settings, data) {
        // Guardar estado en localStorage
        localStorage.setItem(
          "DataTables_" + settings.sInstance,
          JSON.stringify(data)
        );
      },
      stateLoadCallback: function (settings) {
        // Cargar estado desde localStorage
        return JSON.parse(
          localStorage.getItem("DataTables_" + settings.sInstance)
        );
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
          // Override the default button rendering
          init: function (api, node, config) {
            $(node).removeClass("dt-button");
            $(node).find(".dt-down-arrow").remove();
          },
        },
      ],
    });
    console.log("Tabla inicializada correctamente"); // Depuración
  } catch (error) {
    console.error("Error al inicializar DataTables:", error); // Depuración
  }

  // Mantener fijos los controles de paginación
  $(window).scroll(function () {
    var tableBottom =
      $(".datatable-container").offset().top +
      $(".datatable-container").height();
    var scrollPosition = $(window).scrollTop() + $(window).height();

    if (scrollPosition < tableBottom) {
      // Fijar los controles en la parte inferior
      $(".dataTables_paginate, .dataTables_info, .dataTables_length")
        .css("position", "fixed")
        .css("bottom", "0")
        .css("background", "#fff")
        .css("width", $(".datatable-container").width())
        .css("z-index", "10")
        .css("padding", "8px 0")
        .css("box-shadow", "0 -2px 5px rgba(0,0,0,0.1)");
    } else {
      // Volver a la posición normal
      $(".dataTables_paginate, .dataTables_info, .dataTables_length")
        .css("position", "static")
        .css("box-shadow", "none");
    }
  });

  // Redibujar tabla - mueve esto dentro de document.ready
  table.columns.adjust().draw();

  // Redibujar tabla
  setTimeout(function () {
    table.columns.adjust().draw();
  }, 200);

  // Asegurar de que los botones de DataTables estén inicializados correctamente
  $(document).on("click", function (e) {
    if (
      !$(e.target).closest(".custom-columns-menu, #icono-visibilidad").length
    ) {
      $(".custom-columns-menu").remove();
    }
  });
});

// Función para configurar el botón personalizado para mostrar/ocultar columnas
function setupCustomColvisButton() {
  setTimeout(function () {
    // Ocultar el botón original
    $(".dt-buttons").css("display", "none");

    // Verificar si ya existe nuestro botón personalizado para evitar duplicados
    if ($("#btn-colvis-custom").length === 0) {
      // Crear un contenedor personalizado para nuestro menú
      var customColvisMenu = $(
        '<div class="custom-colvis-menu" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; box-shadow:0 2px 4px rgba(0,0,0,0.2); padding:10px; z-index:1000; max-height:400px; overflow-y:auto;"></div>'
      );
      $("body").append(customColvisMenu);

      // Crear un botón personalizado con el mismo icono
      var customButton = $(
        '<div class="icono-buscador" id="btn-colvis-custom" data-tooltip="Mostrar/ocultar columnas"><i class="fa fa-eye"></i></div>'
      );

      // Insertar el botón en la ubicación deseada
      customButton.insertBefore("#icono-filtro");

      // Eliminar el botón no funcional
      $("#icono-visibilidad, #btn-colvis").remove();

      // Evento para abrir/cerrar el menú personalizado
      customButton.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Si el menú está visible, ocultarlo
        if (customColvisMenu.is(":visible")) {
          customColvisMenu.hide();
          return;
        }

        // Posicionar el menú debajo del botón
        var buttonPos = $(this).offset();
        customColvisMenu.css({
          top: buttonPos.top + $(this).outerHeight() + 5,
          left: buttonPos.left,
        });

        // Llenar el menú con las columnas disponibles
        customColvisMenu.empty();

        // Añadir un título
        customColvisMenu.append(
          '<div style="font-weight:bold; margin-bottom:10px; padding-bottom:5px; border-bottom:1px solid #eee;">Mostrar/Ocultar columnas</div>'
        );

        // Añadir una opción para cada columna (excepto la primera)
        table.columns().every(function (index) {
          if (index > 0) {
            // Excluir la primera columna
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

              // Guardar estado después de cambiar visibilidad
              table.state.save();

              // Redimensionar para corregir anchos después del cambio
              table.columns.adjust().draw();
            });

            customColvisMenu.append(checkbox);
          }
        });

        // Mostrar el menú
        customColvisMenu.show();

        // Re-inicializar tooltips si es necesario
        if (typeof initializeCustomTooltips === "function") {
          initializeCustomTooltips();
        }
      });

      // Cerrar el menú al hacer clic fuera de él
      $(document).on("click", function (e) {
        if (
          !$(e.target).closest(".custom-colvis-menu, #btn-colvis-custom")
            .length
        ) {
          customColvisMenu.hide();
        }
      });

      // Cerrar el menú con la tecla ESC
      $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
          customColvisMenu.hide();
        }
      });
    }
  }, 300);
}

// Ajusta las columnas después de la carga completa
$(window).on("load", function () {
  if (table && table.columns) {
    setTimeout(function () {
      table.columns.adjust().draw();
    }, 500); // Un pequeño retraso puede ayudar
  }
});

// Ajusta las columnas si el tamaño de la ventana cambia
$(window).on("resize", function () {
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
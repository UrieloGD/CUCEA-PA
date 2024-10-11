/**
 * Inicializa y configura la tabla HTML con DataTables aplicando múltiples opciones personalizadas.
 * - "language": Carga un archivo de traducción en español para la tabla.
 * - "pageLength": Establece el número predeterminado de filas visibles por página (50).
 * - "lengthMenu": Define un menú desplegable para seleccionar el número de filas por página (10, 25, 50 o "Todos").
 * - "responsive": Habilita la adaptación automática de la tabla para dispositivos móviles.
 * - "ordering": Permite la funcionalidad de ordenamiento en las columnas de la tabla.
 * - "info": Muestra información del estado de la tabla, como la cantidad de filas y la página actual.
 * - "dom": Personaliza la estructura de los elementos visibles en la tabla
 * - "scrollX": Habilita el desplazamiento horizontal para manejar tablas más anchas que el viewport.
 * - "scrollCollapse": Habilita la reducción del área de scroll cuando los datos son menos que el espacio disponible.
 * - "fixedHeader": Mantiene visible el encabezado de la tabla mientras se desplaza verticalmente.
 */

$(document).ready(function () {
  var table = $("#tabla-datos").DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
    },
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    responsive: true,
    ordering: false,
    info: true,
    dom: '<"top"f>rt<"bottom"ip><"clear">',
    scrollX: true,
    scrollCollapse: true,
    fixedHeader: true,
    columnDefs: [
      { orderable: false, targets: 0 },
      { reorderable: false, targets: 0 },
      { orderable: false, targets: -1 },
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
        columns: ":not(:first-child)", // Excluye la primera columna (checkbox)
      },
    ],
  });

  // Vincula la funcionalidad del botón de visibilidad al icono en el encabezado
  $("#icono-visibilidad").on("click", function () {
    table.button(".buttons-colvis").trigger();
  });

  // Inicialización del plugin FixedColumns
  new $.fn.dataTable.FixedColumns(table, {
    leftColumns: 1,
    rightColumns: 0,
  });
});

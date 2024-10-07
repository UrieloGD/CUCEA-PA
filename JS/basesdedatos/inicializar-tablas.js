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

$(document).ready(function() {
    // Verificar si hay un orden de columnas guardado en localStorage
    var savedOrder = localStorage.getItem('columnOrder');
    
    var table = $('#tabla-datos').DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        responsive: true,
        ordering: true,
        info: true,
        dom: '<"top"fB>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'colvis',
                collectionLayout: 'fixed columns',
                popoverTitle: 'Control de visibilidad de columnas'
            }
        ],
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: true,
        rowReorder: {
            selector: 'td:first-child',  // La primera columna se usa para reordenar las filas
            update: false // No actualizar los datos automáticamente
        }, 
        columnDefs: [
            { orderable: false, targets: 0 },  // La columna de selección (índice 0) no es ordenable
            { reorderable: false, targets: 0 }, // La columna de selección (índice 0) no es reordenable
            { orderable: false, targets: -1 }  // La última columna tampoco es ordenable
        ],
        order: [[1, 'asc']],
        colReorder: {
            fixedColumnsLeft: 1,  // Solo se fija la primera columna (índice 0) a la izquierda
            fixedColumnsRight: 0,  // No se fija ninguna columna a la derecha
            order: savedOrder ? JSON.parse(savedOrder) : null  // Aplica el orden guardado si existe
        },
    });

    // Guardar el nuevo orden de las columnas en localStorage al reordenarlas
    table.on('column-reorder', function (e, settings, details) {
        var newOrder = table.colReorder.order();
        localStorage.setItem('columnOrder', JSON.stringify(newOrder));
    });

    // Guardar el nuevo orden de las filas en el backend o localStorage
    table.on('row-reorder', function (e, diff, edit) {
        // Aquí podrías implementar la lógica para guardar el nuevo orden
        console.log('Nuevos índices de fila:', diff);
        // Podrías guardar el nuevo orden en el backend o en localStorage si es necesario
    });

    // Inicialización del plugin FixedColumns para mantener fija la primera columna
    new $.fn.dataTable.FixedColumns(table, {
        leftColumns: 1, // La primera columna queda fija
        rightColumns: 0 // No se fija ninguna columna en el lado derecho
    });
});

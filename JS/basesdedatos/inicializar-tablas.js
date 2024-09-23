/**
 * Inicializa y configura la tabla HTML con DataTables, aplicando opciones personalizadas:
 * - "language": Traducción al español.
 * - "pageLength": 50 filas por página por defecto.
 * - "lengthMenu": Opciones de 10, 25, 50 filas o mostrar todas.
 * - "responsive": Adaptación automática para dispositivos móviles.
 * - "ordering": Permite ordenar columnas.
 * - "info": Muestra información sobre la tabla (página, filas).
 * - "dom": Personaliza la posición del buscador y la paginación.
 * - "buttons": Botón para mostrar/ocultar columnas.
 */

$(document).ready(function() {
    $('#tabla-datos').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pageLength": 50,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "responsive": true,
        "ordering": true,
        "info": true,
        "dom": '<"top"fB>rt<"bottom"ip><"clear">',
        "buttons": [
            {
                extend: 'colvis',
                collectionLayout: 'fixed columns',
                popoverTitle: 'Control de visibilidad de columnas'
            }
        ]
    });
});

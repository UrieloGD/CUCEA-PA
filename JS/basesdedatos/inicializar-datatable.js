$(document).ready(function() {
    var table = $('#tabla-datos').DataTable({
        "paging": true,
        "ordering": true,
        "info": true,
        "searching": true,
        "lengthChange": true,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "dom": '<"#tabla-controles"fl>t<"bottom"ip>'
    });

    // Mover los controles de DataTables
    $('#tabla-controles').append($('.dataTables_length, .dataTables_filter'));

    // Crear menús de filtro
    $('.filter-icon').each(function(i) {
        var column = table.column(i+1);
        var filterMenu = $('<div class="filter-menu"></div>');
        
        var uniqueValues = column.data().unique().sort().toArray();
        uniqueValues.forEach(function(value) {
            filterMenu.append('<label><input type="checkbox" value="' + value + '"> ' + value + '</label>');
        });
        
        filterMenu.append('<button class="apply-filter">Aplicar</button>');
        filterMenu.append('<button class="clear-filter">Limpiar</button>');
        
        $(this).append(filterMenu);

        // Mostrar/ocultar menú al hacer clic en el icono
        $(this).on('click', function(e) {
            e.stopPropagation();
            $('.filter-menu').not(filterMenu).hide();
            filterMenu.toggle();
        });

        // Aplicar filtro
        filterMenu.find('.apply-filter').on('click', function() {
            var selectedValues = filterMenu.find('input:checked').map(function() {
                return '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$';
            }).get().join('|');
            
            column.search(selectedValues, true, false).draw();
            filterMenu.hide();
        });

        // Limpiar filtro
        filterMenu.find('.clear-filter').on('click', function() {
            filterMenu.find('input').prop('checked', false);
            column.search('').draw();
            filterMenu.hide();
        });
    });

    // Cerrar menús al hacer clic fuera
    $(document).on('click', function() {
        $('.filter-menu').hide();
    });
});
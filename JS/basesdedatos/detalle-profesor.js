// Inicializar DataTable para la tabla de profesores
$(document).ready(function() {
    $('#tabla-profesores').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[1, 'asc']]
    });

    // Búsqueda en tiempo real
    $('#buscar-profesor').on('keyup', function() {
        $('#tabla-profesores').DataTable().search($(this).val()).draw();
    });
});


// Función para ver detalle del profesor
function verDetalleProfesor(codigo_profesor) {
    $.ajax({
        url: 'detalle_profesor.php',
        method: 'POST',
        data: {
            codigo_profesor: codigo_profesor,
            departamento_id: $('#departamento_id').val(),
            tabla_departamento: 'Data_' + $('#departamento_id').val()
        },
        success: function(response) {
            $('#detalle-profesor-contenido').html(response);
            $('#modal-detalle-profesor').show();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar los detalles:', error);
            alert('Error al cargar los detalles del profesor');
        }
    });
}

// Funciones para cerrar modales
function cerrarModalVisualizar() {
    $('#modal-visualizar').hide();
}

function cerrarModalDetalle() {
    $('#modal-detalle-profesor').hide();
}

// Cerrar modales al hacer clic fuera de ellos
$(window).click(function(event) {
    if (event.target.className === 'modal') {
        $('.modal').hide();
    }
});
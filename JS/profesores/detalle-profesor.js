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
    if (!document.getElementById('modalStyles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'modalStyles';
        styleSheet.textContent = `
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
            }
            .modal-content {
                position: relative;
                background-color: #fff;
                margin: 5% auto;
                padding: 0;
                width: 70%;
                max-width: 700px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                animation: modalFadeIn 0.3s ease-out;
            }
            .modal-header {
                padding: 15px 20px;
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
                border-radius: 8px 8px 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .modal-header h3 {
                margin: 0;
                color: #333;
                font-size: 1.25rem;
            }
            .close {
                color: #aaa;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
                padding: 0 5px;
            }
            .close:hover {
                color: #333;
            }
            .modal-body {
                padding: 20px;
            }
            .alert {
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 15px;
            }
            .alert-info {
                background-color: #e3f2fd;
                color: #0c5460;
                border: 1px solid #bee5eb;
            }
            .alert-danger {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            @keyframes modalFadeIn {
                from {opacity: 0}
                to {opacity: 1}
            }
            @media screen and (max-width: 768px) {
                .modal-content {
                    width: 90%;
                    margin: 10% auto;
                }
            }
        `;
        document.head.appendChild(styleSheet);
    }
    
    if (!codigo_profesor) {
        // Si no hay código, mostrar mensaje en el modal con botón de cierre
        $('#detalle-profesor-contenido').html(`
            
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Información del Profesor</h3>
                    <span class="close" onclick="cerrarModalDetalle()">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">Este profesor no tiene un código establecido</div>
                </div>
            </div>
        `);
        $('#modal-detalle-profesor').show();
        return;
    }

    $.ajax({
        url: './functions/profesores/detalle-profesor.php',
        method: 'POST',
        data: {
            codigo_profesor: codigo_profesor,
            departamento_id: $('#departamento_id').val(),
        },
        success: function(response) {
            $('#detalle-profesor-contenido').html(response);
            $('#modal-detalle-profesor').show();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar los detalles:', error);
            $('#detalle-profesor-contenido').html(`
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Error</h3>
                        <span class="close" onclick="cerrarModalDetalle()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">Error al cargar los detalles del profesor</div>
                    </div>
                </div>
            `);
            $('#modal-detalle-profesor').show();
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
    if (event.target.className === 'modal-detalle') {
        $('.modal-detalle').hide();
    }
});
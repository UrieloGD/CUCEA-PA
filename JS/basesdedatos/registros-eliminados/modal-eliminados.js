// ./JS/basesdedatos/registros-eliminados/modal-eliminados.js

// Configuraciones para el modal de registros eliminados de jefe de departamento

// Función para cerrar el modal de registros eliminados
function cerrarModalRegistrosEliminados() {
    $('#modalRegistrosEliminados').modal('hide');
}

// Función para mostrar el modal de registros eliminados
function mostrarModalRegistrosEliminados() {
    $('#modalRegistrosEliminados').modal('show');
}

// Define la función que se llama desde el botón en el HTML
function cerrarRegistrosEliminados() {
    cerrarModalRegistrosEliminados();
}

// Función para inicializar la tabla de eliminados
function inicializarTablaEliminados() {
    if (!$.fn.DataTable.isDataTable('#tabla-eliminados')) {
        // Envolver tabla en un div con scroll
        $('#tabla-eliminados').wrap('<div class="tabla-container" style="max-height: 60vh; overflow-y: auto;"></div>'); // Estilos para el modal de registros eliminados
        
        // Inicializar la tabla de registros eliminados
        tablaEliminados = $('#tabla-eliminados').DataTable({
            "ajax": {
                "url": '/CUCEA-PA/functions/basesdedatos/papelera/obtener-registros-eliminados.php',
                "type": 'POST',
                "data": function(d) {
                    d.Papelera = 'inactivo';
                }
            },
            "columns": [
                { "data": "ID_Plantilla", },
                { "data": "Departamento_ID" },
                { "data": "CICLO" },
                { "data": "CRN" },
                { "data": "MATERIA" },
                { "data": "CVE_MATERIA" },
                { "data": "SECCION" },
                { "data": "NIVEL" },
                { "data": "NIVEL_TIPO" },
                { "data": "TIPO" },
                { "data": "C_MIN" },
                { "data": "H_TOTALES" },
                { "data": "ESTATUS" },
                { "data": "TIPO_CONTRATO" },
                { "data": "CODIGO_PROFESOR" },
                { "data": "NOMBRE_PROFESOR" },
                { "data": "CATEGORIA" },
                { "data": "DESCARGA" },
                { "data": "CODIGO_DESCARGA" },
                { "data": "NOMBRE_DESCARGA" },
                { "data": "NOMBRE_DEFINITIVO" },
                { "data": "TITULAR" },
                { "data": "HORAS" },
                { "data": "CODIGO_DEPENDENCIA" },
                { "data": "L" },
                { "data": "M" },
                { "data": "I" },
                { "data": "J" },
                { "data": "V" },
                { "data": "S" },
                { "data": "D" },
                { "data": "DIA_PRESENCIAL" },
                { "data": "DIA_VIRTUAL" },
                { "data": "MODALIDAD" },
                { "data": "FECHA_INICIAL" },
                { "data": "HORA_INICIAL" },
                { "data": "HORA_FINAL" },
                { "data": "MODULO" },
                { "data": "AULA" },
                { "data": "CUPO" },
                { "data": "OBSERVACIONES" },
                { "data": "EXAMEN_EXTRAORDINARIO" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            return `<button type="button" 
                                    class="btn btn-primary btn-restaurar" 
                                    data-id="${row.ID_Plantilla}"
                                    onclick="event.preventDefault();">
                                    Restaurar
                                </button>`;
                        }
                        return '';
                    }
                }
            ],
            // "scrollY": Descartado porque no se pueden mantener fijas las columnas extremas
            "scrollX": true,
            "scrollCollapse": true, // Hace que la tabla sea responsive
            "fixedColumns": {
                "leftColumns": 1, // Columna "ID" fijado a la izquierda
                "rightColumns": 1 // Columna "Acciones" fijado a la derecha
            },
            "fixedHeader": true,
            "pageLength": 10,        // Registros por página
            "lengthChange": true,  // Habilitamos el selector de registros
            // Define las opciones del menú de longitud
            "lengthMenu": [[-1, 15, 25, 50], ["Todos", 15, 25, 50]],
            // Establece -1 como la longitud de página predeterminada (para mostrar todos)
            "pageLength": -1,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]], // Opciones del selector
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                "info": "",
                "lengthMenu": "Mostrar _MENU_ registros", // Personaliza el texto del selector
                "searchPlaceholder": "Buscar...",
            },
            "ordering": true,
            "dom": '<"top"f<"pull-right"l>>t<"bottom"i>',
        });
    }
}

// Event listeners cuando el DOM está cargado
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar el modal al hacer clic en la X
    const closeButton = document.querySelector('#modalRegistrosEliminados .close');
    if (closeButton) {
        closeButton.onclick = function() {
            cerrarModalRegistrosEliminados();
        }
    }
    
    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById('modalRegistrosEliminados');
        if (event.target == modal) {
            cerrarModalRegistrosEliminados();
        }
    }

    // Eventos para ajustar las columnas cuando el modal se muestra
    $('#modalRegistrosEliminados').on('shown.bs.modal', function() {
        if (tablaEliminados) {
            tablaEliminados.columns.adjust();
        }
    });

    // Ajustar columnas cuando cambia el tamaño de la ventana
    $(window).on('resize', function() {
        if ($('#modalRegistrosEliminados').is(':visible') && tablaEliminados) {
            tablaEliminados.columns.adjust();
        }
    });
});
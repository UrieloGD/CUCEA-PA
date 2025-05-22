// Configuraciones para el modal de registros eliminados de coordinación de personal

// Función para cerrar el modal de registros eliminados
function cerrarModalRegistrosEliminados() {
    $('#modalRegistrosEliminados').modal('hide');
}

// Función para mostrar el modal de registros eliminados
function mostrarModalRegistrosEliminados() {
    $('#modalRegistrosEliminados').modal('show');
    inicializarTablaEliminados(); // Inicializar tabla al abrir el modal
}

// Define la función que se llama desde el botón en el HTML
function cerrarRegistrosEliminados() {
    cerrarModalRegistrosEliminados();
}

// Función para inicializar la tabla de eliminados
function inicializarTablaEliminados() {
    if (!$.fn.DataTable.isDataTable('#tabla-eliminados')) {
        // Envolver tabla en un div con scroll
        $('#tabla-eliminados').wrap('<div class="tabla-container" style="max-height: 60vh; overflow-y: auto;"></div>'); 
        
        // Inicializar la tabla de registros eliminados
        tablaEliminados = $('#tabla-eliminados').DataTable({
            "ajax": {
                "url": './functions/coord-personal-plantilla/registros-eliminados/obtener-registros.php',
                "type": 'POST',
                "data": function(d) {
                    d.Papelera = 'inactivo';
                },
                "error": function(xhr, status, error) {
                    console.error("Error en la solicitud AJAX:", status, error);
                    console.log("Respuesta del servidor:", xhr.responseText);
                }
            },
            "columns": [
                { "data": "ID" },
                { "data": "Codigo" },
                { "data": "Paterno" },
                { "data": "Materno" },
                { "data": "Nombres" },
                { "data": "Nombre_completo" },
                { "data": "Departamento" },
                { "data": "Categoria_actual" },
                { "data": "Categoria_actual_dos" },
                { "data": "Horas_frente_grupo" },
                { "data": "Division" },
                { "data": "Tipo_plaza" },
                { "data": "Cat_act" },
                { "data": "Carga_horaria" },
                { "data": "Horas_definitivas" },
                { "data": "Horario" },
                { "data": "Turno" },
                { "data": "Investigacion_nombramiento_cambio_funcion" },
                { "data": "SNI" },
                { "data": "SNI_desde" },
                { "data": "Cambio_dedicacion" },
                { "data": "Telefono_particular" },
                { "data": "Telefono_oficina" },
                { "data": "Domicilio" },
                { "data": "Colonia" },
                { "data": "CP" },
                { "data": "Ciudad" },
                { "data": "Estado" },
                { "data": "No_imss" },
                { "data": "CURP" },
                { "data": "RFC" },
                { "data": "Lugar_nacimiento" },
                { "data": "Estado_civil" },
                { "data": "Tipo_sangre" },
                { "data": "Fecha_nacimiento" },
                { "data": "Edad" },
                { "data": "Nacionalidad" },
                { "data": "Correo" },
                { "data": "Correos_oficiales" },
                { "data": "Ultimo_grado" },
                { "data": "Programa" },
                { "data": "Nivel" },
                { "data": "Institucion" },
                { "data": "Estado_pais" },
                { "data": "Año" },
                { "data": "Gdo_exp" },
                { "data": "Otro_grado" },
                { "data": "Otro_programa" },
                { "data": "Otro_nivel" },
                { "data": "Otro_institucion" },
                { "data": "Otro_estado_pais" },
                { "data": "Otro_año" },
                { "data": "Otro_gdo_exp" },
                { "data": "Otro_grado_alternativo" },
                { "data": "Otro_programa_alternativo" },
                { "data": "Otro_nivel_altenrativo" },
                { "data": "Otro_institucion_alternativo" },
                { "data": "Otro_estado_pais_alternativo" },
                { "data": "Otro_año_alternativo" },
                { "data": "Otro_gdo_exp_alternativo" },
                { "data": "Proesde_24_25" },
                { "data": "A_partir_de" },
                { "data": "Fecha_ingreso" },
                { "data": "Antiguedad" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            return `<button type="button" 
                                    class="btn btn-primary btn-restaurar" 
                                    data-id="${row.ID}">
                                    Restaurar
                                    </button>`;
                        }
                        return '';
                    }
                }
            ],
            "scrollX": true,
            "scrollCollapse": true,
            "fixedColumns": {
                "leftColumns": 1,
                "rightColumns": 1
            },
            "fixedHeader": true,
            "lengthChange": true,
            "lengthMenu": [[-1, 15, 25, 50], ["Todos", 15, 25, 50]],
            "pageLength": -1,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                "info": "",
                "lengthMenu": "Mostrar _MENU_ registros",
                "searchPlaceholder": "Buscar..."
            },
            "ordering": true,
            "dom": '<"top"f<"pull-right"l>>t<"bottom"i>'
        });

        // Evento para restaurar registros
        $('#tabla-eliminados tbody').on('click', '.btn-restaurar', function() {
            const id = $(this).data('id');
            restaurarRegistro(id);
        });
    }
}

// Función para restaurar un registro
function restaurarRegistro(id) {
    Swal.fire({
        title: '¿Restaurar registro?',
        text: "Esta acción devolverá el registro a la tabla principal",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: "confirmar-registrosRestaurar",
            cancelButton: "cancelar-todo",
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: './functions/coord-personal-plantilla/registros-eliminados/restaurar-registro.php',
                type: 'POST',
                data: { ids: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        tablaEliminados.ajax.reload();
                        // Si tienes una variable tablaPrincipal definida:
                        if (typeof tablaPrincipal !== 'undefined') {
                            tablaPrincipal.ajax.reload();
                        }
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            customClass: {
                                confirmButton: "OK-boton",
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            customClass: {
                                confirmButton: "OK-boton",
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al conectar con el servidor: ' + error,
                        icon: 'error',
                        customClass: {
                            confirmButton: "OK-boton",
                        }
                    });
                }
            });
        }
    });
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
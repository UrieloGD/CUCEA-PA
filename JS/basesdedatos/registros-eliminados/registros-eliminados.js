// ./JS/basesdedatos/registros-eliminados/registros-eliminados.js
let tablaEliminados; // Variable global

document.addEventListener('DOMContentLoaded', (function() {
    const departamentoId = document.getElementById('departamento_id').value;

    // Función para inicializar la tabla de registros eliminados
    const inicializarTabla = () => {
        if (tablaEliminados) {
            tablaEliminados.destroy();
        }

        tablaEliminados = $('#tabla-eliminados').DataTable({
            ajax: {
                url: './functions/basesdedatos/modal-registros-eliminados/obtener-registros-eliminados.php',
                type: 'POST',
                data: {
                    Departamento_ID: departamentoId,
                    papelera: 'inactivo'
                },
                dataSrc: 'data',
                error: function(xhr) {
                    console.error('Detalles del error:', {
                        status: xhr.status,
                        response: xhr.responseText
                    });
                }
            },
            columns: [
                { data: 'ID_Plantilla' },
                { data: 'CICLO' },
                { data: 'CRN' },
                { data: 'MATERIA' },
                { data: 'CVE_MATERIA' },
                { data: 'SECCION' },
                { data: 'NIVEL' },
                { data: 'NIVEL_TIPO' },
                { data: 'TIPO' },
                { data: 'C_MIN' },
                { data: 'H_TOTALES' },
                { data: 'ESTATUS' },
                { data: 'TIPO_CONTRATO' },
                { data: 'CODIGO_PROFESOR' },
                { data: 'NOMBRE_PROFESOR' },
                { data: 'CATEGORIA' },
                { data: 'DESCARGA' },
                { data: 'CODIGO_DESCARGA' },
                { data: 'NOMBRE_DESCARGA' },
                { data: 'NOMBRE_DEFINITIVO' },
                { data: 'TITULAR' },
                { data: 'HORAS' },
                { data: 'CODIGO_DEPENDENCIA' },
                { data: 'L' },
                { data: 'M' },
                { data: 'I' },
                { data: 'J' },
                { data: 'V' },
                { data: 'S' },
                { data: 'D' },
                { data: 'DIA_PRESENCIAL' },
                { data: 'DIA_VIRTUAL' },
                { data: 'MODALIDAD' },
                { data: 'FECHA_INICIAL' },
                { data: 'FECHA_FINAL' },
                { data: 'HORA_INICIAL' },
                { data: 'HORA_FINAL' },
                { data: 'MODULO' },
                { data: 'AULA' },
                { data: 'CUPO' },
                { data: 'OBSERVACIONES' },
                { data: 'EXAMEN_EXTRAORDINARIO' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<button class="btn btn-primary btn-restaurar" 
                                  data-id="${row.ID_Plantilla}" 
                                  style="width: 100px;">
                                Restaurar
                              </button>`;
                    }
                }
            ],
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                searchPlaceholder: 'Buscar registros...',
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                lengthMenu: "Mostrar _MENU_ registros"
            },
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            pageLength: 10,
            dom: "<'row'<'col-sm-6'f><'col-sm-6'l>>rtip",
            ordering: true
        });
    };

    // Evento para abrir modal
    document.getElementById('icono-papelera').addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalRegistrosEliminados'));
        modal.show();
        
        // Inicializar tabla después de que el modal sea visible
        $('#modalRegistrosEliminados').on('shown.bs.modal', function() {
            inicializarTabla();
            
            // Aplicar FixedColumns después de que la tabla tenga datos
            setTimeout(() => {
                if (tablaEliminados) {
                    // Primero ajustar columnas
                    tablaEliminados.columns.adjust();
                    
                    // Verificar si la tabla tiene datos
                    if (tablaEliminados.data().length > 0) {
                        new $.fn.dataTable.FixedColumns(tablaEliminados, {
                            leftColumns: 1,  // Fija la columna ID
                            rightColumns: 1  // Fija la columna ACCIONES
                        });
                    }
                }
            }, 500); // Dar tiempo suficiente para que se renderice
        });
    });

    // Evento para restaurar registros
    document.getElementById('modalRegistrosEliminados').addEventListener('click', async (e) => {
        if(e.target.closest('.btn-restaurar')) {
            const boton = e.target.closest('.btn-restaurar');
            const idRegistro = boton.dataset.id;

            // Confirmación con SweetAlert
            const { isConfirmed } = await Swal.fire({
                title: '¿Restaurar registro?',
                text: "Esta acción volverá a mostrar el registro en la tabla principal",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0071B0',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            });

            if (isConfirmed) {
                try {
                    // Crear FormData en lugar de JSON
                    const formData = new FormData();
                    formData.append('id', idRegistro);
                    
                    const respuesta = await fetch('./functions/basesdedatos/modal-registros-eliminados/restaurar-registro.php', {
                        method: 'POST',
                        body: formData
                    });

                    // Para depuración, capturamos primero el texto
                    const textoRespuesta = await respuesta.text();
                    console.log('Respuesta del servidor:', textoRespuesta);
                    
                    try {
                        const resultado = JSON.parse(textoRespuesta);
                        
                        if (resultado.success) {
                            tablaEliminados.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Restaurado!',
                                text: resultado.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Recargar la página principal
                            });
                        } else {
                            throw new Error(resultado.message || 'Error desconocido');
                        }
                    } catch (parseError) {
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });
                }
            }
        }
    });

    // Manejo de eventos para ajustar la tabla cuando cambia el tamaño de la ventana
    $(window).on('resize', function() {
        if ($('#modalRegistrosEliminados').is(':visible') && tablaEliminados) {
            tablaEliminados.columns.adjust();
        }
    });

    // Función para cerrar el modal
    window.cerrarRegistrosEliminados = function() {
        bootstrap.Modal.getInstance(document.getElementById('modalRegistrosEliminados')).hide();
    };
})());
// ./JS/basesdedatos/registros-eliminados/registros-eliminados.js
let tablaEliminados = null;

document.addEventListener('DOMContentLoaded', (function() {
    const departamentoId = document.getElementById('departamento_id').value;
    const modal = document.getElementById('modalRegistrosEliminados');

    // Función para inicializar/recargar la tabla
    const inicializarTabla = () => {
        if (tablaEliminados !== null) {
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
                    console.error('Error al cargar datos:', xhr);
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
                                  style="min-width: 100px;">
                                Restaurar
                              </button>`;
                    },
                    orderable: false,
                    className: 'acciones-col'
                }
            ],
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1
            },
            fixedHeader: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
                searchPlaceholder: 'Buscar...',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros'
            },
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            pageLength: 10,
            dom: '<"top"<"row"<"col-sm-6"f><"col-sm-6"l>>>rt<"bottom"ip>',
            initComplete: function() {
                new $.fn.dataTable.FixedColumns(this, {
                  leftColumns: 1,
                  rightColumns: 1
                });
            }
        });
    };

    // Evento para abrir el modal
    document.getElementById('icono-papelera').addEventListener('click', () => {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
        
        // Inicializar tabla solo una vez cuando el modal se muestra
        $(modal).one('shown.bs.modal', function() {
            inicializarTabla();
            
            // Forzar ajuste de columnas después de renderizar
            setTimeout(() => {
                tablaEliminados.columns.adjust();
                $(window).trigger('resize');
            }, 50);
        });
    });

    // Evento para restaurar registros
    modal.addEventListener('click', async (e) => {
        if(e.target.closest('.btn-restaurar')) {
            const boton = e.target.closest('.btn-restaurar');
            const idRegistro = boton.dataset.id;

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
                    const formData = new FormData();
                    formData.append('id', idRegistro);
                    
                    const respuesta = await fetch('./functions/basesdedatos/modal-registros-eliminados/restaurar-registro.php', {
                        method: 'POST',
                        body: formData
                    });

                    const resultado = await respuesta.json();
                    
                    if (resultado.success) {
                        tablaEliminados.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Restaurado!',
                            text: resultado.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#tabla-datos').DataTable().ajax.reload();
                        });
                    } else {
                        throw new Error(resultado.message || 'Error desconocido');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: typeof error === 'object' ? error.message : error
                    });
                }
            }
        }
    });

    // Manejo de redimensionamiento
    $(window).on('resize', function() {
        if ($(modal).is(':visible') && tablaEliminados) {
          tablaEliminados.columns.adjust();
          tablaEliminados.fixedColumns().relayout();
        }
    });

    // Cerrar modal desde el botón
    window.cerrarRegistrosEliminados = function() {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    };
})());
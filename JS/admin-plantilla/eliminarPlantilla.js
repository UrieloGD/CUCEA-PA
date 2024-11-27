function eliminarPlantilla(id) {
    const nombreArchivo = document.getElementById(`nombre-archivo-${id}`).innerText;

    if (nombreArchivo === 'No hay archivo asignado') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No hay una plantilla asignada para eliminar.',
            customClass: {
                confirmButton: 'btn-guardar'
            },
            buttonsStyling: false
        });
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar SweetAlert de carga
            Swal.fire({
                title: 'Eliminando plantilla',
                html: 'Por favor, espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Llamada a la función para eliminar la plantilla
            fetch(`./functions/admin-plantilla/eliminar-plantilla.php?departamento_id=${id}`)
                .then(response => response.text())
                .then(data => {
                    if (data.includes('success')) {
                        Swal.fire({
                            title: 'Eliminado!',
                            text: 'La plantilla ha sido eliminada.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn-guardar'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al eliminar la plantilla.',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn-guardar'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error inesperado. Por favor, inténtalo de nuevo.',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn-guardar'
                        },
                        buttonsStyling: false
                    });
                });
        }
    });
}
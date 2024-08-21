function eliminarPlantilla(id) {
    const nombreArchivo = document.getElementById(`nombre-archivo-${id}`).innerText;

    if (nombreArchivo === 'No hay archivo asignado') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No hay una plantilla asignada para eliminar.'
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
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamada a la función para eliminar la plantilla
            fetch(`./functions/admin-plantilla/eliminar-plantilla.php?departamento_id=${id}`)
                .then(response => response.text())
                .then(data => {
                    if (data.includes('success')) {
                        Swal.fire(
                            'Eliminado!',
                            'La plantilla ha sido eliminada.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            'Ocurrió un error al eliminar la plantilla.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error',
                        'Ocurrió un error inesperado. Por favor, inténtalo de nuevo.',
                        'error'
                    );
                });
        }
    });
}

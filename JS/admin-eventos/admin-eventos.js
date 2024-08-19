function deleteEvent(eventId) {
    Swal.fire({
        title: 'Estás a punto de eliminar el evento',
        text: "¿Estás seguro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('./actions/admin-eventos/eliminarEvento.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: eventId
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Eliminado',
                            'El evento ha sido eliminado.',
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Error al eliminar el evento. Por favor, inténtalo de nuevo.',
                        'error'
                    );
                });
        }
    });
}

function editEvent(eventId) {
    window.location.href = `./editarEvento.php?id=${eventId}`;
}
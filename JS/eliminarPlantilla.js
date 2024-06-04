function eliminarPlantilla(departamentoId) {
    console.log("Función eliminarPlantilla llamada con ID:", departamentoId);

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Sí, elimínalo!'
    }).then((result) => {
        console.log("Resultado de Swal.fire:", result);

        if (result.isConfirmed) {
            console.log("Usuario confirmó la eliminación");

            // Enviar solicitud AJAX al servidor
            jQuery.ajax({
                url: "./config/eliminar_plantilla.php",
                type: "POST",
                data: { departamentoId: departamentoId },
                success: function(response) {
                    console.log("Respuesta del servidor:", response);
                    Swal.fire(
                        '¡Eliminado!',
                        'La plantilla ha sido eliminada.',
                        'success'
                    ).then(() => {
                        console.log("Recargar la página");
                        window.location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log("Error en la solicitud AJAX:");
                    console.log("Status:", status);
                    console.log("Error:", error);
                    Swal.fire(
                        'Error',
                        'Hubo un error al eliminar la plantilla: ' + error,
                        'error'
                    );
                }
            });
        } else {
            console.log("Usuario canceló la eliminación");
        }
    });
}
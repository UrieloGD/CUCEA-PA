function descargarPlantilla(id) {
    $.ajax({
        url: './functions/admin-plantilla/descargar-plantilla.php',
        method: 'GET',
        data: { departamento_id: id },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            // Verificar si la respuesta es JSON (indica un error)
            const contentType = xhr.getResponseHeader('Content-Type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                // Si la respuesta es JSON, convertirla a objeto y mostrar el error
                const reader = new FileReader();
                reader.onload = function() {
                    const errorResponse = JSON.parse(reader.result);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorResponse.message
                    });
                };
                reader.readAsText(data);
            } else {
                // Si la respuesta es un archivo, proceder con la descarga
                const filename = xhr.getResponseHeader('Content-Disposition').split('filename=')[1];
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = filename;
                link.click();
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al intentar descargar el archivo. Por favor, inténtalo de nuevo más tarde.'
            });
        }
    });
}

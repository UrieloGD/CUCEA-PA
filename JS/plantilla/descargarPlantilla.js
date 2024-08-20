function descargarArchivo(departamentoId) {
    $.ajax({
        url: './actions/plantilla/descargarPlantilla.php',
        method: 'GET',
        data: { departamento_id: departamentoId },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            const contentType = xhr.getResponseHeader('Content-Type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
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
                const disposition = xhr.getResponseHeader('Content-Disposition');
                let filename = "archivo_descargado";
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    filename = disposition.split('filename=')[1].replace(/['"]/g, '');
                }
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
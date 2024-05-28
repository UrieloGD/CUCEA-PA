document.getElementById('formulario-subida').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

    let formData = new FormData();
    let fileInput = document.getElementById('input-file');

    // Verificar si hay archivos seleccionados
    if (fileInput.files.length > 0) {
        let file = fileInput.files[0];

        // Validar el tipo de archivo y el tamaño
        const validExtensions = ['xlsx', 'xls'];
        const maxFileSize = 2 * 1024 * 1024; // 2MB
        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (validExtensions.includes(fileExtension) && file.size <= maxFileSize) {
            formData.append('file', file);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Archivo no válido',
                text: 'Asegúrate de subir solamente archivos con extensión .xls y .xlsx, y que no excedan los 2MB.',
            });
            return;
        }
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, selecciona un archivo.'
        });
        return;
    }

    // Crear una solicitud XMLHttpRequest
    let xhr = new XMLHttpRequest();
    xhr.open('POST', './config/upload.php', true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    didClose: () => {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar el archivo.'
            });
        }
    };

    xhr.onerror = function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al intentar cargar el archivo.'
        });
    };

    xhr.send(formData);
});
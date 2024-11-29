function subirArchivo(id) {
    Swal.fire({
        title: 'Selecciona un archivo',
        input: 'file',
        inputAttributes: {
            'accept': '*/*',
            'aria-label': 'Selecciona un archivo'
        },
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        // Aquí añadimos la personalización de colores
        customClass: {
            confirmButton: 'btn-guardar', // Clase personalizada para el botón de guardar
            cancelButton: 'btn-cancelar', // Clase personalizada para el botón de cancelar
            input: 'input-archivo' // Clase personalizada para el input de archivo
        },
        buttonsStyling: false, // Desactivamos los estilos por defecto
        preConfirm: (file) => {
            if (file) {
                const inputFileElement = document.getElementById(`input-file-${id}`);
                inputFileElement.files = new DataTransfer().files;
                const dt = new DataTransfer();
                dt.items.add(file);
                inputFileElement.files = dt.files;

                console.log("Archivo seleccionado:", file.name);

                actualizarNombreArchivo(inputFileElement, id);
                actualizarFechaSubida(id);
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar SweetAlert de carga
            Swal.fire({
                title: 'Subiendo archivo',
                html: 'Por favor, espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('file', document.getElementById(`input-file-${id}`).files[0]);
            formData.append('Departamento_ID', id);
            formData.append('Nombre_Archivo_Dep', document.getElementById(`nombre-archivo-${id}`).textContent);
            formData.append('Fecha_Subida_Dep', document.getElementById(`fecha-subida-${id}`).textContent);

            console.log("Datos del formulario para enviar:", {
                file: document.getElementById(`input-file-${id}`).files[0],
                Departamento_ID: id,
                Nombre_Archivo_Dep: document.getElementById(`nombre-archivo-${id}`).textContent,
                Fecha_Subida_Dep: document.getElementById(`fecha-subida-${id}`).textContent
            });

            fetch('./functions/admin-plantilla/upload-plantilla.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Respuesta del servidor:", data);
                if (data.includes('success')) {
                    Swal.fire('Archivo subido', 'El archivo se ha subido correctamente.', 'success')
                        .then(() => {
                            location.reload();
                        });
                } else {
                    Swal.fire('Error', 'Ocurrió un error al subir el archivo. Por favor, inténtalo de nuevo.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error inesperado. Por favor, inténtalo de nuevo.', 'error');
            });
        }
    });
}

function actualizarNombreArchivo(input, id) {
    if (input.files.length > 0) {
        var nombreArchivo = input.files[0].name;
        document.getElementById(`nombre-archivo-${id}`).innerText = nombreArchivo;
        document.getElementById(`Nombre_Archivo_Dep-${id}`).value = nombreArchivo;
        console.log(`Nombre de archivo actualizado para departamento ${id}: ${nombreArchivo}`);
    } else {
        document.getElementById(`nombre-archivo-${id}`).innerText = 'No se ha subido un archivo';
        console.log(`No se ha subido un archivo para departamento ${id}`);
    }
}

function actualizarFechaSubida(id) {
    var fechaFormateada = obtenerFechaHoraActual();
    document.getElementById(`fecha-subida-${id}`).innerText = fechaFormateada;
    document.getElementById(`Fecha_Subida_Dep-${id}`).value = fechaFormateada;
    console.log(`Fecha de subida actualizada para departamento ${id}: ${fechaFormateada}`);
}

function obtenerFechaHoraActual() {
    var fecha = new Date();
    var dia = fecha.getDate().toString().padStart(2, '0');
    var mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
    var año = fecha.getFullYear();
    var horas = fecha.getHours().toString().padStart(2, '0');
    var minutos = fecha.getMinutes().toString().padStart(2, '0');
    return `${dia}/${mes}/${año} ${horas}:${minutos}`;
}

function handleFileChange(event, id) {
    var inputFileElement = event.target;
    actualizarNombreArchivo(inputFileElement, id);
    actualizarFechaSubida(id);
    console.log(`Cambio de archivo manejado para departamento ${id}`);
    subirArchivo(id);
}

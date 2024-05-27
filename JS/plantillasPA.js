function obtenerFechaHoraActual() {
    var fecha = new Date();
    var dia = fecha.getDate().toString().padStart(2, '0');
    var mes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Los meses son 0-indexados
    var año = fecha.getFullYear();
    var horas = fecha.getHours().toString().padStart(2, '0');
    var minutos = fecha.getMinutes().toString().padStart(2, '0');
    return `${dia}/${mes}/${año} ${horas}:${minutos}`;
}

function actualizarNombreArchivo(input) {
    if (input.files.length > 0) {
        var nombreArchivo = input.files[0].name;
        document.getElementById('nombre-archivo').innerText = nombreArchivo;
        document.getElementById('Nombre_Archivo_Dep').value = nombreArchivo;
    } else {
        document.getElementById('nombre-archivo').innerText = 'No se ha subido un archivo';
    }
}

function actualizarFechaSubida() {
    var fechaFormateada = obtenerFechaHoraActual();
    document.getElementById('fecha-subida').innerText = fechaFormateada;
    document.getElementById('Fecha_Subida_Dep').value = fechaFormateada;
}

// Enviar el formulario automáticamente después de actualizar los campos ocultos
document.getElementById('input-file').addEventListener('change', function() {
    actualizarNombreArchivo(this);
    actualizarFechaSubida();

    var formData = new FormData(document.getElementById('formulario-subida'));
    
    fetch('./config/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la tabla con los datos del archivo subido
            document.getElementById('nombre-archivo').innerText = data.nombre_archivo;
            document.getElementById('fecha-subida').innerText = data.fecha_subida;
        } else {
            console.error('Error en la subida:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));
});

function obtenerFechaHoraActual() {
    var fecha = new Date();
    var dia = fecha.getDate().toString().padStart(2, '0');
    var mes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Los meses son 0-indexados
    var año = fecha.getFullYear();
    var horas = fecha.getHours().toString().padStart(2, '0');
    var minutos = fecha.getMinutes().toString().padStart(2, '0');
    return `${dia}/${mes}/${año} ${horas}:${minutos}`;
}

function actualizarNombreArchivo(input, id) {
    if (input.files.length > 0) {
        var nombreArchivo = input.files[0].name;
        document.getElementById(`nombre-archivo-${id}`).innerText = nombreArchivo;
        document.getElementById(`Nombre_Archivo_Dep-${id}`).value = nombreArchivo;
    } else {
        document.getElementById(`nombre-archivo-${id}`).innerText = 'No se ha subido un archivo';
    }
}

function actualizarFechaSubida(id) {
    var fechaFormateada = obtenerFechaHoraActual();
    document.getElementById(`fecha-subida-${id}`).innerText = fechaFormateada;
    document.getElementById(`Fecha_Subida_Dep-${id}`).value = fechaFormateada;
}

// Enviar el formulario automáticamente después de actualizar los campos ocultos
document.querySelectorAll('.hidden-input').forEach(function(input) {
    input.addEventListener('change', function() {
        var id = this.id.split('-')[2]; // Obtiene el ID del input-file
        actualizarNombreArchivo(this, id);
        actualizarFechaSubida(id);

        var formData = new FormData(document.getElementById(`formulario-subida-${id}`));
        
        fetch('./config/upload_sa.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Cambiar a response.text()
        .then(data => {
            if (data.includes('success')) {
                // Actualizar la tabla directamente en la base de datos
                location.reload(); // Recargar la página para mostrar los datos actualizados
            } else {
                console.error('Error en la subida:', data);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
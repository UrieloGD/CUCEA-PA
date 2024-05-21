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
    } else {
        // Si no se selecciona ningún archivo, mantener el nombre original
        document.getElementById('nombre-archivo').innerText = 'No se ha subido un archivo';
    }
}

function actualizarFechaSubida() {
    var fechaFormateada = obtenerFechaHoraActual();
    document.getElementById('fecha-subida').innerText = fechaFormateada;
}
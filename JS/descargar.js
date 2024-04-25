function descargarArchivo() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'config/download.php', true);
    xhr.responseType = 'blob';

    xhr.onload = function() {
        if (xhr.status === 200) {
            var blob = xhr.response;
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'Prueba.xlsx';
            document.body.appendChild(a);
            a.click();
            a.remove();
        } else {
            alert('No se encontró el archivo "Prueba.xlsx" en la base de datos.');
            console.error('Error al descargar el archivo');
        }
    };

    xhr.onerror = function() {
        alert('No se encontró el archivo "Prueba.xlsx" en la base de datos.');
        console.error('Error al descargar el archivo');
    };

    xhr.send();
}
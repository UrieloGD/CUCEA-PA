function eliminarRegistrosSeleccionados() {
    var checkboxes = document.querySelectorAll('input[name="registros_seleccionados[]"]:checked');
    var ids = [];

    checkboxes.forEach(function(checkbox) {
        ids.push(checkbox.value);
    });

    if (ids.length === 0) {
        Swal.fire({
            title: "Error",
            text: "No se han seleccionado registros para borrar.",
            icon: "error"
        });
        return;
    }

    Swal.fire({
        title: "¿Desea continuar?",
        text: "Se eliminarán " + ids.length + " registro(s)",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', './config/eliminar_registros.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "Los registros se han eliminado correctamente.",
                        icon: "success"
                    }).then(() => {
                        location.reload();
                    });
                }
            };

            // Obtener el departamento_id desde el HTML
            var departamento_id = document.getElementById('departamento_id').value;
            var datos = { 'departamento_id': departamento_id };
            xhr.send('ids=' + encodeURIComponent(ids.join(',')) + '&' + convertirObjeto(datos));
        }
    });
}

// Función auxiliar para convertir un objeto en una cadena de consulta
function convertirObjeto(obj) {
    var str = [];
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    }
    return str.join("&");
}
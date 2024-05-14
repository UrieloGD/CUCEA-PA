function eliminarRegistrosSeleccionados() {
    var checkboxes = document.querySelectorAll('input[name="registros_seleccionados[]"]:checked');
    var ids = [];

    checkboxes.forEach(function(checkbox) {
        ids.push(checkbox.value);
    });

    if (ids.length === 0) {
        alert("No se han seleccionado registros");
        return;
    }

    var confirmacion = confirm("¿Estás seguro de que deseas eliminar " + ids.length + " registro(s)?");

    if (confirmacion) {
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
        xhr.send('ids=' + encodeURIComponent(ids.join(',')));
    }

}
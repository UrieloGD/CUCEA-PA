function mostrarFormularioAñadir() {
    document.getElementById('formulario-añadir').style.display = 'flex';
}

function cerrarFormularioAñadir() {
    document.getElementById('formulario-añadir').style.display = 'none';
}

function añadirRegistro() {
    var form = document.getElementById('form-añadir-registro');
    var datos = new FormData(form);
    
    // Obtener el departamento_id desde el HTML
    var departamento_id = document.getElementById('departamento_id').value;
    datos.append('departamento_id', departamento_id);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', './config/añadir_registro.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    var respuesta = JSON.parse(xhr.responseText);
                    if (respuesta.success) {
                        Swal.fire({
                            title: "¡Éxito!",
                            text: respuesta.message,
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: respuesta.message,
                            icon: "error"
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        title: "Error",
                        text: "Error al procesar la respuesta del servidor",
                        icon: "error"
                    });
                }
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Error de conexión con el servidor",
                    icon: "error"
                });
            }
        }
    };
    xhr.send(datos);
}
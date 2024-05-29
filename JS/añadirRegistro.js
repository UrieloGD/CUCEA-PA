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
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                var respuesta = xhr.responseText;
                if (respuesta === "Registro añadido correctamente") {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "El registro se ha añadido correctamente.",
                        icon: "success"
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: respuesta,
                        icon: "error"
                    });
                }
            }
        };
        xhr.send(datos);
    }

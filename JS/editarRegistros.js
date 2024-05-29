var editandoRegistro = false; // Variable para rastrear si ya se está editando un registro

function editarRegistrosSeleccionados() {
    if (editandoRegistro) {
        Swal.fire({
            title: "Error",
            text: "Ya se está editando un registro.",
            icon: "error"
        })
        return;
    }

    var checkboxes = document.querySelectorAll('input[name="registros_seleccionados[]"]:checked');

    if (checkboxes.length === 0) {
        Swal.fire({
            title: "Error",
            text: "No se han seleccionado registros para editar.",
            icon: "error"
        })
        return;
    } else if (checkboxes.length > 1) {
        Swal.fire({
            title: "Error",
            text: "No puede editar más de dos registros simultáneamente.",
            icon: "error"
        })
        return;
    }

    editandoRegistro = true; // Indicar que se está editando un registro

    var checkbox = checkboxes[0];
    var id = checkbox.value;
    var fila = checkbox.parentNode.parentNode;
    var celdas = fila.getElementsByTagName("td");

    // Guardar los valores originales en un array
    var valoresOriginales = [];
    for (var i = 2; i < celdas.length; i++) { // Comenzar desde el índice 2 para omitir el checkbox y el ID
        valoresOriginales.push(celdas[i].textContent);
    }

        // Crear inputs para editar los campos
        for (var i = 2; i < celdas.length; i++) { // Comenzar desde el índice 2 para omitir el checkbox y el ID
            var celda = celdas[i];
            var valorActual = celda.textContent;
            var input = document.createElement("input");
            input.type = "text";
            input.style.width = "80px"; // Ajustar el ancho del input
            input.value = valorActual;
            celda.textContent = "";
            celda.appendChild(input);
        }

    // Crear un contenedor para los botones
    var contenedorBotones = document.createElement("div");

    // Agregar botón para guardar los cambios
    var botonGuardar = document.createElement("button");
    botonGuardar.textContent = "Guardar";
    botonGuardar.onclick = function() {
        var datos = {
            id: id
        };

        for (var i = 2; i < celdas.length; i++) {
            var input = celdas[i].firstChild;
            var campo = obtenerNombreCampo(i);
            datos[campo] = input.value;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', './config/editar_registro.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                var respuesta = xhr.responseText;
                if (respuesta === "Registro actualizado correctamente") {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "El registro se ha actualizado correctamente.",
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
                editandoRegistro = false; // Permitir la edición de otro registro
            }
        };

        var departamento_id = document.getElementById('departamento_id').value;
        datos['departamento_id'] = departamento_id;
        xhr.send(convertirObjeto(datos));

        xhr.send(convertirObjeto(datos));
    };
    
    contenedorBotones.appendChild(botonGuardar);

    // Agregar botón para cancelar la edición
    var botonCancelar = document.createElement("button");
    botonCancelar.textContent = "Cancelar";
    botonCancelar.onclick = function() {
        for (var i = 2; i < celdas.length; i++) {
            celdas[i].textContent = valoresOriginales[i - 2];
        }
        contenedorBotones.remove();
        editandoRegistro = false; // Permitir la edición de otro registro
    };
    contenedorBotones.appendChild(botonCancelar);

    fila.appendChild(contenedorBotones);
}

function obtenerNombreCampo(indice) {
    var campos = ["CICLO", "NRC", "FECHA_INI", "FECHA_FIN", "L", "M", "I", "J", "V", "S", "D", "HORA_INI", "HORA_FIN", "EDIF", "AULA"];
    return campos[indice - 2];
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
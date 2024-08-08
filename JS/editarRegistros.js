var editandoRegistro = false;

function editarRegistrosSeleccionados() {
    var checkboxes = document.querySelectorAll('input[name="registros_seleccionados[]"]:checked');

    if (checkboxes.length === 0) {
        Swal.fire({
            title: "Error",
            text: "No se han seleccionado registros para editar.",
            icon: "error"
        });
        return;
    } else if (checkboxes.length > 1) {
        Swal.fire({
            title: "Error",
            text: "No puede editar más de un registro simultáneamente.",
            icon: "error"
        });
        return;
    }

    var checkbox = checkboxes[0];
    var id = checkbox.value;
    var fila = checkbox.parentNode.parentNode;
    var celdas = fila.getElementsByTagName("td");

    // Llenar el formulario con los datos del registro seleccionado
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-ciclo').value = celdas[2].textContent;
    document.getElementById('edit-crn').value = celdas[3].textContent;
    document.getElementById('edit-materia').value = celdas[4].textContent;
    document.getElementById('edit-cve_materia').value = celdas[5].textContent;
    document.getElementById('edit-seccion').value = celdas[6].textContent;
    document.getElementById('edit-nivel').value = celdas[7].textContent;
    document.getElementById('edit-nivel_tipo').value = celdas[8].textContent;
    document.getElementById('edit-tipo').value = celdas[9].textContent;
    document.getElementById('edit-c_min').value = celdas[10].textContent;
    document.getElementById('edit-h_totales').value = celdas[11].textContent;
    document.getElementById('edit-estatus').value = celdas[12].textContent;
    document.getElementById('edit-tipo_contrato').value = celdas[13].textContent;
    document.getElementById('edit-codigo_profesor').value = celdas[14].textContent;
    document.getElementById('edit-nombre_profesor').value = celdas[15].textContent;
    document.getElementById('edit-categoria').value = celdas[16].textContent;
    document.getElementById('edit-descarga').value = celdas[17].textContent;
    document.getElementById('edit-codigo_descarga').value = celdas[18].textContent;
    document.getElementById('edit-nombre_descarga').value = celdas[19].textContent;
    document.getElementById('edit-nombre_definitivo').value = celdas[20].textContent;
    document.getElementById('edit-titular').value = celdas[21].textContent;
    document.getElementById('edit-horas').value = celdas[22].textContent;
    document.getElementById('edit-codigo_dependencia').value = celdas[23].textContent;
    document.getElementById('edit-l').value = celdas[24].textContent;
    document.getElementById('edit-m').value = celdas[25].textContent;
    document.getElementById('edit-i').value = celdas[26].textContent;
    document.getElementById('edit-j').value = celdas[27].textContent;
    document.getElementById('edit-v').value = celdas[28].textContent;
    document.getElementById('edit-s').value = celdas[29].textContent;
    document.getElementById('edit-d').value = celdas[30].textContent;
    document.getElementById('edit-dia_presencial').value = celdas[31].textContent;
    document.getElementById('edit-dia_virtual').value = celdas[32].textContent;
    document.getElementById('edit-modalidad').value = celdas[33].textContent;
    document.getElementById('edit-fecha_inicial').value = celdas[34].textContent;
    document.getElementById('edit-fecha_final').value = celdas[35].textContent;
    document.getElementById('edit-hora_inicial').value = celdas[36].textContent;
    document.getElementById('edit-hora_final').value = celdas[37].textContent;
    document.getElementById('edit-modulo').value = celdas[38].textContent;
    document.getElementById('edit-aula').value = celdas[39].textContent;
    document.getElementById('edit-cupo').value = celdas[40].textContent;
    document.getElementById('edit-observaciones').value = celdas[41].textContent;
    document.getElementById('edit-examen_extraordinario').value = celdas[42].textContent;

    // Mostrar el modal
    document.getElementById('modal-editar').style.display = 'block';
}

function cerrarFormularioEditar() {
    document.getElementById('modal-editar').style.display = 'none';
}

function guardarCambios() {
    var datos = {
        id: document.getElementById('edit-id').value,
        departamento_id: document.getElementById('departamento_id').value,
        CICLO: document.getElementById('edit-ciclo').value,
        CRN: document.getElementById('edit-crn').value,
        MATERIA: document.getElementById('edit-materia').value,
        CVE_MATERIA: document.getElementById('edit-cve_materia').value,
        SECCION: document.getElementById('edit-seccion').value,
        NIVEL: document.getElementById('edit-nivel').value,
        NIVEL_TIPO: document.getElementById('edit-nivel_tipo').value,
        TIPO: document.getElementById('edit-tipo').value,
        C_MIN: document.getElementById('edit-c_min').value,
        H_TOTALES: document.getElementById('edit-h_totales').value,
        ESTATUS: document.getElementById('edit-estatus').value,
        TIPO_CONTRATO: document.getElementById('edit-tipo_contrato').value,
        CODIGO_PROFESOR: document.getElementById('edit-codigo_profesor').value,
        NOMBRE_PROFESOR: document.getElementById('edit-nombre_profesor').value,
        CATEGORIA: document.getElementById('edit-categoria').value,
        DESCARGA: document.getElementById('edit-descarga').value,
        CODIGO_DESCARGA: document.getElementById('edit-codigo_descarga').value,
        NOMBRE_DESCARGA: document.getElementById('edit-nombre_descarga').value,
        NOMBRE_DEFINITIVO: document.getElementById('edit-nombre_definitivo').value,
        TITULAR: document.getElementById('edit-titular').value,
        HORAS: document.getElementById('edit-horas').value,
        CODIGO_DEPENDENCIA: document.getElementById('edit-codigo_dependencia').value,
        L: document.getElementById('edit-l').value,
        M: document.getElementById('edit-m').value,
        I: document.getElementById('edit-i').value,
        J: document.getElementById('edit-j').value,
        V: document.getElementById('edit-v').value,
        S: document.getElementById('edit-s').value,
        D: document.getElementById('edit-d').value,
        DIA_PRESENCIAL: document.getElementById('edit-dia_presencial').value,
        DIA_VIRTUAL: document.getElementById('edit-dia_virtual').value,
        MODALIDAD: document.getElementById('edit-modalidad').value,
        FECHA_INICIAL: document.getElementById('edit-fecha_inicial').value,
        FECHA_FINAL: document.getElementById('edit-fecha_final').value,
        HORA_INICIAL: document.getElementById('edit-hora_inicial').value,
        HORA_FINAL: document.getElementById('edit-hora_final').value,
        MODULO: document.getElementById('edit-modulo').value,
        AULA: document.getElementById('edit-aula').value,
        CUPO: document.getElementById('edit-cupo').value,
        OBSERVACIONES: document.getElementById('edit-observaciones').value,
        EXAMEN_EXTRAORDINARIO: document.getElementById('edit-examen_extraordinario').value
    };

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
            cerrarFormularioEditar();
        }
    };

    xhr.send(convertirObjeto(datos));
}

function obtenerNombreCampo(indice) {
    var campos = ["CICLO", "CRN", "MATERIA", "CVE_MATERIA", "SECCION", "NIVEL", "NIVEL_TIPO",
            "TIPO", "C_MIN", "H_TOTALES", "ESTATUS", "TIPO_CONTRATO", "CODIGO_PROFESOR",
            "NOMBRE_PROFESOR", "CATEGORIA", "DESCARGA", "CODIGO_DESCARGA", "NOMBRE_DESCARGA",
            "NOMBRE_DEFINITIVO", "TITULAR", "HORAS", "CODIGO_DEPENDENCIA", "L", "M", "I",
            "J", "V", "S", "D", "DIA_PRESENCIAL", "DIA_VIRTUAL", "MODALIDAD",
            "FECHA_INICIAL", "FECHA_FINAL", "HORA_INICIAL", "HORA_FINAL", "MODULO",
            "AULA", "CUPO", "OBSERVACIONES", "EXAMEN_EXTRAORDINARIO"];
    return campos[indice - 2];
}

function convertirObjeto(obj) {
    var str = [];
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    }
    return str.join("&");
}
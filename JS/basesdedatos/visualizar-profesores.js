function mostrarModalTodosProfesores() {
    var modal = document.getElementById('modal-todos-profesores');
    modal.style.display = 'block';
}

function cerrarModalTodosProfesores() {
    var modal = document.getElementById('modal-todos-profesores');
    modal.style.display = 'none';
}

function filtrarTodosProfesores() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("buscar-todos-profesores");
    filter = input.value.toUpperCase();
    table = document.getElementById("lista-todos-profesores");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        // Buscar en la columna de código y nombre
        var tdCodigo = tr[i].getElementsByTagName("td")[0];
        var tdNombre = tr[i].getElementsByTagName("td")[1];
        
        if (tdCodigo && tdNombre) {
            var txtValueCodigo = tdCodigo.textContent || tdCodigo.innerText;
            var txtValueNombre = tdNombre.textContent || tdNombre.innerText;
            
            if (txtValueCodigo.toUpperCase().indexOf(filter) > -1 || 
                txtValueNombre.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function verDetalleProfesorTodos(codigoProfesor) {
    // Similar a la función verDetalleProfesor existente, 
    // pero adaptada para consultar en Coord_Per_Prof
    $.ajax({
        url: './ajax/obtener-detalle-profesor-todos.php', // Necesitarás crear este archivo
        method: 'POST',
        data: { codigo: codigoProfesor },
        success: function(response) {
            // Poblar el modal de detalle-profesor con la respuesta
            // Similar a la función existente
        }
    });
}
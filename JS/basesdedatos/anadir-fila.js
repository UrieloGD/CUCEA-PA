function añadirFila() {
    var table = $('#tabla-datos').DataTable();
    
    var rowNode = table.row.add([
        '<input type="checkbox" style="display:none;">',
        '', // ID vacío para autoincremento
        '<input type="text" class="form-control input-sm" placeholder="Código">',
        '<input type="text" class="form-control input-sm" placeholder="Paterno">', 
        '<input type="text" class="form-control input-sm" placeholder="Materno">',
        '<input type="text" class="form-control input-sm" placeholder="Nombres">',
        '<div class="btn-group">' + 
            '<button class="btn btn-success btn-sm btn-guardar"><i class="fas fa-save"></i></button>' +
            '<button class="btn btn-danger btn-sm btn-cancelar"><i class="fas fa-times"></i></button>' +
        '</div>',
        '<input type="text" class="form-control input-sm" placeholder="">',
        '<input type="text" class="form-control input-sm" placeholder="">',
        '<input type="text" class="form-control input-sm" placeholder="">',
        '<input type="text" class="form-control input-sm" placeholder="">',
        '<input type="text" class="form-control input-sm" placeholder="">',
    ]).draw(false).node();
  
    // Estilos de los inputs al agregar fila
    $(rowNode).find('input, select').css({
        'width': '100%',
        'padding': '3px',
        'box-sizing': 'border-box'
    });
  
    $(rowNode).find('.btn-guardar').on('click', function() {
        var datos = {};
        var hayErrores = false;
        
        // Recolectar datos de inputs específicos
        var codigo = $(rowNode).find('input[placeholder="Código"]').val();
        var paterno = $(rowNode).find('input[placeholder="Paterno"]').val();
        var materno = $(rowNode).find('input[placeholder="Materno"]').val();
        var nombres = $(rowNode).find('input[placeholder="Nombres"]').val();
        
        // Validar campos requeridos
        if (!codigo || !paterno || !materno || !nombres) {
            hayErrores = true;
            Swal.fire({
                icon: 'error',
                title: 'Campos requeridos',
                text: 'Por favor complete todos los campos marcados'
            });
            return;
        }
    
        // Preparar datos para enviar
        datos = {
            'Codigo': codigo,
            'Paterno': paterno,
            'Materno': materno,
            'Nombres': nombres
        };
    
        console.log(datos);
    
        $.ajax({
            url: '/CUCEA-PA/functions/coord-personal-plantilla/anadir-profesor.php',
            method: 'POST',
            data: datos,
            dataType: 'json',
            beforeSend: function() {
                // Mostrar loading
                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if(response.success) {
                    // Convertir inputs a texto
                    $(rowNode).find('input, select').each(function() {
                        var valor = $(this).val();
                        $(this).parent().html(valor);
                    });
                    
                    // Reemplazar botones
                    $(rowNode).find('.btn-group').html(
                        '<button class="btn btn-primary btn-sm btn-editar"><i class="fas fa-edit"></i></button>' +
                        '<button class="btn btn-danger btn-sm btn-eliminar"><i class="fas fa-trash"></i></button>'
                    );
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro guardado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al guardar el registro'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error Details:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: 'Verifique la consola para más detalles'
                });
            }
        });
    });
  
    // Manejar la cancelación
    $(rowNode).find('.btn-cancelar').on('click', function() {
        table.row($(rowNode)).remove().draw();
    });
}

// Agregar el evento al botón de añadir
$('#icono-añadir-fila').on('click', function() {
    añadirFila();
});

// Asignar evento de clic al botón de filtro
$("#icono-filtro").on("click", toggleFilterIcons);
$('#filtrar').click(function() {
    var modulo = $('#modulo').val();
    var dia = $('#dia').val();
    var hora_inicio = $('#horario_inicio').val();
    var hora_fin = $('#horario_fin').val();
    var tiempoReal = $('#tiempo-real').is(':checked');

    if (tiempoReal) {
        dia = getDiaActual();
        hora_inicio = getHoraActual();
        hora_fin = calcularHoraFin(hora_inicio);
    }

    $.ajax({
        url: './functions/espacios/obtener-espacios.php',
        method: 'GET',
        data: {
            modulo: modulo,
            dia: dia,
            hora_inicio: hora_inicio,
            hora_fin: hora_fin
        },
        success: function(response) {
            var espacios_ocupados = JSON.parse(response);
            
            // Resetear todos los espacios a no ocupados
            $('.sala').removeClass('aula-ocupada laboratorio-ocupado ocupado').removeAttr('data-info');
            
            // Marcar los espacios ocupados
            Object.keys(espacios_ocupados).forEach(function(espacio) {
                var salaElement = $('[data-espacio="' + espacio + '"]');
                var info = espacios_ocupados[espacio];
                
                if (salaElement.hasClass('aula')) {
                    salaElement.addClass('aula-ocupada');
                } else if (salaElement.hasClass('laboratorio')) {
                    salaElement.addClass('laboratorio-ocupado');
                } else {
                    salaElement.addClass('ocupado');
                }
                
                salaElement.attr('data-info', JSON.stringify(info));
            });
        }
    });
});
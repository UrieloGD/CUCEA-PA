function getDiaActual() {
    var dias = ['D', 'L', 'M', 'I', 'J', 'V', 'S'];
    return dias[new Date().getDay()];
}

// Función para obtener la hora actual en formato HH:00
function getHoraActual() {
    var hora = new Date().getHours();
    return (hora < 10 ? '0' : '') + hora + ':00';
}

// Función para calcular la hora fin basada en la hora inicio
function calcularHoraFin(horaInicio) {
    var hora = parseInt(horaInicio.split(':')[0]);
    var horaFin = (hora + 1) % 24;
    return (horaFin < 10 ? '0' : '') + horaFin + ':55';
}

// Actualizar opciones de hora fin basadas en la hora inicio seleccionada
$('#horario_inicio').change(function() {
    var horaInicio = $(this).val();
    var $horaFin = $('#horario_fin');
    $horaFin.empty();
    $horaFin.append('<option value="">Hora fin</option>');

    if (horaInicio) {
        var horaInicioNum = parseInt(horaInicio.split(':')[0]);
        for (var i = horaInicioNum + 1; i <= 21; i++) {
            var hour = (i < 10 ? '0' : '') + i + ':55';
            $horaFin.append('<option value="' + hour + '">' + hour + '</option>');
        }
    }
});

$('#tiempo-real').change(function() {
    if ($(this).is(':checked')) {
        var diaActual = getDiaActual();
        var horaActual = getHoraActual();
        var horaFin = calcularHoraFin(horaActual);

        $('#dia').val(diaActual).prop('disabled', true);
        $('#horario_inicio').val(horaActual).prop('disabled', true);
        $('#horario_fin').val(horaFin).prop('disabled', true);
        
        // Ejecutar el filtro inmediatamente
        $('#filtrar').click();
    } else {
        $('#dia, #horario_inicio, #horario_fin').prop('disabled', false);
    }
});
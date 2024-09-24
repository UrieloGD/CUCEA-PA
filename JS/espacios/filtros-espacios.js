$(document).ready(function() {
    $('#modulo').change(function() {
        var modulo = $(this).val();
        window.location.href = 'espacios.php?modulo=' + modulo;
    });

    function getDiaActual() {
        var dias = ['D', 'L', 'M', 'I', 'J', 'V', 'S'];
        return dias[new Date().getDay()];
    }

    function getHoraActual() {
        var hora = new Date().getHours();
        return (hora < 10 ? '0' : '') + hora + ':00';
    }

    function calcularHoraFin(horaInicio) {
        var hora = parseInt(horaInicio.split(':')[0]);
        var horaFin = (hora + 1) % 24;
        return (horaFin < 10 ? '0' : '') + horaFin + ':55';
    }

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
                
                $('.sala').removeClass('aula-ocupada laboratorio-ocupado ocupado').removeAttr('data-info');
                
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

    $(document).on('mouseenter', '.sala[data-info]', function(e) {
        var info = JSON.parse($(this).attr('data-info'));
        var infoHtml = '<div class="info-hover">' +
                    '<p><strong>CVE Materia:</strong> ' + info.cve_materia + '</p>' +
                    '<p><strong>Materia:</strong> ' + info.materia + '</p>' +
                    '<p><strong>Profesor:</strong> ' + info.profesor + '</p>' +
                    '</div>';
        var $infoElement = $(infoHtml).appendTo('body');
        
        var salaRect = this.getBoundingClientRect();
        var infoRect = $infoElement[0].getBoundingClientRect();
        
        var top = salaRect.top - infoRect.height - 10;
        var left = salaRect.left + (salaRect.width / 2) - (infoRect.width / 2);
        
        $infoElement.css({
            position: 'fixed',
            top: Math.max(0, top) + 'px',
            left: Math.max(0, left) + 'px'
        });
        
        $(this).data('infoElement', $infoElement);
    }).on('mouseleave', '.sala[data-info]', function() {
        var $infoElement = $(this).data('infoElement');
        if ($infoElement) {
            $infoElement.remove();
        }
    });

    // Evento de clic para abrir el modal
    $(document).on('click', '.sala.aula-ocupada, .sala.laboratorio-ocupado', function() {
        var espacio = $(this).data('espacio');
        var modulo = $('#modulo').val();
        
        $.ajax({
            url: './functions/espacios/obtener-horario-aula.php',
            method: 'GET',
            data: { espacio: espacio, modulo: modulo },
            dataType: 'json', // Especifica que esperamos JSON
            success: function(horarios) {
                console.log("Respuesta del servidor:", horarios);
                if (typeof horarios === 'object' && horarios !== null) {
                    mostrarModal(espacio, horarios);
                } else {
                    console.error("La respuesta no es un objeto válido:", horarios);
                    alert("Hubo un error al cargar los horarios. Por favor, intente de nuevo.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
                console.error("Respuesta del servidor:", xhr.responseText);
                alert("Hubo un error al cargar los horarios. Por favor, intente de nuevo.");
            }
        });
    });

    // Cerrar el modal
    $('.close').click(function() {
        $('#claseModal').hide();
    });

    // Cerrar el modal si se hace clic fuera de él
    $(window).click(function(event) {
        if (event.target == document.getElementById('claseModal')) {
            $('#claseModal').hide();
        }
    });
});
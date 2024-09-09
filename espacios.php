<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<?php
// Obtener el módulo seleccionado (por defecto CEDA)
$modulo_seleccionado = isset($_GET['modulo']) ? $_GET['modulo'] : 'CEDA';

// Consulta para obtener los espacios del módulo seleccionado
$query = "SELECT * FROM Espacios WHERE Modulo = '$modulo_seleccionado' ORDER BY Espacio";
$result = mysqli_query($conexion, $query);

// Organizar los espacios por piso
$espacios = [
    '03' => [],
    '02' => [],
    '01' => []
];

while ($row = mysqli_fetch_assoc($result)) {
    $piso = substr($row['Espacio'], 0, 2);
    if (isset($espacios[$piso])) {
        $espacios[$piso][] = $row;
    }
}
?>

<title>Espacios</title>
<link rel="stylesheet" href="./CSS/espacios.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Espacios</h3>
        </div>
    </div>

<!-- Cuadros de texto y desplegables -->
<div class="filtros">

    <div class="filtro">
        <label for="modulo">Módulo</label>
        <select id="modulo" name="modulo">
            <!-- <option value="">Seleccione un módulo</option> -->
            <?php
            $query = "SELECT DISTINCT modulo FROM Espacios ORDER BY modulo";
            $result = mysqli_query($conexion, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $selected = ($row['modulo'] == $modulo_seleccionado) ? 'selected' : '';
                echo "<option value='" . $row['modulo'] . "' $selected>" . $row['modulo'] . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="filtro">
        <label for="dia">Día</label>
        <select id="dia" name="dia">
            <option value="">Seleccione un día</option>
            <option value="L">Lunes</option>
            <option value="M">Martes</option>
            <option value="I">Miércoles</option>
            <option value="J">Jueves</option>
            <option value="V">Viernes</option>
            <option value="S">Sábado</option>
            <option value="D">Domingo</option>
        </select>
    </div>
    <div class="filtro">
        <label for="horario_inicio">Hora Inicio</label>
        <select id="horario_inicio" name="horario_inicio">
            <option value="">Hora inicio</option>
            <?php
            for ($i = 7; $i <= 20; $i++) {
                $hour = str_pad($i, 2, "0", STR_PAD_LEFT) . ":00";
                echo "<option value='$hour'>$hour</option>";
            }
            ?>
        </select>
    </div>
    <div class="filtro">
        <label for="horario_fin">Hora Fin</label>
        <select id="horario_fin" name="horario_fin">
            <option value="">Hora fin</option>
            <?php
            for ($i = 8; $i <= 21; $i++) {
                $hour = str_pad($i, 2, "0", STR_PAD_LEFT) . ":55";
                echo "<option value='$hour'>$hour</option>";
            }
            ?>
        </select>
    </div>
    <div class="filtro tiempo-real-container">
        <label for="tiempo-real">Tiempo Real</label>
        <div class="toggle-switch">
            <input type="checkbox" id="tiempo-real" name="tiempo-real">
            <label for="tiempo-real"></label>
        </div>
    </div>
    <div id="filtrar-container">
        <button id="filtrar">Filtrar</button>
    </div>
</div>

<!-- Aquí empieza el código del Edificio -->
<div class="contenedor-principal">
    <div class="techo"></div>
    <div class="contenido-edificio">
        <div class="columna-lateral izquierda">
            <div class="letra-piso">
                <span><?php echo substr($modulo_seleccionado, -1); ?></span>
            </div>
            <div class="escaleras-container">
                <div class="escalera-superior"></div>
                <div class="escalera-inferior"></div>
            </div>
        </div>

        <div class="cuadro-grande">
        <?php foreach ($espacios as $piso => $espacios_piso): ?>
            <div class="piso">
                <div class="numero-piso"></div>
                <div class="salas">
                <?php $espacios_piso = array_reverse($espacios_piso); // Invertir el orden de los espacios
                    foreach ($espacios_piso as $espacio): ?>
                        <div class="sala-container">
                            <span class="sala-texto"><?php echo $espacio['Espacio']; ?></span>
                            <div class="sala <?php echo strtolower(str_replace(' ', '-', $espacio['Etiqueta'])); ?> <?php echo (strpos(strtolower($espacio['Etiqueta']), 'aula') !== false) ? 'aula' : ((strpos(strtolower($espacio['Etiqueta']), 'laboratorio') !== false) ? 'laboratorio' : ''); ?>" data-espacio="<?php echo $espacio['Espacio']; ?>">
                                <img src="./Img/icons/iconos-espacios/icono-<?php echo strtolower(str_replace(' ', '-', $espacio['Etiqueta'])); ?>.png" alt="<?php echo $espacio['Etiqueta']; ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($piso == '02'): ?>
                    <div class="barandal"></div>
                <?php elseif ($piso == '01'): ?>
                    <div class="barandal"></div>
                <?php endif; ?>
                <div class="piso-gris"></div>
            </div>
        <?php endforeach; ?>
        </div>

        <div class="columna-lateral derecha">
            <div class="letra-piso">
                <span><?php echo substr($modulo_seleccionado, -1); ?></span>
            </div>
            <div class="escaleras-container">
                <div class="escalera-superior"></div>
                <div class="escalera-inferior"></div>
            </div>
        </div>
    </div>
</div>

<div class="leyenda">
    <div class="leyenda-item">
        <div class="cuadrito aula"></div>
        <span>Aula</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito aula-ocupada"></div>
        <span>Aula ocupada</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito laboratorio"></div>
        <span>Laboratorio</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito laboratorio-ocupado"></div>
        <span>Laboratorio ocupado</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito bodega"></div>
        <span>Bodega</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito administrativo"></div>
        <span>Administrativo</span>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#modulo').change(function() {
            var modulo = $(this).val();
            window.location.href = 'espacios.php?modulo=' + modulo;
    });

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

    // Agregar evento hover para mostrar información
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
});
</script>

<?php include './template/footer.php'; ?>

<?php include './template/footer.php' ?>
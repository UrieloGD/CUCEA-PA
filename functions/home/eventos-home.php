<!-- Función para mostrar eventos próximos en el home -->

<?php
function obtenerEventosProximos($conexion, $codigo_usuario, $limite = 4) {
    $consulta = "SELECT 
        ID_Evento as id,
        Nombre_Evento as titulo,
        DATE_FORMAT(Fecha_Inicio, '%d') as dia_evento,
        CONCAT(
            DATE_FORMAT(Fecha_Inicio, '%d/%m/%Y'),
            ' ',
            Hora_Inicio,
            ' h'
        ) as fecha_completa
    FROM eventos_admin
    WHERE Fecha_Inicio >= CURDATE() 
    AND Estado = 'activo'
    AND FIND_IN_SET(?, Participantes) > 0
    ORDER BY Fecha_Inicio, Hora_Inicio ASC
    LIMIT ?";
    
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param('si', $codigo_usuario, $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $eventos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $eventos[] = $fila;
    }
    return $eventos;
}

// Función para renderizar los eventos próximos en el home
function renderizarEventosProximos($eventos) {
    if (empty($eventos)) {
        return '<div class="evento-item">
            <div class="evento-detalle">
                <span>No hay eventos próximos</span>
            </div>
        </div>';
    }

    $html = '';
    $total_eventos = count($eventos);

    // Iterar sobre los eventos y construir el HTML
    foreach ($eventos as $index => $evento) {
        $html .= '
        <div class="evento-item">
            <div class="evento-icono">
                <div class="cuadro-numero">
                    <span id="cuadro-numero">' . $evento['dia_evento'] . '</span>
                </div>
            </div>
            <div class="evento-detalle">
                <span>' . htmlspecialchars($evento['titulo']) . '</span>
                <p>' . $evento['fecha_completa'] . '</p>
            </div>
        </div>';
        
        if ($index < $total_eventos - 1) {
            $html .= '<hr>';
        }
    }

    return $html;
}
?>
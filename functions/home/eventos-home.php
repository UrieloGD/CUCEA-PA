<?php
if (!isset($conexion)) {
    die("Error: No hay conexión a la base de datos");
}

function obtenerEventosProximos($conexion, $codigo_usuario, $limite = 4) {
    // Primero obtenemos los eventos
    $consulta = "SELECT 
        e.ID_Evento as id,
        e.Nombre_Evento as titulo,
        e.Descripcion_Evento as descripcion,
        DATE_FORMAT(e.Fecha_Inicio, '%d') as dia_evento,
        DATE_FORMAT(e.Fecha_Inicio, '%d/%m/%Y') as fecha_inicio,
        DATE_FORMAT(e.Fecha_Fin, '%d/%m/%Y') as fecha_fin,
        e.Hora_Inicio,
        e.Hora_Fin,
        e.Etiqueta,
        e.Participantes as codigos_participantes,
        CONCAT(
            DATE_FORMAT(e.Fecha_Inicio, '%d/%m/%Y'),
            ' ',
            e.Hora_Inicio,
            ' h'
        ) as fecha_completa
    FROM eventos_admin e
    WHERE e.Fecha_Inicio >= CURDATE() 
    AND e.Estado = 'activo'
    AND FIND_IN_SET(?, e.Participantes) > 0
    ORDER BY e.Fecha_Inicio, e.Hora_Inicio ASC
    LIMIT ?";
    
    $stmt = $conexion->prepare($consulta);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param('si', $codigo_usuario, $limite);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $resultado = $stmt->get_result();
    $eventos = [];
    
    while ($fila = $resultado->fetch_assoc()) {
        // Para cada evento, obtenemos los nombres de los participantes
        $codigos = explode(',', $fila['codigos_participantes']);
        $nombres_participantes = [];
        
        // Consulta para obtener los nombres de los participantes
        $consulta_nombres = "SELECT 
            CONCAT(Nombre, ' ', Apellido) as nombre_completo 
            FROM usuarios 
            WHERE Codigo = ?";
        $stmt_nombres = $conexion->prepare($consulta_nombres);
        
        if ($stmt_nombres) {
            foreach ($codigos as $codigo) {
                $stmt_nombres->bind_param('s', $codigo);
                $stmt_nombres->execute();
                $resultado_nombre = $stmt_nombres->get_result();
                if ($fila_nombre = $resultado_nombre->fetch_assoc()) {
                    $nombres_participantes[] = $fila_nombre['nombre_completo'];
                }
            }
            $stmt_nombres->close();
        }
        
        // Reemplazamos los códigos con los nombres
        $fila['Participantes'] = implode(', ', $nombres_participantes);
        $eventos[] = $fila;
    }
    
    $stmt->close();
    return $eventos;
}

function renderizarEventosProximos($eventos) {
    if (empty($eventos)) {
        return '<div class="evento-item">
            <div class="evento-vacio">
                <span>No hay eventos próximos</span>
            </div>
        </div>';
    }

    $html = '';
    $total_eventos = count($eventos);
    $clase_contenedor = 'eventos-' . $total_eventos;

    foreach ($eventos as $index => $evento) {
        $html .= '<div class="evento-item ' . $clase_contenedor . '">';
        
        // El cuadro del número se ajusta según la cantidad de eventos
        $html .= '<div class="evento-icono">
                <div class="cuadro-numero">
                    <span id="cuadro-numero">' . $evento['dia_evento'] . '</span>
                </div>';
        
        switch($total_eventos) {
            case 1:
                $html .= '<div class="evento-detalle">
                    <div class="evento-info">
                        <span class="evento-titulo">' . htmlspecialchars($evento['titulo']) . '</span>
                        <p><strong>Descripción:</strong> ' . substr(htmlspecialchars($evento['descripcion']), 0, 110) . '...</p>
                        <p><strong>Categoría:</strong> ' . htmlspecialchars($evento['Etiqueta']) . '</p>
                        <p><strong>Participantes:</strong> ' . htmlspecialchars($evento['Participantes']) . '</p>
                        <p><strong>Inicio:</strong> ' . $evento['fecha_inicio'] . '<strong><span style="padding: 0 5 0px;"> • </span></strong>' . $evento['Hora_Inicio'] . 'h</p>
                        <p><strong>Fin:&nbsp&nbsp&nbsp&nbsp&nbsp</strong> ' . $evento['fecha_fin'] . '<strong><span style="padding: 0 5 0px;"> • </span></strong>' . $evento['Hora_Fin'] . 'h</p>
                    </div>
                </div></div>';
                break;
                
            case 2:
                $html .= '<div class="evento-detalle">
                    <div class="evento-info">
                        <span class="evento-titulo">' . htmlspecialchars($evento['titulo']) . '</span>
                        <p class="evento-descripcion" style="margin: 0;"><strong>Descripción:</strong> ' . 
                            substr(htmlspecialchars($evento['descripcion']), 0, 50) . 
                            (strlen($evento['descripcion']) > 50 ? '...' : '') . 
                        '</p>
                        <p style="margin: 0;"><strong>Categoría:</strong> ' . htmlspecialchars($evento['Etiqueta']) . '</p>
                        <p style="margin: 0;"><strong>Inicio:</strong> ' . $evento['fecha_inicio'] . '<strong><span style="padding: 0 5 0px;"> • </span></strong>' . $evento['Hora_Inicio'] . 'h</p>
                    </div>
                </div></div>';
                break;
                
            case 3:
                $html .= '<div class="evento-detalle">
                    <div class="evento-info">
                        <span class="evento-titulo">' . htmlspecialchars($evento['titulo']) . '</span>
                        <p class="evento-descripcion">' . 
                            substr(htmlspecialchars($evento['descripcion']), 0, 75) . 
                            (strlen($evento['descripcion']) > 75 ? '...' : '') . 
                        '</p>
                        <p class="evento-fecha">' . $evento['fecha_inicio'] . '<strong><span style="padding: 0 7 0px;"> • </span></strong>' . $evento['Hora_Inicio'] . 'h</p>
                    </div>
                </div></div>';
                break;
                
            default:
            $html .= '<div class="evento-detalle">
                <span class="evento-titulo">' . htmlspecialchars($evento['titulo']) . '</span>
                <p class="evento-fecha">' . $evento['fecha_inicio'] . '<strong><span style="padding: 0 7 0px;"> • </span></strong>' . $evento['Hora_Inicio'] . 'h</p>
                </div></div>';
            break;
        }
        
        $html .= '</div>';
        
        if ($index < $total_eventos - 1) {
            $html .= '<hr class="' . $clase_contenedor . '">';
        }
    }

    return $html;
}
?>
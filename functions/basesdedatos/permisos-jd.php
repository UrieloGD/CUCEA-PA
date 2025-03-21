<?php
/**
 * Control de permisos para edición de bases de datos
 * Este archivo contiene las funciones relacionadas con la verificación
 * de permisos de edición basados en eventos de Programación Académica
 */

// ./functions/basesdedatos/permisos-jd.php

function tienePermisosDeEdicion($usuario_id, $conexion) {
    // Verificar si es admin (rol 0)
    if ($_SESSION['Rol_ID'] == 0) {
        return true;
    }

    // El resto de la lógica original para otros roles...
    if (empty($usuario_id)) {
        return false;
    }
    
    $fecha_actual = date('Y-m-d');
    // $hora_actual = date('H:i:s');

    $sql_usuario = "SELECT Codigo FROM usuarios WHERE Codigo = ? LIMIT 1";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    
    if (!$stmt_usuario) {
        return false;
    }
    
    $stmt_usuario->bind_param("i", $usuario_id);
    if (!$stmt_usuario->execute()) {
        return false;
    }
    
    $result_usuario = $stmt_usuario->get_result();
    if ($result_usuario->num_rows == 0) {
        return false;
    }
    
    $usuario = $result_usuario->fetch_assoc();
    $codigo_usuario = $usuario['Codigo'];
    
    $sql_evento = "SELECT e.Hora_Inicio, e.Hora_Fin 
                   FROM eventos_admin e 
                   WHERE e.Etiqueta = 'Programación Académica' 
                   AND e.Estado = 'activo'
                   AND e.Fecha_Inicio <= ? 
                   AND e.Fecha_Fin >= ?
                   AND (e.Participantes LIKE ? OR e.Participantes LIKE ? OR e.Participantes LIKE ?)
                   LIMIT 1";
    
    // Patrones para buscar el código de usuario en la lista de participantes
    $pattern_inicio = $codigo_usuario . ',%';
    $pattern_medio = '%,' . $codigo_usuario . ',%';
    $pattern_fin = '%,' . $codigo_usuario;
    
    $stmt_evento = $conexion->prepare($sql_evento);
    if (!$stmt_evento) {
        return false;
    }
    
    $stmt_evento->bind_param("sssss", $fecha_actual, $fecha_actual, $pattern_inicio, $pattern_medio, $pattern_fin);
    if (!$stmt_evento->execute()) {
        return false;
    }
    
    $resultado = $stmt_evento->get_result();
    
    return $resultado->num_rows > 0;
}
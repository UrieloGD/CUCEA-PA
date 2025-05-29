<?php
include_once './../../config/db.php'; // Ajusta la ruta según tu estructura
function filtros($conexion) {
    $departamentos = [];
    
    try {
        // Consulta para obtener departamentos únicos usando las tablas relacionadas
        $query = "
            SELECT DISTINCT d.Nombre_Departamento 
            FROM (
                SELECT DISTINCT Departamento_ID FROM solicitudes_baja 
                WHERE Departamento_ID IS NOT NULL
                UNION 
                SELECT DISTINCT Departamento_ID FROM solicitudes_propuesta 
                WHERE Departamento_ID IS NOT NULL
                UNION 
                SELECT DISTINCT Departamento_ID FROM solicitudes_baja_propuesta 
                WHERE Departamento_ID IS NOT NULL
            ) AS dept_ids
            INNER JOIN departamentos d ON dept_ids.Departamento_ID = d.Departamento_ID
            ORDER BY d.Nombre_Departamento ASC
        ";
        
        $stmt = $conexion->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $departamentos[] = $row['Nombre_Departamento'];
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Error al obtener departamentos: " . $e->getMessage());
    }
    
    return $departamentos;
}

// Función para obtener estados únicos de todas las solicitudes
function obtenerEstadosDisponibles($conexion) {
    $estados = [];
    
    try {
        $query = "
            SELECT DISTINCT ESTADO_B as estado FROM solicitudes_baja WHERE ESTADO_B IS NOT NULL AND ESTADO_B != ''
            UNION 
            SELECT DISTINCT ESTADO_P as estado FROM solicitudes_propuesta WHERE ESTADO_P IS NOT NULL AND ESTADO_P != ''
            UNION 
            SELECT DISTINCT ESTADO_BP as estado FROM solicitudes_baja_propuesta WHERE ESTADO_BP IS NOT NULL AND ESTADO_P != ''
            ORDER BY estado ASC
        ";
        
        $stmt = $conexion->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $estados[] = $row['estado'];
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Error al obtener estados: " . $e->getMessage());
    }
    
    return $estados;
}
?>
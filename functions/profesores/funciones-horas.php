<?php
//profesores/funciones-horas.php

function getSumaHorasPorProfesor($codigo, $conexion) {
    $departamentos = mysqli_query($conexion, "SELECT Nombre_Departamento, Departamentos FROM departamentos");
    $suma_cargo_plaza = 0;
    $suma_horas_definitivas = 0;
    $suma_horas_temporales = 0;
    $profesor_encontrado = false;

    while ($dept = mysqli_fetch_assoc($departamentos)) {
        $tabla = "data_" . $dept['Nombre_Departamento'];
        
        $query = "SELECT HORAS, TIPO_CONTRATO FROM $tabla WHERE CODIGO_PROFESOR = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()) {
            $profesor_encontrado = true;
            $horas = !empty($row['HORAS']) ? intval($row['HORAS']) : 2;
            
            $tipo_contrato = strtolower(trim($row['TIPO_CONTRATO']));
            
            // Separar la suma según el tipo de contrato
            switch($tipo_contrato) {
                case 'cargo a plaza':
                    $suma_cargo_plaza += $horas;
                    break;
                case 'horas definitivas':
                    $suma_horas_definitivas += $horas;
                    break;
                case 'asignatura':
                    $suma_horas_temporales += $horas;
                    break;
            }
        }
        
        $stmt->close();
    }

    // Obtener las horas totales del profesor de coord_per_prof
    $query_horas = "SELECT Horas_frente_grupo, Horas_definitivas FROM coord_per_prof WHERE Codigo = ?";
    $stmt_horas = $conexion->prepare($query_horas);
    $stmt_horas->bind_param("s", $codigo);
    $stmt_horas->execute();
    $result_horas = $stmt_horas->get_result();
    $horas_totales = $result_horas->fetch_assoc();
    
    $horas_frente_grupo = $horas_totales['Horas_frente_grupo'] ?? 0;
    $horas_definitivasDB = $horas_totales['Horas_definitivas'] ?? 0;

    if (!$profesor_encontrado) {
        return [0, 0, 0, $horas_frente_grupo, $horas_definitivasDB];
    }

    return [
        $suma_cargo_plaza,         // Ahora solo cargo a plaza para frente a grupo
        $suma_horas_definitivas,   // Solo horas definitivas
        $suma_horas_temporales,    // Solo asignatura
        $horas_frente_grupo,
        $horas_definitivasDB
    ];
}
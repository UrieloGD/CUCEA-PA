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
            $horas = intval($row['HORAS'] ?? 2);
            $tipo_contrato = strtolower(trim($row['TIPO_CONTRATO'] ?? ''));
            
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

function getSumaHorasSegura($codigo_profesor, $conexion) {
    if ($codigo_profesor === null) {
        return [0, 0, 0, 0, 0, 'Sin datos', 'Sin datos', 'Sin datos'];
    }
    
    $departamentos = mysqli_query($conexion, "SELECT Nombre_Departamento, Departamentos FROM departamentos");
    $suma_cargo_plaza = 0;
    $suma_horas_definitivas = 0;
    $suma_horas_temporales = 0;
    $horas_por_depto_cargo = [];
    $horas_por_depto_def = [];
    $horas_por_depto_temp = [];
    
    while ($dept = mysqli_fetch_assoc($departamentos)) {
        $tabla = "data_" . $dept['Nombre_Departamento'];
        $query = "SELECT HORAS, TIPO_CONTRATO FROM $tabla WHERE CODIGO_PROFESOR = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo_profesor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $suma_dept_cargo = 0;
        $suma_dept_def = 0;
        $suma_dept_temp = 0;
        
        while($row = $result->fetch_assoc()) {
            // Add null check before trim()
            $tipo_contrato = isset($row['TIPO_CONTRATO']) ? strtolower(trim($row['TIPO_CONTRATO'])) : '';
            $horas = !empty($row['HORAS']) ? intval($row['HORAS']) : 2;
            
            if ($tipo_contrato === 'cargo a plaza') {
                $suma_dept_cargo += $horas;
                $suma_cargo_plaza += $horas;
            } elseif ($tipo_contrato === 'horas definitivas') {
                $suma_dept_def += $horas;
                $suma_horas_definitivas += $horas;
            } elseif ($tipo_contrato === 'asignatura') {
                $suma_dept_temp += $horas;
                $suma_horas_temporales += $horas;
            }
        }
        
        // Guardar las horas por departamento si hay alguna
        if ($suma_dept_cargo > 0) {
            $horas_por_depto_cargo[] = $dept['Departamentos'] . ": " . $suma_dept_cargo;
        }
        if ($suma_dept_def > 0) {
            $horas_por_depto_def[] = $dept['Departamentos'] . ": " . $suma_dept_def;
        }
        if ($suma_dept_temp > 0) {
            $horas_por_depto_temp[] = $dept['Departamentos'] . ": " . $suma_dept_temp;
        }
        
        $stmt->close();
    }
    
    // Convertir arrays a strings
    $horas_cargo_str = !empty($horas_por_depto_cargo) ? implode("\n", $horas_por_depto_cargo) : 'Sin secciones registradas';
    $horas_def_str = !empty($horas_por_depto_def) ? implode("\n", $horas_por_depto_def) : 'Sin secciones registradas';
    $horas_temp_str = !empty($horas_por_depto_temp) ? implode("\n", $horas_por_depto_temp) : 'Sin secciones registradas';
    
    // Consultar horas frente a grupo y definitivas de la base de datos
    $query_horas = "SELECT Horas_frente_grupo, Horas_definitivas FROM coord_per_prof WHERE Codigo = ?";
    $stmt = $conexion->prepare($query_horas);
    $stmt->bind_param("s", $codigo_profesor);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $horas_frente_grupo = $row ? intval($row['Horas_frente_grupo']) : 0;
    $horas_definitivasDB = $row ? intval($row['Horas_definitivas']) : 0;
    
    return [
        $suma_cargo_plaza,
        $suma_horas_definitivas,
        $suma_horas_temporales,
        $horas_frente_grupo,
        $horas_definitivasDB,
        $horas_cargo_str,
        $horas_def_str,
        $horas_temp_str
    ];
}
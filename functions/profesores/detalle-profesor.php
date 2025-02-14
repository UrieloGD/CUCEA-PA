<?php
include './../../config/db.php';
include './funciones-horas.php';

function obtenerMateriasPorProfesor($conexion, $codigo_profesor) {
    // Obtener todos los departamentos
    $sql_departamentos = "SELECT Nombre_Departamento FROM departamentos";
    $result_departamentos = mysqli_query($conexion, $sql_departamentos);
    
    $materias_unificadas = [];
    
    while ($row_departamento = mysqli_fetch_assoc($result_departamentos)) {
        $tabla = "data_" . $row_departamento['Nombre_Departamento'];
        
        // Consulta base para obtener materias del departamento
        $sql = "SELECT * FROM $tabla WHERE CODIGO_PROFESOR = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $codigo_profesor);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($materia = mysqli_fetch_assoc($result)) {
            $crn = $materia['CRN'];
            
            // Determinar la modalidad y días
            $modalidad_info = determinarModalidad($materia);
            
            // Si la materia ya existe, actualizar la información
            if (isset($materias_unificadas[$crn])) {
                $materias_unificadas[$crn] = unificarInformacionMaterias(
                    $materias_unificadas[$crn],
                    array_merge($materia, $modalidad_info)
                );
            } else {
                $materias_unificadas[$crn] = array_merge($materia, $modalidad_info, [
                    'departamento_origen' => $tabla
                ]);
            }
        }
    }
    
    return array_values($materias_unificadas);
}

function determinarModalidad($materia) {
    $modalidad_info = [
        'dias_presenciales' => [],
        'dias_virtuales' => [],
        'modalidad_unificada' => 'PRESENCIAL'
    ];
    
    // Caso 1: Usando campos explícitos de modalidad
    if (!empty($materia['MODALIDAD'])) {
        switch ($materia['MODALIDAD']) {
            case 'VIRTUAL':
                $modalidad_info['modalidad_unificada'] = 'VIRTUAL';
                // Verificar si el campo existe y no es nulo antes de procesarlo
                $dia_virtual = isset($materia['DIA_VIRTUAL']) ? $materia['DIA_VIRTUAL'] : '';
                if (strtolower(trim($dia_virtual)) === 'ambos') {
                    $modalidad_info['dias_virtuales'] = extraerDias($materia);
                } else {
                    $modalidad_info['dias_virtuales'] = extraerDiasDeCampo($dia_virtual);
                }
                break;
                
            case 'PRESENCIAL ENRIQUECIDA':
            case 'PRESENCIAL ENRIQUECIDO':
                $modalidad_info['modalidad_unificada'] = 'MIXTA';
                
                // Manejar días presenciales con verificación de nulos
                $dia_presencial = isset($materia['DIA_PRESENCIAL']) ? $materia['DIA_PRESENCIAL'] : '';
                if (strtolower(trim($dia_presencial)) === 'ambos') {
                    $modalidad_info['dias_presenciales'] = extraerDias($materia);
                } else {
                    $modalidad_info['dias_presenciales'] = extraerDiasDeCampo($dia_presencial);
                }
                
                // Manejar días virtuales con verificación de nulos
                $dia_virtual = isset($materia['DIA_VIRTUAL']) ? $materia['DIA_VIRTUAL'] : '';
                if (strtolower(trim($dia_virtual)) === 'ambos') {
                    $modalidad_info['dias_virtuales'] = extraerDias($materia);
                } else {
                    $modalidad_info['dias_virtuales'] = extraerDiasDeCampo($dia_virtual);
                }
                break;
                
            default:
                $modalidad_info['dias_presenciales'] = extraerDias($materia);
        }
    }
    // Caso 2: Usando campo módulo
    else {
        if (isset($materia['MODULO']) && $materia['MODULO'] === 'CVIRTU') {
            $modalidad_info['modalidad_unificada'] = 'VIRTUAL';
            $modalidad_info['dias_virtuales'] = extraerDias($materia);
        } else {
            $modalidad_info['dias_presenciales'] = extraerDias($materia);
        }
    }
    
    return $modalidad_info;
}

function extraerDias($materia) {
    $dias = [];
    $columnas_dias = ['L', 'M', 'I', 'J', 'V', 'S'];
    
    foreach ($columnas_dias as $dia) {
        if ($materia[$dia] === $dia) {
            $dias[] = $dia;
        }
    }
    
    return $dias;
}

function extraerDiasDeCampo($dias_texto) {
    if (empty($dias_texto)) return [];
    
    $dias_map = [
        'lunes' => 'L', 'martes' => 'M', 'miercoles' => 'I',
        'jueves' => 'J', 'viernes' => 'V', 'sabado' => 'S'
    ];
    
    $dias = [];
    $dias_array = array_map('trim', explode(',', strtolower($dias_texto)));
    
    foreach ($dias_array as $dia) {
        if (isset($dias_map[$dia])) {
            $dias[] = $dias_map[$dia];
        }
    }
    
    return $dias;
}

function unificarInformacionMaterias($materia_existente, $materia_nueva) {
    // Combinar días presenciales y virtuales
    $materia_existente['dias_presenciales'] = array_unique(array_merge(
        $materia_existente['dias_presenciales'] ?? [],
        $materia_nueva['dias_presenciales'] ?? []
    ));
    
    $materia_existente['dias_virtuales'] = array_unique(array_merge(
        $materia_existente['dias_virtuales'] ?? [],
        $materia_nueva['dias_virtuales'] ?? []
    ));
    
    // Mantener la modalidad original si todos los días son del mismo tipo
    if (!empty($materia_existente['dias_presenciales']) && empty($materia_existente['dias_virtuales'])) {
        $materia_existente['modalidad_unificada'] = 'PRESENCIAL';
    } elseif (empty($materia_existente['dias_presenciales']) && !empty($materia_existente['dias_virtuales'])) {
        $materia_existente['modalidad_unificada'] = 'VIRTUAL';
    } elseif (!empty($materia_existente['dias_presenciales']) && !empty($materia_existente['dias_virtuales'])) {
        $materia_existente['modalidad_unificada'] = 'MIXTA';
    }
    
    // Si no hay días especificados, mantener la modalidad original de la materia nueva
    if (empty($materia_existente['dias_presenciales']) && empty($materia_existente['dias_virtuales'])) {
        $materia_existente['modalidad_unificada'] = $materia_nueva['modalidad_unificada'] ?? 'PRESENCIAL';
    }
    
    return $materia_existente;
}


function renderizarTablaMaterias($materias) {
    ?>
    <table class="table-profesor">
        <thead>
            <tr>
                <th class="col-nrc th-L">CRN</th>
                <th class="col-materia">Materia</th>
                <th class="col-departamento">Departamento</th>
                <th class="col-hora">Hora</th>
                <th class="col-dias">Día(s)</th>
                <th class="col-aula">Aula</th>
                <th class="col-modalidad th-R">Edificio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materias as $materia): 
                $hora_inicio = substr($materia['HORA_INICIAL'] ?? '0000', 0, 2) . ':' . 
                             substr($materia['HORA_INICIAL'] ?? '0000', 2, 2);
                $hora_fin = substr($materia['HORA_FINAL'] ?? '0000', 0, 2) . ':' . 
                           substr($materia['HORA_FINAL'] ?? '0000', 2, 2);
            ?>
                <tr>
                    <td class="col-nrc"><?= htmlspecialchars($materia['CRN']) ?></td>
                    <td class="col-materia"><?= htmlspecialchars($materia['MATERIA']) ?></td>
                    <td class="col-departamento">
                        <?= htmlspecialchars(str_replace('data_', '', $materia['departamento_origen'])) ?>
                    </td>
                    <td class="col-hora"><?= $hora_inicio . ' - ' . $hora_fin ?></td>
                    <td class="col-dias">
                        <div class="weekdays">
                            <?php
                            $dias = ['L', 'M', 'I', 'J', 'V', 'S'];
                            foreach ($dias as $dia):
                                $clase = '';
                                if (in_array($dia, $materia['dias_presenciales'])) {
                                    $clase = 'active';
                                } elseif (in_array($dia, $materia['dias_virtuales'])) {
                                    $clase = 'active virtual';
                                }
                            ?>
                                <div class="day <?= $clase ?>"><?= $dia ?></div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="col-aula"><?= htmlspecialchars($materia['AULA'] ?? 'No hay datos') ?></td>
                    <td class="col-modalidad">
                        <?= htmlspecialchars($materia['MODULO'] . ' (' . $materia['modalidad_unificada'] . ')') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

// Código principal
if (isset($_POST['codigo_profesor'])) {
    $codigo_profesor = (int)$_POST['codigo_profesor'];
    
    // Obtener materias del profesor
    $materias = obtenerMateriasPorProfesor($conexion, $codigo_profesor);
    
    // Obtener información del profesor
    $sql_profesor = "SELECT DISTINCT Codigo, Nombre_completo, Correo, Categoria_actual, Departamento 
                    FROM coord_per_prof WHERE Codigo = ?";
    $stmt_profesor = mysqli_prepare($conexion, $sql_profesor);
    mysqli_stmt_bind_param($stmt_profesor, "i", $codigo_profesor);
    mysqli_stmt_execute($stmt_profesor);
    $datos_profesor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_profesor));
    
    if ($datos_profesor && !empty($materias)) {
        // Renderizar la interfaz
        include 'vista-profesor-materias.php';
    } else {
        echo '<div class="alert alert-info">No se encontró información para este profesor.</div>';
    }
}
?>
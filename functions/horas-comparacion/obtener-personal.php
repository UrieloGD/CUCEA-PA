<?php
//obtener_personal.php
// Evitar que se muestren errores en la salida
error_reporting(0);
ini_set('display_errors', 0);

include './../../config/db.php';

// Verificar la conexión
if (!$conexion) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// Obtener el departamento
$departamento = isset($_POST['departamento']) ? $_POST['departamento'] : '';

function getDepartamentoQuery($departamento)
{
    $departamentosMap = [
        'Administración'  => ['Administracion'],
        'PALE'  => ['ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA', 'PALE', 'Programa de Aprendizaje de Lengua Extranjera'],
        'Auditoría'  => ['Auditoria', 'SECRETARIA ADMINISTRATIVA/AUDITORIA'],
        'Ciencias_Sociales'  => ['CERI/CIENCIAS SOCIALES', 'CIENCIAS SOCIALES'],
        'Politicas_Públicas'  => ['POLITICAS PUBLICAS'],
        'Contabilidad'  => ['CONTABILIDAD'],
        'Economía'  => ['ECONOMIA'],
        'Estudios_Regionales'  => ['ESTUDIOS REGIONALES'],
        'Finanzas'  => ['Finanzas'],
        'Impuestos'  => ['IMPUESTOS'],
        'Mercadotecnia y Negocios Internacionales'  => ['MERCADOTECNIA'],
        'Métodos_Cuantitativos'  => ['METODOS CUANTITATIVOS'],
        'Recursos_Humanos'  => ['RECURSOS_HUMANOS'],
        'Mercadotecnia'  => ['MERCADOTECNIA'],
        'Sistemas_de_Información'  => ['SISTEMAS DE INFORMACION'],
        'Turismo' => ['Turismo', 'Turismo R. y S.']
    ];

    if (isset($departamentosMap[$departamento])) {
        $conditions = array_map(function ($dep) {
            return "Departamento = ?";
        }, $departamentosMap[$departamento]);
        return [
            'query' => "WHERE " . implode(" OR ", $conditions),
            'valores' => $departamentosMap[$departamento]
        ];
    }

    return [
        'query' => "WHERE Departamento = ?",
        'valores' => [$departamento]
    ];
}

function getSumaHorasPorProfesor($codigo, $conexion) {
    // Modificamos la consulta inicial para obtener ambos nombres
    $departamentos = mysqli_query($conexion, "SELECT Nombre_Departamento, Departamentos FROM Departamentos");
    $suma_horas = 0;
    $suma_cargo_plaza = 0;
    $suma_horas_definitivas = 0;
    $horas_por_departamento_definitivas = array();
    $horas_por_departamento_cargo = array();
    $profesor_encontrado = false;

    // Crear un mapeo de nombres técnicos a nombres de visualización
    $dept_mapping = array();
    while ($dept = mysqli_fetch_assoc($departamentos)) {
        $dept_mapping[$dept['Nombre_Departamento']] = $dept['Departamentos'];
    }

    // Reiniciar el puntero del resultado
    mysqli_data_seek($departamentos, 0);

    while ($dept = mysqli_fetch_assoc($departamentos)) {
        $tabla = "Data_" . $dept['Nombre_Departamento'];
        
        $query = "SELECT HORAS, TIPO_CONTRATO FROM $tabla WHERE CODIGO_PROFESOR = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $suma_dept_definitivas = 0;
        $suma_dept_cargo = 0;
        
        while($row = $result->fetch_assoc()) {
            $profesor_encontrado = true;
            
            $horas = !empty($row['HORAS']) ? intval($row['HORAS']) : 2;
            $suma_horas += $horas;
            
            $tipo_contrato = strtolower(trim($row['TIPO_CONTRATO']));
            
            if (strtolower(trim($tipo_contrato)) === strtolower('cargo a plaza')) {
                $suma_cargo_plaza += $horas;
                $suma_dept_cargo += $horas;
            }
            
            if (strtolower(trim($tipo_contrato)) === strtolower('horas definitivas')) {
                $suma_horas_definitivas += $horas;
                $suma_dept_definitivas += $horas;
            }
        }
        
        // Usar el nombre de visualización del departamento
        $nombre_dept_mostrar = $dept_mapping[$dept['Nombre_Departamento']];
        
        // Agregar horas definitivas por departamento
        if ($suma_dept_definitivas > 0 && $tabla != "Data_" . $departamento) {
            if (isset($horas_por_departamento_definitivas[$nombre_dept_mostrar])) {
                $horas_por_departamento_definitivas[$nombre_dept_mostrar] += $suma_dept_definitivas;
            } else {
                $horas_por_departamento_definitivas[$nombre_dept_mostrar] = $suma_dept_definitivas;
            }
        }
        
        // Agregar horas cargo a plaza por departamento
        if ($suma_dept_cargo > 0 && $tabla != "Data_" . $departamento) {
            if (isset($horas_por_departamento_cargo[$nombre_dept_mostrar])) {
                $horas_por_departamento_cargo[$nombre_dept_mostrar] += $suma_dept_cargo;
            } else {
                $horas_por_departamento_cargo[$nombre_dept_mostrar] = $suma_dept_cargo;
            }
        }
        
        $stmt->close();
    }

    if (!$profesor_encontrado) {
        $suma_horas = 0;
        $suma_cargo_plaza = 0;
        $suma_horas_definitivas = 0;
        $horas_definitivas_str = ['N/A'];
        $horas_cargo_str = ['N/A'];
    } else {
        // Formatear strings de horas por departamento
        $horas_definitivas_str = array();
        foreach ($horas_por_departamento_definitivas as $dept => $horas) {
            $horas_definitivas_str[] = "$dept: $horas";
        }
        if (empty($horas_definitivas_str)) {
            $horas_definitivas_str = ['N/A'];
        }

        $horas_cargo_str = array();
        foreach ($horas_por_departamento_cargo as $dept => $horas) {
            $horas_cargo_str[] = "$dept: $horas";
        }
        if (empty($horas_cargo_str)) {
            $horas_cargo_str = ['N/A'];
        }
    }

    return [
        $suma_horas, 
        implode("<br>", $horas_definitivas_str),
        implode("<br>", $horas_cargo_str),
        $suma_cargo_plaza,
        $suma_horas_definitivas
    ];
}

try {
    // Modificar la consulta base
    if ($departamento === 'todos') {
        $query = "SELECT Codigo, Nombre_completo, Departamento, Categoria_actual, 
                             Tipo_plaza, Carga_horaria, Horas_frente_grupo, 
                             Horas_definitivas 
                      FROM Coord_Per_Prof 
                      ORDER BY Nombre_completo";
        $stmt = $conexion->prepare($query);
    } else {
        $queryInfo = getDepartamentoQuery($departamento);
        $query = "SELECT Codigo, Nombre_completo, Departamento, Categoria_actual, 
                             Tipo_plaza, Carga_horaria, Horas_frente_grupo, 
                             Horas_definitivas 
                      FROM Coord_Per_Prof " .
            $queryInfo['query'] . " 
                      ORDER BY Nombre_completo";
        $stmt = $conexion->prepare($query);

        // Bind dinámico de parámetros
        $types = str_repeat("s", count($queryInfo['valores']));
        $stmt->bind_param($types, ...$queryInfo['valores']);
    }

    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Obtener los resultados
    $personal = array();
    while ($row = $result->fetch_assoc()) {
        $row['Horas_frente_grupo'] = intval($row['Horas_frente_grupo']);
        
        list($suma_horas, $horas_definitivas_por_departamento, $horas_cargo_por_departamento, 
             $suma_cargo_plaza, $suma_horas_definitivas) = 
            getSumaHorasPorProfesor($row['Codigo'], $conexion);
            
        $row['suma_horas'] = $suma_horas;
        $row['suma_cargo_plaza'] = $suma_cargo_plaza;
        $row['suma_horas_definitivas'] = $suma_horas_definitivas;
        $row['horas_definitivas_por_departamento'] = $horas_definitivas_por_departamento;
        $row['horas_cargo_por_departamento'] = $horas_cargo_por_departamento;

        // Calcular la comparación
        if ($row['Categoria_actual'] == 'cargo a plaza') {
            $row['comparacion'] = $row['suma_horas'] - $row['Horas_frente_grupo'];
        } else {
            $row['comparacion'] = $row['suma_horas'] - $row['suma_horas_definitivas'];
        }

        $personal[] = $row;
    }

    // Enviar la respuesta
    header('Content-Type: application/json');
    echo json_encode($personal);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}

// Cerrar la conexión y el statement
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conexion)) {
    $conexion->close();
}

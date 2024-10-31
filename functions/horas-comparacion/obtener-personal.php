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
        'PALE'  => ['ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA'],
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
    $departamentos = mysqli_query($conexion, "SELECT Nombre_Departamento FROM Departamentos");
    $suma_horas = 0;
    $suma_cargo_plaza = 0;
    $suma_horas_definitivas = 0;
    $horas_por_departamento = array();
    $profesor_encontrado = false; // Nueva variable para verificar si el profesor existe en alguna Data_

    while ($dept = mysqli_fetch_assoc($departamentos)) {
        $tabla = "Data_" . $dept['Nombre_Departamento'];
        
        $query = "SELECT HORAS, TIPO_CONTRATO FROM $tabla WHERE CODIGO_PROFESOR = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $suma_dept = 0;
        
        while($row = $result->fetch_assoc()) {
            $profesor_encontrado = true; // Marcamos que encontramos al profesor
            
            // Solo asignamos horas si realmente hay un registro
            $horas = !empty($row['HORAS']) ? intval($row['HORAS']) : 2;
            $suma_horas += $horas;
            
            $tipo_contrato = strtolower(trim($row['TIPO_CONTRATO']));
            
            if (strtolower(trim($tipo_contrato)) === strtolower('cargo a plaza')) {
                $suma_cargo_plaza += $horas;
            }
            
            if (strtolower(trim($tipo_contrato)) === strtolower('horas definitivas')) {
                $suma_horas_definitivas += $horas;
            }
            
            $suma_dept += $horas;
        }
        
        if ($suma_dept > 0 && $tabla != "Data_" . $departamento) {
            if (isset($horas_por_departamento[$dept['Nombre_Departamento']])) {
                $horas_por_departamento[$dept['Nombre_Departamento']] += $suma_dept;
            } else {
                $horas_por_departamento[$dept['Nombre_Departamento']] = $suma_dept;
            }
        }
        
        $stmt->close();
    }

    // Si el profesor no fue encontrado en ninguna tabla Data_, establecemos todo en 0
    if (!$profesor_encontrado) {
        $suma_horas = 0;
        $suma_cargo_plaza = 0;
        $suma_horas_definitivas = 0;
    }

    $horas_otros_departamentos = array();
    foreach ($horas_por_departamento as $dept => $horas) {
        $horas_otros_departamentos[] = "$dept: $horas";
    }

    return [
        $suma_horas, 
        implode(", ", $horas_otros_departamentos),
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
        
        list($suma_horas, $horas_otros_departamentos, $suma_cargo_plaza, $suma_horas_definitivas) = 
            getSumaHorasPorProfesor($row['Codigo'], $conexion);
            
        // Solo asignamos suma_horas si realmente hay horas
        $row['suma_horas'] = $suma_horas;
        $row['suma_cargo_plaza'] = $suma_cargo_plaza;
        $row['suma_horas_definitivas'] = $suma_horas_definitivas;
        $row['horas_otros_departamentos'] = $horas_otros_departamentos;

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

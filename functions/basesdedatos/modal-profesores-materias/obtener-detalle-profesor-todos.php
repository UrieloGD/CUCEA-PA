<?php
include './../../config/db.php';

$codigo = $_POST['codigo'];
$departamento_nombre = $_POST['departamento']; // Asegúrate de pasar el departamento en la llamada AJAX

// Array de mapeo de departamentos (el mismo array que arriba)
$departamentos_mapping = [ 
    'Administración' => [
        'Administracion',
        'ADMINISTRACION',
        'Administración'
    ],
    'PALE' => [
        'ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA',
        'PALE',
        'Programa de Aprendizaje de Lengua Extranjera'
    ],
    'Auditoría' => [
        'Auditoria',
        'AUDITORIA',
        'Auditoría',
        'SECRETARIA ADMINISTRATIVA/AUDITORIA'
    ],
    'Ciencias_Sociales' => [
        'CERI/CIENCIAS SOCIALES',
        'CIENCIAS SOCIALES',
        'Ciencias Sociales'
    ],
        'Politicas_Públicas' => [
        'POLITICAS PUBLICAS',
        'Políticas Públicas',
        'Politicas Publicas'
    ],
    'Contabilidad' => [
        'CONTABILIDAD',
        'Contabilidad'
    ],
    'Economía' => [
        'ECONOMIA',
        'Economía',
        'Economia'
    ],
        'Estudios_Regionales' => [
        'ESTUDIOS REGIONALES',
        'Estudios Regionales'
    ],
        'Finanzas' => [
        'FINANZAS',
        'Finanzas'
    ],
    'Impuestos' => [
        'IMPUESTOS',
        'Impuestos'
    ],
    'Mercadotecnia' => [
        'MERCADOTECNIA',
        'Mercadotecnia',
        'MERCADOTECNIA Y NEGOCIOS INTERNACIONALES'
    ],
    
        'Métodos_Cuantitativos' => [
        'METODOS CUANTITATIVOS',
        'Métodos Cuantitativos',
        'Metodos Cuantitativos'
    ],

    'Recursos_Humanos' => [
        'RECURSOS HUMANOS',
        'Recursos Humanos',
        'RECURSOS_HUMANOS'
    ],

        'Sistemas_de_Información' => [
        'SISTEMAS DE INFORMACION',
        'Sistemas de Información',
        'Sistemas de Informacion'
    ],

    'Turismo' => [
        'TURISMO',
        'Turismo',
        'Turismo R. y S.'
    ]
];

// Encontrar todas las variantes del departamento
$departamento_variantes = [];
foreach ($departamentos_mapping as $key => $variants) {
    if ($key === $departamento_nombre) {
        $departamento_variantes = $variants;
        break;
    }
}

// Crear la condición WHERE para la consulta SQL
$where_conditions = [];
foreach ($departamento_variantes as $variante) {
    $where_conditions[] = "Departamento = '" . mysqli_real_escape_string($conexion, $variante) . "'";
}
$where_clause = count($where_conditions) > 0 ? implode(' OR ', $where_conditions) : "1=0";

$sql = "SELECT * FROM coord_per_prof WHERE Codigo = ? AND ($where_clause)";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $codigo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$profesor = mysqli_fetch_assoc($result);

// Devolver los datos como JSON
echo json_encode($profesor);

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>
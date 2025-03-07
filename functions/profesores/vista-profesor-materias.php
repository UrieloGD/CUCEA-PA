<?php
require_once './funciones-horas.php';

$codigo_profesor = (int)$_POST['codigo_profesor'];
    
// Añade una verificación para departamento_id, usando un valor predeterminado si no está establecido
$departamento_id = isset($_POST['departamento_id']) ? (int)$_POST['departamento_id'] : 0;

// Añade una verificación nula antes de acceder al resultado de la base de datos
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt_departamento = mysqli_prepare($conexion, $sql_departamento);
mysqli_stmt_bind_param($stmt_departamento, "i", $departamento_id);
mysqli_stmt_execute($stmt_departamento);
$result_departamento = mysqli_stmt_get_result($stmt_departamento);

// Solo continúa si se encuentra un departamento
$nombre_departamento = 'Sin Departamento';
$departamento_nombre = 'Sin Departamento';

if ($row_departamento = mysqli_fetch_assoc($result_departamento)) {
    $nombre_departamento = $row_departamento['Nombre_Departamento'] ?? 'Sin Departamento';
    $departamento_nombre = $row_departamento['Departamentos'] ?? 'Sin Departamento';
}

// Obtener información personal del profesor
$sql_profesor = "SELECT DISTINCT 
                    Codigo, 
                    Nombre_completo, 
                    Correo, 
                    Categoria_actual, 
                    Departamento
                FROM coord_per_prof 
                WHERE Codigo = ?";
                                    
$stmt_profesor = mysqli_prepare($conexion, $sql_profesor);
mysqli_stmt_bind_param($stmt_profesor, "i", $codigo_profesor);
mysqli_stmt_execute($stmt_profesor);
$result_profesor = mysqli_stmt_get_result($stmt_profesor);
$datos_profesor = mysqli_fetch_assoc($result_profesor);

list($suma_cargo_plaza, $suma_horas_definitivas, $suma_horas_temporales, 
    $horas_frente_grupo, $horas_definitivasDB, $horas_por_depto_cargo,
    $horas_por_depto_def, $horas_por_depto_temp) = 
    getSumaHorasSegura($codigo_profesor, $conexion);

// Obtener los valores
if ($datos_profesor) {
    list($suma_cargo_plaza, $suma_horas_definitivas, $suma_horas_temporales, 
        $horas_frente_grupo, $horas_definitivasDB) = 
        getSumaHorasPorProfesor($codigo_profesor, $conexion);
}

// Funciones auxiliares para determinar la clase CSS
function getHorasClass($actual, $esperado) {
    if ($esperado == 0 && $actual == 0) return 'horas-cero';
    if ($actual < $esperado) return 'horas-faltantes';
    if ($actual == $esperado) return 'horas-correctas';
    return 'horas-excedidas';
}

function formatearHorasDepartamento($horasString) {
    if (empty($horasString) || $horasString === 'Sin secciones registradas') {
        return 'Sin horas registradas';
    }
    return str_replace("\n", "<br>", htmlspecialchars($horasString));
}

// Mappeo de todas las posibles variaciones
$departmentMapping = [
    'Estudios Regionales' => [
        'ESTUDIOS REGIONALES',
        'Estudios Regionales',
        'Est. Regionales',
        'Estudios_Regionales'
    ],
    'Finanzas' => [
        'FINANZAS',
        'Finanzas',
        'FIN'
    ],
    'Ciencias Sociales' => [
        'CIENCIAS SOCIALES',
        'Ciencias Sociales',
        'CERI/CIENCIAS SOCIALES',
        'CIENCIAS SOCIALES/POLITICAS PUBLICAS ',
        'CIENCIAS SOCIALES/POLITICAS PUBLICAS',
        'Ciencias_Sociales'
    ],
    'PALE' => [
        'PALE',
        'PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA',
        'ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA'
    ],
    'Economía' => [
        'ECONOMIA',
        'Economía',
        'Economia'
    ],
    'Recursos Humanos' => [
        'RECURSOS HUMANOS',
        'Recursos Humanos',
        'RH',
        'RECURSOS_HUMANOS',
        'Recursos_Humanos'
    ],
    'Métodos Cuantitativos' => [
        'METODOS CUANTITATIVOS',
        'Métodos Cuantitativos',
        'Metodos Cuantitativos',
        'Métodos_Cuantitativos'
    ],
    'Políticas Públicas' => [
        'POLITICAS PUBLICAS',
        'Políticas Públicas',
        'Politicas Publicas',
        'CIENCIAS SOCIALES/POLITICAS PUBLICAS ',
        'CIENCIAS SOCIALES/POLITICAS PUBLICAS',
        'Políticas_Públicas'
    ],
    'Administración' => [
        'ADMINISTRACION',
        'Administración',
        'Administracion',
        'ADMINISTRACION ',
        'ADMINISTRACION  '
    ],
    'Auditoria' => [
        'AUDITORIA',
        'AUDITORIA ',
        'Auditoría',
        'Auditoria',
        'SECRETARIA ADMINISTRATIVA/AUDITORIA'
    ],
    'Mercadotecnia' => [
        'MERCADOTECNIA',
        'Mercadotecnia',
        'MERCADOTECNIA Y NEGOCIOS INTERNACIONALES'
    ],
    'Impuestos' => [
        'IMPUESTOS',
        'Impuestos',
        'IMP'
    ],
    'Sistemas de Información' => [
        'SISTEMAS DE INFORMACION',
        'Sistemas de Información',
        'Sistemas de Informacion',
        'Sistemas_de_Información'
    ],
    'Turismo' => [
        'TURISMO',
        'Turismo',
        'TURISMO R. Y S.'
    ],
    'Posgrados' => [
        'Posgrados'
    ],
    'Contabilidad' => [
        'CONTABILIDAD',
        'Contabilidad',
        'CONT'
    ],
    'Otros' => [
        'SECRETARIA ADMINISTRATIVA/AUDITORIA',
        'CERI/SECRETARIA ACADEMICA',
        'SECRETARIA ACADEMICA',
        'SECRETARIA ACADEMICA  '
    ]
];

// Función para normalizar el nombre del departamento
function normalizeDepartmentName($dbDepartment) {
    global $departmentMapping;
    
    foreach ($departmentMapping as $standardName => $variations) {
        if (in_array($dbDepartment, $variations)) {
            return $standardName;
        }
    }
    
    return $dbDepartment; // Return original if no mapping found
}

// Función para obtener la clase CSS según el departamento
function getDepartmentClass($department) {
    // Normalizar el nombre del departamento primero
    $department = normalizeDepartmentName($department);
    
    // Mapeo de departamentos a clases CSS
    $departmentClasses = [
        'Estudios Regionales' => 'dept-estudios-regionales',
        'Finanzas' => 'dept-finanzas',
        'Ciencias Sociales' => 'dept-ciencias-sociales',
        'PALE' => 'dept-pale',
        'Posgrados' => 'dept-posgrados',
        'Economía' => 'dept-economia',
        'Recursos Humanos' => 'dept-recursos-humanos',
        'Métodos Cuantitativos' => 'dept-metodos-cuantitativos',
        'Políticas Públicas' => 'dept-politicas-publicas',
        'Administración' => 'dept-administracion',
        'Auditoria' => 'dept-auditoria',
        'Mercadotecnia' => 'dept-mercadotecnia',
        'Impuestos' => 'dept-impuestos',
        'Sistemas de Información' => 'dept-sistemas',
        'Turismo' => 'dept-turismo',
        'Contabilidad' => 'dept-contabilidad',
        'Otros' => 'dept-otros'
    ];
    
    return isset($departmentClasses[$department]) ? $departmentClasses[$department] : 'dept-default';
}

// Función para obtener la suma total de horas de un string formateado
function obtenerSumaHorasDepartamento($horasString) {
    if (empty($horasString) || $horasString === 'Sin secciones registradas') {
        return 0;
    }
    
    $total = 0;
    $lineas = explode("\n", $horasString);
    
    foreach ($lineas as $linea) {
        if (preg_match('/(\d+(\.\d+)?)/', $linea, $matches)) {
            $total += floatval($matches[1]);
        }
    }
    
    return $total;
}

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

?>
<link rel="stylesheet" href="./CSS/profesores/detalle-profesor.css">

<div class="container-profesor">
    <!-- Sección del Encabezado con Información del Profesor -->
    <div class="header-profesor">
        <div class="profesor-avatar-modal">
            <span class="avatar-initials">
                <?php 
                    $nombres = explode(' ', $datos_profesor['Nombre_completo']);
                    echo substr($nombres[0], 0, 1) . (isset($nombres[1]) ? substr($nombres[1], 0, 1) : '');
                ?>
            </span>
        </div>
        <div class="profile-info">
            <div class="profile-name-mail">
                <h2><?= htmlspecialchars($datos_profesor['Nombre_completo']) ?></h2>
                <p><?= htmlspecialchars($datos_profesor['Correo']) ?></p>
            </div>
            <div class="profile-details">
            <table class="table-profile">
                    <tbody>
                        <tr>
                            <td>
                                <div>
                                    <span class="profile-span">Código:</span>
                                    <?= htmlspecialchars($datos_profesor['Codigo']) ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="profile-span">Categoría:</span>
                                    <?= htmlspecialchars($datos_profesor['Categoria_actual']) ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="profile-span">Departamento:</span>
                                    <span class="data-value3 <?= getDepartmentClass($datos_profesor['Departamento']) ?>">
                                        <?php $departamento_normalizado = normalizeDepartmentName($datos_profesor['Departamento']); ?>
                                        <?= htmlspecialchars(mb_strtoupper($departamento_normalizado, 'UTF-8')) ?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <span class="profile-span">Horas frente a grupo:</span>
                                    <div class='tooltip'>
                                        <?php
                                        $suma_total_cargo = obtenerSumaHorasDepartamento($horas_por_depto_cargo);
                                        $clase = getHorasClass($suma_total_cargo, $horas_frente_grupo);
                                        ?>
                                        <span class="<?= $clase ?>">
                                            <?= $suma_total_cargo ?>/<?= $horas_frente_grupo ?>
                                        </span>
                                        <span class='tooltiptext'><?= formatearHorasDepartamento($horas_por_depto_cargo) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="profile-span">Horas definitivas:</span>
                                    <div class='tooltip'>
                                        <?php
                                        $suma_total_def = obtenerSumaHorasDepartamento($horas_por_depto_def);
                                        $clase = getHorasClass($suma_total_def, $horas_definitivasDB);
                                        ?>
                                        <span class="<?= $clase ?>">
                                            <?= $suma_total_def ?>/<?= $horas_definitivasDB ?>
                                        </span>
                                        <span class='tooltiptext'><?= formatearHorasDepartamento($horas_por_depto_def) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="profile-span">Horas temporales:</span>
                                    <div class='tooltip'>
                                        <?php
                                        $suma_total_temp = obtenerSumaHorasDepartamento($horas_por_depto_temp);
                                        ?>
                                        <span class="horas-temporales <?= getDepartmentClass($datos_profesor['Departamento']) ?>">
                                            <?= $suma_total_temp ?>
                                        </span>
                                        <span class='tooltiptext'><?= formatearHorasDepartamento($horas_por_depto_temp) ?></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <span class="close" onclick="cerrarModalDetalle()">&times;</span>
    </div>

    <!-- Sección de Búsqueda -->
    <div class="search-section">
        <div class="search-box">
            <input type="text" placeholder="Buscar..." id="search-input">
        </div>
    </div>

    <!-- Navegación de Materias -->
    <div class="navigation">
        <button class="nav-arrow prev-arrow" disabled><</button>
        <div class="nav-items-container">
            <a href="#todas" class="nav-item active" data-section="todas">TODAS LAS MATERIAS</a>
            <?php
            $materias_agrupadas = [];
            foreach ($materias as $materia) {
                $nombre_materia = $materia['MATERIA'];
                if (!isset($materias_agrupadas[$nombre_materia])) {
                    $materias_agrupadas[$nombre_materia] = [];
                }
                $materias_agrupadas[$nombre_materia][] = $materia;
            }
            
            foreach ($materias_agrupadas as $nombre_materia => $grupo) {
                $grupo_id = 'grupo_' . md5($nombre_materia);
                ?>
                <a href="#<?= htmlspecialchars($grupo_id) ?>" 
                   class="nav-item" 
                   data-section="<?= htmlspecialchars($grupo_id) ?>">
                    <?= htmlspecialchars(mb_strtoupper($nombre_materia, 'UTF-8')) ?> <!-- MAYUS -->
                </a>
                <?php
            }
            ?>
        </div>
        <button class="nav-arrow next-arrow">></button>
    </div>

    <div class="navigation-space"></div>

    <!-- Contenedor de Secciones de Materias -->
    <div class="sections-container">
        <!-- Sección de Todas las Materias -->
        <div class="curso-seccion active" id="todas">
            <?php renderizarTablaMaterias($materias); ?>
        </div>

        <!-- Secciones Individuales por Materia -->
        <?php foreach ($materias_agrupadas as $nombre_materia => $grupo): ?>
            <div class="curso-seccion" id="grupo_<?= md5($nombre_materia) ?>">
                <?php renderizarTablaMaterias($grupo); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="./JS/profesores/materias.js"></script>
<script src="./JS/profesores/profesores-materias.js"></script>
<?php
// vista-profesor-materias.php
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

// Después de $datos_profesor = mysqli_fetch_assoc($result_profesor);
list($suma_horas, $suma_horas_definitivas, $suma_horas_temporales, 
$horas_frente_grupo, $horas_definitivasDB) = 
getSumaHorasPorProfesor($codigo_profesor, $conexion);

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

// Calcular horas temporales
$horas_temporales = $suma_horas - $suma_horas_definitivas;

?>
<link rel="stylesheet" href="./CSS/detalle-profesor.css">

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
            <h2><?= htmlspecialchars($datos_profesor['Nombre_completo']) ?></h2>
            <p><?= htmlspecialchars($datos_profesor['Correo']) ?></p>
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
                                    <span class="data-value3">
                                        <?= strtoupper(htmlspecialchars($datos_profesor['Departamento'])) ?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <span class="profile-span">Horas frente a grupo:</span>
                                    <?php
                                    $clase = getHorasClass($suma_cargo_plaza, $horas_frente_grupo);
                                    ?>
                                    <span class="<?= $clase ?>">
                                        <?= $suma_cargo_plaza ?>/<?= $horas_frente_grupo ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="profile-span">Horas definitivas:</span>
                                    <?php
                                    $clase = getHorasClass($suma_horas_definitivas, $horas_definitivasDB);
                                    ?>
                                    <span class="<?= $clase ?>">
                                        <?= $suma_horas_definitivas ?>/<?= $horas_definitivasDB ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="profile-span">Horas temporales:</span>
                                    <span class="horas-temporales">
                                        <?= $suma_horas_temporales ?>
                                    </span>
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
            <a href="#todas" class="nav-item active" data-section="todas">Todas las materias</a>
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
                    <?= htmlspecialchars($nombre_materia) ?>
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
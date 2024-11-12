<link rel="stylesheet" href="./CSS/basesdedatos.css">

<script>
function actualizarDiasActivos(dias, crnId, modulo) {
    const diasSemana = document.querySelectorAll(`#weekdays-${crnId}-${modulo} .day`);
    
    diasSemana.forEach(dia => {
        const letraDia = dia.textContent;
        if (dias.includes(letraDia)) {
            dia.classList.add('active');
        } else {
            dia.classList.remove('active');
        }
    });
}
</script>

<?php
include './config/db.php';

if(isset($_POST['codigo_profesor'])) {
    $codigo_profesor = (int)$_POST['codigo_profesor'];
    $departamento_id = (int)$_POST['departamento_id'];

    $sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM Departamentos WHERE Departamento_ID = $departamento_id";
    $result_departamento = mysqli_query($conexion, $sql_departamento);
    $row_departamento = mysqli_fetch_assoc($result_departamento);
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    $departamento_nombre = $row_departamento['Departamentos'];

   // Obtener información personal del profesor
   $sql_profesor = "SELECT DISTINCT 
                        Codigo, 
                        Nombre_completo, 
                        Correo, 
                        Categoria_actual, 
                        Departamento
                    FROM Coord_Per_Prof 
                    WHERE Codigo = ?";
                                       
    $stmt_profesor = mysqli_prepare($conexion, $sql_profesor);
    mysqli_stmt_bind_param($stmt_profesor, "i", $codigo_profesor);
    mysqli_stmt_execute($stmt_profesor);
    $result_profesor = mysqli_stmt_get_result($stmt_profesor);
    $datos_profesor = mysqli_fetch_assoc($result_profesor);

    // Función para obtener todas las tablas de departamentos
    function obtenerTablasDepartamentos($conexion) {
        $sql = "SELECT Nombre_Departamento FROM Departamentos";
        $result = mysqli_query($conexion, $sql);
        $tablas = [];
        while($row = mysqli_fetch_assoc($result)) {
            $tablas[] = "Data_" . $row['Nombre_Departamento'];
        }
        return $tablas;
    }

    // Función modificada para obtener materias de todas las tablas
    // Función modificada para eliminar los registros duplicados
    function obtenerTodasLasMaterias($codigo_profesor, $tablas) {
        global $conexion;
        $todas_las_materias = [];
        $materias_unicas = [];
        
        foreach($tablas as $tabla) {
            // Verificar si la tabla existe
            $check_table = mysqli_query($conexion, "SHOW TABLES LIKE '$tabla'");
            if(mysqli_num_rows($check_table) > 0) {
                $sql = "SELECT DISTINCT *, '$tabla' as departamento_origen 
                        FROM $tabla 
                        WHERE CODIGO_PROFESOR = ?";
                
                $stmt = mysqli_prepare($conexion, $sql);
                mysqli_stmt_bind_param($stmt, "i", $codigo_profesor);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                while($row = mysqli_fetch_assoc($result)) {
                    $unique_key = $row['CRN'] . '-' . $row['MATERIA'] . '-' . $row['AULA'] . '-' . $row['MODULO'];
                    if(!isset($materias_unicas[$unique_key])) {
                        $materias_unicas[$unique_key] = $row;
                        $todas_las_materias[] = $row;
                    }
                }
            }
        }
        
        return $todas_las_materias;
    }
    
    // Obtener todas las tablas de departamentos
    $tablas_departamentos = obtenerTablasDepartamentos($conexion);
    
    // Obtener todas las materias del profesor
    $materias = obtenerTodasLasMaterias($codigo_profesor, $tablas_departamentos);
    
    if(!empty($materias) && $datos_profesor) {
        $profesor = $materias[0];
        ?>
        <!-- Contenido del profesor -->
        <div class="profesor-container">
            <!-- Columna izquierda -->
            <div class="left-column">
                <div class="profesor-header">
                    <div class="profesor-avatar">
                        <span class="avatar-initials">
                        <?php 
                            $nombres = explode(' ', $datos_profesor['Nombre_completo']);
                            echo substr($nombres[0], 0, 1) . (isset($nombres[1]) ? substr($nombres[1], 0, 1) : '');
                        ?>
                        </span>
                    </div>
                    <div class="profesor-details">
                    <h2><?php echo htmlspecialchars($datos_profesor['Nombre_completo']); ?></h2>
                    <p><?php echo htmlspecialchars($datos_profesor['Correo']); ?></p>
                    </div>
                </div>
                        
                <table class="profesor-data">
                    <tr>
                        <th>Código</th>
                        <td><?php echo htmlspecialchars($datos_profesor['Codigo']); ?></td>
                    </tr>
                    <tr>
                        <th>Categoría</th>
                        <td><?php echo htmlspecialchars($datos_profesor['Categoria_actual']); ?></td>
                    </tr>
                    <tr>
                        <th>Horas asignadas</th>
                        <td><span class="data-value2">36/40</span></td>
                    </tr>
                    <tr>
                        <th>Departamento</th>
                        <td><span class="data-value3"><?php echo htmlspecialchars($datos_profesor['Departamento']); ?></span></td>
                    </tr>
                </table>
            </div>

            <!-- Columna derecha -->
            <div class="right-column">
                <?php 
                // Agrupar materias por departamento
                $materias_por_departamento = [];
                foreach($materias as $materia) {
                    $dept = $materia['departamento_origen'];
                    if(!isset($materias_por_departamento[$dept])) {
                        $materias_por_departamento[$dept] = [];
                    }
                    $materias_por_departamento[$dept][] = $materia;
                }

                // Modificar la sección donde se muestran las materias
                foreach($materias_por_departamento as $dept => $materias_dept): 
                    $dept_nombre = str_replace('Data_', '', $dept);
                    
                    // Agrupar materias por nombre
                    $materias_agrupadas = [];
                    foreach($materias_dept as $materia) {
                        $nombre_materia = $materia['MATERIA'];
                        if(!isset($materias_agrupadas[$nombre_materia])) {
                            $materias_agrupadas[$nombre_materia] = [];
                        }
                        $materias_agrupadas[$nombre_materia][] = $materia;
                    }
                ?>
                    <h3>Materias en <?php echo htmlspecialchars($dept_nombre); ?></h3>
                    <?php foreach($materias_agrupadas as $nombre_materia => $secciones): ?>
                        <div class="class-info">
                        <h3><?php echo htmlspecialchars($nombre_materia); ?></h3>
                        <table class="class-details">
                            <tr>
                                <th>NRC</th>
                                <th>Horario</th>
                                <th>Edificio</th>
                                <th>Aula</th>
                            </tr>
                            <?php foreach($secciones as $materia): ?>
                            <tr>
                                <td><?php echo isset($materia['CRN']) ? htmlspecialchars($materia['CRN']) : ''; ?></td>
                                <td>
                                    <?php 
                                    $dias = '';
                                    if($materia['L'] == 'L') $dias .= 'L';
                                    if($materia['M'] == 'M') $dias .= 'M';
                                    if($materia['I'] == 'I') $dias .= 'I';
                                    if($materia['J'] == 'J') $dias .= 'J';
                                    if($materia['V'] == 'V') $dias .= 'V';
                                    if($materia['S'] == 'S') $dias .= 'S';
                                    if($materia['D'] == 'D') $dias .= 'D';
                                    if($dias == '') $dias .= 'Sin Datos';
                                    
                                    $horaInicial = $materia['HORA_INICIAL'] ?? '0000';
                                    $horaFinal = $materia['HORA_FINAL'] ?? '0000';
                                    
                                    $horarioFormateado = sprintf('%s %s', 
                                        substr($horaInicial, 0, 2) . ':' . substr($horaInicial, 2, 2),
                                        substr($horaFinal, 0, 2) . ':' . substr($horaFinal, 2, 2)
                                    );
                                    echo $horarioFormateado;
                                    ?>
                                    <div class="weekdays" id="weekdays-<?php echo $materia['CRN']; ?>-<?php echo $materia['MODULO']; ?>">
                                        <div class="day">L</div>
                                        <div class="day">M</div>
                                        <div class="day">I</div>
                                        <div class="day">J</div>
                                        <div class="day">V</div>
                                        <div class="day">S</div>
                                    </div>
                                    <script>
                                        actualizarDiasActivos('<?php echo $dias; ?>', '<?php echo $materia['CRN']; ?>', '<?php echo $materia['MODULO']; ?>');
                                    </script>
                                </td>
                                <td><?php echo isset($materia['MODULO']) ? htmlspecialchars($materia['MODULO']) : '-'; ?></td>
                                <td><?php echo isset($materia['AULA']) ? htmlspecialchars($materia['AULA']) : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    } else {
        echo "<p>No se encontró información para este profesor.</p>";
    }
}
?>
<link rel="stylesheet" href="./CSS/basesdedatos.css">
<?php

if(isset($_POST['codigo_profesor'])) {
    include './config/db.php';
    
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
    function obtenerTodasLasMaterias($codigo_profesor, $tablas) {
        global $conexion;
        $todas_las_materias = [];
        
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
                    $todas_las_materias[] = $row;
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
            <h3></h3>
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

                foreach($materias_por_departamento as $dept => $materias_dept): 
                    $dept_nombre = str_replace('Data_', '', $dept);
                ?>
                    <h3>Materias en <?php echo htmlspecialchars($dept_nombre); ?></h3>
                    <?php foreach($materias_dept as $materia): ?>
                    <div class="class-info">
                        <h4><?php echo htmlspecialchars($materia['MATERIA']); ?></h4>
                        <table class="class-details">
                            <tr>
                                <th>NRC</th>
                                <th>Horario</th>
                                <th>Edificio</th>
                                <th>Aula</th>
                            </tr>
                            <tr>
                                <td><?php echo htmlspecialchars($materia['CRN']); ?></td>
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
                                    echo $dias . ' ' . 
                                        substr($materia['HORA_INICIAL'], 0, 2) . ':' . 
                                        substr($materia['HORA_INICIAL'], 2, 2) . ' - ' . 
                                        substr($materia['HORA_FINAL'], 0, 2) . ':' . 
                                        substr($materia['HORA_FINAL'], 2, 2);
                                ?>
                                </td>
                                <td><?php echo isset($materia['MODULO']) ? htmlspecialchars($materia['MODULO']) : ''; ?></td>
                                <td><?php echo isset($materia['AULA']) ? htmlspecialchars($materia['AULA']) : 'CVIRTU'; ?></td>
                            </tr>
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
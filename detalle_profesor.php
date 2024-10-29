<?php
function obtenerDetalleProfesor($codigo_profesor, $departamento_id, $tabla_departamento) {
    include './config/db.php';
    
    $sql = "SELECT * FROM $tabla_departamento 
            WHERE CODIGO_PROFESOR = ? 
            AND Departamento_ID = ?";
            
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $codigo_profesor, $departamento_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $materias = [];
    while($row = mysqli_fetch_assoc($result)) {
        $materias[] = $row;
    }
    
    return $materias;
}
?>

<!-- Archivo detalle_profesor.php -->
<?php
if(isset($_POST['codigo_profesor'])) {
    include './config/db.php';
    
    $codigo_profesor = (int)$_POST['codigo_profesor'];
    $departamento_id = (int)$_POST['departamento_id'];
    $tabla_departamento = $_POST['tabla_departamento'];
    
    $materias = obtenerDetalleProfesor($codigo_profesor, $departamento_id, $tabla_departamento);
    
    if(!empty($materias)) {
        $profesor = $materias[0];
        ?>
        <div class="detalle-profesor">
            <h3><?php echo htmlspecialchars($profesor['NOMBRE_PROFESOR']); ?></h3>
            <div class="info-general">
                <p><strong>Código:</strong> <?php echo htmlspecialchars($profesor['CODIGO_PROFESOR']); ?></p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($profesor['CATEGORIA']); ?></p>
                <p><strong>Tipo Contrato:</strong> <?php echo htmlspecialchars($profesor['TIPO_CONTRATO']); ?></p>
            </div>
            
            <h4>Materias Asignadas</h4>
            <table class="tabla-materias">
                <thead>
                    <tr>
                        <th>CRN</th>
                        <th>Materia</th>
                        <th>Sección</th>
                        <th>Horas</th>
                        <th>Horario</th>
                        <th>Modalidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($materias as $materia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($materia['CRN']); ?></td>
                        <td><?php echo htmlspecialchars($materia['MATERIA']); ?></td>
                        <td><?php echo htmlspecialchars($materia['SECCION']); ?></td>
                        <td><?php echo htmlspecialchars($materia['HORAS']); ?></td>
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
                        <td><?php echo htmlspecialchars($materia['MODALIDAD']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        echo "<p>No se encontró información para este profesor.</p>";
    }
}
?>
<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<?php 

// Obtener la fecha límite más reciente
$sql_fecha_limite = "SELECT Fecha_Limite FROM Fechas_Limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$fecha_limite = null;
if ($result_fecha_limite && mysqli_num_rows($result_fecha_limite) > 0) {
    $fecha_limite = mysqli_fetch_assoc($result_fecha_limite)['Fecha_Limite'];
}

// Obtener información de todos los departamentos
$sql_departamentos = "SELECT * FROM Departamentos";
$result_departamentos = mysqli_query($conexion, $sql_departamentos);

// Periodo actual (ajusta esto según cómo determines el periodo actual)
$periodo_actual = "2025A";

?>

<title>Reporte de entrega</title>
<link rel="stylesheet" href="./CSS/admin-reportes.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">

    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd"> 
            <h3>Reporte de entrega</h3>
        </div>
    </div>

    <div class="reporte-container">
        
        <?php while ($departamento = mysqli_fetch_assoc($result_departamentos)) : ?>
            <?php
            // Obtener información de la plantilla para este departamento
            $sql_plantilla = "SELECT * FROM Plantilla_Dep WHERE Departamento_ID = {$departamento['Departamento_ID']} ORDER BY Fecha_Subida_Dep DESC LIMIT 1";
            $result_plantilla = mysqli_query($conexion, $sql_plantilla);
            $plantilla = mysqli_fetch_assoc($result_plantilla);
            
            // Determinar el estado de la entrega
            $estado_entrega = "Sin entregar";
            $fecha_entrega = "-";
            if ($plantilla) {
                $fecha_entrega = date("d/m/Y h:s", strtotime($plantilla['Fecha_Subida_Dep']));
                if ($fecha_limite) {
                    if (strtotime($plantilla['Fecha_Subida_Dep']) <= strtotime($fecha_limite)) {
                        $estado_entrega = "Entregada";
                    } elseif (strtotime($plantilla['Fecha_Subida_Dep']) > strtotime($fecha_limite)) {
                        $estado_entrega = "Atrasada";
                    }
                } else {
                    $estado_entrega = "Entregada"; // Si no hay fecha límite, consideramos que está entregada
                }
            }
            
            // Obtener justificación si existe
            $sql_justificacion = "SELECT Justificacion FROM Justificaciones WHERE Departamento_ID = {$departamento['Departamento_ID']} ORDER BY Fecha_Justificacion DESC LIMIT 1";
            $result_justificacion = mysqli_query($conexion, $sql_justificacion);
            $justificacion = mysqli_fetch_assoc($result_justificacion);
            
            $notas_justificacion = "";
            if ($estado_entrega == "Entregada") {
                $notas_justificacion = "Entregado a tiempo.";
            } elseif ($estado_entrega == "Sin entregar") {
                $notas_justificacion = "Aún sin entregar.";
            } elseif ($estado_entrega == "Atrasada") {
                $notas_justificacion = $justificacion ? $justificacion['Justificacion'] : "Entrega atrasada sin justificación.";
            }
            ?>

            <div class="departamento-info">
                <div class="info-row">
                    <div class="info-col">
                        <div class="info-label">Departamento</div>
                        <div class="info-value"><?php echo $departamento['Departamentos']; ?></div>
                    </div>
                    <div class="info-col">
                        <div class="info-label">Periodo</div>
                        <div class="info-value"><?php echo $periodo_actual; ?></div>
                    </div>
                    <div class="info-col">
                        <div class="info-label">Estado de la entrega</div>
                        <div class="info-value-entrega">
                            <span class="estado-<?php echo str_replace(' ', '-', strtolower($estado_entrega)); ?>"><?php echo $estado_entrega; ?></span>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-col">
                        <div class="info-label">Fecha límite</div>
                        <div class="info-value"><?php echo $fecha_limite ? date("d/m/Y", strtotime($fecha_limite)) : "-"; ?></div>
                    </div>
                    <div class="info-col">
                        <div class="info-label">Fecha de entrega</div>
                        <div class="info-value"><?php echo $fecha_entrega; ?></div>
                    </div>
                </div>
                <div class="info-label">Notas/Justificacion</div>
                <div class="notas-justificacion">
                    <p><?php echo $notas_justificacion; ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include './template/footer.php' ?>

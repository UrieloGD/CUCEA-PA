<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>

<?php
// Obtener la fecha límite más reciente
$sql_fecha_limite = "SELECT Fecha_Limite FROM fechas_limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$fecha_limite = null;
if ($result_fecha_limite && mysqli_num_rows($result_fecha_limite) > 0) {
    $fecha_limite = mysqli_fetch_assoc($result_fecha_limite)['Fecha_Limite'];
}

// Obtener información de todos los departamentos
$sql_departamentos = "SELECT d.*, MAX(p.Fecha_Subida_Dep) AS Fecha_Subida_Dep
                      FROM departamentos d
                      LEFT JOIN plantilla_dep p ON d.Departamento_ID = p.Departamento_ID
                      GROUP BY d.Departamento_ID";
$result_departamentos = mysqli_query($conexion, $sql_departamentos);

// Periodo actual (ajusta esto según cómo determines el periodo actual)
$periodo_actual = "2025B";
?>

<title>Reporte de entrega</title>
<link rel="stylesheet" href="./CSS/admin-reportes.css?=v1.0" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Reporte de entrega</h3>
        </div>
    </div>

    <div class="export-button">
        <a href="./functions/admin-reportes/generar-pdf.php" class="btn-exportar">Exportar a PDF</a>
    </div>

    <div class="reporte-container">
        <?php while ($departamento = mysqli_fetch_assoc($result_departamentos)) : ?>
            <?php
            $fecha_subida = $departamento['Fecha_Subida_Dep'];
            $fecha_actual = date("Y-m-d");

            // Buscar justificación
            $sql_justificacion = "SELECT Justificacion FROM justificaciones 
            WHERE Departamento_ID = {$departamento['Departamento_ID']}
            ORDER BY Fecha_Justificacion DESC LIMIT 1";
            $result_justificacion = mysqli_query($conexion, $sql_justificacion);
            $tiene_justificacion = mysqli_num_rows($result_justificacion) > 0;

            if ($fecha_subida !== null) {
                $fecha_entrega = date("d/m/Y H:i", strtotime($fecha_subida));

                if ($tiene_justificacion) {
                    $estado_entrega = "Entregada";
                    $notas_justificacion = "Entregado con retraso. ";
                } else {
                    $estado_entrega = "Entregada";
                    $notas_justificacion = "Entregado a tiempo. ";
                }
            } else {
                if ($fecha_limite && $fecha_actual > $fecha_limite) {
                    $estado_entrega = "Atrasada";
                    $fecha_entrega = "-";
                    $notas_justificacion = "No entregado. Fecha límite excedida. ";
                } else {
                    $estado_entrega = "Pendiente";
                    $fecha_entrega = "-";
                    $notas_justificacion = "Aún sin entregar. ";
                }
            }

            if ($tiene_justificacion) {
                $justificacion = mysqli_fetch_assoc($result_justificacion)['Justificacion'];
                $notas_justificacion .= "<br><br><b>Justificación:</b> " . $justificacion;
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
                        <div class="info-label">Estado</div>
                        <div class="info-value-entrega">
                            <span class="estado-<?php echo strtolower($estado_entrega); ?>"><?php echo $estado_entrega; ?></span>
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
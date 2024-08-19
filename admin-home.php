<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>

<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener los departamentos que han subido un archivo (solo la fecha más reciente por departamento)
$sql_departamentos_subidos = "SELECT Departamento_ID, MAX(Fecha_Subida_Dep) AS Fecha_Subida_Dep
                              FROM Plantilla_Dep
                              GROUP BY Departamento_ID";
$result_departamentos_subidos = mysqli_query($conexion, $sql_departamentos_subidos);
$departamentos_subidos = array();
while ($row = mysqli_fetch_assoc($result_departamentos_subidos)) {
    if ($row['Fecha_Subida_Dep'] !== null) {
        $departamentos_subidos[] = $row['Departamento_ID'];
    }
}

// Obtener el total de departamentos
$sql_total_departamentos = "SELECT COUNT(*) AS total FROM Departamentos";
$result_total_departamentos = mysqli_query($conexion, $sql_total_departamentos);
$row_total_departamentos = mysqli_fetch_assoc($result_total_departamentos);
$total_departamentos = $row_total_departamentos['total'];

// Calcular el número de departamentos que han entregado
$departamentos_entregados = count($departamentos_subidos);

// Calcular el porcentaje de avance
$porcentaje_avance = ($departamentos_entregados / $total_departamentos) * 100;

// Obtener la fecha límite más reciente
$sql_fecha_limite = "SELECT Fecha_Limite FROM Fechas_Limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$fecha_limite = $result_fecha_limite;
if ($result_fecha_limite && mysqli_num_rows($result_fecha_limite) > 0) {
    $fecha_limite = mysqli_fetch_assoc($result_fecha_limite)['Fecha_Limite'];
}
?>

<title>Centro de Gestión</title>
<link rel="stylesheet" href="./CSS/admin-home.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">

    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Gestión de usuarios</h3>
        </div>
    </div>

    <!-- Recuadros superiores -->
    <div class="recuadros-superiores">
        <div class="recuadro active" onclick="activateRecuadro(this)">
            <a class="texto" href="./admin-reportes.php">
                <img src="./Img/img-admin/img-reporte-entrega.jpg" alt="Reporte de entrega">
                <div class="texto">Reporte de entrega</div>
            </a>
        </div>
        <div class="recuadro" onclick="activateRecuadro(this)">
            <a class="texto" href="./admin-eventos.php">
                <img src="./Img/img-admin/img-control-eventos.jpg" alt="Control de eventos">
                <div class="texto">Control de eventos</div>
            </a>
        </div>
        <div class="recuadro" onclick="activateRecuadro(this)">
            <a class="texto" href="./admin-usuarios.php">
                <img src="./Img/img-admin/img-gestion-usuarios.jpeg" alt="Gestión de usuarios">
                <div class="texto">Gestión de usuarios</div>
            </a>
        </div>
    </div>

    <div class="contenido">
        <div class="izquierda">
            <div class="contenedor-tabla">
                <h3 class="titulo-tabla">Bases de Datos Pendientes de Entrega</h3>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Estado de la entrega</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta para obtener los departamentos y la fecha de subida más reciente
                        $sql_departamentos = "SELECT d.Departamento_ID, d.Departamentos, MAX(p.Fecha_Subida_Dep) AS Fecha_Subida_Dep
                                            FROM Departamentos d
                                            LEFT JOIN Plantilla_Dep p ON d.Departamento_ID = p.Departamento_ID
                                            GROUP BY d.Departamento_ID, d.Departamentos";
                        $result_departamentos = mysqli_query($conexion, $sql_departamentos);

                        while ($row = mysqli_fetch_assoc($result_departamentos)) {
                            $departamento_id = $row['Departamento_ID'];
                            $nombre_departamento = $row['Departamentos'];
                            $fecha_subida = $row['Fecha_Subida_Dep'];
                            echo "<tr>";
                            echo "<td>$nombre_departamento</td>";
                            if ($fecha_subida !== null) {
                                echo "<td><span class='entregada'>Entregada</span></td>";
                            } else {
                                $fecha_actual = date("Y-m-d");
                                if ($fecha_actual > $fecha_limite) {
                                    echo "<td><span class='atrasada'>Atrasada</span></td>";
                                } else {
                                    echo "<td><span class='sin-entregar'>Pendiente</span></td>";
                                }
                            }
                            echo "</tr>";
                        }

                        // Calcular el porcentaje de avance
                        $porcentaje_avance = ($departamentos_entregados / $total_departamentos) * 100;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="derecha">
            <h3>Progreso de Entregas</h3>
            <div class="progreso">
                <p>Se ha alcanzado el <?php echo round($porcentaje_avance); ?>% del total de entregas necesarias.</p>
                <div class="circulo-progreso">
                    <div class="circulo">
                        <span class="porcentaje"><?php echo round($porcentaje_avance); ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include your footer here -->
<script>
    function updateCircleProgress(percentage) {
        var circleElement = document.querySelector('.circulo-progreso');
        var porcentajeElement = document.querySelector('.porcentaje');
        porcentajeElement.textContent = percentage + '%';
        circleElement.style.setProperty('--progress', percentage + '%');
    }

    updateCircleProgress(<?php echo round($porcentaje_avance); ?>);
</script>

<?php include './template/footer.php' ?>
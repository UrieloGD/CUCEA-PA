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
?>

<title>Plantillas Departamentos</title>
<link rel="stylesheet" href="./CSS/plantillasDepartamentos.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
  <div class="encabezado">
    <div class="titulo-bd">
      <h3>Plantillas Programación Academica</h3>
    </div>
  </div>

  <!-- Barra de progreso -->
  <div class="progress-container">
    <div class="progress-bar" data-progress="<?php echo round($porcentaje_avance); ?>" style="width: <?php echo round($porcentaje_avance); ?>%;"></div>
  </div>

  <h3 class="centrado">Porcentaje total de entregas</h3>

  <!--Pestaña azul-->
  <!-- <div class="header-bar">
  <h2>Plantilla</h2>
</div> -->

  <!--Tabla Entrega BD-->
  <div class="tabla">
    <table>
      <tr>
        <th style="text-align: center;">Departamento</th>
        <th style="text-align: center;">Estado de la entrega</th>
        <th style="text-align: center;">Fecha de entrega</th>
        <th style="text-align: center;">Base de datos</th>
      </tr>
      <tr>
        <?php
        // Fecha límite para marcar como "Atrasado"
        $fecha_limite = "2022-06-01";

        // Consulta para obtener los departamentos y la fecha de subida más reciente
        $sql_departamentos = "SELECT d.Departamento_ID, d.Nombre_Departamento, MAX(p.Fecha_Subida_Dep) AS Fecha_Subida_Dep
                        FROM Departamentos d
                        LEFT JOIN Plantilla_Dep p ON d.Departamento_ID = p.Departamento_ID
                        GROUP BY d.Departamento_ID, d.Nombre_Departamento";
        $result_departamentos = mysqli_query($conexion, $sql_departamentos);

        while ($row = mysqli_fetch_assoc($result_departamentos)) {
          $departamento_id = $row['Departamento_ID'];
          $nombre_departamento = $row['Nombre_Departamento'];
          $fecha_subida = $row['Fecha_Subida_Dep'];
          echo "<tr>";
          echo "<td>$nombre_departamento</td>";
          if ($fecha_subida !== null) {
            echo "<td class='entregada'>Entregada</td>";
            echo "<td style='text-align: center;'>" . date('d/m/Y H:i:s', strtotime($fecha_subida)) . "</td>";
          } else {
            $fecha_actual = date("Y-m-d");
            if ($fecha_actual > $fecha_limite) {
              echo "<td class='atrasada'>Atrasada</td>";
              echo "<td style='text-align: center; font-style: italic;'>Sin entregar</td>";
            } else {
              echo "<td class='sin-entregar'>Pendiente</td>";
              echo "<td style='text-align: center; font-style: italic;'>Sin entregar</td>";
            }
          }
          echo "<td style='text-align: center;'><a href='basesdedatos.php?departamento_id=$departamento_id' class='btn-ir'>Ir</a></td>";
          echo "</tr>";
        }
        ?>
    </table>
  </div>

  <?php include './template/footer.php' ?>
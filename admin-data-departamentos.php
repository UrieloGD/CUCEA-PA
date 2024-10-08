<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>

<?php
// Verifica si el parámetro de éxito está presente en la URL
if (isset($_GET['success']) && $_GET['success'] == '1') {
  echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Fecha límite actualizada',
                text: 'La fecha límite se ha actualizado correctamente.',
                confirmButtonText: 'Aceptar'
            }).then(function() {
                // Eliminar el parámetro success de la URL
                if (window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('success');
                    window.history.replaceState({path: url.href}, '', url.href);
                }
            });
        });
    </script>";
}
?>

<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener la última fecha límite de la base de datos
$sql_fecha_limite = "SELECT Fecha_Limite FROM Fechas_Limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$row_fecha_limite = mysqli_fetch_assoc($result_fecha_limite);
$fecha_limite = $row_fecha_limite ? $row_fecha_limite['Fecha_Limite'] : "2024-11-10";

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
<link rel="stylesheet" href="./CSS/admin-data-departamentos.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
  <div class="encabezado">
    <div class="titulo-bd">
      <h3>Plantillas Programación Academica</h3>
    </div>
  </div>

  <!-- Barra de progreso -->
  <h3 class="centrado">Porcentaje total de entregas</h3>
  <div class="progress-container">
    <div class="progress-bar" data-progress="<?php echo round($porcentaje_avance); ?>" style="width: <?php echo round($porcentaje_avance); ?>%;"></div>
  </div>


  <!--Pestaña azul-->
  <!-- <div class="header-bar">
  <h2>Plantilla</h2>
</div> -->

  <div class="fechalimite">
    <span>La fecha límite actual es <?php echo date('d/m/Y', strtotime($fecha_limite)); ?></span>
  </div>

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

          // Nueva lógica
          $sql_justificacion = "SELECT * FROM Justificaciones WHERE Departamento_ID = '$departamento_id' ORDER BY Fecha_Justificacion DESC LIMIT 1";
          $result_justificacion = mysqli_query($conexion, $sql_justificacion);
          $justificacion = mysqli_fetch_assoc($result_justificacion);

          if ($fecha_subida !== null) {
            echo "<td><span class='estado-entregada'>Entregada</span></td>";
            echo "<td style='text-align: center;'>" . date('d/m/Y H:i:s', strtotime($fecha_subida)) . "</td>";
          } else {
            $fecha_actual = date("Y-m-d H:i:s");
            if (strtotime($fecha_actual) > strtotime($fecha_limite)) {
              echo "<td><span class='estado-atrasada'>Atrasada</span></td>";
              if ($justificacion) {
                echo "<td style='text-align: center; font-style: italic;'>Justificación enviada</td>";
              } else {
                echo "<td style='text-align: center; font-style: italic;'>Sin entregar</td>";
              }
            } else {
              echo "<td><span class='estado-pendiente'>Pendiente</span></td>";
              echo "<td style='text-align: center; font-style: italic;'>Sin entregar</td>";
            }
          }

          echo "<td style='text-align: center;'><a href='./basesdedatos.php?departamento_id=$departamento_id' class='btn-ir'>Ir</a></td>";
          echo "</tr>";
        }
        ?>
    </table>
  </div>

  <div class="form-actions">
    <button type="button" class="btn" onclick="openModal()">Cambiar Fecha Límite</button>
    <a href="./admin-reportes.php"><button type="submit" class="btn">Generar Reporte</button></a>
  </div>

  <!-- Modal para cambiar fecha límite -->
  <div id="modalFechaLimite" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Cambiar Fecha Límite</h2>
      <form id="fechaLimiteForm" action="./functions/admin-data-departamentos/updateFechaLimite.php" method="post">
        <label for="fecha_limite">Nueva Fecha Límite:</label>
        <input type="date" id="fecha_limite" name="fecha_limite" required>
        <button type="submit" class="btn-guardar">Guardar</button>
      </form>
    </div>
  </div>

  <script src="./JS/admin-data-departamentos/modalFechaLimite.js"></script>
  <!-- Script updateFechaLimite -->
  <!-- Por alguna razon no se puede hacer modular el codigo de abajo -->
  <script>
    document.getElementById('fechaLimiteForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Mostrar Sweet Alert de procesamiento
    Swal.fire({
      title: 'Procesando...',
      html: 'Por favor espere mientras se actualiza la fecha límite.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    var formData = new FormData(this);

    fetch('./functions/admin-data-departamentos/updateFechaLimite.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        Swal.close();
        if (data.includes("Location: ../admin-data-departamentos.php?success=1")) {
          Swal.fire({
            icon: 'success',
            title: 'Fecha límite actualizada',
            text: 'La fecha límite se ha actualizado correctamente.',
            confirmButtonText: 'Aceptar'
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al actualizar la fecha límite.',
            confirmButtonText: 'Aceptar'
          });
        }
      })
      .catch(error => {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Hubo un problema al procesar la solicitud.',
          confirmButtonText: 'Aceptar'
        });
        console.error('Error:', error);
      });
  });
  </script>

  <?php include './template/footer.php' ?>
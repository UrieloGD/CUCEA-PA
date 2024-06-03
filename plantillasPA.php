<!-- header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Progreso Plantillas</title>
<link rel="stylesheet" href="./CSS/plantillasPA.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">

  <!--Pestaña azul-->
  <div class="encabezado">
    <div class="titulo-bd">
      <h3>Plantillas Programación Academica</h3>
    </div>
  </div>
  <br><br>

  <!--Tabla Subir Plantilla-->
  <div class="tabla">
    <table>
      <tr>
        <th style="text-align: center;">Departamento</th>
        <th style="text-align: center;">Archivo</th>
        <th style="text-align: center;">Ultima Actualización</th>
        <th style="text-align: center;">Acciones</th>
      </tr>

      <!-- Asignar el ID para cada departamento -->
      <?php
      $departamentos = [
        1 => "Estudios Regionales",
        2 => "Finanzas",
        3 => "Ciencias Sociales",
        4 => "PALE",
        5 => "Posgrados",
        6 => "Economía",
        7 => "Recursos Humanos",
        8 => "Métodos Cuantitativos",
        9 => "Políticas Públicas",
        10 => "Administración"
      ];

      foreach ($departamentos as $id => $nombre) {
      ?>
        <tr>
          <td><?php echo $nombre; ?></td>
          <td id="nombre-archivo-<?php echo $id; ?>" style="text-align: center;"></td>
          <td id="fecha-subida-<?php echo $id; ?>" style="text-align: center;"></td>
          <td style="text-align: center;">
            <form id="formulario-subida-<?php echo $id; ?>" action="./config/upload_sa.php" method="POST" enctype="multipart/form-data">
              <label for="input-file-<?php echo $id; ?>" class="btn">
                <img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png" alt="Subir Archivo">
              </label>
              <input id="input-file-<?php echo $id; ?>" class="hidden-input" type="file" name="file" onchange="actualizarNombreArchivo(this, <?php echo $id; ?>); actualizarFechaSubida(<?php echo $id; ?>);">
              <!-- Campos ocultos para enviar datos adicionales -->
              <input type="hidden" name="Departamento_ID" value="<?php echo $id; ?>">
              <input type="hidden" id="Nombre_Archivo_Dep-<?php echo $id; ?>" name="Nombre_Archivo_Dep">
              <input type="hidden" id="Fecha_Subida_Dep-<?php echo $id; ?>" name="Fecha_Subida_Dep">
              <button type="submit" class="hidden-button"></button>
            </form>
            <style>
              #input-file-<?php echo $id; ?> {
                display: none;
                /* Oculta el input de subir archivo */
              }

              .hidden-button {
                display: none;
                /* Oculta el botón de enviar */
              }
            </style>
            <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
            <a href="./config/descargar_plantilla.php?departamento_id=<?php echo $id; ?>" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
            <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
          </td>
        </tr>
      <?php } ?>

      <?php
        $sql = "SELECT Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep FROM Plantilla_SA";
        $result = mysqli_query($conexion, $sql);

        if ($result->num_rows > 0) {
            echo "<script>";
            while ($row = $result->fetch_assoc()) {
                $departamento_id = $row["Departamento_ID"];
                $nombre_archivo = $row["Nombre_Archivo_Dep"] ? $row["Nombre_Archivo_Dep"] : "No hay archivo asignado";
                $fecha_subida = $row["Fecha_Subida_Dep"] ? $row["Fecha_Subida_Dep"] : "---";
                echo "document.getElementById('nombre-archivo-$departamento_id').innerText = '$nombre_archivo';";
                echo "document.getElementById('fecha-subida-$departamento_id').innerText = '$fecha_subida';";
            }
            echo "</script>";
        } else {
            echo "No se encontraron archivos subidos.";
        }
        ?>

      <script src="./JS/plantillasPA.js"></script>
    </table>
  </div>
  <?php include './template/footer.php' ?>
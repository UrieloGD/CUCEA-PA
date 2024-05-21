<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Progreso Plantillas</title>
<link rel="stylesheet" href="./CSS/plantillasPA.css"/>

<!--Cuadro principal del home-->
<div class="cuadro-principal">

<!--Pestaña azul-->
<div class="encabezado">
    <div class="titulo-bd">
      <h3>Plantillas Programación Academica</h3>
    </div>
  </div>
<!--Pestaña azul-->
<!-- <div class="header-bar">
  <h2>Plantilla</h2>
</div> -->
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
    <tr>
      <td>Estudios Regionales</td>
      <td style="text-align: center;">plantilla-reg.xls</td>
      <td style="text-align: center;">01/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <!-- Modificaciones para subida de archivos -->
    <tr>
      <td>Finanzas</td>
      <td id="nombre-archivo" style="text-align: center;">No se ha subido un archivo</td>
      <td id="fecha-subida" style="text-align: center;">01/10/2023</td>
        <td style="text-align: center;">
              <form id="formulario-subida" action="./config/upload.php" method="POST" enctype="multipart/form-data">
                  <label for="input-file" class="btn">
                      <img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png" alt="Subir Archivo">
                  </label>
                  <input id="input-file" class="hidden-input" type="file" name="file" onchange="actualizarNombreArchivo(this); actualizarFechaSubida();">
              </form>
              <style>
              #input-file {
              display: none; /* Oculta display de la función de subir archivo  */
              }
              </style>
              <!-- Modificaciones para subida de archivos -->
              <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
              <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
              <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
        </td>
      </tr>
<!-- Modificaciones para subida de archivos -->
    <tr>
      <td>Ciencias Sociales</td>
      <td style="text-align: center;">pa-ciencias-sociales.xls</td>
      <td style="text-align: center;">01/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>PALE</td>
      <td style="text-align: center;">PA-PALE.xls</td>
      <td style="text-align: center;">09/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>Administración</td>
      <td style="text-align: center;">planeación-dep-admin.xls</td>
      <td style="text-align: center;">01/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>Posgrados</td>
      <td style="text-align: center; font-style: italic;">Sin archivo</td>
      <td style="text-align: center; font-style: italic;">Sin archivo</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>Economía</td>
      <td style="text-align: center; font-style: italic;">Sin archivo</td>
      <td style="text-align: center; font-style: italic;">Sin archivo</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>Recursos Humanos</td>
      <td style="text-align: center;">rh.xls</td>
      <td style="text-align: center;">24/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>Métodos Cuantitativos</td>
      <td style="text-align: center;">plantilla_metodos_cuantitativos.xls</td>
      <td style="text-align: center;">24/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <tr>
      <td>Políticas Públicas</td>
      <td style="text-align: center;">pa_pp.xls</td>
      <td style="text-align: center;">24/10/2023</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-visualizar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
      </td>
    </tr>
    <script src="./JS/plantillasPA"></script>
  </table>
</div>
<?php include './template/footer.php' ?>


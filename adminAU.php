<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Añadir Usuarios</title>
<link rel="stylesheet" href="./CSS/adminAU.css"/>

<!--Cuadro principal del home-->
<div class="cuadro-principal">

<!--Pestaña azul-->
<div class="encabezado">
    <div class="titulo-bd">
      <h3>Gestión de usuarios</h3>
    </div>
  </div>
<br><br>

<!-- Barra de Busqueda-->
<div class="busqueda">
  <input type="text" placeholder="Buscar..." id="search-input">
  <div class="button-container">
    <button id="add-user-btn">Añadir usuario</button>
    <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/ajustes.png"></a>
  </div>
</div>

<!-- Boton Usuario 
<button id="add-user-btn">Añadir usuario</button> -->

<!-- Boton Ajustes -->


<!--Tabla Añadir Usuarios-->

<div class="tabla">
  <table>
    <tr>
      <th style="text-align: center;">Nombre</th>
      <th style="text-align: center;">Correo</th>
      <th style="text-align: center;">Rol</th>
      <th style="text-align: center;">Estado</th>
      <th style="text-align: center;">Acción</th>
    </tr>

    <tr>
      <td style="text-align: center;">Esmeralda Martínez Villanueva</td>
      <td style="text-align: center;">esmeralda.mtz@cucea.udg.mx</td>
      <td style="text-align: center;">Jefe de departamento</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar2.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar2.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Arturo González López</td>
      <td style="text-align: center;">arturo.glez@cucea.udg.mx</td>
      <td style="text-align: center;">Coordinador</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">María Fernández Vázquez</td>
      <td style="text-align: center;">maria.fdez@cucea.udg.mx</td>
      <td style="text-align: center;">Jefe de departamento</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Juan Camacho Ramírez</td>
      <td style="text-align: center;">juan.camacho@cucea.udg.mx</td>
      <td style="text-align: center;">Jefe de departamento</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>

      </td>
    </tr>
    <tr>

      <td style="text-align: center;">Saúl Jimenez Rojas</td>
      <td style="text-align: center;">saul.jimenez@cucea.udg.mx</td>
      <td style="text-align: center;">Coordinador</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>
    <tr>

      <td style="text-align: center;">Ernesto Díaz Quiroz</td>
      <td style="text-align: center;">ernesto.diaz@cucea.udg.mx</td>
      <td style="text-align: center;">Jefe de departamento</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Rosario Dominguez Duarte</td>
      <td style="text-align: center;">rosario.dguez@cucea.udg.mx</td>
      <td style="text-align: center;">Coordinador</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Karla Nuñez Soria</td>
      <td style="text-align: center;">karla.nuñez@cucea.udg.mx</td>
      <td style="text-align: center;">Coordinador</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Ricardo Torres Hernández</td>
      <td style="text-align: center;">ricardo.torres@cucea.udg.mx</td>
      <td style="text-align: center;">Jefe de departamento</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Ana García Fernández</td>
      <td style="text-align: center;">ana.garcia@cucea.udg.mx</td>
      <td style="text-align: center;">Jefe de departamento</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

    <tr>
      <td style="text-align: center;">Miguel Gómez Martínez</td>
      <td style="text-align: center;">miguel.gomez@cucea.udg.mx</td>
      <td style="text-align: center;">Coordinador</td>
      <td style="text-align: center;">Activa</td>
      <td style="text-align: center;">
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/editar.png"></a>
        <a href="#" class="btn"><img src="./Img/Icons/iconos - adminAU/borrar.png"></a>
      </td>
    </tr>

  </table>

</div>
<?php include './template/footer.php' ?>
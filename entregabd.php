<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Entrega Base de Datos</title>
<link rel="stylesheet" href="./CSS/entregabd.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
  <div class="encabezado">
      <div class="titulo-bd">
        <h2>Base de datos</h2>
      </div>
  </div>
<!--Pestaña azul-->
<!-- <div class="header-bar">
  <h2>Plantilla</h2>
</div> -->

<br><br>

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

  <td>Estudios Regionales</td>
    <td class="sin-entregar">Pendiente</td>
    <td style="text-align: center;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Finanzas</td>
    <td class="entregada">Entregada</td>
    <td style="text-align: center;">01/10/2024</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Ciencias Sociales</td>
    <td class="atrasada">Atrasada</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>PALE</td>
    <td class="sin-entregar">Pendiente</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Administración</td>
    <td class="entregada">Entregada</td>
    <td style="text-align: center;">03/10/2024</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Posgrados</td>
    <td class="sin-entregar">Pendiente</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Economía</td>
    <td class="atrasada">Atrasada</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Recursos Humanos</td>
    <td class="atrasada">Atrasada</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Métodos Cuantitativos</td>
    <td class="sin-entregar">Pendiente</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>

  <tr>
    <td>Políticas Públicas</td>
    <td class="sin-entregar">Pendiente</td>
    <td style="text-align: center; font-style: italic;">Sin entregar</td>
    <td style="text-align: center;">
      <a href="#" class="btn"><img src="./Icons/boton-ir.png"></a>
    </td>
  </tr>
</table>
</div>
<?php include './template/footer.php' ?>


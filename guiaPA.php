<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Guía PA</title>
<link rel="stylesheet" href="./CSS/guiaPA.css">
<?php
// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] !=0) {
    header("Location: home.php");
    exit();
}
?>

<!--Cuadro principal del home-->
<div class="cuadro-principal">
     <!--Pestañas-->
     <div class="tab-container">
          <div class="tab-buttons">
               <button class="tab-button active">Guía</button>
               <button class="tab-button">Etapa 1</button>
               <button class="tab-button">Etapa 2</button>
               <button class="tab-button">Etapa 3</button>
               <button class="tab-button">Etapa 4</button>
               <button class="tab-button">Etapa 5</button>
          </div>
          <div class="tab-content">
               <div class="tab-pane active">

               </div>
          </div>
     </div>

     <script src="./JS/pestañas-plantilla.js"></script>

     <?php include("./template/footer.php"); ?>
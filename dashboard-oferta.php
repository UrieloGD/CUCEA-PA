<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php require_once './config/db.php' ?>

<title>Oferta Académica</title>
<link rel="stylesheet" href="./CSS/dashboard-oferta.css?=v1.0" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="cuadro-scroll">

    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Dashboard Oferta Académica</h3>
        </div>
    </div>

    <div class="powerbi-container">
        <iframe title="BD Oferta Académica" width="1700px" height="760px" src="https://app.powerbi.com/reportEmbed?reportId=dd6df1fe-bfd2-452d-8ee6-deee2dccfb98&autoAuth=true&ctid=39962f80-9212-4b1f-8b95-e7962c4acb30" frameborder="0" allowFullScreen="true" style="margin: 30px 10px 0 10px;"></iframe>
    </div>

    <?php include './template/footer.php' ?>
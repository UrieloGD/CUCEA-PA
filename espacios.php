<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Calendario</title>
<link rel="stylesheet" href="./CSS/espacios.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Espacios</h3>
        </div>
    </div>

<!-- Aquí empieza el código de Espacios-->

<!-- Cuadros de texto y desplegables -->
<div class="filtros">
    <div class="filtro">
        <label for="ciclo">Ciclo</label>
        <select id="ciclo" name="ciclo">
            <option value="2019A">2019A</option>
            <!-- Agregar más opciones según sea necesario -->
        </select>
    </div>
    <div class="filtro">
        <label for="edificio">Edificio</label>
        <select id="edificio" name="edificio">
            <option value="CEDA">CEDA</option>
            <!-- Agregar más opciones según sea necesario -->
        </select>
    </div>
    <div class="filtro">
        <label for="dia">Día</label>
        <select id="dia" name="dia">
            <option value="Lunes">Lunes</option>
            <!-- Agregar más opciones según sea necesario -->
        </select>
    </div>
    <div class="filtro">
        <label for="horario">Horario</label>
        <select id="horario" name="horario">
            <option value="16:00-18:00">16:00 - 18:00</option>
            <!-- Agregar más opciones según sea necesario -->
        </select>
    </div>
    <div class="filtro">
        <label for="tiempo-real">Tiempo real</label>
        <input type="text" id="tiempo-real" name="tiempo-real" readonly>
    </div>
</div>

<!-- Aquí empieza el código del Edificio -->
 

<?php include './template/footer.php' ?>

    
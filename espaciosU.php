<?php include './template/header.php' ?>
<?php include './template/navbar.php' ?>

<title>Espacios</title>
<link rel="stylesheet" href="./CSS/espacios.css" />

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Visualización de espacios</h3>
        </div>
    </div>
    
    <div class="filtros">
        <select id="ciclo">
            <option value="2019A">2019A</option>
            <!-- Agrega más opciones según sea necesario -->
        </select>
        <select id="edificio">
            <option value="CEDA">CEDA</option>
            <!-- Agrega más opciones según sea necesario -->
        </select>
        <select id="dia">
            <option value="Lunes">Lunes</option>
            <!-- Agrega más opciones según sea necesario -->
        </select>
        <select id="horario">
            <option value="16:00-18:00">16:00 - 18:00</option>
            <!-- Agrega más opciones según sea necesario -->
        </select>
        <button id="tiempoReal">Tiempo real</button>
    </div>

<?php include './template/footer.php' ?>

<script src="./JS/espacios/espacios.js"></script>
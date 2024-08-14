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
<div class="contenedor-principal">
    <div class="columna-lateral izquierda">
        <div class="letra-piso">A</div>
    </div>

    <div class="cuadro-grande">
        <div class="piso">
            <div class="numero-piso">Piso 3</div>
            <div class="salas">
                <div class="sala administrativo">A-309</div>
                <div class="sala aula">A-308</div>
                <div class="sala aula">A-307</div>
                <div class="sala aula">A-306</div>
                <div class="sala aula">A-305</div>
                <div class="sala bodega">A-304</div>
                <div class="sala aula">A-303</div>
                <div class="sala aula">A-302</div>
                <div class="sala aula">A-301</div>
            </div>
        </div>
        <div class="piso">
            <div class="numero-piso">Piso 2</div>
            <div class="salas">
                <div class="sala administrativo">A-206</div>
                <div class="sala bodega">A-202</div>
                <div class="sala aula">A-203</div>
                <div class="sala administrativo">A-204</div>
                <div class="sala administrativo">A-205</div>
                <div class="sala administrativo">A-201</div>
            </div>
        </div>
        <div class="piso">
            <div class="numero-piso">Piso 1</div>
            <div class="salas">
                <div class="sala administrativo">A-204</div>
                <div class="sala administrativo">A-203</div>
                <div class="sala administrativo">A-202</div>
                <div class="sala administrativo">A-201</div>
            </div>
        </div>
    </div>

    <div class="columna-lateral derecha">
        <div class="letra-piso">A</div>
    </div>
</div>

<?php include './template/footer.php' ?>

    
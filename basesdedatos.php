<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<?php
// Consulta SQL para obtener los datos de la tabla 'bd'
$sql = "SELECT * FROM Data_Plantilla";
$result = mysqli_query($conexion, $sql);

// Número de registros por página
$registros_por_pagina = 50;

// Determinar la página actual
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Calcular el offset para la consulta SQL
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta SQL para obtener los datos de la tabla 'bd' con límite y offset
$sql = "SELECT * FROM Data_Plantilla LIMIT $registros_por_pagina OFFSET $offset";
$result = mysqli_query($conexion, $sql);

// Obtener el total de registros
$total_registros = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM Data_Plantilla"));
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<title>Bases de datos</title>
<link rel="stylesheet" href="./CSS/basesdedatos.css">

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Base de datos</h3>
        </div>
        <div class="icono-buscador" id="icono-buscador">
            <i class="fa fa-search" aria-hidden="true"></i>
        </div>
        <div class="barra-buscador" id="barra-buscador" style="display: none;">
            <input type="text" id="input-buscador" placeholder="Buscar...">
            <!-- <button id="btn-buscar">Buscar</button> -->
        </div>
        <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </div>

        <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </div>
        <div class="icono-buscador" id="icono-descargar">
            <i class="fa fa-download" aria-hidden="true"></i>
        </div>

        <!-- <div class="registros-por-pagina">
            <label for="select-registros">Registros por página:</label>
            <select id="select-registros">
                <option value="50" selected>50</option>
                <option value="100">100</option>
                <option value="200">200</option> 
                <option value="all">Todos</option>
            </select>
        </div> -->
    </div>
    <?php

    ?>
    <div class="Tabla">
        <table id="tabla-datos">
            <tr>
                <th></th> <!-- columna para el checkbox -->
                <th>ID</th>
                <th>CICLO</th>
                <th>NRC</th>
                <th>FECHA INI</th>
                <th>FECHA FIN</th>
                <th>L</th>
                <th>M</th>
                <th>I</th>
                <th>J</th>
                <th>V</th>
                <!-- <th>S</th>
                <th>D</th> -->
                <th>HORA INI</th>
                <th>HORA FIN</th>
                <th>EDIF</th>
                <th>AULA</th>
            </tr>

            <?php
            // Verificar si se obtuvieron resultados
            if (mysqli_num_rows($result) > 0) {
                // Recorrer los resultados y mostrarlos en la tabla
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . $row["ID_Plantilla"] . "'></td>"; // Agregar el checkbox
                    echo "<td>" . $row["ID_Plantilla"] . "</td>";
                    echo "<td>" . $row["CICLO"] . "</td>";
                    echo "<td>" . $row["NRC"] . "</td>";
                    echo "<td>" . $row["FECHA_INI"] . "</td>";
                    echo "<td>" . $row["FECHA_FIN"] . "</td>";
                    echo "<td>" . $row["L"] . "</td>";
                    echo "<td>" . $row["M"] . "</td>";
                    echo "<td>" . $row["I"] . "</td>";
                    echo "<td>" . $row["J"] . "</td>";
                    echo "<td>" . $row["V"] . "</td>";
                    echo "<td>" . $row["HORA_INI"] . "</td>";
                    echo "<td>" . $row["HORA_FIN"] . "</td>";
                    echo "<td>" . $row["EDIF"] . "</td>";
                    echo "<td>" . $row["AULA"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='16'>No hay datos disponibles</td></tr>";
            }
            // Cerrar la conexión a la base de datos
            mysqli_close($conexion);
            ?>
        </table>
        <!-- Enlaces de paginación -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_paginas; $i++) {
                $active = ($i == $pagina_actual) ? 'active' : '';
                echo "<a href='?pagina=$i' class='$active'>$i</a>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Barra de búsqueda oculta -->
<script src="./JS/barradebusqueda.js"></script>
<script src="./JS/eliminarRegistro.js"></script>
<script src="./JS/editarRegistros.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include("./template/footer.php"); ?>
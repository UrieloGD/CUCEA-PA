<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener el nombre y el ID del departamento del usuario desde la sesión o desde el parámetro GET
$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : $_SESSION['Departamento_ID'];

// Obtener el nombre del departamento usando el ID
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento = $row_departamento['Departamentos'];

// Número de registros por página
$registros_por_pagina = 50;

// Determinar la página actual
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Calcular el offset para la consulta SQL
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

// Consulta SQL para obtener los datos de la tabla correspondiente al departamento
$sql = "SELECT * FROM `$tabla_departamento` WHERE Departamento_ID = $departamento_id LIMIT $registros_por_pagina OFFSET $offset";
$result = mysqli_query($conexion, $sql);

// Calcular el total de registros en la tabla correspondiente al departamento
$total_registros = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM `$tabla_departamento` WHERE Departamento_ID = $departamento_id"));

// Calcular el total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<title>Bases de datos</title>
<link rel="stylesheet" href="./CSS/basesdedatos.css">

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Data - <?php echo $departamento; ?></h3>
        </div>
        <div class="iconos-container">
            <div class="icono-buscador" id="icono-buscador">
                <i class="fa fa-search" aria-hidden="true"></i>
            </div>
            <div class="barra-buscador" id="barra-buscador" style="display: none;">
                <input type="text" id="input-buscador" placeholder="Buscar...">
            </div>
            <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                <i class="fa fa-add" aria-hidden="true"></i>
            </div>
            <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
            <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </div>
            <div class="icono-buscador" id="icono-descargar" onclick="descargarExcel()">
                <i class="fa fa-download" aria-hidden="true"></i>
            </div>
        </div>
    </div>
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
                <th>S</th>
                <th>D</th>
                <th>HORA INI</th>
                <th>HORA FIN</th>
                <th>EDIF</th>
                <th>AULA</th>
            </tr>
            <?php
            // Imprimir los datos en la tabla
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><input type='checkbox' name='registro_seleccionado' value='" . $row['ID_Plantilla'] . "'></td>";
                echo "<td>" . $row['ID_Plantilla'] . "</td>";
                echo "<td>" . $row['CICLO'] . "</td>";
                echo "<td>" . $row['NRC'] . "</td>";
                echo "<td>" . $row['FECHA_INI'] . "</td>";
                echo "<td>" . $row['FECHA_FIN'] . "</td>";
                echo "<td>" . $row['L'] . "</td>";
                echo "<td>" . $row['M'] . "</td>";
                echo "<td>" . $row['I'] . "</td>";
                echo "<td>" . $row['J'] . "</td>";
                echo "<td>" . $row['V'] . "</td>";
                echo "<td>" . $row['S'] . "</td>";
                echo "<td>" . $row['D'] . "</td>";
                echo "<td>" . $row['HORA_INI'] . "</td>";
                echo "<td>" . $row['HORA_FIN'] . "</td>";
                echo "<td>" . $row['EDIF'] . "</td>";
                echo "<td>" . $row['AULA'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div class="paginacion">
        <?php
        // Imprimir enlaces de paginación
        for ($i = 1; $i <= $total_paginas; $i++) {
            if ($i == $pagina_actual) {
                echo "<span>$i</span> ";
            } else {
                echo "<a href='basesdedatos.php?departamento_id=$departamento_id&pagina=$i'>$i</a> ";
            }
        }
        ?>
    </div>
</div>

<!-- Barra de búsqueda oculta -->
<script src="./JS/barradebusqueda.js"></script>
<script src="./JS/eliminarRegistro.js"></script>
<script src="./JS/editarRegistros.js"></script>
<script src="./JS/añadirRegistro.js"></script>
<script>
    function descargarExcel() {
        window.location.href = './config/descargar_excel.php';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include("./template/footer.php"); ?>
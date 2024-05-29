<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener el nombre y el ID del departamento del usuario desde la sesión
//$nombre_departamento = $_SESSION['Nombre_Departamento'];
$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : $_SESSION['Departamento_ID'];

// Obtener el nombre del departamento usando el ID
$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento_nombre = $row_departamento['Departamentos'];

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

// Consulta SQL para obtener los datos de la tabla correspondiente al departamento sin paginación
$sql = "SELECT * FROM `$tabla_departamento` WHERE Departamento_ID = $departamento_id";
$result = mysqli_query($conexion, $sql);
?>

<title>Data - <?php echo $departamento_nombre; ?></title>
<link rel="stylesheet" href="./CSS/basesdedatos.css">

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Data - <?php echo $departamento_nombre; ?></h3>
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
        <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>">
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
            <!-- Añadir nuevos registros-->
            <tr>
                <td colspan="17">
                    <div id="formulario-añadir" style="display: none;">
                        <form id="form-añadir-registro" style="display: grid; grid-template-columns: repeat(17, 1fr);">
                            <div></div> <!-- Celda vacía para la columna "ID" -->
                            <input type="text" id="ciclo" name="ciclo" placeholder="CICLO" required class="input-formulario">
                            <input type="text" id="nrc" name="nrc" placeholder="NRC" required class="input-formulario">
                            <input type="text" id="fecha_ini" name="fecha_ini" placeholder="FECHA INI" required class="input-formulario">
                            <input type="text" id="fecha_fin" name="fecha_fin" placeholder="FECHA FIN" required class="input-formulario">
                            <input type="text" id="l" name="l" placeholder="L" class="input-formulario">
                            <input type="text" id="m" name="m" placeholder="M" class="input-formulario">
                            <input type="text" id="i" name="i" placeholder="I" class="input-formulario">
                            <input type="text" id="j" name="j" placeholder="J" class="input-formulario">
                            <input type="text" id="v" name="v" placeholder="V" class="input-formulario">
                            <input type="text" id="s" name="s" placeholder="S" class="input-formulario">
                            <input type="text" id="d" name="d" placeholder="D" class="input-formulario">
                            <input type="text" id="hora_ini" name="hora_ini" placeholder="HORA INI" required class="input-formulario">
                            <input type="text" id="hora_fin" name="hora_fin" placeholder="HORA FIN" required class="input-formulario">
                            <input type="text" id="edif" name="edif" placeholder="EDIF" required class="input-formulario">
                            <input type="text" id="aula" name="aula" placeholder="AULA" required class="input-formulario">
                            <div style="display: flex; flex-direction: column;">
                                <button type="button" onclick="añadirRegistro()">Añadir</button>
                                <button type="button" onclick="cerrarFormularioAñadir()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>

            <?php
            // Verificar si seß obtuvieron resultados
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
                    echo "<td>" . $row["S"] . "</td>";
                    echo "<td>" . $row["D"] . "</td>";
                    echo "<td>" . $row["HORA_INI"] . "</td>";
                    echo "<td>" . $row["HORA_FIN"] . "</td>";
                    echo "<td>" . $row["EDIF"] . "</td>";
                    echo "<td>" . $row["AULA"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='17'>No hay datos disponibles</td></tr>";
            }
            // Cerrar la conexión a la base de datos
            mysqli_close($conexion);
            ?>
        </table>
    </div>
</div>

<!-- Barra de búsqueda oculta -->
<script src="./JS/barradebusqueda.js"></script>
<script src="./JS/eliminarRegistro.js"></script>
<script src="./JS/editarRegistros.js"></script>
<script src="./JS/añadirRegistro.js"></script>
<script>
    function descargarExcel() {
        var departamento_id = document.getElementById('departamento_id').value;
        window.location.href = './config/descargar_excel.php?departamento_id=' + departamento_id;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include("./template/footer.php"); ?>
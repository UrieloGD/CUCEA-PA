<?php include './template/header.php' ?>
<?php include './template/navbar.php' ?>
<?php
include './config/db.php';

$departamento_id = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : $_SESSION['Departamento_ID'];

$sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];
$departamento_nombre = $row_departamento['Departamentos'];

$tabla_departamento = "Data_" . $nombre_departamento;

$sql = "SELECT * FROM $tabla_departamento WHERE Departamento_ID = $departamento_id";
$result = mysqli_query($conexion, $sql);
?>

<title>Data - <?php echo $departamento_nombre; ?></title>
<link rel="stylesheet" href="./CSS/basesdedatos.css">

<style>
    .modal input, .modal select {
        border-radius: 5px;
        padding: 5px;
        border: 1px solid #ccc;
    }
</style>

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="encabezado-izquierda" style="display: flex; align-items: center;">
            <div class="barra-buscador" id="barra-buscador">
                <div class="icono-buscador" id="icono-buscador">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
                <input type="text" id="input-buscador" placeholder="Buscar...">
            </div>
        </div>
        <div class="encabezado-centro">
            <h3>Data - <?php echo $departamento_nombre; ?></h3>
        </div>
        <div class="encabezado-derecha">
            <div class="iconos-container">
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarPopupColumnas()">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    <div id="popup-columnas">
        <h3>Selecciona las columnas a descargar</h3>
        <div id="opciones-columnas"></div>
        <button onclick="descargarExcelSeleccionado()">Descargar</button>
        <button onclick="cerrarPopupColumnas()">Cancelar</button>
    </div>
    <div class="Tabla">
        <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>">
        <table id="tabla-datos">
            <tr>
                <th></th>
                <th>ID</th>
                <th>CICLO</th>
                <th>CRN</th>
                <th>MATERIA</th>
                <th>CVE MATERIA</th>
                <th>SECCIÓN</th>
                <th>NIVEL</th>
                <th>NIVEL TIPO</th>
                <th>TIPO</th>
                <th>C. MIN</th>
                <th>H. TOTALES</th>
                <th>STATUS</th>
                <th>TIPO CONTRATO</th>
                <th>CÓDIGO</th>
                <th>NOMBRE PROFESOR</th>
                <th>CATEGORIA</th>
                <th>DESCARGA</th>
                <th>CÓDIGO DESCARGA</th>
                <th>NOMBRE DESCARGA</th>
                <th>NOMBRE DEFINITIVO</th>
                <th>TITULAR</th>
                <th>HORAS</th>
                <th>CÓDIGO DEPENDENCIA</th>
                <th>L</th>
                <th>M</th>
                <th>I</th>
                <th>J</th>
                <th>V</th>
                <th>S</th>
                <th>D</th>
                <th>DÍA PRESENCIAL</th>
                <th>DÍA VIRTUAL</th>
                <th>MODALIDAD</th>
                <th>FECHA INICIAL</th>
                <th>FECHA FINAL</th>
                <th>HORA INICIAL</th>
                <th>HORA FINAL</th>
                <th>MÓDULO</th>
                <th>AULA</th>
                <th>CUPO</th>
                <th>OBSERVACIONES</th>
                <th>EXTRAORDINARIO</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . $row["ID_Plantilla"] . "'></td>";
                    echo "<td>" . $row["ID_Plantilla"] . "</td>";
                    echo "<td>" . $row["CICLO"] . "</td>";
                    echo "<td>" . $row["CRN"] . "</td>";
                    echo "<td>" . $row["MATERIA"] . "</td>";
                    echo "<td>" . $row["CVE_MATERIA"] . "</td>";
                    echo "<td>" . $row["SECCION"] . "</td>";
                    echo "<td>" . $row["NIVEL"] . "</td>";
                    echo "<td>" . $row["NIVEL_TIPO"] . "</td>";
                    echo "<td>" . $row["TIPO"] . "</td>";
                    echo "<td>" . $row["C_MIN"] . "</td>";
                    echo "<td>" . $row["H_TOTALES"] . "</td>";
                    echo "<td>" . $row["ESTATUS"] . "</td>";
                    echo "<td>" . $row["TIPO_CONTRATO"] . "</td>";
                    echo "<td>" . $row["CODIGO_PROFESOR"] . "</td>";
                    echo "<td>" . $row["NOMBRE_PROFESOR"] . "</td>";
                    echo "<td>" . $row["CATEGORIA"] . "</td>";
                    echo "<td>" . $row["DESCARGA"] . "</td>";
                    echo "<td>" . $row["CODIGO_DESCARGA"] . "</td>";
                    echo "<td>" . $row["NOMBRE_DESCARGA"] . "</td>";
                    echo "<td>" . $row["NOMBRE_DEFINITIVO"] . "</td>";
                    echo "<td>" . $row["TITULAR"] . "</td>";
                    echo "<td>" . $row["HORAS"] . "</td>";
                    echo "<td>" . $row["CODIGO_DEPENDENCIA"] . "</td>";
                    echo "<td>" . $row["L"] . "</td>";
                    echo "<td>" . $row["M"] . "</td>";
                    echo "<td>" . $row["I"] . "</td>";
                    echo "<td>" . $row["J"] . "</td>";
                    echo "<td>" . $row["V"] . "</td>";
                    echo "<td>" . $row["S"] . "</td>";
                    echo "<td>" . $row["D"] . "</td>";
                    echo "<td>" . $row["DIA_PRESENCIAL"] . "</td>";
                    echo "<td>" . $row["DIA_VIRTUAL"] . "</td>";
                    echo "<td>" . $row["MODALIDAD"] . "</td>";
                    echo "<td>" . $row["FECHA_INICIAL"] . "</td>";
                    echo "<td>" . $row["FECHA_FINAL"] . "</td>";
                    echo "<td>" . $row["HORA_INICIAL"] . "</td>";
                    echo "<td>" . $row["HORA_FINAL"] . "</td>";
                    echo "<td>" . $row["MODULO"] . "</td>";
                    echo "<td>" . $row["AULA"] . "</td>";
                    echo "<td>" . $row["CUPO"] . "</td>";
                    echo "<td>" . $row["OBSERVACIONES"] . "</td>";
                    echo "<td>" . $row["EXAMEN_EXTRAORDINARIO"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='43'>No hay datos disponibles</td></tr>";
            }
            mysqli_close($conexion);
            ?>
        </table>
    </div>
</div>

<!-- Modal para añadir registros -->
<div id="modal-añadir" class="modal">
    <div class="modal-content" style="max-width: 800px; margin: 5% auto;">
        <span class="close">&times;</span>
        <h2>Añadir Nuevo Registro</h2>
        <form id="form-añadir-registro" style="display: flex; flex-wrap: wrap; gap: 5px;">
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="ciclo" name="ciclo" placeholder="CICLO" required style="flex: 1;">
                <input type="text" id="crn" name="crn" placeholder="CRN" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="materia" name="materia" placeholder="MATERIA" required style="flex: 3;">
                <input type="text" id="cve_materia" name="cve_materia" placeholder="CVE MATERIA" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="seccion" name="seccion" placeholder="SECCIÓN" required style="flex: 1;">
                <input type="text" id="nivel" name="nivel" placeholder="NIVEL" required style="flex: 1;">
                <input type="text" id="nivel_tipo" name="nivel_tipo" placeholder="NIVEL TIPO" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="tipo" name="tipo" placeholder="TIPO" required style="flex: 1;">
                <input type="text" id="c_min" name="c_min" placeholder="C. MIN" required style="flex: 1;">
                <input type="text" id="h_totales" name="h_totales" placeholder="H. TOTALES" required style="flex: 1;">
                <input type="text" id="estatus" name="estatus" placeholder="STATUS" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="tipo_contrato" name="tipo_contrato" placeholder="TIPO CONTRATO" required style="flex: 1;">
                <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="CÓDIGO" required style="flex: 1;">
            </div>
            <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="NOMBRE PROFESOR" required style="width: 100%;">
            <input type="text" id="categoria" name="categoria" placeholder="CATEGORIA" required style="width: 100%;">
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="descarga" name="descarga" placeholder="DESCARGA" required style="flex: 1;">
                <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="CÓDIGO DESCARGA" required style="flex: 1;">
                <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="NOMBRE DESCARGA" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="NOMBRE DEFINITIVO" required style="flex: 1;">
                <input type="text" id="titular" name="titular" placeholder="TITULAR" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="horas" name="horas" placeholder="HORAS" required style="flex: 1;">
                <input type="text" id="codigo_dependencia" name="codigo_dependencia" placeholder="CÓDIGO DEPENDENCIA" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px; flex-wrap: wrap;">
                <input type="text" id="l" name="l" placeholder="L" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'L') this.value = '';">
                <input type="text" id="m" name="m" placeholder="M" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'M') this.value = '';">
                <input type="text" id="i" name="i" placeholder="I" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'I') this.value = '';">
                <input type="text" id="j" name="j" placeholder="J" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'J') this.value = '';">
                <input type="text" id="v" name="v" placeholder="V" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'V') this.value = '';">
                <input type="text" id="s" name="s" placeholder="S" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'S') this.value = '';">
                <input type="text" id="d" name="d" placeholder="D" style="flex: 1; min-width: 40px;" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'D') this.value = '';">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="dia_presencial" name="dia_presencial" placeholder="DÍA PRESENCIAL" required style="flex: 1;">
                <input type="text" id="dia_virtual" name="dia_virtual" placeholder="DÍA VIRTUAL" required style="flex: 1;">
                <input type="text" id="modalidad" name="modalidad" placeholder="MODALIDAD" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="fecha_inicial" name="fecha_inicial" placeholder="FECHA INICIAL" required style="flex: 1;">
                <input type="text" id="fecha_final" name="fecha_final" placeholder="FECHA FINAL" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="hora_inicial" name="hora_inicial" placeholder="HORA INICIAL" required style="flex: 1;">
                <input type="text" id="hora_final" name="hora_final" placeholder="HORA FINAL" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="modulo" name="modulo" placeholder="MÓDULO" required style="flex: 1;">
                <input type="text" id="aula" name="aula" placeholder="AULA" required style="flex: 1;">
                <input type="text" id="cupo" name="cupo" placeholder="CUPO" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; gap: 5px;">
                <input type="text" id="observaciones" name="observaciones" placeholder="OBSERVACIONES" required style="flex: 2;">
                <input type="text" id="examen_extraordinario" name="examen_extraordinario" placeholder="EXAMEN EXTRAORDINARIO" required style="flex: 1;">
            </div>
            <div style="width: 100%; display: flex; justify-content: center; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="añadirRegistro()" style="padding: 5px 10px;">Añadir</button>
                <button type="button" onclick="cerrarFormularioAñadir()" style="padding: 5px 10px;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="./JS/barradebusqueda.js"></script>
<script src="./JS/eliminarRegistro.js"></script>
<script src="./JS/editarRegistros.js"></script>
<script src="./JS/añadirRegistro.js"></script>
<script src="./JS/descargarExcel&popUp.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include("./template/footer.php"); ?>
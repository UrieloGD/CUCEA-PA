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

//$tabla_departamento = "Data_" . $nombre_departamento;
$tabla_departamento = "Coord_Per_Prof";

$sql = "SELECT * FROM $tabla_departamento WHERE Departamento_ID = $departamento_id";
$result = mysqli_query($conexion, $sql);
?>

<title>Data - <?php echo $departamento_nombre; ?></title>
<link rel="stylesheet" href="./CSS/basesdedatos.css">

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
            <h3>Hola C:</h3>
        </div>
        <div class="encabezado-derecha">
            <div class="iconos-container">
                <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()">
                    <i class="fa fa-save" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <!-- <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div> -->
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
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
                <th>CÓDIGO PROFESOR</th>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>EDAD</th>
                <th>CATEGORÍA</th>
                <th>TIPO DE PLAZA</th>
                <th>INVESTIGACIÓN/NOMBRAMIENTO/CAMBIO DE FUNCIÓN</th>
                <th>SNI</th>
                <th>A PARTIR DE CUANDO</th>
                <th>CUANDO SE VENCE</th>
                <th>HORAS DEFINITIVAS</th>
                <th>HORAS FRENTE A GRUPO</th>
                <th>HORARIOS NOMBRAMIENTO</th>
                <th>TÉLEFONO</th>
                <th>IMSS</th>
                <th>RFC</th>
                <th>CURP</th>
                <th>CORREO</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . ($row["ID_Plantilla"] ?? '') . "'></td>";
                    echo "<td>" . htmlspecialchars($row["ID"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Codigo_Profesor"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Nombre"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Apellido"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Edad"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Categoria"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Tipo_Plaza"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Investigacion_Nombramiento_Cambio_de_Funcion"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["SNI"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["A_partir_de_cuando"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Cuando_se_vence"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Horas_definitivas"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Horas_frente_grupo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Horarios_nombramiento"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Telefono"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["IMSS"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["RFC"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["CURP"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Correo"] ?? '') . "</td>";
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
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Añadir nuevo registro</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-añadir-registro">
            <div class="form-container">
                <div class="form-section">
                    <h3>Materia</h3>
                    <div class="form-row">
                        <input type="text" id="ciclo" name="ciclo" placeholder="Ciclo">
                        <input type="text" id="crn" name="crn" placeholder="CRN">
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="CVE Materia">
                    </div>
                    <div class="form-row">
                        <input type="text" id="materia" name="materia" placeholder="Materia" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nivel" name="nivel" placeholder="Nivel">
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo" name="tipo" placeholder="Tipo">
                        <input type="text" id="nivel_tipo" name="nivel_tipo" placeholder="Nivel tipo">
                        <input type="text" id="seccion" name="seccion" placeholder="Sección">
                    </div>
                    <div class="form-row">
                        <input type="text" id="c_min" name="c_min" placeholder="C. Min" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="h_totales" name="h_totales" placeholder="Horas totales" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="estatus" name="estatus" placeholder="Status">
                    </div>
                    <div class="form-row weekdays">
                        <input type="text" id="l" name="l" placeholder="L" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'L') this.value = '';">
                        <input type="text" id="m" name="m" placeholder="M" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'M') this.value = '';">
                        <input type="text" id="i" name="i" placeholder="I" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'I') this.value = '';">
                        <input type="text" id="j" name="j" placeholder="J" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'J') this.value = '';">
                        <input type="text" id="v" name="v" placeholder="V" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'V') this.value = '';">
                        <input type="text" id="s" name="s" placeholder="S" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'S') this.value = '';">
                        <input type="text" id="d" name="d" placeholder="D" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'D') this.value = '';">
                    </div>
                    <div class="form-row">
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="Día presencial">
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="Día virtual">
                        <input type="text" id="modalidad" name="modalidad" placeholder="Modalidad">
                    </div>
                    <div class="form-row">
                        <input type="text" id="fecha_inicial" name="fecha_inicial" placeholder="Fecha inicial">
                        <input type="text" id="fecha_final" name="fecha_final" placeholder="Fecha final">
                    </div>
                    <div class="form-row">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="Hora inicial" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="hora_final" name="hora_final" placeholder="Hora final" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row">
                        <input type="text" id="modulo" name="modulo" placeholder="Módulo">
                        <input type="text" id="aula" name="aula" placeholder="Aula">
                    </div>
                    <div class="form-row">
                        <input type="text" id="cupo" name="cupo" placeholder="Cupo" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="examen_extraordinario" name="examen_extraordinario" placeholder="Examen extraordinario">
                    </div>
                    <div class="form-row">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Observaciones" class="full-width">
                    </div>
                </div>
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="Código Profesor" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="Nombre profesor" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo_contrato" name="tipo_contrato" placeholder="Tipo contrato">
                        <input type="text" id="categoria" name="categoria" placeholder="Categoría">
                    </div>
                    <div class="form-row">
                        <input type="text" id="descarga" name="descarga" placeholder="Descarga" class="full-width">
                        <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="Código descarga" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="Nombre descarga" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="Nombre definitivo" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas_totales" name="horas" placeholder="Horas totales" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="titular" name="titular" placeholder="Titular">
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas" name="horas" placeholder="Horas" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="codigo_dependencia" name="codigo_dependencia" placeholder="Código dependencia">
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" onclick="añadirRegistro()">Guardar</button>
                <button type="button" onclick="cerrarFormularioAñadir()">Descartar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para editar registros -->
<div id="modal-editar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarFormularioEditar()">&times;</span>
        <h2>Editar registro</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-editar-registro">
            <input type="hidden" id="edit-id" name="id">
            <div class="form-container">
                <div class="form-section">
                    <h3>Materia</h3>
                    <div class="form-row">
                        <input type="text" id="edit-ciclo" name="ciclo" placeholder="Ciclo">
                        <input type="text" id="edit-crn" name="crn" placeholder="CRN">
                        <input type="text" id="edit-cve_materia" name="cve_materia" placeholder="CVE Materia">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-materia" name="materia" placeholder="Materia" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-nivel" name="nivel" placeholder="Nivel">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-tipo" name="tipo" placeholder="Tipo">
                        <input type="text" id="edit-nivel_tipo" name="nivel_tipo" placeholder="Nivel tipo">
                        <input type="text" id="edit-seccion" name="seccion" placeholder="Sección">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-c_min" name="c_min" placeholder="C. Min" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="edit-h_totales" name="h_totales" placeholder="Horas totales" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="edit-estatus" name="estatus" placeholder="Status">
                    </div>
                    <div class="form-row weekdays">
                        <input type="text" id="edit-l" name="l" placeholder="L" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'L') this.value = '';">
                        <input type="text" id="edit-m" name="m" placeholder="M" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'M') this.value = '';">
                        <input type="text" id="edit-i" name="i" placeholder="I" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'I') this.value = '';">
                        <input type="text" id="edit-j" name="j" placeholder="J" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'J') this.value = '';">
                        <input type="text" id="edit-v" name="v" placeholder="V" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'V') this.value = '';">
                        <input type="text" id="edit-s" name="s" placeholder="S" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'S') this.value = '';">
                        <input type="text" id="edit-d" name="d" placeholder="D" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'D') this.value = '';">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-dia_presencial" name="dia_presencial" placeholder="Día presencial">
                        <input type="text" id="edit-dia_virtual" name="dia_virtual" placeholder="Día virtual">
                        <input type="text" id="edit-modalidad" name="modalidad" placeholder="Modalidad">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-fecha_inicial" name="fecha_inicial" placeholder="Fecha inicial">
                        <input type="text" id="edit-fecha_final" name="fecha_final" placeholder="Fecha final">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-hora_inicial" name="hora_inicial" placeholder="Hora inicial" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="edit-hora_final" name="hora_final" placeholder="Hora final" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-modulo" name="modulo" placeholder="Módulo">
                        <input type="text" id="edit-aula" name="aula" placeholder="Aula">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-cupo" name="cupo" placeholder="Cupo" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="edit-examen_extraordinario" name="examen_extraordinario" placeholder="Examen extraordinario">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-observaciones" name="observaciones" placeholder="Observaciones" class="full-width">
                    </div>
                </div>
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row">
                        <input type="text" id="edit-codigo_profesor" name="codigo_profesor" placeholder="Código Profesor" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-nombre_profesor" name="nombre_profesor" placeholder="Nombre profesor" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-tipo_contrato" name="tipo_contrato" placeholder="Tipo contrato">
                        <input type="text" id="edit-categoria" name="categoria" placeholder="Categoría">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-descarga" name="descarga" placeholder="Descarga" class="full-width">
                        <input type="text" id="edit-codigo_descarga" name="codigo_descarga" placeholder="Código descarga" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-nombre_descarga" name="nombre_descarga" placeholder="Nombre descarga" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-nombre_definitivo" name="nombre_definitivo" placeholder="Nombre definitivo" class="full-width">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-horas" name="horas" placeholder="Horas totales" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="edit-titular" name="titular" placeholder="Titular">
                    </div>
                    <div class="form-row">
                        <input type="text" id="edit-codigo_dependencia" name="codigo_dependencia" placeholder="Código dependencia">
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" onclick="guardarCambios()">Guardar cambios</button>
                <button type="button" onclick="cerrarFormularioEditar()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="./JS/basesdedatos/tabla-editable.js"></script>
<script src="./JS/basesdedatos/barra-busqueda.js"></script>
<script src="./JS/basesdedatos/eliminar-registro.js"></script>
<script src="./JS/basesdedatos/editar-registros.js"></script>
<script src="./JS/basesdedatos/añadir-registro.js"></script>
<script src="./JS/basesdedatos/descargar-data-excel.js"></script>

<?php include("./template/footer.php"); ?>
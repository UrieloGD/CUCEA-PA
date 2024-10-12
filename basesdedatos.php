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

<!-- CSS de DataTables, buttons y colReorder -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.2/css/fixedColumns.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">


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
                <div class="icono-buscador" id="icono-guardar" onclick="saveAllChanges()">
                    <i class="fa fa-save" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-deshacer" onclick="undoAllChanges()">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-visibilidad">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-descargar" onclick="mostrarPopupColumnas()">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                    <i class="fa fa-add" aria-hidden="true"></i>
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
    <div class="datatable-container">
        <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>">
        <table id="tabla-datos" class="display">
            <thead>
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
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . ($row["ID_Plantilla"] ?? '') . "'></td>";
                        echo "<td>" . htmlspecialchars($row["ID_Plantilla"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CICLO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CRN"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["MATERIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CVE_MATERIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["SECCION"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NIVEL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NIVEL_TIPO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["TIPO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["C_MIN"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["H_TOTALES"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["ESTATUS"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["TIPO_CONTRATO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CODIGO_PROFESOR"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NOMBRE_PROFESOR"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CATEGORIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["DESCARGA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CODIGO_DESCARGA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NOMBRE_DESCARGA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["NOMBRE_DEFINITIVO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["TITULAR"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["HORAS"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CODIGO_DEPENDENCIA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["L"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["M"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["I"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["J"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["V"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["S"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["D"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["DIA_PRESENCIAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["DIA_VIRTUAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["MODALIDAD"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["FECHA_INICIAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["FECHA_FINAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["HORA_INICIAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["HORA_FINAL"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["MODULO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["AULA"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["CUPO"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["OBSERVACIONES"] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row["EXAMEN_EXTRAORDINARIO"] ?? '') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='43'>No hay datos disponibles</td></tr>";
                }
                mysqli_close($conexion);
                ?>
            </tbody>
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

<!-- Scripts de DataTables y extensiones -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js"></script>
<script src="https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.js"></script>
<script src="https://cdn.datatables.net/colreorder/2.0.4/js/colReorder.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.2/js/dataTables.fixedColumns.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.2/js/fixedColumns.dataTables.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
<script></script>

<script src="./JS/basesdedatos/tabla-editable.js"></script>
<script src="./JS/basesdedatos/barra-busqueda.js"></script>
<script src="./JS/basesdedatos/eliminar-registro.js"></script>
<script src="./JS/basesdedatos/editar-registros.js"></script>
<script src="./JS/basesdedatos/añadir-registro.js"></script>
<script src="./JS/basesdedatos/descargar-data-excel.js"></script>
<script src="./JS/basesdedatos/inicializar-tablas.js"></script>

<?php include("./template/footer.php"); ?>
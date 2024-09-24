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

<!-- CSS de DataTables
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"> -->

<!-- CSS de DataTables y de Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">

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
                <div class="icono-buscador" id="icono-añadir" onclick="mostrarFormularioAñadir()">
                    <i class="fa fa-add" aria-hidden="true"></i>
                </div>
                <!-- <div class="icono-buscador" id="icono-editar" onclick="editarRegistrosSeleccionados()">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div> -->
                <div class="icono-buscador" id="icono-borrar-seleccionados" onclick="eliminarRegistrosSeleccionados()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
                <!-- Icono agregado por Cass -->
                 <div class="icono-buscador" id="icono-visualizar" onclick="visualizarInformacionProfesores()">
                    <i class="fa fa-user" aria-hidden="true"></i>
                 </div>
                <!-- -->
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
    <div class="tabla-container">
        <div id="tabla-controles">
        </div>
    <div class="Tabla">
    <input type="hidden" id="departamento_id" value="<?php echo $departamento_id; ?>">
        <table id="tabla-datos" class="display">
            <thead>
                <tr>
                    <th></th>
                    <th>ID <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CICLO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CRN <span class="filter-icon">&#x1F50D;</span></th>
                    <th>MATERIA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CVE MATERIA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>SECCIÓN <span class="filter-icon">&#x1F50D;</span></th>
                    <th>NIVEL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>NIVEL TIPO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>TIPO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>C. MIN <span class="filter-icon">&#x1F50D;</span></th>
                    <th>H. TOTALES <span class="filter-icon">&#x1F50D;</span></th>
                    <th>STATUS <span class="filter-icon">&#x1F50D;</span></th>
                    <th>TIPO CONTRATO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CÓDIGO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>NOMBRE PROFESOR <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CATEGORIA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>DESCARGA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CÓDIGO DESCARGA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>NOMBRE DESCARGA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>NOMBRE DEFINITIVO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>TITULAR <span class="filter-icon">&#x1F50D;</span></th>
                    <th>HORAS <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CÓDIGO DEPENDENCIA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>L <span class="filter-icon">&#x1F50D;</span></th>
                    <th>M <span class="filter-icon">&#x1F50D;</span></th>
                    <th>I <span class="filter-icon">&#x1F50D;</span></th>
                    <th>J <span class="filter-icon">&#x1F50D;</span></th>
                    <th>V <span class="filter-icon">&#x1F50D;</span></th>
                    <th>S <span class="filter-icon">&#x1F50D;</span></th>
                    <th>D <span class="filter-icon">&#x1F50D;</span></th>
                    <th>DÍA PRESENCIAL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>DÍA VIRTUAL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>MODALIDAD <span class="filter-icon">&#x1F50D;</span></th>
                    <th>FECHA INICIAL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>FECHA FINAL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>HORA INICIAL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>HORA FINAL <span class="filter-icon">&#x1F50D;</span></th>
                    <th>MÓDULO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>AULA <span class="filter-icon">&#x1F50D;</span></th>
                    <th>CUPO <span class="filter-icon">&#x1F50D;</span></th>
                    <th>OBSERVACIONES <span class="filter-icon">&#x1F50D;</span></th>
                    <th>EXTRAORDINARIO <span class="filter-icon">&#x1F50D;</span></th>
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

<!-- Modal para visualizar información detallada del profesor by Cass -->
            <!-- Referencias tomadas de los modales anteriores -->
<div id="modal-visualizar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalVisualizar()">&times;</span>
       
        <!-- código del contenido del modal -->
        <table class="contenedor">
            <tr>
                <td class="left-column">
                    <div class="profesor-header">
                        <div class="profesor-avatar">
                            <span class="avatar-initials">FQ</span>
                        </div>
                        <div class="profesor-details">
                            <h2 id="profesor-nombre">Fabiola Quezada Limón</h2>
                            <p id="profesor-email">fabiolaquezada@academicos.udg.mx</p>
                        </div>
                    </div>
            <table class="profesor-data">
                <tr>
                    <th>Código</th>
                    <td id="profesor-codigo">123456789</td>
                </tr>
                <tr>
                    <th>Categoría</th>
                    <td id="profesor-categoria">Asignatura B</td>
                </tr>
                <tr>
                    <th>Horas asignadas</th>
                    <td id=profesor-horas" class="data-value2">39/40</td>
                </tr>
                <tr>
                    <th>Departamento</th>
                    <td id="profesor-departamento" class="data-value3">Administración</td>
                </tr>     
            </table>
        </td>

        <!-- Desarrollo Emprendedores -->
         <td class="right-column">
            <div class="class-info">
                <h3>Desarrollo de emprendedores</h3>
                <table class="class-details">
                    <tr>
                        <th>NRC</th>
                        <th>Horario</th>
                        <th>Edificio/Aula</th>
                    </tr>
                    <tr>
                        <td>141917</td>
                        <!-- <td>
                            07:00 - 8:55
                            <div class="weekdays">
                                <div class="day active">L</div>
                                <div class="day">M</div>
                                <div class="day active">I</div>
                                <div class="day">J</div>
                                <div class="day">V</div>
                                <div class="day">S</div>
                            </div>
                        </td> -->
                        <td>
                            <div class="horario-container">
                            <span class="horario-tiempo">07:00 - 8:55</span>
                            <div class="weekdays">
                            <div class="day active">L</div>
                            <div class="day">M</div>
                            <div class="day active">I</div>
                            <div class="day">J</div>
                            <div class="day">V</div>
                            <div class="day">S</div>
                        </div>
                        </div>
                        </td>
                        <td>F106</td>
                    </tr>
                    <tr>
                        <td>151917</td>
                        <td>
                            09:00 - 10:55
                            <div class="weekdays">
                                <div class="day">L</div>
                                <div class="day active">M</div>
                                <div class="day">I</div>
                                <div class="day active">J</div>
                                <div class="day">V</div>
                                <div class="day">S</div>
                            </div>
                        </td>
                        <td>G302</td>
                    </tr>
                    <tr>
                        <td>167143</td>
                    <td>
                    13:00 - 14:55
                            <div class="weekdays">
                                <div class="day">L</div>
                                <div class="day active">M</div>
                                <div class="day">I</div>
                                <div class="day active">J</div>
                                <div class="day">V</div>
                                <div class="day">S</div>
                            </div>
                        </td>
                        <td>C101</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
        
        <!-- fin contenido del modal -->
    </div>
</div>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script src="./JS/basesdedatos/inicializar-datatable.js"></script>
<script src="./JS/basesdedatos/tabla-editable.js"></script>
<script src="./JS/basesdedatos/barra-busqueda.js"></script>
<script src="./JS/basesdedatos/eliminar-registro.js"></script>
<script src="./JS/basesdedatos/editar-registros.js"></script>
<script src="./JS/basesdedatos/añadir-registro.js"></script>
<script src="./JS/basesdedatos/descargar-data-excel.js"></script>
<script src="./JS/basesdedatos/visualizar-profesores.js"></script> <!-- Llama el js de visualizar by Cass -->

<?php include("./template/footer.php"); ?>
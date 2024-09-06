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

//$tabla_departamento
$tabla_departamento = "Coord_Per_Prof";

function convertExcelDate($value)
{
    if (!is_numeric($value)) {
        return $value;
    }
    $unix_date = ($value - 25569) * 86400;
    return date("Y-m-d", $unix_date);
}

function formatDateForDisplay($mysqlDate)
{
    if (!$mysqlDate || $mysqlDate == '0000-00-00') {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $mysqlDate);
    return $date ? $date->format('d/m/Y') : '';
}

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
                <th>CODIGO</th>
                <th>PATERNO</th>
                <th>MATERNO</th>
                <th>NOMBRES</th>
                <th>NOMBRE COMPLETO </th>
                <th>SEXO</th>
                <th>DEPARTAMENTO</th>
                <th>CATEGORIA ACTUAL</th>
                <th>CATEGORIA ACTUAL</th>
                <th>HORAS FRENTE A GRUPO</th>
                <th>DIVISION</th>
                <th>TIPO DE PLAZA</th>
                <th>CAT.ACT.</th>
                <th>CARGA HORARIA</th>
                <th>HORAS DEFINITIVAS</th>
                <th>HORARIO</th>
                <th>TURNO</th>
                <th>INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION </th>
                <th>S.N.I.</th>
                <th>SIN DESDE</th>
                <th>CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR</th>
                <th>INICIO</th>
                <th>FIN</th>
                <th>2024A</th> <!-- Esto cambia cada semestre -->
                <th>TELEFONO PARTICULAR</th>
                <th>TELEFONO OFICINA O CELULAR</th>
                <th>DOMICILIO</th>
                <th>COLONIA</th>
                <th>C.P.</th>
                <th>CIUDAD</th>
                <th>ESTADO</th>
                <th>NO. AFIL. I.M.S.S.</th>
                <th>C.U.R.P.</th>
                <th>RFC</th>
                <th>LUGAR DE NACIMIENTO</th>
                <th>ESTADO CIVIL</th>
                <th>TIPO DE SANGRE</th>
                <th>FECHA NAC.</th>
                <th>EDAD</th>
                <th>NACIONALIDAD</th>
                <th>CORREO ELECTRONICO</th>
                <th>CORREOS OFICIALES</th>
                <th>ULTIMO GRADO</th>
                <th>PROGRAMA</th>
                <th>NIVEL</th>
                <th>INSTITUCION</th>
                <th>ESTADO/PAIS</th>
                <th>AÑO</th>
                <th>GDO EXP</th>
                <th>OTRO GRADO</th>
                <th>PROGRAMA</th>
                <th>NIVEL</th>
                <th>INSTITUCION</th>
                <th>ESTADO/PAIS</th>
                <th>AÑO</th>
                <th>GDO EXP</th>
                <th>OTRO GRADO</th>
                <th>PROGRAMA</th>
                <th>NIVEL</th>
                <th>INSTITUCION</th>
                <th>ESTADO/PAIS</th>
                <th>AÑO</th>
                <th>GDO EXP</th>
                <th>PROESDE 24-25</th>
                <th>A PARTIR DE</th>
                <th>FECHA DE INGRESO</th>
                <th>ANTIGÜEDAD</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . ($row["ID"] ?? '') . "'></td>";
                    echo "<td>" . htmlspecialchars($row["ID"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Codigo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Paterno"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Materno"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Nombres"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Nombre_completo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Sexo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Departamento"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Categoria_actual"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Categoria_actual_dos"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Horas_frente_grupo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Division"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Tipo_plaza"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Cat_act"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Carga_horaria"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Horas_definitivas"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Horario"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Turno"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Investigacion_nombramiento_cambio_funcion"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["SNI"] ?? '') . "</td>";
                    echo "<td>" . formatDateForDisplay($row['SIN_desde']) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Cambio_dedicacion"] ?? '') . "</td>";
                    echo "<td>" . formatDateForDisplay($row['Inicio']) . "</td>";
                    echo "<td>" . formatDateForDisplay($row['Fin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row["2024A"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Telefono_particular"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Telefono_oficina"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Domicilio"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Colonia"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["CP"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Ciudad"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Estado"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["No_imss"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["CURP"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["RFC"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Lugar_nacimiento"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Estado_civil"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Tipo_sangre"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Fecha_nacimiento"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Edad"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Nacionalidad"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Correo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Correos_oficiales"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Ultimo_grado"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Programa"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Nivel"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Institucion"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Estado_pais"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Año"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Gdo_exp"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_grado"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_programa"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_nivel"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_institucion"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_estado_pais"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_año"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_gdo_exp"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_grado_alternativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_programa_alternativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_nivel_altenrativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_institucion_alternativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_estado_pais_alternativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_año_alternativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Otro_gdo_exp_alternativo"] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Proesde_24_25"] ?? '') . "</td>";
                    echo "<td>" . formatDateForDisplay($row['A_partir_de']) . "</td>";
                    echo "<td>" . formatDateForDisplay($row['Fecha_ingreso']) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Antiguedad"] ?? '') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='70'>No hay datos disponibles</td></tr>";
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
        <h2>Añadir nuevo profesor</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-añadir-registro">
            <div class="form-container">
                <div class="form-section">
                    <h3>Datos</h3>
                    <div class="form-row">
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre">
                        <input type="text" id="apellido" name="apellido" placeholder="Apellido">
                        <input type="text" id="edad" name="edad" placeholder="Edad">
                    </div>
                    <div class="form-row">
                        <input type="text" id="codigo" name="codigo" placeholder="Código">
                        <input type="text" id="categoria" name="categoria" placeholder="Categoría">
                        <input type="text" id="tipo_plaza" name="tipo_plaza" placeholder="Tipo de Plaza">
                    </div>
                    <div class="form-row">
                        <input type="text" id="investigacion" name="investsigacion" placeholder="Investigación / Nombramiento / Cambio de función">
                    </div>
                    <div class="form-row">
                        <input type="text" id="sni" name="sni" placeholder="SNI">
                        <input type="text" id="cuando" name="cuando" placeholder="A partir de cuando">
                        <input type="text" id="vencimiento" name="vencimiento" placeholder="Cuando se vence">
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas_definitivas" name="horas_definitivas" placeholder="Horas Definitivas">
                        <input type="text" id="horas_grupo" name="horas_grupo" placeholder="Horas frente a grupo">
                        <input type="text" id="horarios_nombramiento" name="horarios_nombramiento" placeholder="Horas de nombramiento">
                    </div>
                    <div class="form-row">
                        <input type="text" id="tel" name="tel" placeholder="Télefono">
                        <input type="text" id="imss" name="imss" placeholder="IMSS">
                    </div>
                    <div class="form-row">
                        <input type="text" id="rfc" name="rfc" placeholder="RFC">
                        <input type="text" id="curp" name="curp" placeholder="CURP">
                    </div>
                    <div class="form-row">
                        <input type="email" id="email" name="email" placeholder="CORREO">
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
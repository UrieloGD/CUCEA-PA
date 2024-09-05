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
            <h3>Plantilla Académica</h3>
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
                    echo "<tr data-id='" . htmlspecialchars($row["ID"] ?? '') . "'>";
                    echo "<td><input type='checkbox' name='registros_seleccionados[]' value='" . htmlspecialchars($row["ID"] ?? '') . "'></td>";
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
        <h2>Registrar nuevo profesor</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-añadir-registro">
            <div class="form-container">
                <div class="form-section">
                    <h3>Datos</h3>
                    <div class="form-row">
                        <input type="number" id="codigo" name="codigo" placeholder="Código">
                        <input type="text" id="paterno" name="paterno" placeholder="Paterno">
                        <input type="text" id="materno" name="materno" placeholder="Materno">
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-row">
                        <input type="text" id="completo" name="completo" placeholder="Nomre Completo">
                        <input type="text" id="sexo" name="sexo" placeholder="Sexo">
                    </div>
                    <div class="form-row">
                        <input type="text" id="departamento" name="departamento" placeholder="Departamento">
                        <input type="text" id="categoria_actual" name="categoria_actual" placeholder="Categoría Actual">
                        <input type="text" id="categoria_actual_dos" name="categoria_actual_dos" placeholder="Categoría Actual">
                    </div>

                    <div class="form-row">
                        <input type="text" id="horas_frente_grupo" name="horas_frente_grupo" placeholder="Horas Frente a Grupo">
                        <input type="text" id="division" name="division" placeholder="División">
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo_plaza" name="tipo_plaza" placeholder="Tipo de Plaza">
                        <input type="text" id="cat_act" name="cat_act" placeholder="CAT_ACT">
                    </div>
                    <div class="form-row">
                        <input type="text" id="carga_horaria" name="carga_horaria" placeholder="Carga Horaria">
                        <input type="text" id="horas_definitivas" name="horas_definitivas" placeholder="Horas Definitivas ">
                        <input type="text" id="horario" name="horario" placeholder="Horario">
                        <input type="text" id="turno" name="turno" placeholder="Turno">
                    </div>
                    <div class="form-row">
                        <input type="text" id="investigacion" name="investsigacion" placeholder="Investigación / Nombramiento / Cambio de función">
                    </div>
                    <div class="form-row">
                        <input type="text" id="sni" name="sni" placeholder="S.N.I">
                        <input type="text" id="sin_desde" name="sin_desde" placeholder="SIN Desde">
                    </div>
                    <div class="form-row">
                        <input type="text" id="cambio_dediacion" name="cambio_dediacion" placeholder="Cambio de Dedicación">
                        <input type="text" id="inicio" name="inicio" placeholder="Inicio">
                        <input type="text" id="fin" name="fin" placeholder="Fin">
                        <input type="text" id="a_2024" name="a_2024" placeholder="2024A">
                    </div>
                    <div class="form-row">
                        <input type="text" id="telefono_particular" name="telefono_particular" placeholder="Telefono Particular">
                        <input type="text" id="telefono_oficina" name="telefono_oficina" placeholder="Telefono Oficina o Celuar">
                    </div>
                    <div class="form-row">
                        <input type="text" id="domicilio" name="domicilio" placeholder="Domicilio">
                        <input type="text" id="colonia" name="colonia" placeholder="Colonia">
                        <input type="text" id="cp" name="cp" placeholder="C.P">
                    </div>
                    <div class="form-row">
                        <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad">
                        <input type="text" id="estado" name="estado" placeholder="Estado">
                    </div>
                    <div class="form-row">
                        <input type="text" id="no_imss" name="no_imss" placeholder="NO. AFIL. I.M.S.S.">
                        <input type="text" id="curp" name="curp" placeholder="C.U.R.P">
                        <input type="text" id="rfc" name="rfc" placeholder="RFC">
                    </div>
                    <div class="form-row">
                        <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" placeholder="Lugar de Nacimiento">
                        <input type="text" id="estado_civil" name="estado_civil" placeholder="Estado Civil">
                        <input type="text" id="tipo_sangre" name="tipo_sangre" placeholder="Tipo de Sangre">
                    </div>
                    <div class="form-row">
                        <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="Fecha de Nacimiento">
                        <input type="text" id="edad" name="edad" placeholder="Edad">
                        <input type="text" id="nacionalidad" name="nacionalidad" placeholder="Nacionalidad">
                    </div>
                    <div class="form-row">
                        <input type="email" id="correo" name="correo" placeholder="Correo Electrónico">
                        <input type="email" id="correos_oficiales" name="correos_oficiales" placeholder="Correos Oficiales">
                    </div>
                    <div class="form-row">
                        <input type="text" id="ultimo_grado" name="ultimo_grado" placeholder="Último grado">
                        <input type="text" id="programa" name="programa" placeholder="Programa">
                        <input type="text" id="nivel" name="nivel" placeholder="Nivel">
                        <input type="text" id="institucion" name="institucion" placeholder="Institución">
                    </div>
                    <div class="form-row">
                        <input type="text" id="estado_pais" name="estado_pais" placeholder="Estado/País">
                        <input type="text" id="año" name="año" placeholder="Año">
                        <input type="text" id="gdo_exp" name="gdo_exp" placeholder="Gdo_Exp">
                    </div>
                    <div class="form-row">
                        <input type="text" id="otro_grado" name="otro_grado" placeholder="Último grado">
                        <input type="text" id="otro_programa" name="otro_programa" placeholder="Programa">
                        <input type="text" id="otro_nivel" name="otro_nivel" placeholder="Nivel">
                        <input type="text" id="otro_institucion" name="otro_institucion" placeholder="Institución">
                    </div>
                    <div class="form-row">
                        <input type="text" id="otro_estado_pais" name="otro_estado_pais" placeholder="Estado/País">
                        <input type="text" id="otro_año" name="otro_año" placeholder="Año">
                        <input type="text" id="otro_gdo_exp" name="otro_gdo_exp" placeholder="Gdo_Exp">
                    </div>
                    <div class="form-row">
                        <input type="text" id="otro_grado_alternativo" name="otro_grado_alternativo" placeholder="Último grado">
                        <input type="text" id="otro_programa_alternativo" name="otro_programa_alternativo" placeholder="Programa">
                        <input type="text" id="otro_nivel_alternativo" name="otro_nivel_alternativo" placeholder="Nivel">
                        <input type="text" id="otro_institucion_alternativo" name="otro_institucion_alternativo" placeholder="Institución">
                    </div>
                    <div class="form-row">
                        <input type="text" id="otro_estado_pais_alternativo" name="otro_estado_pais_alternativo" placeholder="Estado/País">
                        <input type="text" id="otro_año_alternativo" name="otro_año_alternativo" placeholder="Año">
                        <input type="text" id="otro_gdo_exp_alternativo" name="otro_gdo_exp_alternativo" placeholder="Gdo_Exp">
                    </div>
                    <div class="form-row">
                        <input type="text" id="proesde" name="proesde" placeholder="Proesde 24-25">
                        <input type="text" id="a_partir_de" name="a_partir_de" placeholder="A Partir De">
                        <input type="text" id="fecha_ingreso" name="fecha_ingreso" placeholder="Fecha de Ingreso">
                        <input type="text" id="Antiguedad" name="Antiguedad" placeholder="Antigüedad">
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

<script src="./JS/coordinacion-personal-plantilla/tabla-editable-coord.js"></script> <!-- Listo -->
<script src="./JS/basesdedatos/barra-busqueda.js"></script> <!-- Listo -->
<script src="./JS/coordinacion-personal-plantilla/eliminar-registro-coord.js"></script>
<script src="./JS/coordinacion-personal-plantilla/añadir-profesor.js"></script> <!-- Listo -->
<script src="./JS/basesdedatos/descargar-data-excel.js"></script>

<?php include("./template/footer.php"); ?>
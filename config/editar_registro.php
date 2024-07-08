<?php
// Conexión a la base de datos
include '../config/db.php';
session_start(); // Iniciar la sesión

// Obtener el ID del departamento desde el formulario
$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

// Obtener el nombre del departamento usando el ID
$sql_departamento = "SELECT Nombre_Departamento FROM Departamentos WHERE Departamento_ID = $departamento_id";
$result_departamento = mysqli_query($conexion, $sql_departamento);
$row_departamento = mysqli_fetch_assoc($result_departamento);
$nombre_departamento = $row_departamento['Nombre_Departamento'];

// Obtener los datos enviados desde el formulario
$id = isset($_POST['id']) ? $_POST['id'] : '';
$ciclo = isset($_POST['CICLO']) ? $_POST['CICLO'] : '';
$crn = isset($_POST['CRN']) ? $_POST['CRN'] : '';
$materia = isset($_POST['MATERIA']) ? $_POST['MATERIA'] : '';
$cve_materia = isset($_POST['CVE_MATERIA']) ? $_POST['CVE_MATERIA'] : '';
$seccion = isset($_POST['SECCION']) ? $_POST['SECCION'] : '';
$nivel = isset($_POST['NIVEL']) ? $_POST['NIVEL'] : '';
$nivel_tipo = isset($_POST['NIVEL_TIPO']) ? $_POST['NIVEL_TIPO'] : '';
$tipo = isset($_POST['TIPO']) ? $_POST['TIPO'] : '';
$c_min = isset($_POST['C_MIN']) ? $_POST['C_MIN'] : '';
$h_totales = isset($_POST['H_TOTALES']) ? $_POST['H_TOTALES'] : '';
$estatus = isset($_POST['ESTATUS']) ? $_POST['ESTATUS'] : '';
$tipo_contrato = isset($_POST['TIPO_CONTRATO']) ? $_POST['TIPO_CONTRATO'] : '';
$codigo_profesor = isset($_POST['CODIGO_PROFESOR']) ? $_POST['CODIGO_PROFESOR'] : '';
$nombre_profesor = isset($_POST['NOMBRE_PROFESOR']) ? $_POST['NOMBRE_PROFESOR'] : '';
$categoria = isset($_POST['CATEGORIA']) ? $_POST['CATEGORIA'] : '';
$descarga = isset($_POST['DESCARGA']) ? $_POST['DESCARGA'] : '';
$codigo_descarga = isset($_POST['CODIGO_DESCARGA']) ? $_POST['CODIGO_DESCARGA'] : '';
$nombre_descarga = isset($_POST['NOMBRE_DESCARGA']) ? $_POST['NOMBRE_DESCARGA'] : '';
$nombre_definitivo = isset($_POST['NOMBRE_DEFINITIVO']) ? $_POST['NOMBRE_DEFINITIVO'] : '';
$titular = isset($_POST['TITULAR']) ? $_POST['TITULAR'] : '';
$horas = isset($_POST['HORAS']) ? $_POST['HORAS'] : '';
$codigo_dependencia = isset($_POST['CODIGO_DEPENDENCIA']) ? $_POST['CODIGO_DEPENDENCIA'] : '';
$L = isset($_POST['L']) ? $_POST['L'] : '';
$M = isset($_POST['M']) ? $_POST['M'] : '';
$I = isset($_POST['I']) ? $_POST['I'] : '';
$J = isset($_POST['J']) ? $_POST['J'] : '';
$V = isset($_POST['V']) ? $_POST['V'] : '';
$S = isset($_POST['S']) ? $_POST['S'] : '';
$D = isset($_POST['D']) ? $_POST['D'] : '';
$dia_presencial = isset($_POST['DIA_PRESENCIAL']) ? $_POST['DIA_PRESENCIAL'] : '';
$dia_virtual = isset($_POST['DIA_VIRTUAL']) ? $_POST['DIA_VIRTUAL'] : '';
$modalidad = isset($_POST['MODALIDAD']) ? $_POST['MODALIDAD'] : '';
$fecha_inicial = isset($_POST['FECHA_INICIAL']) ? $_POST['FECHA_INICIAL'] : '';
$fecha_final = isset($_POST['FECHA_FINAL']) ? $_POST['FECHA_FINAL'] : '';
$hora_inicial = isset($_POST['HORA_INICIAL']) ? $_POST['HORA_INICIAL'] : '';
$hora_final = isset($_POST['HORA_FINAL']) ? $_POST['HORA_FINAL'] : '';
$modulo = isset($_POST['MODULO']) ? $_POST['MODULO'] : '';
$aula = isset($_POST['AULA']) ? $_POST['AULA'] : '';
$cupo = isset($_POST['CUPO']) ? $_POST['CUPO'] : '';
$observaciones = isset($_POST['OBSERVACIONES']) ? $_POST['OBSERVACIONES'] : '';
$examen_extraordinario = isset($_POST['EXAMEN_EXTRAORDINARIO']) ? $_POST['EXAMEN_EXTRAORDINARIO'] : '';

// Construir el nombre de la tabla según el departamento
$tabla_departamento = "Data_" . $nombre_departamento;

// Verificar que se proporcionó un ID válido
if (!$id) {
    echo "ID de registro no proporcionado";
    exit;
}

// Consulta SQL para actualizar el registro en la base de datos
$sql = "UPDATE `$tabla_departamento` SET 
    CICLO='$ciclo', 
    CRN='$crn', 
    MATERIA='$materia', 
    CVE_MATERIA='$cve_materia', 
    SECCION='$seccion', 
    NIVEL='$nivel', 
    NIVEL_TIPO='$nivel_tipo', 
    TIPO='$tipo', 
    C_MIN='$c_min', 
    H_TOTALES='$h_totales', 
    ESTATUS='$estatus', 
    TIPO_CONTRATO='$tipo_contrato', 
    CODIGO_PROFESOR='$codigo_profesor', 
    NOMBRE_PROFESOR='$nombre_profesor', 
    CATEGORIA='$categoria', 
    DESCARGA='$descarga', 
    CODIGO_DESCARGA='$codigo_descarga', 
    NOMBRE_DESCARGA='$nombre_descarga', 
    NOMBRE_DEFINITIVO='$nombre_definitivo', 
    TITULAR='$titular', 
    HORAS='$horas', 
    CODIGO_DEPENDENCIA='$codigo_dependencia', 
    L='$L', 
    M='$M', 
    I='$I', 
    J='$J', 
    V='$V', 
    S='$S', 
    D='$D', 
    DIA_PRESENCIAL='$dia_presencial', 
    DIA_VIRTUAL='$dia_virtual', 
    MODALIDAD='$modalidad', 
    FECHA_INICIAL='$fecha_inicial', 
    FECHA_FINAL='$fecha_final', 
    HORA_INICIAL='$hora_inicial', 
    HORA_FINAL='$hora_final', 
    MODULO='$modulo', 
    AULA='$aula', 
    CUPO='$cupo', 
    OBSERVACIONES='$observaciones', 
    EXAMEN_EXTRAORDINARIO='$examen_extraordinario' 
WHERE ID_Plantilla='$id' AND Departamento_ID='$departamento_id'";

if (mysqli_query($conexion, $sql)) {
    echo "Registro actualizado correctamente";
} else {
    echo "Error al actualizar el registro: " . mysqli_error($conexion);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);

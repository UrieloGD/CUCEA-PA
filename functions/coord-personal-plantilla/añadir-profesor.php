<?php
include './../../config/db.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Codigo'])) {
    die(json_encode(["success" => false, "message" => "Usuario no autenticado."]));
}

// Preparar la consulta SQL
$sql = "INSERT INTO Coord_Per_Prof (
    Codigo, Paterno, Materno, Nombres, Nombre_completo, Sexo, Departamento,
    Categoria_actual, Categoria_actual_dos, Horas_frente_grupo, Division, Tipo_plaza, Cat_act,
    Carga_horaria, Horas_definitivas, Horario, Turno, Investigacion_nombramiento_cambio_funcion,
    SNI, SIN_desde, Cambio_dedicacion, Inicio, Fin, `2024A`, Telefono_particular, Telefono_oficina,
    Domicilio, Colonia, CP, Ciudad, Estado, No_imss, CURP, RFC, Lugar_nacimiento, Estado_civil,
    Tipo_sangre, Fecha_nacimiento, Edad, Nacionalidad, Correo, Correos_oficiales, Ultimo_grado,
    Programa, Nivel, Institucion, Estado_pais, Año, Gdo_exp, Otro_grado, Otro_programa,
    Otro_nivel, Otro_institucion, Otro_estado_pais, Otro_año, Otro_gdo_exp,
    Otro_grado_alternativo, Otro_programa_alternativo, Otro_nivel_altenrativo,
    Otro_institucion_alternativo, Otro_estado_pais_alternativo, Otro_año_alternativo,
    Otro_gdo_exp_alternativo, Proesde_24_25, A_partir_de, Fecha_ingreso, Antiguedad
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die(json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]));
}

// Vincular parámetros
$stmt->bind_param(
    "sssssssssissssisssssssssiissississssssissssssssissssssissssssisssss",
    $_POST['codigo'],
    $_POST['paterno'],
    $_POST['materno'],
    $_POST['nombre'],
    $_POST['completo'],
    $_POST['sexo'],
    $_POST['departamento'],
    $_POST['categoria_actual'],
    $_POST['categoria_actual_dos'],
    $_POST['horas_frente_grupo'],
    $_POST['division'],
    $_POST['tipo_plaza'],
    $_POST['cat_act'],
    $_POST['carga_horaria'],
    $_POST['horas_definitivas'],
    $_POST['horario'],
    $_POST['turno'],
    $_POST['investigacion'],
    $_POST['sni'],
    $_POST['sin_desde'],
    $_POST['cambio_dediacion'],
    $_POST['inicio'],
    $_POST['fin'],
    $_POST['a_2024'],
    $_POST['telefono_particular'],
    $_POST['telefono_oficina'],
    $_POST['domicilio'],
    $_POST['colonia'],
    $_POST['cp'],
    $_POST['ciudad'],
    $_POST['estado'],
    $_POST['no_imss'],
    $_POST['curp'],
    $_POST['rfc'],
    $_POST['lugar_nacimiento'],
    $_POST['estado_civil'],
    $_POST['tipo_sangre'],
    $_POST['fecha_nacimiento'],
    $_POST['edad'],
    $_POST['nacionalidad'],
    $_POST['correo'],
    $_POST['correos_oficiales'],
    $_POST['ultimo_grado'],
    $_POST['programa'],
    $_POST['nivel'],
    $_POST['institucion'],
    $_POST['estado_pais'],
    $_POST['año'],
    $_POST['gdo_exp'],
    $_POST['otro_grado'],
    $_POST['otro_programa'],
    $_POST['otro_nivel'],
    $_POST['otro_institucion'],
    $_POST['otro_estado_pais'],
    $_POST['otro_año'],
    $_POST['otro_gdo_exp'],
    $_POST['otro_grado_alternativo'],
    $_POST['otro_programa_alternativo'],
    $_POST['otro_nivel_alternativo'],
    $_POST['otro_institucion_alternativo'],
    $_POST['otro_estado_pais_alternativo'],
    $_POST['otro_año_alternativo'],
    $_POST['otro_gdo_exp_alternativo'],
    $_POST['proesde'],
    $_POST['a_partir_de'],
    $_POST['fecha_ingreso'],
    $_POST['Antiguedad']
);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registro añadido correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error añadiendo registro: " . $stmt->error]);
}

$stmt->close();
$conexion->close();

<?php
include './../../config/db.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Codigo'])) {
    die(json_encode(["success" => false, "message" => "Usuario no autenticado."]));
}

$papelera = "ACTIVO";

// Obtener el ID del departamento
$departamento_id = isset($_POST['departamento_id']) ? $_POST['departamento_id'] : '';

if (empty($departamento_id)) {
    die(json_encode(["success" => false, "message" => "ID de departamento no proporcionado."]));
}

// Obtener el nombre del departamento
$sql_departamento = "SELECT Departamentos FROM departamentos WHERE Departamento_ID = ?";
$stmt = $conexion->prepare($sql_departamento);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$result_departamento = $stmt->get_result();
$row_departamento = $result_departamento->fetch_assoc();
$nombre_departamento = $row_departamento['Departamentos'];
$stmt->close();

// Construir el nombre de la tabla
$tabla_departamento = "data_" . str_replace(' ', '_', $nombre_departamento);

// Preparar la consulta SQL
$sql = "INSERT INTO `$tabla_departamento` (
    Departamento_ID, CICLO, CRN, MATERIA, CVE_MATERIA, SECCION, NIVEL, NIVEL_TIPO, TIPO,
    C_MIN, H_TOTALES, ESTATUS, TIPO_CONTRATO, CODIGO_PROFESOR, NOMBRE_PROFESOR,
    CATEGORIA, DESCARGA, CODIGO_DESCARGA, NOMBRE_DESCARGA, NOMBRE_DEFINITIVO,
    TITULAR, HORAS, CODIGO_DEPENDENCIA, L, M, I, J, V, S, D, DIA_PRESENCIAL,
    DIA_VIRTUAL, MODALIDAD, FECHA_INICIAL, FECHA_FINAL, HORA_INICIAL, HORA_FINAL,
    MODULO, AULA, CUPO, OBSERVACIONES, EXAMEN_EXTRAORDINARIO, PAPELERA
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die(json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]));
}

// Vincular parámetros
$stmt->bind_param("issssssssssssssssssssssssssssssssssssssssss", 
    $departamento_id, 
    $_POST['ciclo'],
    $_POST['crn'],
    $_POST['materia'],
    $_POST['cve_materia'],
    $_POST['seccion'],
    $_POST['nivel'],
    $_POST['nivel_tipo'],
    $_POST['tipo'],
    $_POST['c_min'],
    $_POST['h_totales'],
    $_POST['estatus'],
    $_POST['tipo_contrato'],
    $_POST['codigo_profesor'],
    $_POST['nombre_profesor'],
    $_POST['categoria'],
    $_POST['descarga'],
    $_POST['codigo_descarga'],
    $_POST['nombre_descarga'],
    $_POST['nombre_definitivo'],
    $_POST['titular'],
    $_POST['horas'],
    $_POST['codigo_dependencia'],
    $_POST['l'],
    $_POST['m'],
    $_POST['i'],
    $_POST['j'],
    $_POST['v'],
    $_POST['s'],
    $_POST['d'],
    $_POST['dia_presencial'],
    $_POST['dia_virtual'],
    $_POST['modalidad'],
    $_POST['fecha_inicial'],
    $_POST['fecha_final'],
    $_POST['hora_inicial'],
    $_POST['hora_final'],
    $_POST['modulo'],
    $_POST['aula'],
    $_POST['cupo'],
    $_POST['observaciones'],
    $_POST['examen_extraordinario'],
    $papelera
);

try {
    // Ejecutar la consulta
    if ($stmt->execute()) {
        $stmt->close();
        
        // Si el usuario es administrador (ROL 0), enviar notificación
        if ($_SESSION['Rol_ID'] == 0) {
            try {
                // Creamos una notificación a nivel de departamento en lugar de usuario específico
                // basándonos en cómo se muestran las notificaciones en obtener-notificaciones.php
                $mensaje = "El administrador ha añadido un nuevo registro a la base de datos: " . 
                           $_POST['materia'] . " (CRN: " . $_POST['crn'] . ")";
                
                // Insertar la notificación directamente para el departamento
                $sql_notificacion = "INSERT INTO notificaciones 
                                   (Tipo, Mensaje, Departamento_ID, Vista, Emisor_ID) 
                                   VALUES ('modificacion_bd', ?, ?, 0, ?)";
                
                $stmt_notificacion = $conexion->prepare($sql_notificacion);
                
                if ($stmt_notificacion === false) {
                    throw new Exception("Error preparando consulta de notificación: " . $conexion->error);
                }
                
                $stmt_notificacion->bind_param("sii", $mensaje, $departamento_id, $_SESSION['Codigo']);
                $result_notificacion = $stmt_notificacion->execute();
                
                if (!$result_notificacion) {
                    throw new Exception("Error al enviar la notificación: " . $stmt_notificacion->error);
                }
                
                $stmt_notificacion->close();
                
                // Adicionalmente, podemos registrar el evento para depuración
                error_log("Notificación enviada al departamento ID: $departamento_id");
                
            } catch (Exception $e) {
                error_log("Error al enviar notificación: " . $e->getMessage());
            }
        }
        
        echo json_encode(["success" => true, "message" => "Registro añadido correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error añadiendo registro: " . $stmt->error]);
        $stmt->close();
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al procesar: " . $e->getMessage()]);
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
}

$conexion->close();
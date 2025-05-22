<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php'; // Incluimos la función de correo
session_start();
// Establecer zona horaria
date_default_timezone_set('America/Mexico_City');

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

// Función para enviar correo al jefe de departamento
function enviarCorreoNotificacion($conexion, $departamento_id, $mensaje, $emisor_id, $detalles_materia) {
    // Obtener el correo del jefe de departamento
    $sql_jefe = "SELECT u.Codigo, u.Correo, d.Departamentos 
                 FROM usuarios u 
                 JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID
                 JOIN departamentos d ON ud.Departamento_ID = d.Departamento_ID 
                 WHERE d.Departamento_ID = ? AND u.rol_id = 1";
    
    // Usar el estilo orientado a objetos consistentemente
    $stmt_jefe = $conexion->prepare($sql_jefe);
    $stmt_jefe->bind_param("i", $departamento_id);
    $stmt_jefe->execute();
    $result_jefe = $stmt_jefe->get_result();
    $jefe = $result_jefe->fetch_assoc();
    $stmt_jefe->close();

    if ($jefe) {
        // Obtener información del administrador emisor
        $sql_emisor = "SELECT Nombre, Apellido FROM usuarios WHERE Codigo = ?";
        $stmt_emisor = $conexion->prepare($sql_emisor);
        $stmt_emisor->bind_param("i", $emisor_id);
        $stmt_emisor->execute();
        $result_emisor = $stmt_emisor->get_result();
        $emisor = $result_emisor->fetch_assoc();
        $nombre_emisor = $emisor ? $emisor['Nombre'] . ' ' . $emisor['Apellido'] : 'Un administrador';
        $stmt_emisor->close();

        // Fecha de la acción
        $fecha_accion = date('d/m/Y H:i');

        // Enviar correo electrónico
        $asunto = "Nuevo registro añadido - Programación Académica";
        $cuerpo = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                .container { width: 80%; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                .header { text-align: center; padding-bottom: 20px; }
                .header img { width: 300px; }
                .content { padding: 20px; }
                h2 { color: #2c3e50; }
                p { line-height: 1.5; color: #333; }
                .info { margin: 20px 0; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #3498db; }
                .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                </div>
                <div class='content'>
                    <h2>Notificación de nuevo registro</h2>
                    <p>{$mensaje}</p>
                    <p><strong>Departamento:</strong> {$jefe['Departamentos']}</p>
                    <p><strong>Acción realizada por:</strong> {$nombre_emisor}</p>
                    <p><strong>Fecha y hora:</strong> {$fecha_accion}</p>
                    <div class='info'>
                        <p><strong>Detalles del nuevo registro:</strong></p>
                        <p><strong>Materia:</strong> {$detalles_materia['materia']}</p>
                        <p><strong>CRN:</strong> {$detalles_materia['crn']}</p>
                        <p><strong>Clave de materia:</strong> {$detalles_materia['cve_materia']}</p>
                        <p><strong>Profesor:</strong> {$detalles_materia['profesor']}</p>
                        <p><strong>Código del profesor:</strong> {$detalles_materia['codigo_profesor']}</p>
                    </div>
                    <p>Por favor, ingrese al sistema para ver el registro completo.</p>
                </div>
                <div class='footer'>
                    <p>Centro para la Sociedad Digital</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $result = enviarCorreo($jefe['Correo'], $asunto, $cuerpo);
        if ($result) {
            error_log("Correo enviado exitosamente al jefe del departamento {$jefe['Departamentos']}");
            return true;
        } else {
            error_log("Error al enviar correo al jefe del departamento {$jefe['Departamentos']}");
            return false;
        }
    } else {
        error_log("No se encontró jefe de departamento para el Departamento_ID: $departamento_id");
        return false;
    }
}

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
                
                // Datos para el correo
                $detalles_materia = [
                    'materia' => $_POST['materia'],
                    'crn' => $_POST['crn'],
                    'cve_materia' => $_POST['cve_materia'],
                    'profesor' => $_POST['nombre_profesor'],
                    'codigo_profesor' => $_POST['codigo_profesor']
                ];
                
                // Enviar correo electrónico
                $correo_enviado = enviarCorreoNotificacion($conexion, $departamento_id, $mensaje, $_SESSION['Codigo'], $detalles_materia);
                
                // Registrar el evento para depuración
                error_log("Notificación enviada para el departamento ID: $departamento_id, correo enviado: " . ($correo_enviado ? "Sí" : "No"));
            } catch (Exception $e) {
                error_log("Error al procesar notificación: " . $e->getMessage());
                // Continuar con la respuesta exitosa aunque falle la notificación
            }
        }
        
        // Guardar el registro en la tabla de historial
        try {
            $accion = "INSERTAR";
            $descripcion = "Se ha añadido un nuevo registro con CRN: " . $_POST['crn'] . " en la materia: " . $_POST['materia'];
            $usuario = $_SESSION['Codigo'];
            $fecha = date('Y-m-d H:i:s');
            
            $sql_historial = "INSERT INTO historial_cambios (Accion, Descripcion, Usuario_ID, Fecha, Departamento_ID) 
                             VALUES (?, ?, ?, ?, ?)";
            
            $stmt_historial = $conexion->prepare($sql_historial);
            
            if ($stmt_historial === false) {
                throw new Exception("Error preparando consulta de historial: " . $conexion->error);
            }
            
            $stmt_historial->bind_param("ssisi", $accion, $descripcion, $usuario, $fecha, $departamento_id);
            $stmt_historial->execute();
            $stmt_historial->close();
            
        } catch (Exception $e) {
            error_log("Error al registrar en historial: " . $e->getMessage());
            // Continuar con la respuesta exitosa aunque falle el registro en historial
        }
        
        // Devolver respuesta exitosa
        echo json_encode([
            "success" => true, 
            "message" => "Registro añadido correctamente.",
            "departamento" => $nombre_departamento,
            "crn" => $_POST['crn']
        ]);
        
    } else {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
} catch (Exception $e) {
    // En caso de error, devolvemos un mensaje de error
    echo json_encode([
        "success" => false, 
        "message" => "Error al añadir el registro: " . $e->getMessage()
    ]);
}

// Cerrar la conexión
$conexion->close();
?>
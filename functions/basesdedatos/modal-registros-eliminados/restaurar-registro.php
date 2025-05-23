<?php
// Archivo: restaurar-registro.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
header('Content-Type: application/json');

require_once './../../../config/db.php';
// Modificar la ruta para usar una ruta absoluta y asegurarnos que siempre encuentra email_functions.php
$rootDir = dirname(dirname(dirname(__DIR__))); // Sube tres niveles desde el directorio actual
require_once $rootDir . '/functions/notificaciones-correos/email_functions.php';

// Para depuración
$debug_info = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'request' => file_get_contents('php://input'),
    'session' => $_SESSION
];

// Comprobar si hay datos en POST
if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Intentar recuperar datos de php://input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if ($data && isset($data['id'])) {
        $_POST['id'] = $data['id'];
    }
}

// Validar sesión y parámetros
if (!isset($_SESSION['Departamento_ID']) && !isset($_POST['departamento_id'])) {
    error_log("Departamento_ID no disponible: " . json_encode($_SESSION));
    echo json_encode(['success' => false, 'message' => 'Departamento_ID requerido', 'debug' => $debug_info]);
    exit();
}

// Función para enviar correo al jefe de departamento
function enviarCorreoNotificacionRestauracion($conexion, $departamento_id, $mensaje, $emisor_id, $id_registro, $departamento_nombre)
{
    // Obtener el correo del jefe de departamento
    $sql_jefe = "SELECT u.Codigo, u.Correo, u.Nombre, u.Apellido
                 FROM usuarios u 
                 JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID
                 WHERE ud.Departamento_ID = ? AND u.rol_id = 1
                 LIMIT 1";
    $stmt = mysqli_prepare($conexion, $sql_jefe);
    mysqli_stmt_bind_param($stmt, "i", $departamento_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $jefe = mysqli_fetch_assoc($result);

    if ($jefe) {
        // Obtener información del administrador emisor
        $sql_emisor = "SELECT Nombre, Apellido FROM usuarios WHERE Codigo = ?";
        $stmt_emisor = mysqli_prepare($conexion, $sql_emisor);
        mysqli_stmt_bind_param($stmt_emisor, "i", $emisor_id);
        mysqli_stmt_execute($stmt_emisor);
        $result_emisor = mysqli_stmt_get_result($stmt_emisor);
        $emisor = mysqli_fetch_assoc($result_emisor);
        $nombre_emisor = $emisor ? $emisor['Nombre'] . ' ' . $emisor['Apellido'] : 'Un administrador';

        // Fecha de la acción
        $fecha_accion = date('d/m/Y H:i');

        // Enviar correo electrónico
        $asunto = "Restauración de registro - Programación Académica";
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
                .changes { margin: 20px 0; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #3498db; }
                .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                </div>
                <div class='content'>
                    <h2>Notificación de restauración de registro</h2>
                    <p>{$mensaje}</p>
                    <p><strong>Departamento:</strong> {$departamento_nombre}</p>
                    <p><strong>Acción realizada por:</strong> {$nombre_emisor}</p>
                    <p><strong>Fecha y hora:</strong> {$fecha_accion}</p>
                    <div class='changes'>
                        <p><strong>Detalles:</strong></p>
                        <p>Se ha restaurado el registro con ID: {$id_registro} que anteriormente estaba en la papelera.</p>
                        <p>Este registro ahora está disponible nuevamente en la base de datos principal.</p>
                    </div>
                    <p>Por favor, ingrese al sistema para verificar los cambios.</p>
                </div>
                <div class='footer'>
                    <p>Centro para la Sociedad Digital</p>
                </div>
            </div>
        </body>
        </html>
        ";

        if (enviarCorreo($jefe['Correo'], $asunto, $cuerpo)) {
            error_log("Correo de restauración enviado exitosamente al jefe {$jefe['Nombre']} {$jefe['Apellido']} del departamento {$departamento_nombre}");
            return true;
        } else {
            error_log("Error al enviar correo de restauración al jefe {$jefe['Nombre']} {$jefe['Apellido']} del departamento {$departamento_nombre}");
            return false;
        }
    } else {
        error_log("No se encontró jefe de departamento para el Departamento_ID: $departamento_id");
        return false;
    }
}

try {
    $id = (int)$_POST['id'];
    // Usar departamento_id de POST si está disponible, de lo contrario usar el de la sesión
    $departamento_id = isset($_POST['departamento_id']) ? (int)$_POST['departamento_id'] : (int)$_SESSION['Departamento_ID'];

    // Obtener nombre del departamento
    $stmt = $conexion->prepare("SELECT Nombre_Departamento, Departamentos FROM departamentos WHERE Departamento_ID = ?");
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $departamento = $result->fetch_assoc();

    if (!$departamento) {
        throw new Exception('Departamento no encontrado: ' . $departamento_id);
    }

    $nombre_departamento = $departamento['Nombre_Departamento'];
    $departamento_nombre = $departamento['Departamentos'];
    $tabla = "data_" . str_replace(' ', '_', $nombre_departamento);

    // Comprobar si la tabla existe
    $result_check = $conexion->query("SHOW TABLES LIKE '$tabla'");
    if ($result_check->num_rows === 0) {
        throw new Exception("Tabla $tabla no existe");
    }

    // Actualizar registro
    $conexion->begin_transaction();
    $stmt = $conexion->prepare("UPDATE $tabla SET PAPELERA = 'activo' WHERE ID_Plantilla = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Registro no encontrado: ID=$id en tabla $tabla");
    }

    // Si el usuario es administrador (rol_id = 0) o secretaría administrativa (rol_id = 2)
    $rol_id = isset($_SESSION['Rol_ID']) ? (int)$_SESSION['Rol_ID'] : -1;

    if ($rol_id == 0 || $rol_id == 2) {
        // Modificación: Buscar jefes de departamento en la tabla usuarios_departamentos
        $query_jefe = "SELECT u.Codigo FROM usuarios u 
                      JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID 
                      WHERE ud.Departamento_ID = ? AND u.rol_id = 1 
                      LIMIT 1";

        $stmt_jefe = $conexion->prepare($query_jefe);
        $stmt_jefe->bind_param("i", $departamento_id);
        $stmt_jefe->execute();
        $result_jefe = $stmt_jefe->get_result();

        // Si encontramos al jefe del departamento
        if ($result_jefe->num_rows > 0) {
            $jefe_info = $result_jefe->fetch_assoc();
            $codigo_jefe = $jefe_info['Codigo'];

            // Asegurar que el ID del emisor esté disponible
            if (isset($_SESSION['Codigo'])) {
                $emisor_id = (int)$_SESSION['Codigo'];

                // Crear mensaje de notificación
                $mensaje = "Un administrador ha restaurado un registro en la base de datos del departamento de $departamento_nombre.";

                // Inserción con prepared statement
                // Cambiamos el tipo de notificación a "restauracion_bd" para que sea reconocido por obtener-notificaciones.php
                $tipo = "restauracion_bd";
                $fecha_actual = date('Y-m-d H:i:s');
                $vista = 0;

                $stmt_notif = $conexion->prepare("INSERT INTO notificaciones 
                                             (Tipo, Fecha, Mensaje, Vista, Usuario_ID, Emisor_ID, Departamento_ID) 
                                             VALUES (?, ?, ?, ?, ?, ?, ?)");

                $stmt_notif->bind_param("sssiiii", $tipo, $fecha_actual, $mensaje, $vista, $codigo_jefe, $emisor_id, $departamento_id);
                $result_stmt = $stmt_notif->execute();

                if (!$result_stmt) {
                    error_log("Error al insertar notificación: " . $stmt_notif->error);
                } else {
                    error_log("Notificación insertada correctamente: Jefe ID=$codigo_jefe, Departamento ID=$departamento_id");

                    // Enviar correo electrónico al jefe del departamento
                    enviarCorreoNotificacionRestauracion($conexion, $departamento_id, $mensaje, $emisor_id, $id, $departamento_nombre);
                }
            } else {
                error_log("No se encontró el código del usuario en la sesión");
            }
        } else {
            error_log("No se encontró jefe de departamento para Departamento_ID=$departamento_id");

            // Alternativa: Guardar la notificación a nivel de departamento
            if (isset($_SESSION['Codigo'])) {
                $emisor_id = (int)$_SESSION['Codigo'];
                $mensaje = "Un administrador ha restaurado un registro en la base de datos del departamento de $departamento_nombre.";
                $tipo = "restauracion_bd";
                $fecha_actual = date('Y-m-d H:i:s');
                $vista = 0;

                // Insertar notificación a nivel departamento
                $stmt_notif_dep = $conexion->prepare("INSERT INTO notificaciones 
                                         (Tipo, Fecha, Mensaje, Vista, Emisor_ID, Departamento_ID) 
                                         VALUES (?, ?, ?, ?, ?, ?)");

                $stmt_notif_dep->bind_param("sssiii", $tipo, $fecha_actual, $mensaje, $vista, $emisor_id, $departamento_id);
                $result_stmt_dep = $stmt_notif_dep->execute();

                if (!$result_stmt_dep) {
                    error_log("Error al insertar notificación a nivel departamento: " . $stmt_notif_dep->error);
                } else {
                    error_log("Notificación a nivel departamento insertada correctamente para Departamento_ID=$departamento_id");
                }
            }
        }
    }

    $conexion->commit();
    echo json_encode(['success' => true, 'message' => 'Registro restaurado correctamente']);
} catch (Exception $e) {
    if ($conexion->connect_error === null) {
        $conexion->rollback();
    }
    error_log("Error en restaurar-registro.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => $debug_info
    ]);
}

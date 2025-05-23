<?php
// Limpiar cualquier salida previa
ob_clean();

// Iniciar sesión para acceder a las variables de sesión
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Conexión a la base de datos
require_once './../../../config/db.php';

// Añadir la importación de las funciones de email
$rootDir = dirname(dirname(dirname(__DIR__))); // Sube tres niveles desde el directorio actual
require_once $rootDir . '/functions/notificaciones-correos/email_functions.php';

// Asegurar encabezado JSON limpio
header('Content-Type: application/json; charset=utf-8');

// Para depuración
$debug_info = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'request' => file_get_contents('php://input'),
    'session' => $_SESSION
];

// Función para enviar correo de notificación a coordinadores
function enviarCorreoNotificacionRestauracion($conexion, $mensaje, $emisor_id, $id_registro)
{
    // Obtener los correos de los coordinadores
    $sql_coords = "SELECT u.Codigo, u.Correo, u.Nombre, u.Apellido
                   FROM usuarios u 
                   WHERE u.rol_id = 3";
    $stmt = mysqli_prepare($conexion, $sql_coords);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $enviados = 0;
    $fallidos = 0;

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

    while ($coord = mysqli_fetch_assoc($result)) {
        // Enviar correo electrónico
        $asunto = "Restauración de registro - Coordinación";
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
                    <p><strong>Acción realizada por:</strong> {$nombre_emisor}</p>
                    <p><strong>Fecha y hora:</strong> {$fecha_accion}</p>
                    <div class='changes'>
                        <p><strong>Detalles:</strong></p>
                        <p>Se ha restaurado el registro con ID: {$id_registro} que anteriormente estaba en la papelera.</p>
                        <p>Este registro ahora está disponible nuevamente en la base de datos principal de coordinación.</p>
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

        if (enviarCorreo($coord['Correo'], $asunto, $cuerpo)) {
            error_log("Correo de restauración enviado exitosamente al coordinador {$coord['Nombre']} {$coord['Apellido']}");
            $enviados++;
        } else {
            error_log("Error al enviar correo de restauración al coordinador {$coord['Nombre']} {$coord['Apellido']}");
            $fallidos++;
        }
    }

    return ['enviados' => $enviados, 'fallidos' => $fallidos];
}

try {
    // Validar método de solicitud
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar ID
    if (!isset($_POST['ids'])) {
        throw new Exception('No se proporcionó ID');
    }

    $id = filter_var($_POST['ids'], FILTER_VALIDATE_INT);

    if ($id === false) {
        throw new Exception('ID no válido');
    }

    // Verificar si el registro existe y está en la papelera
    $checkSql = "SELECT ID FROM coord_per_prof WHERE ID = ? AND PAPELERA = 'inactivo'";
    $checkStmt = $conexion->prepare($checkSql);
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        throw new Exception('No se encontró el registro con ID ' . $id . ' en estado inactivo');
    }

    // Iniciar transacción
    $conexion->begin_transaction();

    // Actualizar registro
    $sql = "UPDATE coord_per_prof SET PAPELERA = 'activo' WHERE ID = ? AND PAPELERA = 'inactivo'";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error preparando la consulta: ' . $conexion->error);
    }

    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        throw new Exception('Error ejecutando la consulta: ' . $stmt->error);
    }

    if ($stmt->affected_rows <= 0) {
        throw new Exception('No se actualizó ningún registro. ID: ' . $id);
    }

    // Si el usuario es administrador (rol_id = 0) o secretaría administrativa (rol_id = 2)
    $rol_id = isset($_SESSION['Rol_ID']) ? (int)$_SESSION['Rol_ID'] : -1;

    if ($rol_id == 0 || $rol_id == 2) {
        // Buscar coordinadores (rol_id = 3)
        $query_coord = "SELECT Codigo FROM usuarios WHERE rol_id = 3";

        $stmt_coord = $conexion->prepare($query_coord);
        $stmt_coord->execute();
        $result_coord = $stmt_coord->get_result();

        // Si encontramos coordinadores
        if ($result_coord->num_rows > 0) {
            // Asegurar que el ID del emisor esté disponible
            if (isset($_SESSION['Codigo'])) {
                $emisor_id = (int)$_SESSION['Codigo'];

                // Crear mensaje de notificación
                $mensaje = "Un administrador ha restaurado un registro en la base de datos de coordinación.";

                // Tipo de notificación
                $tipo = "modificacion_bd";
                $fecha_actual = date('Y-m-d H:i:s');
                $vista = 0;

                // Preparar consulta para insertar notificaciones
                $stmt_notif = $conexion->prepare("INSERT INTO notificaciones 
                                         (Tipo, Fecha, Mensaje, Vista, Usuario_ID, Emisor_ID) 
                                         VALUES (?, ?, ?, ?, ?, ?)");

                $notificaciones_enviadas = 0;
                // Enviar notificación a cada coordinador
                while ($coord = $result_coord->fetch_assoc()) {
                    $coord_id = $coord['Codigo'];

                    $stmt_notif->bind_param("sssiii", $tipo, $fecha_actual, $mensaje, $vista, $coord_id, $emisor_id);
                    $result_stmt = $stmt_notif->execute();

                    if (!$result_stmt) {
                        error_log("Error al insertar notificación para coordinador ID=$coord_id: " . $stmt_notif->error);
                    } else {
                        error_log("Notificación insertada correctamente para coordinador ID=$coord_id");
                        $notificaciones_enviadas++;
                    }
                }

                // Enviar correos electrónicos a los coordinadores
                $resultado_correos = enviarCorreoNotificacionRestauracion($conexion, $mensaje, $emisor_id, $id);
                error_log("Correos enviados: {$resultado_correos['enviados']}, Fallidos: {$resultado_correos['fallidos']}");
            } else {
                error_log("No se encontró el código del usuario en la sesión");
            }
        } else {
            error_log("No se encontraron coordinadores (rol_id=3)");
        }
    }

    // Confirmar transacción
    $conexion->commit();

    // Devolver respuesta JSON limpia
    echo json_encode([
        "success" => true,
        "message" => "Registro restaurado exitosamente",
        "affected_rows" => $stmt->affected_rows,
        "id" => $id
    ]);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($conexion->connect_error === null) {
        $conexion->rollback();
    }

    // Asegurar que el error se devuelva como JSON
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "debug" => $debug_info
    ]);
} finally {
    // Cerrar declaraciones
    if (isset($checkStmt)) $checkStmt->close();
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_coord)) $stmt_coord->close();
    if (isset($stmt_notif)) $stmt_notif->close();
}
exit(); // Asegurar que no haya salida adicional

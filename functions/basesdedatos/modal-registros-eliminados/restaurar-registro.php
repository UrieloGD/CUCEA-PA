<?php
// Archivo: restaurar-registro.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
header('Content-Type: application/json');

require_once './../../../config/db.php';

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
                // Cambiamos el tipo de notificación a "modificacion_bd" para que sea reconocido por obtener-notificaciones.php
                $tipo = "modificacion_bd";
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
                $tipo = "modificacion_bd";
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
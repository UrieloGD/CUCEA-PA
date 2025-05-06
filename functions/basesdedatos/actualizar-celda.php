<?php
// Asegurarse de que los errores no se muestren en la salida
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para manejar errores y devolverlos como JSON
function handleError($errno, $errstr, $errfile, $errline) {
    $response = [
        'success' => false,
        'error' => "Error: [$errno] $errstr en $errfile en la línea $errline"
    ];
    error_log(json_encode($response));
    echo json_encode($response);
    exit;
}

// Establecer el manejador de errores
set_error_handler("handleError");

// Asegurarse de que la salida sea JSON
header('Content-Type: application/json');

// Iniciar la sesión
session_start();

include './../../config/db.php';

$response = ['success' => false, 'oldValue' => '', 'error' => ''];

try {
    if (!isset($_POST['id']) || !isset($_POST['column']) || !isset($_POST['value'])) {
        throw new Exception("Faltan datos requeridos (id, column o value)");
    }

    $id = mysqli_real_escape_string($conexion, $_POST['id']);
    $column = mysqli_real_escape_string($conexion, $_POST['column']);
    $value = mysqli_real_escape_string($conexion, $_POST['value']);
    $user_role = isset($_POST['user_role']) ? intval($_POST['user_role']) : -1;
    $department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : null;

    // Mapeo de columnas
    $columnMap = [
        'ID' => 'ID_Plantilla',
        'CICLO' => 'CICLO',
        'CRN' => 'CRN',
        'MATERIA' => 'MATERIA',
        'CVE MATERIA' => 'CVE_MATERIA',
        'SECCIÓN' => 'SECCION',
        'NIVEL' => 'NIVEL',
        'NIVEL TIPO' => 'NIVEL_TIPO',
        'TIPO' => 'TIPO',
        'C. MIN' => 'C_MIN',
        'H. TOTALES' => 'H_TOTALES',
        'STATUS' => 'ESTATUS',
        'TIPO CONTRATO' => 'TIPO_CONTRATO',
        'CÓDIGO' => 'CODIGO_PROFESOR',
        'NOMBRE PROFESOR' => 'NOMBRE_PROFESOR',
        'CATEGORIA' => 'CATEGORIA',
        'DESCARGA' => 'DESCARGA',
        'CÓDIGO DESCARGA' => 'CODIGO_DESCARGA',
        'NOMBRE DESCARGA' => 'NOMBRE_DESCARGA',
        'NOMBRE DEFINITIVO' => 'NOMBRE_DEFINITIVO',
        'TITULAR' => 'TITULAR',
        'HORAS' => 'HORAS',
        'CÓDIGO DEPENDENCIA' => 'CODIGO_DEPENDENCIA',
        'L' => 'L',
        'M' => 'M',
        'I' => 'I',
        'J' => 'J',
        'V' => 'V',
        'S' => 'S',
        'D' => 'D',
        'DÍA PRESENCIAL' => 'DIA_PRESENCIAL',
        'DÍA VIRTUAL' => 'DIA_VIRTUAL',
        'MODALIDAD' => 'MODALIDAD',
        'FECHA INICIAL' => 'FECHA_INICIAL',
        'FECHA FINAL' => 'FECHA_FINAL',
        'HORA INICIAL' => 'HORA_INICIAL',
        'HORA FINAL' => 'HORA_FINAL',
        'MÓDULO' => 'MODULO',
        'AULA' => 'AULA',
        'CUPO' => 'CUPO',
        'OBSERVACIONES' => 'OBSERVACIONES',
        'EXTRAORDINARIO' => 'EXAMEN_EXTRAORDINARIO'
    ];
    // Validación de department_id
    if ($user_role === 0) {
        if (!$department_id || $department_id <= 0) {
            throw new Exception("ID de departamento inválido para superadmin");
        }
    } else {
        if (!isset($_SESSION['Departamento_ID'])) {
            throw new Exception("No se ha establecido el Departamento_ID en la sesión");
        }
        $department_id = $_SESSION['Departamento_ID'];
    }

    // Obtener el nombre del departamento
    $sql_departamento = "SELECT `Nombre_Departamento`, `Departamentos` FROM `departamentos` WHERE `Departamento_ID` = ?";
    $stmt = $conexion->prepare($sql_departamento);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result_departamento = $stmt->get_result();

    if ($result_departamento->num_rows === 0) {
        throw new Exception("Departamento no encontrado con ID: $department_id");
    }

    $row_departamento = $result_departamento->fetch_assoc();
    
    // Validar que exista el campo Nombre_Departamento
    if (!isset($row_departamento['Nombre_Departamento'])) {
        throw new Exception("El campo Nombre_Departamento no existe en los resultados");
    }

    $nombre_departamento = str_replace(' ', '_', $row_departamento['Nombre_Departamento']);
    $tabla_departamento = "data_" . $nombre_departamento;
    $departamento_nombre = $row_departamento['Departamentos'];

    // Verificar si la tabla existe
    $sql_check_table = "SHOW TABLES LIKE '$tabla_departamento'";
    $result_check = mysqli_query($conexion, $sql_check_table);
    if (!$result_check || mysqli_num_rows($result_check) === 0) {
        throw new Exception("La tabla $tabla_departamento no existe");
    }

    // Obtener el valor antiguo
    $column_db = isset($columnMap[$column]) ? $columnMap[$column] : $column;
    $sql_old = "SELECT `$column_db` FROM `$tabla_departamento` WHERE `ID_Plantilla` = ?";
    $stmt_old = $conexion->prepare($sql_old);
    $stmt_old->bind_param("s", $id);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();

    if (!$result_old || $result_old->num_rows === 0) {
        throw new Exception("Registro no encontrado con ID: $id");
    }

    $row_old = $result_old->fetch_assoc();
    $response['oldValue'] = $row_old[$column_db];

    // Validaciones específicas por columna
    $numericColumns = [
        'CICLO' => 10,        // Máximo 10 caracteres
        'C_MIN' => 2,          // Máximo 2 dígitos
        'H_TOTALES' => 2,      // Máximo 2 dígitos
        'CODIGO_PROFESOR' => 9,// Código de 9 dígitos
        'CODIGO_DESCARGA' => 9,
        'HORA_INICIAL' => 4,   // Formato 24h -> 1330
        'HORA_FINAL' => 4,
        'CUPO' => 3            // Máximo 3 dígitos
    ];

    if (isset($numericColumns[$column_db])) {
        if ($value !== '' && !preg_match('/^\d+$/', $value)) {
            throw new Exception("$column debe ser un número entero");
        }
    }

    // Actualización
    $sql = "UPDATE `$tabla_departamento` SET `$column_db` = ? WHERE `ID_Plantilla` = ?";
    $stmt_update = $conexion->prepare($sql);
    $stmt_update->bind_param("ss", $value, $id);

    if ($stmt_update->execute()) {
        $response['success'] = true;
        
        // Lógica de notificaciones restaurada
        if ($user_role === 0 && $response['oldValue'] != $value) {
            // Obtener el ID del usuario administrador
            $usuario_admin_id = $_SESSION['Codigo'];
            
            // Crear mensaje de notificación
            $mensaje = "Un administrador ha modificado el campo '$column' del registro #$id en la base de datos del departamento de $departamento_nombre";
            
            // Insertar notificación para el departamento
            $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Departamento_ID, Emisor_ID) 
                                VALUES ('modificacion_bd', ?, ?, ?)";
            $stmt_notificacion = $conexion->prepare($sql_notificacion);
            $stmt_notificacion->bind_param("sii", $mensaje, $department_id, $usuario_admin_id);
            
            if (!$stmt_notificacion->execute()) {
                error_log("Error al crear notificación de modificación: " . $stmt_notificacion->error);
            }
            
            // También notificar al jefe del departamento
            $sql_jefe = "SELECT u.Codigo 
                         FROM usuarios u 
                         JOIN usuarios_departamentos ud ON u.Codigo = ud.Usuario_ID
                         WHERE ud.Departamento_ID = ? AND u.rol_id = 1";
            $stmt_jefe = $conexion->prepare($sql_jefe);
            $stmt_jefe->bind_param("i", $department_id);
            $stmt_jefe->execute();
            $result_jefe = $stmt_jefe->get_result();
            
            if ($row_jefe = $result_jefe->fetch_assoc()) {
                $jefe_id = $row_jefe['Codigo'];
                
                // Mensaje específico para el jefe
                $mensaje_jefe = "Un administrador ha modificado el campo '$column' del registro #$id. Valor anterior: '{$response['oldValue']}'. Nuevo valor: '$value'";
                
                $sql_notificacion_jefe = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                         VALUES ('modificacion_bd', ?, ?, ?)";
                $stmt_notificacion_jefe = $conexion->prepare($sql_notificacion_jefe);
                $stmt_notificacion_jefe->bind_param("sii", $mensaje_jefe, $jefe_id, $usuario_admin_id);
                
                if (!$stmt_notificacion_jefe->execute()) {
                    error_log("Error al crear notificación para el jefe: " . $stmt_notificacion_jefe->error);
                }
            }
        }
    } else {
        throw new Exception("Error al actualizar: " . $stmt_update->error);
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error en actualizar-celda.php: " . $e->getMessage());
}

// Cerrar conexiones
if (isset($stmt)) $stmt->close();
if (isset($stmt_old)) $stmt_old->close();
if (isset($stmt_update)) $stmt_update->close();
if (isset($conexion)) mysqli_close($conexion);

echo json_encode($response);
?>
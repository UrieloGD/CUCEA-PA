<?php
// Asegurarse de que los errores no se muestren en la salida
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para manejar errores y devolverlos como JSON
function handleError($errno, $errstr, $errfile, $errline)
{
    $response = [
        'success' => false,
        'error' => "Error: [$errno] $errstr en $errfile en la línea $errline"
    ];
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
include './../notificaciones-correos/email_functions.php'; // Incluimos la función de correo
date_default_timezone_set('America/Mexico_City');

$response = ['success' => false, 'oldValue' => '', 'error' => ''];

// Función para enviar correo a los coordinadores
function enviarCorreoModificacion($conexion, $campo, $id_registro, $valor_anterior, $valor_nuevo, $emisor_id) {
    // Obtener todos los coordinadores
    $sql_coordinadores = "SELECT Codigo, Nombre, Apellido, Correo FROM usuarios WHERE rol_id = 3";
    $result_coordinadores = mysqli_query($conexion, $sql_coordinadores);
    
    if (!$result_coordinadores) {
        error_log("Error al obtener coordinadores: " . mysqli_error($conexion));
        return false;
    }
    
    // Obtener información del usuario emisor
    $sql_emisor = "SELECT Nombre, Apellido, rol_id FROM usuarios WHERE Codigo = ?";
    $stmt_emisor = mysqli_prepare($conexion, $sql_emisor);
    mysqli_stmt_bind_param($stmt_emisor, "i", $emisor_id);
    mysqli_stmt_execute($stmt_emisor);
    $result_emisor = mysqli_stmt_get_result($stmt_emisor);
    $emisor = mysqli_fetch_assoc($result_emisor);
    
    $nombre_emisor = $emisor ? $emisor['Nombre'] . ' ' . $emisor['Apellido'] : 'Un usuario';
    $tipo_usuario = ($emisor && $emisor['rol_id'] === 0) ? "administrador" : "usuario";
    
    // Información adicional para el correo
    $fecha_accion = date('d/m/Y H:i');
    
    $correos_enviados = 0;
    
    // Enviar un correo a cada coordinador
    while ($coordinador = mysqli_fetch_assoc($result_coordinadores)) {
        // No enviar al mismo usuario que hizo el cambio si es coordinador
        if ($coordinador['Codigo'] == $emisor_id) {
            continue;
        }
        
        // Asunto y cuerpo del correo

        $asunto = "Modificación por coordinador - Programación Académica";
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
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                </div>
                <div class='content'>
                    <h2>Notificación de modificación por coordinador</h2>
                    <p>El Administrador $nombre_emisor ha modificado el campo <strong>'$campo'</strong> del registro #$id_registro en su base de datos.</p>
                    <p><strong>Fecha y hora:</strong> $fecha_accion</p>
                    <div class='changes'>
                        <p><strong>Detalles del cambio:</strong></p>
                        <table>
                            <tr>
                                <th>Campo</th>
                                <th>Valor anterior</th>
                                <th>Valor nuevo</th>
                            </tr>
                            <tr>
                                <td>{$campo}</td>
                                <td>{$valor_anterior}</td>
                                <td>{$valor_nuevo}</td>
                            </tr>
                        </table>
                        <p><strong>ID del registro:</strong> {$id_registro}</p>
                    </div>
                    <p>Por favor, ingrese al sistema para más información o si necesita revisar este cambio.</p>
                </div>
                <div class='footer'>
                    <p>Centro para la Sociedad Digital</p>
                </div>
            </div>
        </body>
        </html>";
        
        if (enviarCorreo($coordinador['Correo'], $asunto, $cuerpo)) {
            error_log("Correo enviado exitosamente al coordinador {$coordinador['Nombre']}");
            $correos_enviados++;
        } else {
            error_log("Error al enviar correo al coordinador {$coordinador['Nombre']}");
        }
    }
    
    // Si el cambio lo hizo un coordinador, notificar a los administradores
    if ($emisor && $emisor['rol_id'] == 3) {
        $sql_admins = "SELECT Codigo, Nombre, Apellido, Correo FROM usuarios WHERE rol_id = 0";
        $result_admins = mysqli_query($conexion, $sql_admins);
        
        if ($result_admins) {
            while ($admin = mysqli_fetch_assoc($result_admins)) {
                $asunto = "Modificación por coordinador - Programación Académica";
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
                        .details { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 10px 0; }
                        .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                        </div>
                        <div class='content'>
                            <h2>Notificación de modificación por coordinador</h2>
                            <p>El coordinador $nombre_emisor ha modificado el campo <strong>'$campo'</strong> del registro #$id_registro en la base de datos de Coordinación.</p>
                            <div class='details'>
                                <p><strong>Fecha y hora:</strong> $fecha_accion</p>
                                <p><strong>Campo modificado:</strong> $campo</p>
                                <p><strong>ID del registro:</strong> $id_registro</p>
                                <p><strong>Valor anterior:</strong> $valor_anterior</p>
                                <p><strong>Nuevo valor:</strong> $valor_nuevo</p>
                            </div>
                            <p>Por favor, ingrese al sistema para más información o si necesita revisar este cambio.</p>
                        </div>
                        <div class='footer'>
                            <p>Centro para la Sociedad Digital</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                if (enviarCorreo($admin['Correo'], $asunto, $cuerpo)) {
                    error_log("Correo enviado exitosamente al administrador {$admin['Nombre']}");
                    $correos_enviados++;
                } else {
                    error_log("Error al enviar correo al administrador {$admin['Nombre']}");
                }
            }
        }
    }
    
    return $correos_enviados > 0;
}

try {
    if (!isset($_POST['id']) || !isset($_POST['column']) || !isset($_POST['value'])) {
        throw new Exception("Faltan datos requeridos (id, column o value)");
    }

    $id = mysqli_real_escape_string($conexion, $_POST['id']);
    $column = mysqli_real_escape_string($conexion, $_POST['column']);
    $value = mysqli_real_escape_string($conexion, $_POST['value']);
    $user_role = isset($_POST['user_role']) ? intval($_POST['user_role']) : -1;
    $department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : null;

    // Depuración de código
    error_log("ID recibido: " . $id);
    error_log("Columna recibida: " . $column);
    error_log("Valor recibido: " . $value);
    error_log("Rol de usuario: " . $user_role);
    error_log("Departamento ID: " . $department_id);

    // Mapear los nombres de las columnas si es necesario
    $columnMap = [
        'ID' => 'ID',
        'CODIGO' => 'Codigo',
        'PATERNO' => 'Paterno',
        'MATERNO' => 'Materno',
        'NOMBRES' => 'Nombres',
        'NOMBRE COMPLETO' => 'Nombre_completo',
        'SEXO' => 'Sexo',
        'DEPARTAMENTO' => 'Departamento',
        'CATEGORIA ACTUAL' => 'Categoria_actual',
        'CATEGORIA ACTUAL' => 'Categoria_actual_dos',
        'HORAS FRENTE A GRUPO' => 'Horas_frente_grupo',
        'DIVISION' => 'Division',
        'TIPO DE PLAZA' => 'Tipo_plaza',
        'CAT.ACT.' => 'Cat_act',
        'CARGA HORARIA' => 'Carga_horaria',
        'HORAS DEFINITIVAS' => 'Horas_definitivas',
        'HORARIO' => 'Horario',
        'TURNO' => 'Turno',
        'INVESTIGADOR POR NOMBRAMIENTO O CAMBIO DE FUNCION' => 'Investigacion_nombramiento_cambio_funcion',
        'S.N.I.' => 'SNI',
        'SIN DESDE' => 'SIN_desde',
        'CAMBIO DEDICACION DE PLAZA DOCENTE A INVESTIGADOR' => 'Cambio_dedicacion',
        'INICIO' => 'Inicio',
        'FIN' => 'Fin',
        '2024A' => '2024A',
        'TELEFONO PARTICULAR' => 'Telefono_particular',
        'TELEFONO OFICINA O CELULAR' => 'Telefono_oficina',
        'DOMICILIO' => 'Domicilio',
        'COLONIA' => 'Colonia',
        'C.P.' => 'CP',
        'CIUDAD' => 'Ciudad',
        'ESTADO' => 'Estado',
        'NO. AFIL. I.M.S.S.' => 'No_imss',
        'C.U.R.P.' => 'CURP',
        'RFC' => 'RFC',
        'LUGAR DE NACIMIENTO' => 'Lugar_nacimiento',
        'ESTADO CIVIL' => 'Estado_civil',
        'TIPO DE SANGRE' => 'Tipo_sangre',
        'FECHA NAC.' => 'Fecha_nacimiento',
        'EDAD' => 'Edad',
        'NACIONALIDAD' => 'Nacionalidad',
        'CORREO ELECTRONICO' => 'Correo',
        'CORREOS OFICIALES' => 'Correos_oficiales',
        'ULTIMO GRADO' => 'Ultimo_grado',
        'PROGRAMA' => 'Programa',
        'NIVEL' => 'Nivel',
        'INSTITUCION' => 'Institucion',
        'ESTADO/PAIS' => 'Estado_pais',
        'AÑO' => 'Año',
        'GDO EXP' => 'Gdo_exp',
        'OTRO GRADO' => 'Otro_grado',
        'PROGRAMA' => 'Otro_programa',
        'NIVEL' => 'Otro_nivel',
        'INSTITUCION' => 'Otro_institucion',
        'ESTADO/PAIS' => 'Otro_estado_pais',
        'AÑO' => 'Otro_año',
        'GDO EXP' => 'Otro_gdo_exp',
        'OTRO GRADO' => 'Otro_grado_alternativo',
        'PROGRAMA' => 'Otro_programa_alternativo',
        'NIVEL' => 'Otro_nivel_altenrativo',
        'INSTITUCION' => 'Otro_institucion_alternativo',
        'ESTADO/PAIS' => 'Otro_estado_pais_alternativo',
        'AÑO' => 'Otro_año_alternativo',
        'GDO EXP' => 'Otro_gdo_exp_alternativo',
        'PROESDE 24-25' => 'Proesde_24_25',
        'A PARTIR DE' => 'A_partir_de',
        'FECHA DE INGRESO' => 'Fecha_ingreso',
        'ANTIGÜEDAD' => 'Antiguedad'
    ];

    if (isset($columnMap[$column])) {
        $column = $columnMap[$column];
    }

    // Para el sistema de notificaciones de coordinación (no requiere department_id)
    $departamento_nombre = "Coordinación";
    
    // Identificar el rol del usuario que hace la modificación
    if (!isset($user_role)) {
        $user_role = isset($_SESSION['rol_id']) ? intval($_SESSION['rol_id']) : -1;
    }

    $numericColumns = ['Horas_frente_grupo', 'Horas_definitivas', 'Telefono_particular', 'Telefono_oficina', 'CP', 'No_imss', 'Edad', 'Año', 'Otro_año', 'Otro_año_alternativo'];

    if (in_array($column, $numericColumns)) {
        if ($value !== '' && !is_numeric($value)) {
            throw new Exception("El valor para la columna $column debe ser numérico");
        }
    }

    $tabla = 'coord_per_prof';

    // Verificar si la columna existe en la tabla
    $sql_check_column = "SHOW COLUMNS FROM `$tabla` LIKE '$column'";
    $result_check_column = mysqli_query($conexion, $sql_check_column);
    if (mysqli_num_rows($result_check_column) == 0) {
        throw new Exception("La columna $column no existe en la tabla");
    }

    // Obtener el valor antiguo antes de actualizar
    $sql_old = "SELECT `$column` FROM `$tabla` WHERE ID = ?";
    $stmt_old = $conexion->prepare($sql_old);
    $stmt_old->bind_param("s", $id);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();
    
    if (!$result_old || $result_old->num_rows === 0) {
        throw new Exception("No se encontró ningún registro con el ID proporcionado: $id");
    }
    
    $row_old = $result_old->fetch_assoc();
    $response['oldValue'] = $row_old[$column];

    // Actualizar el valor en la base de datos
    $sql = "UPDATE `$tabla` SET `$column` = ? WHERE `ID` = ?";
    $stmt_update = $conexion->prepare($sql);
    $stmt_update->bind_param("ss", $value, $id);

    if ($stmt_update->execute()) {
        $response['success'] = true;
        
        // Sistema de notificaciones
        if ($response['oldValue'] != $value) {
            // Obtener el ID del usuario que hace la modificación
            $usuario_emisor_id = $_SESSION['Codigo'] ?? 0;
            
            // Determinar tipo de usuario que hace la modificación
            $tipo_usuario = ($user_role === 0) ? "administrador" : "usuario";
            
            // Crear mensaje de notificación
            $mensaje = "Un $tipo_usuario ha modificado el campo '$column' del registro #$id en la base de datos de Coordinación";
            
            // Notificar a todos los coordinadores (rol 3)
            $sql_coordinadores = "SELECT u.Codigo 
                                 FROM usuarios u 
                                 WHERE u.rol_id = 3";
            $stmt_coordinadores = $conexion->prepare($sql_coordinadores);
            $stmt_coordinadores->execute();
            $result_coordinadores = $stmt_coordinadores->get_result();
            
            while ($coordinador = $result_coordinadores->fetch_assoc()) {
                // No notificar al mismo usuario que hizo el cambio
                if ($coordinador['Codigo'] == $usuario_emisor_id) {
                    continue;
                }
                
                $coordinador_id = $coordinador['Codigo'];
                
                // Mensaje detallado para coordinadores
                $mensaje_detallado = "Se ha modificado el campo '$column' del registro #$id. "; //Valor anterior: '{$response['oldValue']}'. Nuevo valor: '$value'"
                
                $sql_notificacion = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                    VALUES ('modificacion_bd', ?, ?, ?)";
                $stmt_notificacion = $conexion->prepare($sql_notificacion);
                $stmt_notificacion->bind_param("sii", $mensaje_detallado, $coordinador_id, $usuario_emisor_id);
                
                if (!$stmt_notificacion->execute()) {
                    error_log("Error al crear notificación para coordinador ID $coordinador_id: " . $stmt_notificacion->error);
                }
            }
            
            // Si el cambio lo hizo un administrador, notificar también a los administradores
            if ($user_role === 0) {
                $sql_admins = "SELECT u.Codigo 
                              FROM usuarios u 
                              WHERE u.rol_id = 0 AND u.Codigo != ?";
                $stmt_admins = $conexion->prepare($sql_admins);
                $stmt_admins->bind_param("i", $usuario_emisor_id);
                $stmt_admins->execute();
                $result_admins = $stmt_admins->get_result();
                
                while ($admin = $result_admins->fetch_assoc()) {
                    $admin_id = $admin['Codigo'];
                    
                    $sql_notificacion_admin = "INSERT INTO notificaciones (Tipo, Mensaje, Usuario_ID, Emisor_ID) 
                                              VALUES ('modificacion_bd', ?, ?, ?)";
                    $stmt_notificacion_admin = $conexion->prepare($sql_notificacion_admin);
                    $stmt_notificacion_admin->bind_param("sii", $mensaje, $admin_id, $usuario_emisor_id);
                    
                    if (!$stmt_notificacion_admin->execute()) {
                        error_log("Error al crear notificación para admin ID $admin_id: " . $stmt_notificacion_admin->error);
                    }
                }
            }
            
            // Enviar notificaciones por correo electrónico
            enviarCorreoModificacion($conexion, $column, $id, $response['oldValue'], $value, $usuario_emisor_id);
        }
    } else {
        throw new Exception("Error al actualizar: " . $stmt_update->error);
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error en actualizar-celda-coord.php: " . $e->getMessage());
}

// Cerrar conexiones
if (isset($stmt)) $stmt->close();
if (isset($stmt_old)) $stmt_old->close();
if (isset($stmt_update)) $stmt_update->close();
if (isset($stmt_coordinadores)) $stmt_coordinadores->close();
if (isset($stmt_notificacion)) $stmt_notificacion->close();
if (isset($stmt_admins)) $stmt_admins->close();
if (isset($stmt_notificacion_admin)) $stmt_notificacion_admin->close();
if (isset($conexion)) mysqli_close($conexion);

echo json_encode($response);
?>
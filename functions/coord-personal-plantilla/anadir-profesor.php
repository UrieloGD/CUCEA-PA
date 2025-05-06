<?php
include './../../config/db.php';
include './../notificaciones-correos/email_functions.php'; // Incluimos la función de correo
session_start();

header('Content-Type: application/json');

try {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['Codigo'])) {
        throw new Exception("Usuario no autenticado.");
    }

    // Validar campos requeridos
    $required_fields = ['codigo', 'paterno', 'materno', 'nombres'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("El campo {$field} es requerido.");
        }
    }

    // Función para convertir valores vacíos o no numéricos a 0
    function convertToIntOrZero($value) {
        return ($value === '' || $value === null) ? 0 : intval($value);
    }

    // Definir los campos que deben ser tratados como enteros
    $integer_fields = [
        'Horas_frente_grupo', 
        'Horas_definitivas', 
        'Edad', 
        'CP',
        'Año', 
        'Otro_año', 
        'Otro_año_alternativo'
    ];

    // Sanitizar entradas
    $codigo = filter_var($_POST['codigo'], FILTER_SANITIZE_NUMBER_INT);
    if (!$codigo) {
        throw new Exception("Código inválido.");
    }

    // Verificar si el código ya existe
    $check_sql = "SELECT Codigo FROM coord_per_prof WHERE Codigo = ?";
    $check_stmt = $conexion->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }

    $check_stmt->bind_param("s", $codigo);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        throw new Exception("El código ya existe en la base de datos.");
    }
    $check_stmt->close();

    // Array de columnas en el orden exacto de la consulta SQL
    $columns = [
        'Datos', 'Codigo', 'Paterno', 'Materno', 'Nombres', 'Nombre_completo', 'Departamento',
        'Categoria_actual', 'Categoria_actual_dos', 'Horas_frente_grupo', 'Division', 'Tipo_plaza',
        'Cat_act', 'Carga_horaria', 'Horas_definitivas', 'Udg_virtual_CIT', 'Horario', 'Turno',
        'Investigacion_nombramiento_cambio_funcion', 'SNI', 'SNI_desde', 'Cambio_dedicacion',
        'Telefono_particular', 'Telefono_oficina', 'Domicilio', 'Colonia', 'CP', 'Ciudad', 
        'Estado', 'No_imss', 'CURP', 'RFC', 'Lugar_nacimiento', 'Estado_civil', 'Tipo_sangre',
        'Fecha_nacimiento', 'Edad', 'Nacionalidad', 'Correo', 'Correos_oficiales', 'Ultimo_grado',
        'Programa', 'Nivel', 'Institucion', 'Estado_pais', 'Año', 'Gdo_exp', 'Otro_grado', 
        'Otro_programa', 'Otro_nivel', 'Otro_institucion', 'Otro_estado_pais', 'Otro_año', 
        'Otro_gdo_exp', 'Otro_grado_alternativo', 'Otro_programa_alternativo', 'Otro_nivel_altenrativo', 
        'Otro_institucion_alternativo', 'Otro_estado_pais_alternativo', 'Otro_año_alternativo',
        'Otro_gdo_exp_alternativo', 'Proesde_24_25', 'A_partir_de', 'Fecha_ingreso', 'Antiguedad',
        'PAPELERA'
    ];
    
    // Prepara los valores para cada columna
    $values = [];
    $types = '';

    // Array asociativo para facilitar el acceso a los datos POST
    $post_data = array_change_key_case($_POST, CASE_LOWER);

    // Mapea cada columna a su valor correspondiente de POST o a un valor predeterminado
    foreach ($columns as $column) {
        // Caso especial para el campo PAPELERA
        if($column === 'PAPELERA'){
            // Si se proporciona en POST, usa ese valor, de lo contrario usar "ACTIVO"
            if(isset($post_data['papelera'])){
                $values[] = $post_data['papelera'];
            } else {
                $values[] = 'ACTIVO'; // Valor predeterminado
            }
            $types .= 's';
            continue;
        }
        
        // Convierte nombre de columna para buscar en $_POST (cambio a minúsculas y sin guiones bajos)
        $post_key = strtolower(str_replace('_', '', $column));
        
        // Caso especial para Cambio_dedicacion
        if ($column === 'Cambio_dedicacion' && isset($post_data['cambio_dedicacion'])) {
            $values[] = $post_data['cambio_dedicacion'];
            $types .= 's';
            error_log("Asignando valor a Cambio_dedicacion: " . $post_data['cambio_dedicacion']);
            continue;
        }

        if (isset($post_data[$post_key])) {
            if (in_array($column, $integer_fields)) {
                $values[] = convertToIntOrZero($post_data[$post_key]);
                $types .= 'i';
            } else {
                $values[] = $post_data[$post_key];
                $types .= 's';
            }
        } else {
            // Si no existe en $_POST, usar valor predeterminado según el tipo
            if (in_array($column, $integer_fields)) {
                $values[] = 0;
                $types .= 'i';
            } else {
                $values[] = '';
                $types .= 's';
            }
        }
    }
    
    // Construye la parte de placeholders de la consulta SQL
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));

    // Prepara la consulta SQL
    $sql = "INSERT INTO coord_per_prof (" . implode(', ', $columns) . ") VALUES (" . $placeholders . ")";

    $stmt = $conexion->prepare($sql);
    if(!$stmt){
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }

    // Crea el array de referencias para bind_param
    $bind_params = array($types);
    foreach ($values as $i => $value) {
        $bind_params[] = &$values[$i];
    }

    // Usa call_user_func_array para bind_param
    call_user_func_array(array($stmt, 'bind_param'), $bind_params);

    // Ejecuta la consulta 
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando la consulta: " . $stmt->error);
    }

    // Si el usuario es administrador (ROL 0), enviar notificación
    if (isset($_SESSION['Rol_ID']) && $_SESSION['Rol_ID'] == 0) {
        try {
            // Crear mensaje de notificación
            $nombre_completo = $_POST['nombres'] . ' ' . $_POST['paterno'] . ' ' . $_POST['materno'];
            $mensaje = "El administrador ha añadido un nuevo profesor a su base de datos: " . 
                      $nombre_completo;
            
            // Obtener todos los coordinadores
            $sql_coordinadores = "SELECT Codigo FROM usuarios WHERE rol_id = 3";
            $result_coordinadores = mysqli_query($conexion, $sql_coordinadores);
            
            if (!$result_coordinadores) {
                throw new Exception("Error al obtener coordinadores: " . mysqli_error($conexion));
            }
            
            // Crear una notificación para cada coordinador
            while ($coordinador = mysqli_fetch_assoc($result_coordinadores)) {
                $sql_notificacion = "INSERT INTO notificaciones 
                                    (Tipo, Mensaje, Usuario_ID, Vista, Emisor_ID) 
                                    VALUES ('modificacion_bd', ?, ?, 0, ?)";
                
                $stmt_notificacion = $conexion->prepare($sql_notificacion);
                
                if ($stmt_notificacion === false) {
                    throw new Exception("Error preparando consulta de notificación: " . $conexion->error);
                }
                
                $stmt_notificacion->bind_param("sii", $mensaje, $coordinador['Codigo'], $_SESSION['Codigo']);
                $result_notificacion = $stmt_notificacion->execute();
                
                if (!$result_notificacion) {
                    throw new Exception("Error al enviar la notificación: " . $stmt_notificacion->error);
                }
                
                $stmt_notificacion->close();
            }
            
            // Enviar correo a todos los coordinadores
            enviarCorreoNotificacion($conexion, $mensaje);
            
        } catch (Exception $e) {
            error_log("Error al enviar notificación: " . $e->getMessage());
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "Registro añadido correctamente"
    ]);
} catch (Exception $e) {
    error_log("Error en añadir-profesor.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}

// Función para enviar correo a los coordinadores
function enviarCorreoNotificacion($conexion, $mensaje) {
    // Obtener todos los coordinadores
    $sql_coordinadores = "SELECT Codigo, Nombre, Correo FROM usuarios WHERE rol_id = 3";
    $result_coordinadores = mysqli_query($conexion, $sql_coordinadores);
    
    if (!$result_coordinadores) {
        error_log("Error al obtener coordinadores: " . mysqli_error($conexion));
        return false;
    }
    
    // Obtener información del administrador emisor
    $sql_emisor = "SELECT Nombre FROM usuarios WHERE Codigo = ?";
    $stmt_emisor = mysqli_prepare($conexion, $sql_emisor);
    mysqli_stmt_bind_param($stmt_emisor, "i", $_SESSION['Codigo']);
    mysqli_stmt_execute($stmt_emisor);
    $result_emisor = mysqli_stmt_get_result($stmt_emisor);
    $emisor = mysqli_fetch_assoc($result_emisor);
    $nombre_emisor = $emisor ? $emisor['Nombre'] . $emisor['Apellido'] : 'Un administrador';
    
    // Información adicional para el correo
    $fecha_accion = date('d/m/Y H:i');
    
    $correos_enviados = 0;
    
    // Enviar un correo a cada coordinador
    while ($coordinador = mysqli_fetch_assoc($result_coordinadores)) {
        // Asunto y cuerpo del correo
        $asunto = "Nuevo profesor añadido - Programación Académica";
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
                .info { color: #3498db; font-weight: bold; }
                .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                </div>
                <div class='content'>
                    <h2>Notificación de actualización de datos</h2>
                    <p class='info'>{$mensaje}</p>
                    <p><strong>Acción realizada por:</strong> {$nombre_emisor}</p>
                    <p><strong>Fecha y hora:</strong> {$fecha_accion}</p>
                    <p>Por favor, ingrese al sistema para ver los detalles completos.</p>
                </div>
                <div class='footer'>
                    <p>Centro para la Sociedad Digital</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        if (enviarCorreo($coordinador['Correo'], $asunto, $cuerpo)) {
            error_log("Correo enviado exitosamente al coordinador {$coordinador['Nombre']}");
            $correos_enviados++;
        } else {
            error_log("Error al enviar correo al coordinador {$coordinador['Nombre']}");
        }
    }
    
    return $correos_enviados > 0;
}
?>
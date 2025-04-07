<?php
include './../../config/db.php';
session_start();

header('Content-Type: application/json');

try {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['Codigo'])) {
        throw new Exception("Usuario no autenticado.");
    }

    // Validar campos requeridos
    $required_fields = ['codigo', 'paterno', 'materno', 'nombre'];
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
        'Otro_año_alternativo',
        // Añade aquí cualquier otro campo numérico de tu tabla
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

    // Preparar la consulta SQL
    $sql = "INSERT INTO coord_per_prof (
        Datos, Codigo, Paterno, Materno, Nombres, Nombre_completo, Departamento,
        Categoria_actual, Categoria_actual_dos, Horas_frente_grupo, Division, 
        Tipo_plaza, Cat_act, Carga_horaria, Horas_definitivas, Udg_virtual_CIT, Horario, 
        Turno, Investigacion_nombramiento_cambio_funcion, SNI, SNI_desde, 
        Cambio_dedicacion, Telefono_particular, 
        Telefono_oficina, Domicilio, Colonia, CP, Ciudad, Estado, No_imss, 
        CURP, RFC, Lugar_nacimiento, Estado_civil, Tipo_sangre, 
        Fecha_nacimiento, Edad, Nacionalidad, Correo, Correos_oficiales, 
        Ultimo_grado, Programa, Nivel, Institucion, Estado_pais, Año, 
        Gdo_exp, Otro_grado, Otro_programa, Otro_nivel, Otro_institucion, 
        Otro_estado_pais, Otro_año, Otro_gdo_exp, Otro_grado_alternativo, 
        Otro_programa_alternativo, Otro_nivel_altenrativo, 
        Otro_institucion_alternativo, Otro_estado_pais_alternativo, 
        Otro_año_alternativo, Otro_gdo_exp_alternativo, Proesde_24_25, 
        A_partir_de, Fecha_ingreso, Antiguedad
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }

    // Crear array de parámetros con conversión de tipos
    $params = array_map(function($key, $value) use ($integer_fields) {
        // Si el campo está en la lista de campos enteros, convertirlo
        if (in_array($key, $integer_fields)) {
            return convertToIntOrZero($value);
        }
        
        // Para otros campos, mantener el valor original
        return $value;
    }, array_keys($_POST), array_values($_POST));

    // Generar cadena de tipos de manera dinámica
    $types = array_map(function($key) use ($integer_fields) {
        // Campos en la lista de enteros se marcan con 'i'
        return in_array($key, $integer_fields) ? 'i' : 's';
    }, array_keys($_POST));
    $types_string = implode('', $types);

    // Bind parameters dinámicamente
    $bind_params = array($types_string);
    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    // Usar call_user_func_array para bind_param
    call_user_func_array(array($stmt, 'bind_param'), $bind_params);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando la consulta: " . $stmt->error);
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
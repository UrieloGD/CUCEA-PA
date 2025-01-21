<?php
require_once './config/db.php'; // Asegúrate de que la ruta es correcta para tu archivo de configuración de la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveChanges') {
    $datos = json_decode($_POST['datos'], true);

    // Limpiar y validar los datos para prevenir inyecciones SQL
    foreach ($datos as &$fila) {
        $fila = array_map(function($value) use ($conexion) {
            return $conexion->real_escape_string($value);
        }, $fila);
    }

    // Proceder con la inserción o actualización de datos
    foreach ($datos as $fila) {
        if ($fila[1] === '') { // Si el ID está vacío, es una nueva entrada
            $sql = "INSERT INTO coord_per_prof (Codigo, Paterno, Materno, Nombres, Nombre_completo, Sexo, Departamento, Categoria_actual, Categoria_actual_dos, Horas_frente_grupo, Division, Tipo_plaza, Cat_act, Carga_horaria, Horas_definitivas, Horario, Turno, Investigacion_nombramiento_cambio_funcion, SNI, SNI_desde, Cambio_dedicacion, Inicio, Fin, 2024A, Telefono_particular, Telefono_oficina, Domicilio, Colonia, CP, Ciudad, Estado, No_imss, CURP, RFC, Lugar_nacimiento, Estado_civil, Tipo_sangre, Fecha_nacimiento, Edad, Nacionalidad, Correo, Correos_oficiales, Ultimo_grado, Programa, Nivel, Institucion, Estado_pais, Año, Gdo_exp, Otro_programa, Otro_nivel, Otro_institucion, Otro_estado_pais, Otro_año, Otro_grado_alternativo, Otro_programa_alternativo, Otro_nivel_altenrativo, Otro_institucion_alternativo, Otro_estado_pais_alternativo, Otro_año_alternativo, Otro_gdo_exp_alternativo, Proesde_24_25, A_Partir_de, Fecha_ingreso, Antiguedad /* ... otras columnas ... */) 
                    VALUES (i, s, s, s, /* ... otros valores ... */)";
            $stmt = $conexion->prepare($sql);
            // Ajusta según las columnas de tu tabla
            $stmt->bind_param("ssss", $fila[1], $fila[2], $fila[3], $fila[4] /* ... otros parámetros según tu estructura ... */);
            // Aquí deberías añadir los valores para todas las columnas según la estructura de tu tabla
        } else {
            // Si el ID no está vacío, es una actualización
            $sql = "UPDATE coord_per_prof SET Codigo=?, Paterno=?, Materno=?, Nombres=?, /* ... otras columnas ... */ 
                    WHERE ID=?";
            $stmt = $conexion->prepare($sql);
            // Ajusta según las columnas de tu tabla
            $stmt->bind_param("ssssi", $fila[1], $fila[2], $fila[3], $fila[4], $fila[0] /* ... otros parámetros según tu estructura ... */);
            // Aquí deberías añadir los valores para todas las columnas según la estructura de tu tabla
        }
        $stmt->execute();
        $stmt->close();
    }

    // Respuesta al cliente
    echo json_encode(['status' => 'success', 'message' => 'Datos guardados exitosamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
?>
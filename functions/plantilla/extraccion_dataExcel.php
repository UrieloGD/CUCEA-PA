<?php
try {
    session_start();
    require './../../vendor/autoload.php';
    include './../notificaciones-correos/email_functions.php';
    include './../../config/db.php';

    // Error reporting configuration
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');

    function writeLog($message)
    {
        $timestamp = date('d/m/Y H:i:s');
        error_log("[$timestamp] $message");
    }

    function safeSubstr($string, $start, $length = null)
    {
        if ($string === null) {
            return null;
        }
        return $length === null ? substr($string, $start) : substr($string, $start, $length);
    }

    // Verificar conexión a la base de datos
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conexion->connect_error);
    }

    $inserted_records = 0;
    $message = "";

    // Verificar si se recibió un archivo
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== 0) {
        throw new Exception("No se recibió ningún archivo o hubo un error en la carga.");
    }

    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

    // Verificar datos de sesión
    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;
    $departamento_id = $_SESSION['Departamento_ID'] ?? null;

    if ($usuario_id === null) {
        throw new Exception("Usuario no autenticado.");
    }

    writeLog("Procesando archivo: $fileName");
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    $tabla_destino = 'data_' . str_replace(' ', '_', $_SESSION['Nombre_Departamento']);
    writeLog("Tabla destino: $tabla_destino");

    // Verificar si la tabla existe
    $tabla_existe = $conexion->query("SHOW TABLES LIKE '$tabla_destino'");
    if ($tabla_existe->num_rows == 0) {
        throw new Exception("La tabla $tabla_destino no existe en la base de datos.");
    }

    // Obtener y verificar los encabezados
    $headers = [];
    foreach ($sheet->getRowIterator(1, 1) as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        foreach ($cellIterator as $cell) {
            $headers[] = $cell->getValue();
        }
    }

    // Mapeo de columnas
    $columnMap = [
        'CICLO' => 'CICLO',
        'CRN' => 'CRN',
        'MATERIA' => 'MATERIA',
        'CVE. MATERIA' => 'CVE_MATERIA',
        'SECCION' => 'SECCION',
        'NIVEL' => 'NIVEL',
        'NIVEL TIPO' => 'NIVEL_TIPO',
        'TIPO' => 'TIPO',
        'C. MIN.' => 'C_MIN',
        'H. TOTALES' => 'H_TOTALES',
        'STATUS' => 'ESTATUS',
        'TIPO CONTRATO' => 'TIPO_CONTRATO',
        'CODIGO PROFESOR' => 'CODIGO_PROFESOR',
        'NOMBRE PROFESOR' => 'NOMBRE_PROFESOR',
        'CATEGORIA' => 'CATEGORIA',
        'DESCARGA' => 'DESCARGA',
        'CODIGO DESCARGA' => 'CODIGO_DESCARGA',
        'NOMBRE DESCARGA' => 'NOMBRE_DESCARGA',
        'NOMBRE DEFINITIVO' => 'NOMBRE_DEFINITIVO',
        'TITULAR' => 'TITULAR',
        'HORAS' => 'HORAS',
        'CODIGO DEPENDENCIA' => 'CODIGO_DEPENDENCIA',
        'L' => 'L',
        'M' => 'M',
        'I' => 'I',
        'J' => 'J',
        'V' => 'V',
        'S' => 'S',
        'D' => 'D',
        'DIA PRESENCIAL' => 'DIA_PRESENCIAL',
        'DIA VIRTUAL' => 'DIA_VIRTUAL',
        'MODALIDAD' => 'MODALIDAD',
        'FECHA INICIAL' => 'FECHA_INICIAL',
        'FECHA FINAL' => 'FECHA_FINAL',
        'HORA INICIAL' => 'HORA_INICIAL',
        'HORA FINAL' => 'HORA_FINAL',
        'MODULO' => 'MODULO',
        'AULA' => 'AULA',
        'CUPO' => 'CUPO',
        'OBSERVACIONES' => 'OBSERVACIONES',
        'EXAMEN EXTRAORDINARIO' => 'EXAMEN_EXTRAORDINARIO'
    ];

    // Crear mapeo de campos
    $fieldMap = [];
    foreach ($headers as $index => $header) {
        if (isset($columnMap[$header])) {
            $fieldMap[$index] = $columnMap[$header];
        }
    }

    // Preparar consulta SQL
    $presentFields = array_merge(['Departamento_ID'], array_values($fieldMap));
    $fields = implode(', ', $presentFields);
    $placeholders = implode(', ', array_fill(0, count($presentFields), '?'));
    $sql = "INSERT INTO $tabla_destino ($fields) VALUES ($placeholders)";

    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Procesar filas
    $successful_inserts = 0;
    for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
        try {
            $rowData = ['Departamento_ID' => $departamento_id];
            foreach ($fieldMap as $columnIndex => $field) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                $cellValue = $sheet->getCell($columnLetter . $row)->getCalculatedValue();

                // Procesar fechas
                if (in_array($field, ['FECHA_INICIAL', 'FECHA_FINAL'])) {
                    if (is_numeric($cellValue)) {
                        try {
                            $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                            $cellValue = $dateValue->format('d/m/Y');
                        } catch (Exception $e) {
                            $cellValue = null;
                            writeLog("Error al procesar fecha en fila $row, columna $field: " . $e->getMessage());
                        }
                    }
                }

                // Procesar otros campos
                if (in_array($field, ['HORA_INICIAL', 'HORA_FINAL', 'AULA'])) {
                    $cellValue = $cellValue !== null ? str_pad(substr($cellValue, 0, 10), 4, '0', STR_PAD_LEFT) : null;
                }

                $rowData[$field] = $cellValue;
            }

            // Preparar datos para inserción
            $dataToInsert = array_map(function ($field) use ($rowData) {
                return $rowData[$field] ?? null;
            }, $presentFields);

            $types = str_repeat('s', count($dataToInsert));

            $stmt->bind_param($types, ...$dataToInsert);
            if ($stmt->execute()) {
                $successful_inserts++;
            } else {
                writeLog("Error en la inserción de la fila $row: " . $stmt->error);
            }
        } catch (Exception $e) {
            writeLog("Error procesando fila $row: " . $e->getMessage());
        }
    }

    // Registrar el archivo en la tabla de plantillas
    if ($successful_inserts > 0) {
        $sqlInsertPlantillaDep = "INSERT INTO plantilla_dep (Nombre_Archivo_Dep, Tamaño_Archivo_Dep, Usuario_ID, Departamento_ID) VALUES (?, ?, ?, ?)";
        $stmtInsertPlantillaDep = $conexion->prepare($sqlInsertPlantillaDep);

        if (!$stmtInsertPlantillaDep) {
            throw new Exception("Error al preparar la inserción en plantilla_dep: " . $conexion->error);
        }

        $stmtInsertPlantillaDep->bind_param("siii", $fileName, $fileSize, $usuario_id, $departamento_id);

        if (!$stmtInsertPlantillaDep->execute()) {
            throw new Exception("Error al insertar en plantilla_dep: " . $stmtInsertPlantillaDep->error);
        }

        // Enviar notificaciones por correo
        $sql_departamento = "SELECT Departamentos FROM departamentos WHERE Departamento_ID = ?";
        $stmt_departamento = $conexion->prepare($sql_departamento);
        $stmt_departamento->bind_param("i", $departamento_id);
        $stmt_departamento->execute();
        $result_departamento = $stmt_departamento->get_result();
        $departamento = $result_departamento->fetch_assoc();

        $sql_secretaria = "SELECT Correo FROM Usuarios WHERE Rol_ID = 2";
        $result_secretaria = $conexion->query($sql_secretaria);

        while ($secretaria = $result_secretaria->fetch_assoc()) {
            $destinatario = $secretaria['Correo'];
            $asunto = "Nueva Base de Datos subida por Jefe de Departamento";
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
                    .footer { text-align: center; padding-top: 20px; color: #999; font-size: 8px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='https://i.imgur.com/gi5dvbb.png' alt='Logo PA'>
                    </div>
                    <div class='content'>
                        <h2>Nueva Base de Datos subida</h2>
                        <p>El Jefe del Departamento de {$departamento['Departamentos']} ha subido una nueva Base de Datos.</p>
                        <p>Nombre del archivo: {$fileName}</p>
                        <p>Fecha de subida: " . date('d/m/y H:i') . "</p>
                        <p>Por favor, ingrese al sistema para más detalles.</p>
                    </div>
                    <div class='footer'>
                        <p>Centro para la Sociedad Digital</p>
                    </div>
                </div>
            </body>
            </html>";

            enviarCorreo($destinatario, $asunto, $cuerpo);
        }

        $message = "Se insertaron $successful_inserts registros exitosamente y el archivo fue cargado correctamente en la base de datos.";
    } else {
        throw new Exception("No se pudo insertar ningún registro. Revise el log para más detalles.");
    }

    // Enviar respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        "success" => true,
        "message" => $message
    ]);
} catch (Exception $e) {
    // Manejar cualquier error
    header('Content-Type: application/json');
    writeLog("Error general: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    // Cerrar conexiones
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($stmtInsertPlantillaDep)) {
        $stmtInsertPlantillaDep->close();
    }
    if (isset($stmt_departamento)) {
        $stmt_departamento->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}

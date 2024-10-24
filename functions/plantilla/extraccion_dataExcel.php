<?php
session_start();
require './../../vendor/autoload.php';
include './../notificaciones-correos/email_functions.php';
ob_start();

use PhpOffice\PhpSpreadsheet\IOFactory;

// Configuración inicial de logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
$logFile = __DIR__ . '/import_log.txt';

function writeLog($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

function safeSubstr($string, $start, $length = null)
{
    if ($string === null) {
        return null;
    }
    return $length === null ? substr($string, $start) : substr($string, $start, $length);
}

if ($conn->connect_error) {
    writeLog("Error de conexión a la base de datos: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit();
}

if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    writeLog("Archivo recibido: " . $_FILES["file"]["name"]);

    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;
    $departamento_id = $_SESSION['Departamento_ID'] ?? null;

    writeLog("Usuario ID: $usuario_id, Rol ID: $rol_id, Departamento ID: $departamento_id");

    if ($usuario_id !== null) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            writeLog("Excel cargado exitosamente");

            $tabla_destino = 'Data_' . str_replace(' ', '_', $_SESSION['Nombre_Departamento']);
            writeLog("Tabla destino: $tabla_destino");

            // Verificar si la tabla existe
            $tabla_existe = $conn->query("SHOW TABLES LIKE '$tabla_destino'");
            if ($tabla_existe->num_rows == 0) {
                writeLog("Error: La tabla $tabla_destino no existe");
                echo json_encode(["success" => false, "message" => "La tabla $tabla_destino no existe en la base de datos."]);
                exit();
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
            writeLog("Encabezados encontrados: " . implode(", ", $headers));

            // Verificar la estructura de la tabla
            $result = $conn->query("DESCRIBE $tabla_destino");
            $table_columns = [];
            while ($row = $result->fetch_assoc()) {
                $table_columns[] = $row['Field'];
            }
            writeLog("Columnas en la tabla: " . implode(", ", $table_columns));

            // Resto del código del mapeo de columnas...
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

            $fieldMap = [];
            foreach ($headers as $index => $header) {
                if (isset($columnMap[$header])) {
                    $fieldMap[$index] = $columnMap[$header];
                }
            }
            writeLog("Mapeo de campos: " . print_r($fieldMap, true));

            // Preparar la consulta SQL
            $presentFields = array_merge(['Departamento_ID'], array_values($fieldMap));
            $fields = implode(', ', $presentFields);
            $placeholders = implode(', ', array_fill(0, count($presentFields), '?'));
            $sql = "INSERT INTO $tabla_destino ($fields) VALUES ($placeholders)";
            writeLog("SQL preparado: $sql");

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                writeLog("Error en la preparación de la consulta: " . $conn->error);
                echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
                exit();
            }

            // Procesar cada fila
            $processed_rows = 0;
            $successful_inserts = 0;

            for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                $rowData = ['Departamento_ID' => $departamento_id];
                foreach ($fieldMap as $columnIndex => $field) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                    $cellValue = $sheet->getCell($columnLetter . $row)->getCalculatedValue();

                    // Procesar el valor según el tipo de campo
                    if (in_array($field, ['FECHA_INICIAL', 'FECHA_FINAL'])) {
                        if (is_numeric($cellValue)) {
                            try {
                                $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                                $cellValue = $dateValue->format('Y-m-d');
                            } catch (Exception $e) {
                                $cellValue = null;
                                writeLog("Error al procesar fecha en fila $row, columna $field: " . $e->getMessage());
                            }
                        }
                    }

                    $rowData[$field] = $cellValue;
                }

                // Preparar los datos para la inserción
                $dataToInsert = array_map(function ($field) use ($rowData) {
                    return $rowData[$field] ?? null;
                }, $presentFields);

                $types = str_repeat('s', count($dataToInsert));

                try {
                    $stmt->bind_param($types, ...$dataToInsert);
                    $result = $stmt->execute();
                    if ($result) {
                        $successful_inserts++;
                    } else {
                        writeLog("Error en la inserción de la fila $row: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    writeLog("Excepción en la fila $row: " . $e->getMessage());
                }

                $processed_rows++;
            }

            writeLog("Filas procesadas: $processed_rows, Inserciones exitosas: $successful_inserts");

            if ($successful_inserts > 0) {
                echo json_encode(["success" => true, "message" => "Se insertaron $successful_inserts registros exitosamente"]);
            } else {
                echo json_encode(["success" => false, "message" => "No se pudo insertar ningún registro. Revise el log para más detalles."]);
            }
        } catch (Exception $e) {
            writeLog("Error general: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Error al procesar el archivo: " . $e->getMessage()]);
        }
    } else {
        writeLog("Error: Usuario no autenticado");
        echo json_encode(["success" => false, "message" => "Usuario no autenticado."]);
    }
} else {
    writeLog("Error: No se recibió ningún archivo");
    echo json_encode(["success" => false, "message" => "No se recibió ningún archivo."]);
}

$output = ob_get_clean();
if (json_decode($output) === null) {
    writeLog("Error en la salida JSON: $output");
    echo json_encode(["success" => false, "message" => $output]);
} else {
    echo $output;
}

$conn->close();


////////////////////////////////////////////////////////////
/*
session_start();
require './../../vendor/autoload.php';
include './../notificaciones-correos/email_functions.php';
ob_start();

use PhpOffice\PhpSpreadsheet\IOFactory;

// Configuración inicial de logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
$logFile = __DIR__ . '/import_log.txt';

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

function safeSubstr($string, $start, $length = null)
{
    if ($string === null) {
        return null;
    }
    return $length === null ? substr($string, $start) : substr($string, $start, $length);
}   

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit();
}

if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

        $usuario_id = $_SESSION['Codigo'] ?? null;
        $rol_id = $_SESSION['Rol_ID'] ?? null;
        $departamento_id = $_SESSION['Departamento_ID'] ?? null;

        if ($usuario_id !== null) {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $tabla_destino = 'Data_' . str_replace(' ', '_', $_SESSION['Nombre_Departamento']);

            // Verificar si la tabla existe
            $tabla_existe = $conexion->query("SHOW TABLES LIKE '$tabla_destino'");
            if ($tabla_existe->num_rows == 0) {
                echo json_encode(["success" => false, "message" => "La tabla $tabla_destino no existe en la base de datos."]);
                exit();
            }

            // Obtener los encabezados del Excel
            $headers = [];
            foreach ($sheet->getRowIterator(1, 1) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $headers[] = $cell->getValue();
                }
            }

            // Mapeo de nombres de columnas del Excel a nombres de columnas de las base de datos
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

            // Crea una asignación del indice de columna del Excel al campo de la Base de Datos
            $fieldMap = [];
            foreach ($headers as $index => $header) {
                if (isset($columnMap[$header])) {
                    $fieldMap[$index] = $columnMap[$header];
                }
            }

            // Prepara consulta SQL dinamica en función de los campos presentes en el Excel
            $presentFields = array_merge(['Departamento_ID'], array_values($fieldMap));
            $fields = implode(', ', $presentFields);
            $placeholders = implode(', ', array_fill(0, count($presentFields), '?'));
            $sql = "INSERT INTO $tabla_destino ($fields) VALUES ($placeholders)";
            $stmt = $conexion->prepare($sql);

            if ($stmt === false) {
                echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]);
                exit();
            }

            $errores = array();
            $profesores_horas = array();

            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = ['Departamento_ID' => $departamento_id];
                foreach ($fieldMap as $columnIndex => $field) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                    $cellValue = safeSubstr($sheet->getCell($columnLetter . $row)->getCalculatedValue(), 0, 80);

                    // Lista de campos que deben ser tratados como fechas
                    $dateFields = ['FECHA_INICIAL', 'FECHA_FINAL'];

                    if (in_array($field, $dateFields)) {
                        // Verificar si es un valor numérico (fecha de Excel)
                        if (is_numeric($cellValue)) {
                            try {
                                $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                                $cellValue = $dateValue->format('Y-m-d'); // Formato MySQL
                            } catch (Exception $e) {
                                $cellValue = null;
                            }
                        } else if ($cellValue !== null) {
                            // Intentar convertir otros formatos de fecha
                            try {
                                $dateValue = new DateTime($cellValue);
                                $cellValue = $dateValue->format('Y-m-d');
                            } catch (Exception $e) {
                                $cellValue = null;
                            }
                        }
                    } else {
                        // Para campos que no son fecha ni hora, mantener el procesamiento original
                        $cellValue = safeSubstr($cellValue, 0, 150);
                    }

                    $rowData[$field] = $cellValue !== null ? safeSubstr($cellValue, 0, 150) : null;
                }
                // Procesar fechas, horas y aula
                if (isset($rowData['FECHA_INICIAL'])) {
                    if (is_numeric($rowData['FECHA_INICIAL'])) {
                        $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData['FECHA_INICIAL']);
                        $rowData['FECHA_INICIAL'] = $fecha->format('d-m-Y'); // Nuevo formato
                    } else {
                        $rowData['FECHA_INICIAL'] = date('d-m-Y', strtotime($rowData['FECHA_INICIAL']));
                    }
                }
                if (isset($rowData['FECHA_FINAL'])) {
                    if (is_numeric($rowData['FECHA_FINAL'])) {
                        $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData['FECHA_FINAL']);
                        $rowData['FECHA_FINAL'] = $fecha->format('d-m-Y'); // Nuevo formato
                    } else {
                        $rowData['FECHA_FINAL'] = date('d-m-Y', strtotime($rowData['FECHA_FINAL']));
                    }
                }
                if (isset($rowData['HORA_INICIAL'])) {
                    $rowData['HORA_INICIAL'] = $rowData['HORA_INICIAL'] !== null ? str_pad(substr($rowData['HORA_INICIAL'], 0, 10), 4, '0', STR_PAD_LEFT) : null;
                }
                if (isset($rowData['HORA_FINAL'])) {
                    $rowData['HORA_FINAL'] = $rowData['HORA_FINAL'] !== null ? str_pad(substr($rowData['HORA_FINAL'], 0, 10), 4, '0', STR_PAD_LEFT) : null;
                }
                if (isset($rowData['AULA'])) {
                    $rowData['AULA'] = $rowData['AULA'] !== null ? str_pad(substr($rowData['AULA'], 0, 10), 4, '0', STR_PAD_LEFT) : null;
                }

                // Sum hours for each professor
                /*
                if (isset($rowData['CODIGO_PROFESOR']) && isset($rowData['HORAS']) && isset($rowData['CATEGORIA']) &&
                    $rowData['CATEGORIA'] !== 'Asignatura A' && $rowData['CATEGORIA'] !== 'Asignatura B' && $rowData['CATEGORIA'] !== 'Asignatura C') {
                    if (!isset($profesores_horas[$rowData['CODIGO_PROFESOR']])) {
                        $profesores_horas[$rowData['CODIGO_PROFESOR']] = 0;
                    }
                    $profesores_horas[$rowData['CODIGO_PROFESOR']] += intval($rowData['HORAS']);
                }
                */


                ////////////////////////////////////////////////////////////////////////////////////

            /*

            //Preparar datos para la inserción a la Base de datos
            $dataToInsert = array_map(function($field) use ($rowData) {
                return $rowData[$field] ?? null;
            }, $presentFields);

                $types = str_repeat('s', count($dataToInsert));
                $stmt->bind_param($types, ...$dataToInsert);

                try {
                    if (!$stmt->execute()) {
                        $errores[] = "Error en la fila $row: " . $stmt->error;
                    }
                } catch (Exception $e) {
                    $errores[] = "Error en la fila $row: " . $e->getMessage();
                }
            }

            function esProfesorAsignatura($categoria)
            {
                if ($categoria === null) {
                    return false;
                }
                $categoriasAsignatura = [
                    'PROFESOR DE ASIGNATURA "A"',
                    'PROFESOR DE ASIGNATURA "B"',
                    'PROFESOR DE ASIGNATURA "C"',
                    'ASIGNATURA "A"',
                    'ASIGNATURA "B"',
                    'ASIGNATURA "C"'
                ];
                return in_array(trim($categoria), $categoriasAsignatura);
            }

            // Validar la carga horaria de cada profesor
            $tablas_departamentos = [
                'data_administración',
                'data_auditoría',
                'data_ciencias_sociales',
                'data_contabilidad',
                'data_economía',
                'data_estudios_regionales',
                'data_finanzas',
                'data_impuestos',
                'data_mercadotecnia',
                'data_métodos_cuantitativos',
                'data_pale',
                'data_políticas_públicas',
                'data_posgrados',
                'data_recursos_humanos',
                'data_sistemas_de_información',
                'data_turismo'
            ];

            $profesores_horas_totales = array();

            foreach ($tablas_departamentos as $tabla) {
                $sql_horas = "SELECT t.CODIGO_PROFESOR, 
                                    SUM(CAST(t.HORAS AS UNSIGNED)) AS total_horas, 
                                    c.Categoria_actual,
                                    c.Nombre_completo
                            FROM $tabla t
                            LEFT JOIN Coord_Per_Prof c ON t.CODIGO_PROFESOR = c.Codigo
                            GROUP BY t.CODIGO_PROFESOR, c.Categoria_actual, c.Nombre_completo";

                $result_horas = $conexion->query($sql_horas);

                if ($result_horas) {
                    while ($row = $result_horas->fetch_assoc()) {
                        $codigo_profesor = $row['CODIGO_PROFESOR'];
                        $horas = intval($row['total_horas']);
                        $categoria = $row['Categoria_actual'];
                        $nombre_completo = $row['Nombre_completo'];

                        // Log para depuración
                        error_log("Procesando profesor: Código=$codigo_profesor, Nombre=$nombre_completo, Categoría=$categoria, Horas=$horas");

                        if (!esProfesorAsignatura($categoria)) {
                            if (!isset($profesores_horas_totales[$codigo_profesor])) {
                                $profesores_horas_totales[$codigo_profesor] = [
                                    'horas' => 0,
                                    'nombre' => $nombre_completo,
                                    'categoria' => $categoria
                                ];
                            }
                            $profesores_horas_totales[$codigo_profesor]['horas'] += $horas;

                            // Log para depuración
                            error_log("Sumando horas: Código=$codigo_profesor, Horas totales=" . $profesores_horas_totales[$codigo_profesor]['horas']);
                        } else {
                            // Log para depuración
                            error_log("Profesor de asignatura excluido: Código=$codigo_profesor, Categoría=$categoria");
                        }
                    }
                } else {
                    // Manejar el error si la consulta falla
                    error_log("Error al consultar la tabla $tabla: " . $conexion->error);
                }
            }

            // Ahora comparamos las horas totales con la carga horaria permitida
            $profesores_excedidos = array();

            foreach ($profesores_horas_totales as $codigo_profesor => $info) {
                $sql_profesor = "SELECT Carga_horaria FROM Coord_Per_Prof WHERE Codigo = ?";
                $stmt_profesor = $conexion->prepare($sql_profesor);
                $stmt_profesor->bind_param("s", $codigo_profesor);
                $stmt_profesor->execute();
                $result_profesor = $stmt_profesor->get_result();

                if ($row_profesor = $result_profesor->fetch_assoc()) {
                    $carga_horaria_permitida = intval($row_profesor['Carga_horaria']);
                    if ($info['horas'] > $carga_horaria_permitida) {
                        $profesores_excedidos[] = array(
                            'codigo' => $codigo_profesor,
                            'nombre' => $info['nombre'],
                            'categoria' => $info['categoria'],
                            'horas_asignadas' => $info['horas'],
                            'carga_permitida' => $carga_horaria_permitida
                        );

                        // Log para depuración
                        error_log("Profesor excedido: Código=$codigo_profesor, Nombre={$info['nombre']}, Horas asignadas={$info['horas']}, Carga permitida=$carga_horaria_permitida");
                    }
                }
                $stmt_profesor->close();
            }

            if (!empty($profesores_excedidos)) {
                $mensaje_advertencia = "Los siguientes profesores exceden su carga horaria permitida:";
                foreach ($profesores_excedidos as $profesor) {
                    $mensaje_advertencia .= "\n{$profesor['codigo']} - {$profesor['nombre']} ({$profesor['categoria']}) " .
                        "(Asignadas: {$profesor['horas_asignadas']}, Permitidas: {$profesor['carga_permitida']})";
                }
                echo json_encode(["success" => false, "message" => $mensaje_advertencia]);
                exit();
            }

            $sqlInsertPlantillaDep = "INSERT INTO Plantilla_Dep (Nombre_Archivo_Dep, Tamaño_Archivo_Dep, Usuario_ID, Departamento_ID) VALUES (?, ?, ?, ?)";
            $stmtInsertPlantillaDep = $conexion->prepare($sqlInsertPlantillaDep);
            $stmtInsertPlantillaDep->bind_param("siii", $fileName, $fileSize, $usuario_id, $departamento_id);

            if ($stmtInsertPlantillaDep->execute()) {
                // Obtener el nombre del departamento
                $sql_departamento = "SELECT Departamentos FROM Departamentos WHERE Departamento_ID = ?";
                $stmt_departamento = $conexion->prepare($sql_departamento);
                $stmt_departamento->bind_param("i", $departamento_id);
                $stmt_departamento->execute();
                $result_departamento = $stmt_departamento->get_result();
                $departamento = $result_departamento->fetch_assoc();

                // Obtener correos de los usuarios de secretaría administrativa
                $sql_secretaria = "SELECT Correo FROM Usuarios WHERE Rol_ID = 2";
                $result_secretaria = $conexion->query($sql_secretaria);

                $envio_exitoso = true;

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
                    </html>
                    ";

                    if (!enviarCorreo($destinatario, $asunto, $cuerpo)) {
                        $envio_exitoso = false;
                    }
                }

                if ($envio_exitoso) {
                    echo json_encode(["success" => true, "message" => "Archivo cargado y datos insertados en la base de datos."]);
                } else {
                    echo json_encode(["success" => true, "message" => "Archivo cargado y datos insertados en la base de datos, pero hubo problemas al enviar algunos correos."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Error al insertar en Plantilla_Dep: " . $stmtInsertPlantillaDep->error]);
            }

            $stmt_departamento->close();
            $stmtInsertPlantillaDep->close();

            echo json_encode(["success" => true, "message" => "Archivo cargado y datos insertados en la base de datos."]);
        } else {
            echo json_encode(["success" => false, "message" => "Usuario no autenticado."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No se recibió ningún archivo."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$output = ob_get_clean();
if (json_decode($output) === null) {
    echo json_encode(["success" => false, "message" => $output]);
} else {
    echo $output;
}

$conn->close();
*/
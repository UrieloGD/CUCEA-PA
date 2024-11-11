<?php
session_start();
require './../../vendor/autoload.php';
include './../notificaciones-correos/email_functions.php';
include './../../config/db.php';
ob_start();

use PhpOffice\PhpSpreadsheet\IOFactory;

function safeSubstr($string, $start, $length = null)
{
    if ($string === null) {
        return null;
    }
    return $length === null ? substr($string, $start) : substr($string, $start, $length);
}   

$inserted_records = 0;

if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;
    $departamento_id = $_SESSION['Departamento_ID'] ?? null;

    if ($usuario_id !== null) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $tabla_destino = 'Data_' . str_replace(' ', '_', $_SESSION['Nombre_Departamento']);

            // Verificar si la tabla existe
            $tabla_existe = $conexion->query("SHOW TABLES LIKE '$tabla_destino'");
            if ($tabla_existe->num_rows == 0) {
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

            // Verificar la estructura de la tabla
            $result = $conexion->query("DESCRIBE $tabla_destino");
            $table_columns = [];
            while ($row = $result->fetch_assoc()) {
                $table_columns[] = $row['Field'];
            }

            // Código del mapeo de columnas
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

            // Preparar la consulta SQL
            $presentFields = array_merge(['Departamento_ID'], array_values($fieldMap));
            $fields = implode(', ', $presentFields);
            $placeholders = implode(', ', array_fill(0, count($presentFields), '?'));
            $sql = "INSERT INTO $tabla_destino ($fields) VALUES ($placeholders)";

            $stmt = $conexion->prepare($sql);
            if ($stmt === false) {
                echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]);
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
                                $cellValue = $dateValue->format('d/m/Y');
                            } catch (Exception $e) {
                                $cellValue = null;
                            }
                        }
                    }
                    
                    $rowData[$field] = $cellValue;
                }

                // Procesar fechas, horas y aula
                if (isset($rowData['FECHA_INICIAL'])) {
                    $rowData['FECHA_INICIAL'] = $rowData['FECHA_INICIAL'] instanceof \PhpOffice\PhpSpreadsheet\Shared\Date 
                        ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData['FECHA_INICIAL'])->format('d/m/Y')
                        : $rowData['FECHA_INICIAL'];
                }
                if (isset($rowData['FECHA_FINAL'])) {
                    $rowData['FECHA_FINAL'] = $rowData['FECHA_FINAL'] instanceof \PhpOffice\PhpSpreadsheet\Shared\Date 
                        ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowData['FECHA_FINAL'])->format('d/m/Y')
                        : $rowData['FECHA_FINAL'];
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

                // Preparar los datos para la inserción
                $dataToInsert = array_map(function($field) use ($rowData) {
                    return $rowData[$field] ?? null;
                }, $presentFields);

                $types = str_repeat('s', count($dataToInsert));
                
                try {
                    $stmt->bind_param($types, ...$dataToInsert);
                    $result = $stmt->execute();
                    if ($result) {
                        $successful_inserts++;
                    }
                } catch (Exception $e) {
                    
                }
                
                $processed_rows++;
            }

            if ($successful_inserts > 0) {
                $inserted_records = $successful_inserts;
            } else {
                echo json_encode(["success" => false, "message" => "No se pudo insertar ningún registro. Revise el log para más detalles."]);
                exit();
            }

        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error al procesar el archivo: " . $e->getMessage()]);
        }

        /////////////////////////////////////////////////////////////
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
                $message = "Se insertaron $inserted_records registros exitosamente y el archivo fue cargado correctamente en la base de datos.";
            } else {
                $message = "Se insertaron $inserted_records registros exitosamente y el archivo fue cargado, pero hubo problemas al enviar algunos correos.";
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al insertar en Plantilla_Dep: " . $stmtInsertPlantillaDep->error]);
            exit();
        }

        $stmt_departamento->close();
        $stmtInsertPlantillaDep->close();

    } else {
        echo json_encode(["success" => false, "message" => "Usuario no autenticado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No se recibió ningún archivo."]);
}

$output = ob_get_clean();

if (empty($output)) {
    if (!empty($message)) {
        echo json_encode([
            "success" => true,
            "message" => $message
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error en el procesamiento del archivo"
        ]);
    }
} else {
    $decoded = json_decode($output, true);
    if ($decoded === null) {
        echo json_encode([
            "success" => false,
            "message" => "Error en el formato de respuesta"
        ]);
    } else {
        echo $output;
    }
}

$conexion->close();
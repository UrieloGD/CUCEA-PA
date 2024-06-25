<?php
session_start();
require './../vendor/autoload.php'; // Incluye PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión a la base de datos
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit();
}

// Verificar si se envió un archivo
if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

    // Obtener el ID del usuario actual desde la sesión
    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;
    $departamento_id = $_SESSION['Departamento_ID'] ?? null;

    if ($usuario_id !== null) {
        // Leer el archivo Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // Obtener el rango de datos
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Obtener el ID del departamento del usuario desde la sesión
        $departamento_id = $_SESSION['Departamento_ID'];

        // Preparar la consulta SQL según el departamento del usuario
        switch ($departamento_id) {
            case 1:
                $tabla_destino = 'Data_Estudios_Regionales';
                break;
            case 2:
                $tabla_destino = 'Data_Finanzas';
                break;
            case 3:
                $tabla_destino = 'Data_Ciencias_Sociales';
                break;
            case 4:
                $tabla_destino = 'Data_PALE';
                break;
            case 5:
                $tabla_destino = 'Data_Posgrados';
                break;
            case 6:
                $tabla_destino = 'Data_Economía';
                break;
            case 7:
                $tabla_destino = 'Data_Recursos_Humanos';
                break;
            case 8:
                $tabla_destino = 'Data_Métodos_Cuantitativos';
                break;
            case 9:
                $tabla_destino = 'Data_Políticas_Públicas';
                break;
            case 10:
                $tabla_destino = 'Data_Administración';
                break;
            case 11:
                $tabla_destino = 'Data_Auditoría';
                break;
            case 12:
                $tabla_destino = 'Data_Mercadotecnia';
                break;
            case 13:
                $tabla_destino = 'Data_Impuestos';
                break;
            case 14:
                $tabla_destino = 'Data_Sistemas_de_Información';
                break;
            case 15:
                $tabla_destino = 'Data_Turismo';
                break;
            case 16:
                $tabla_destino = 'Data_Contabilidad';
                break;
            default:
                echo json_encode(["success" => false, "message" => "Departamento no válido."]);
                exit();
        }

        // Preparar la consulta SQL
        $sql = "INSERT INTO $tabla_destino (DEPARTAMENTO_ID, CICLO, CRN, MATERIA, CVE_MATERIA, SECCION, NIVEL, NIVEL_TIPO, TIPO, C_MIN, H_TOTALES, ESTATUS, TIPO_CONTRATO, CODIGO_PROFESOR, NOMBRE_PROFESOR, CATEGORIA, DESCARGA, CODIGO_DESCARGA, NOMBRE_DESCARGA, NOMBRE_DEFINITIVO, TITULAR, HORAS, CODIGO_DEPENDENCIA, L, M, I, J, V, S, D, DIA_PRESENCIAL, DIA_VIRTUAL, FECHA_INICIAL, FECHA_FINAL, HORA_INICIAL, HORA_FINAL, MODULO, AULA, CUPO, OBSERVACIONES, EXAMEN_EXTRAORDINARIO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $errores = array();

        // Insertar los datos en la tabla correspondiente
        for ($row = 2; $row <= $highestRow; $row++) { // Empezar desde la fila 2 para omitir los encabezados
            $departamento_id = $departamento_id ?? null;
            $ciclo = $sheet->getCell('A' . $row)->getValue() ?? null;
            $crn = $sheet->getCell('B' . $row)->getValue() ?? null;
            $materia = $sheet->getCell('C' . $row)->getValue() ?? null;
            $cve_materia = $sheet->getCell('D' . $row)->getValue() ?? null;
            $seccion = $sheet->getCell('E' . $row)->getValue() ?? null;
            $nivel = $sheet->getCell('F' . $row)->getValue() ?? null;
            $nivel_tipo = $sheet->getCell('G' . $row)->getValue() ?? null;
            $tipo = $sheet->getCell('H' . $row)->getValue() ?? null;
            $c_min = $sheet->getCell('I' . $row)->getValue() ?? null;
            $h_totales = $sheet->getCell('J' . $row)->getValue() ?? null;
            $estatus = $sheet->getCell('K' . $row)->getValue() ?? null;
            $tipo_contrato = $sheet->getCell('L' . $row)->getValue() ?? null;
            $codigo_profesor = $sheet->getCell('M' . $row)->getValue() ?? null;
            $nombre_profesor = $sheet->getCell('N' . $row)->getValue() ?? null;
            $categoria = $sheet->getCell('O' . $row)->getValue() ?? null;
            $descarga = $sheet->getCell('O' . $row)->getValue() ?? null;
            $codigo_descarga = $sheet->getCell('P' . $row)->getValue() ?? null;
            $nombre_descarga = $sheet->getCell('Q' . $row)->getValue() ?? null;
            $nombre_definitivo = $sheet->getCell('R' . $row)->getValue() ?? null;
            $titular = $sheet->getCell('S' . $row)->getValue() ?? null;
            $horas = $sheet->getCell('T' . $row)->getValue() ?? null;
            $codigo_dependencia = $sheet->getCell('V' . $row)->getValue() ?? null;
            $l = $sheet->getCell('W' . $row)->getValue() ?? null;
            $m = $sheet->getCell('X' . $row)->getValue() ?? null;
            $i = $sheet->getCell('Y' . $row)->getValue() ?? null;
            $j = $sheet->getCell('Z' . $row)->getValue() ?? null;
            $v = $sheet->getCell('AA' . $row)->getValue() ?? null;
            $s = $sheet->getCell('AB' . $row)->getValue() ?? null;
            $d = $sheet->getCell('AC' . $row)->getValue() ?? null;
            $dia_presencial = $sheet->getCell('AD' . $row)->getValue() ?? null;
            $dia_virtual = $sheet->getCell('AE' . $row)->getValue() ?? null;
            $modalidad = $sheet->getCell('AF' . $row)->getValue() ?? null;
            $fecha_inicial = $sheet->getCell('AG' . $row)->getFormattedValue() ?? null;
            $fecha_final = $sheet->getCell('AH' . $row)->getFormattedValue() ?? null;
            $hora_inicial = $sheet->getCell('AI' . $row)->getValue() ?? null;
            $hora_final = $sheet->getCell('AJ' . $row)->getValue() ?? null;
            $modulo = $sheet->getCell('AK' . $row)->getValue() ?? null;
            $aula = $sheet->getCell('AL' . $row)->getValue() ?? null;
            $cupo = $sheet->getCell('AM' . $row)->getValue() ?? null;
            $observaciones = $sheet->getCell('AN' . $row)->getValue() ?? null;
            $examen_extraodinario = $sheet->getCell('AO' . $row)->getValue() ?? null;


            // Validaciones
            // ciclo: debe contener no más de 6 caracteres
            if (strlen($ciclo) > 6) {
                $errores[] = "El valor '$ciclo' en la columna ciclo no debe tener más de 6 caracteres.";
            }

            // crn: debe tener no más de 7 caracteres
            // if (strlen($crn) > 7) {
            //     $errores[] = "El valor '$crn' en la columna CRN tiene más de 7 caracteres.";
            // }

            // Días: Solo deben contener la letra correspondiente
            // $dias = ['L', 'M', 'I', 'J', 'V', 'S', 'D'];
            // foreach ($dias as $dia) {
            //     $valor = strtolower($dia);
            //     if ($$valor != $dia && !empty($$valor)) {
            //         $errores[] = "El valor '$valor' en la columna $dia no es válido. Debe contener solo la letra $dia.";
            //     }
            // }

            if (count($errores) > 0) {
                // Mostrar errores y detener el script si hay errores
                echo json_encode(["success" => false, "message" => $errores]);
                exit();
            }

            // Convertir fechas al formato YYYY-MM-DD
            if ($fecha_inicial) {
                $fecha_inicial = DateTime::createFromFormat('d/m/Y', $fecha_inicial);
                if ($fecha_inicial) {
                    $fecha_inicial = $fecha_inicial->format('d-m-Y');
                } else {
                    $errores[] = "El valor '$fecha_inicial' en la columna fecha inicial no es una fecha válida.";
                }
            }
            if ($fecha_final) {
                $fecha_final = DateTime::createFromFormat('d/m/Y', $fecha_final);
                if ($fecha_final) {
                    $fecha_final = $fecha_final->format('d-m-Y');
                } else {
                    $errores[] = "El valor '$fecha_final' en la columna fecha final no es una fecha válida.";
                }
            }

            if (count($errores) > 0) {
                // Mostrar errores y detener el script si hay errores
                echo json_encode(["success" => false, "message" => $errores]);
                exit();
            }

            $stmt->bind_param("isssssssssssssssssssssssssssssssssssssssss", $departamento_id, $ciclo, $crn, $materia, $cve_materia, $seccion, $nivel, $nivel_tipo, $tipo, $c_min, $h_totales, $estatus, $tipo_contrato, $codigo_profesor, $nombre_profesor, $categoria, $descarga, $codigo_descarga, $nombre_descarga, $nombre_definitivo, $titular, $horas, $codigo_dependencia, $l, $m, $i, $j, $v, $s, $d, $dia_presencial, $dia_virtual, $modalidad, $fecha_inicial, $fecha_final, $hora_inicial, $hora_final, $modulo, $aula, $cupo, $observaciones, $examen_extraodinario);
            $stmt->execute();
        }

        // Obtener el nombre del archivo
        $nombreArchivo = $fileName;

        // Obtener el tamaño del archivo en bytes
        $tamanoArchivo = $fileSize;

        // Obtener el ID del usuario desde la sesión
        $usuario_id = $_SESSION['Codigo'];

        // Obtener el ID del departamento desde la sesión
        $departamento_id = $_SESSION['Departamento_ID'];

        // Preparar la consulta SQL para insertar en la tabla Plantilla_Dep
        $sqlInsertPlantillaDep = "INSERT INTO Plantilla_Dep (Nombre_Archivo_Dep, Tamaño_Archivo_Dep, Usuario_ID, Departamento_ID) VALUES (?, ?, ?, ?)";
        $stmtInsertPlantillaDep = $conn->prepare($sqlInsertPlantillaDep);

        // Vincular los parámetros
        $stmtInsertPlantillaDep->bind_param("siii", $nombreArchivo, $tamanoArchivo, $usuario_id, $departamento_id);

        // Ejecutar la consulta
        if ($stmtInsertPlantillaDep->execute()) {
            // El archivo se insertó correctamente en la tabla Plantilla_Dep
        } else {
            // Ocurrió un error al insertar el archivo en la tabla Plantilla_Dep
            echo json_encode(["success" => false, "message" => "Error al insertar el archivo en la tabla Plantilla_Dep: " . $stmtInsertPlantillaDep->error]);
        }

        // Cerrar la sentencia preparada
        $stmtInsertPlantillaDep->close();

        if ($stmt->error) {
            echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta: " . $stmt->error]);
        } else {
            echo json_encode(["success" => true, "message" => "Archivo cargado y datos insertados en la base de datos correctamente."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Usuario no autenticado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No se recibió ningún archivo."]);
}

header('Content-Type: application/json');
echo json_encode($response);
exit();

$conn->close();

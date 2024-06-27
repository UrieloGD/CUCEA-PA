<?php
session_start();
require './../vendor/autoload.php';
ob_start();
use PhpOffice\PhpSpreadsheet\IOFactory;

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pa";

$conn = new mysqli($servername, $username, $password, $dbname);

function safeSubstr($string, $start, $length = null) {
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

        $tabla_destino = 'Data_' . str_replace(' ', '_', $_SESSION['Nombre_Departamento']);

        // Verificar si la tabla existe
        $tabla_existe = $conn->query("SHOW TABLES LIKE '$tabla_destino'");
        if ($tabla_existe->num_rows == 0) {
            echo json_encode(["success" => false, "message" => "La tabla $tabla_destino no existe en la base de datos."]);
            exit();
        }

        $sql = "INSERT INTO $tabla_destino (
            Departamento_ID, CICLO, CRN, MATERIA, CVE_MATERIA, SECCION, NIVEL, NIVEL_TIPO, TIPO,
            C_MIN, H_TOTALES, ESTATUS, TIPO_CONTRATO, CODIGO_PROFESOR, NOMBRE_PROFESOR,
            CATEGORIA, DESCARGA, CODIGO_DESCARGA, NOMBRE_DESCARGA, NOMBRE_DEFINITIVO,
            TITULAR, HORAS, CODIGO_DEPENDENCIA, L, M, I, J, V, S, D, DIA_PRESENCIAL,
            DIA_VIRTUAL, MODALIDAD, FECHA_INICIAL, FECHA_FINAL, HORA_INICIAL, HORA_FINAL,
            MODULO, AULA, CUPO, OBSERVACIONES, EXAMEN_EXTRAORDINARIO
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
            exit();
        }

        $errores = array();

        for ($row = 2; $row <= $highestRow; $row++) {
            $ciclo = safeSubstr($sheet->getCell('A' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $crn = safeSubstr($sheet->getCell('B' . $row)->getCalculatedValue(), 0, 15) ?? null;
            $materia = safeSubstr($sheet->getCell('C' . $row)->getCalculatedValue(), 0, 80) ?? null;
            $cve_materia = safeSubstr($sheet->getCell('D' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $seccion = safeSubstr($sheet->getCell('E' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $nivel = safeSubstr($sheet->getCell('F' . $row)->getCalculatedValue(), 0, 25) ?? null;
            $nivel_tipo = safeSubstr($sheet->getCell('G' . $row)->getCalculatedValue(), 0, 25) ?? null;
            $tipo = safeSubstr($sheet->getCell('H' . $row)->getCalculatedValue(), 0, 1) ?? null;
            $c_min = safeSubstr($sheet->getCell('I' . $row)->getCalculatedValue(), 0, 2) ?? null;
            $h_totales = safeSubstr($sheet->getCell('J' . $row)->getCalculatedValue(), 0, 2) ?? null;
            $estatus = safeSubstr($sheet->getCell('K' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $tipo_contrato = safeSubstr($sheet->getCell('L' . $row)->getCalculatedValue(), 0, 30) ?? null;
            $codigo_profesor = safeSubstr($sheet->getCell('M' . $row)->getCalculatedValue(), 0, 7) ?? null;
            $nombre_profesor = safeSubstr($sheet->getCell('N' . $row)->getCalculatedValue(), 0, 60) ?? null;
            $categoria = safeSubstr($sheet->getCell('O' . $row)->getCalculatedValue(), 0, 40) ?? null;
            $descarga = safeSubstr($sheet->getCell('P' . $row)->getCalculatedValue(), 0, 2) ?? null;
            $codigo_descarga = safeSubstr($sheet->getCell('Q' . $row)->getCalculatedValue(), 0, 7) ?? null;
            $nombre_descarga = safeSubstr($sheet->getCell('R' . $row)->getCalculatedValue(), 0, 60) ?? null;
            $nombre_definitivo = safeSubstr($sheet->getCell('S' . $row)->getCalculatedValue(), 0, 60) ?? null;
            $titular = safeSubstr($sheet->getCell('T' . $row)->getCalculatedValue(), 0, 2) ?? null;
            $horas = safeSubstr($sheet->getCell('U' . $row)->getCalculatedValue(), 0, 1) ?? null;
            $codigo_dependencia = safeSubstr($sheet->getCell('V' . $row)->getCalculatedValue(), 0, 4) ?? null;
            $l = safeSubstr($sheet->getCell('W' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $m = safeSubstr($sheet->getCell('X' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $i = safeSubstr($sheet->getCell('Y' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $j = safeSubstr($sheet->getCell('Z' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $v = safeSubstr($sheet->getCell('AA' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $s = safeSubstr($sheet->getCell('AB' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $d = safeSubstr($sheet->getCell('AC' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $dia_presencial = safeSubstr($sheet->getCell('AD' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $dia_virtual = safeSubstr($sheet->getCell('AE' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $modalidad = safeSubstr($sheet->getCell('AF' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $fecha_inicial = $sheet->getCell('AG' . $row)->getCalculatedValue() ?? null;
            $fecha_final = $sheet->getCell('AH' . $row)->getCalculatedValue() ?? null;
            $hora_inicial = safeSubstr($sheet->getCell('AI' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $hora_final = safeSubstr($sheet->getCell('AJ' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $modulo = safeSubstr($sheet->getCell('AK' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $aula = safeSubstr($sheet->getCell('AL' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $observaciones = safeSubstr($sheet->getCell('AM' . $row)->getCalculatedValue(), 0, 150) ?? null;
            $cupo = safeSubstr($sheet->getCell('AN' . $row)->getCalculatedValue(), 0, 3) ?? null;
            $examen_extraordinario = safeSubstr($sheet->getCell('AO' . $row)->getCalculatedValue(), 0, 2) ?? null;

            if ($fecha_inicial) {
                $fecha_inicial = DateTime::createFromFormat('d/m/Y', $fecha_inicial);
                $fecha_inicial = $fecha_inicial ? $fecha_inicial->format('Y-m-d') : null;
            }
            if ($fecha_final) {
                $fecha_final = DateTime::createFromFormat('d/m/Y', $fecha_final);
                $fecha_final = $fecha_final ? $fecha_final->format('Y-m-d') : null;
            }

            $stmt->bind_param("isssssssssssssssssssssssssssssssssssssssss", 
                $departamento_id, $ciclo, $crn, $materia, $cve_materia, $seccion, $nivel, $nivel_tipo, $tipo,
                $c_min, $h_totales, $estatus, $tipo_contrato, $codigo_profesor, $nombre_profesor,
                $categoria, $descarga, $codigo_descarga, $nombre_descarga, $nombre_definitivo,
                $titular, $horas, $codigo_dependencia, $l, $m, $i, $j, $v, $s, $d, $dia_presencial,
                $dia_virtual, $modalidad, $fecha_inicial, $fecha_final, $hora_inicial, $hora_final,
                $modulo, $aula, $cupo, $observaciones, $examen_extraordinario
            );

            if (!$stmt->execute()) {
                $errores[] = "Error en la fila $row: " . $stmt->error;
            }
        }

        $sqlInsertPlantillaDep = "INSERT INTO Plantilla_Dep (Nombre_Archivo_Dep, Tamaño_Archivo_Dep, Usuario_ID, Departamento_ID) VALUES (?, ?, ?, ?)";
        $stmtInsertPlantillaDep = $conn->prepare($sqlInsertPlantillaDep);
        $stmtInsertPlantillaDep->bind_param("siii", $fileName, $fileSize, $usuario_id, $departamento_id);
        $stmtInsertPlantillaDep->execute();
        $stmtInsertPlantillaDep->close();

        if (count($errores) > 0) {
            echo json_encode(["success" => false, "message" => "Se encontraron errores al insertar los datos:", "errores" => $errores]);
        } else {
            echo json_encode(["success" => true, "message" => "Archivo cargado y datos insertados en la base de datos correctamente."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Usuario no autenticado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No se recibió ningún archivo."]);
}

$output = ob_get_clean();
if (json_decode($output) === null) {
    echo json_encode(["success" => false, "message" => $output]);
} else {
    echo $output;
}

$conn->close();
?>
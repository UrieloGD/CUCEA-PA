<?php
session_start();
require './../../vendor/autoload.php';
include './../notificaciones-correos/email_functions.php';
ob_start();

use PhpOffice\PhpSpreadsheet\IOFactory;

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
            $ciclo = $sheet->getCell('A' . $row)->getCalculatedValue();
            $ciclo = $ciclo !== null ? safeSubstr($ciclo, 0, 10) : null;
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
            $hora_inicial = $sheet->getCell('AI' . $row)->getCalculatedValue();
            $hora_inicial = $hora_inicial !== null ? str_pad(substr($hora_inicial, 0, 10), 4, '0', STR_PAD_LEFT) : null;
            $hora_final = $sheet->getCell('AJ' . $row)->getCalculatedValue();
            $hora_final = $hora_final !== null ? str_pad(substr($hora_final, 0, 10), 4, '0', STR_PAD_LEFT) : null;
            $modulo = safeSubstr($sheet->getCell('AK' . $row)->getCalculatedValue(), 0, 10) ?? null;
            $aula = $sheet->getCell('AL' . $row)->getCalculatedValue();
            $aula = $aula !== null ? str_pad(substr($aula, 0, 10), 4, '0', STR_PAD_LEFT) : null;
            $observaciones = safeSubstr($sheet->getCell('AM' . $row)->getCalculatedValue(), 0, 150) ?? null;
            $cupo = safeSubstr($sheet->getCell('AN' . $row)->getCalculatedValue(), 0, 3) ?? null;
            $examen_extraordinario = safeSubstr($sheet->getCell('AO' . $row)->getCalculatedValue(), 0, 2) ?? null;


            // Sumar las horas para cada profesor
            //if ($codigo_profesor && $horas && $categoria !== 'PROFESOR DE ASIGNATURA "A"' && $categoria !== 'PROFESOR DE ASIGNATURA "B"' && $categoria !== 'PROFESOR DE ASIGNATURA "C"') {
            //    if (!isset($profesores_horas[$codigo_profesor])) {
            //        $profesores_horas[$codigo_profesor] = 0;
            //    }
            //    $profesores_horas[$codigo_profesor] += intval($horas);
            //}

            if ($fecha_inicial) {
                $fecha_inicial = DateTime::createFromFormat('d/m/Y', $fecha_inicial);
                $fecha_inicial = $fecha_inicial ? $fecha_inicial->format('d/m/Y') : null;
            }
            if ($fecha_final) {
                $fecha_final = DateTime::createFromFormat('d/m/Y', $fecha_final);
                $fecha_final = $fecha_final ? $fecha_final->format('d/m/Y') : null;
            }

            $stmt->bind_param(
                "isssssssssssssssssssssssssssssssssssssssss",
                $departamento_id,
                $ciclo,
                $crn,
                $materia,
                $cve_materia,
                $seccion,
                $nivel,
                $nivel_tipo,
                $tipo,
                $c_min,
                $h_totales,
                $estatus,
                $tipo_contrato,
                $codigo_profesor,
                $nombre_profesor,
                $categoria,
                $descarga,
                $codigo_descarga,
                $nombre_descarga,
                $nombre_definitivo,
                $titular,
                $horas,
                $codigo_dependencia,
                $l,
                $m,
                $i,
                $j,
                $v,
                $s,
                $d,
                $dia_presencial,
                $dia_virtual,
                $modalidad,
                $fecha_inicial,
                $fecha_final,
                $hora_inicial,
                $hora_final,
                $modulo,
                $aula,
                $cupo,
                $observaciones,
                $examen_extraordinario
            );

            if (!$stmt->execute()) {
                $errores[] = "Error en la fila $row: " . $stmt->error;
            }
        }

        function esProfesorAsignatura($categoria)
        {
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

            $result_horas = $conn->query($sql_horas);

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
                error_log("Error al consultar la tabla $tabla: " . $conn->error);
            }
        }

        // Ahora comparamos las horas totales con la carga horaria permitida
        $profesores_excedidos = array();

        foreach ($profesores_horas_totales as $codigo_profesor => $info) {
            $sql_profesor = "SELECT Carga_horaria FROM Coord_Per_Prof WHERE Codigo = ?";
            $stmt_profesor = $conn->prepare($sql_profesor);
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
        $stmtInsertPlantillaDep = $conn->prepare($sqlInsertPlantillaDep);
        $stmtInsertPlantillaDep->bind_param("siii", $fileName, $fileSize, $usuario_id, $departamento_id);

        if ($stmtInsertPlantillaDep->execute()) {
            // Obtener el nombre del departamento
            $sql_departamento = "SELECT Departamentos FROM Departamentos WHERE Departamento_ID = ?";
            $stmt_departamento = $conn->prepare($sql_departamento);
            $stmt_departamento->bind_param("i", $departamento_id);
            $stmt_departamento->execute();
            $result_departamento = $stmt_departamento->get_result();
            $departamento = $result_departamento->fetch_assoc();

            // Obtener correos de los usuarios de secretaría administrativa
            $sql_secretaria = "SELECT Correo FROM Usuarios WHERE Rol_ID = 2";
            $result_secretaria = $conn->query($sql_secretaria);

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

$output = ob_get_clean();
if (json_decode($output) === null) {
    echo json_encode(["success" => false, "message" => $output]);
} else {
    echo $output;
}

$conn->close();

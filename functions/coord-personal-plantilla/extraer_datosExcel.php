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

        $sql = "INSERT INTO Coord_Per_Prof (
            Departamento_ID, Codigo_Profesor, Nombre, Apellido, Edad, Categoria, Tipo_Plaza,
            Investigacion_Nombramiento_Cambio_de_Funcion, SNI, A_partir_de_cuando, Cuando_se_vence,
            Horas_definitivas, Horas_frente_grupo, Horarios_nombramiento, Telefono, IMSS, RFC, CURP, Correo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
            exit();
        }

        $errores = array();

        for ($row = 2; $row <= $highestRow; $row++) {
            $codigo_profesor = safeSubstr($sheet->getCell('A' . $row)->getCalculatedValue(), 0, 12) ?? null;
            $nombre = safeSubstr($sheet->getCell('B' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $apellido = safeSubstr($sheet->getCell('C' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $edad = safeSubstr($sheet->getCell('D' . $row)->getCalculatedValue(), 0, 5) ?? null;
            $categoria = safeSubstr($sheet->getCell('E' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $tipo_plaza = safeSubstr($sheet->getCell('F' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $investigacion = safeSubstr($sheet->getCell('G' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $sni = safeSubstr($sheet->getCell('H' . $row)->getCalculatedValue(), 0, 15) ?? null;
            $a_partir_de_cuando = safeSubstr($sheet->getCell('I' . $row)->getCalculatedValue(), 0, 15) ?? null;
            $cuando_se_vence = safeSubstr($sheet->getCell('J' . $row)->getCalculatedValue(), 0, 15) ?? null;
            $horas_definitivas = safeSubstr($sheet->getCell('K' . $row)->getCalculatedValue(), 0, 15) ?? null;
            $horas_frente_grupo = safeSubstr($sheet->getCell('L' . $row)->getCalculatedValue(), 0, 15) ?? null;
            $horarios_nombramiento = safeSubstr($sheet->getCell('M' . $row)->getCalculatedValue(), 0, 30) ?? null;
            $telefono = safeSubstr($sheet->getCell('N' . $row)->getCalculatedValue(), 0, 12) ?? null;
            $imss = safeSubstr($sheet->getCell('O' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $rfc = safeSubstr($sheet->getCell('P' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $curp = safeSubstr($sheet->getCell('Q' . $row)->getCalculatedValue(), 0, 35) ?? null;
            $correo = safeSubstr($sheet->getCell('R' . $row)->getCalculatedValue(), 0, 60) ?? null;

            $stmt->bind_param(
                "issssssssssssssssss",
                $departamento_id,
                $codigo_profesor,
                $nombre,
                $apellido,
                $edad,
                $categoria,
                $tipo_plaza,
                $investigacion,
                $sni,
                $a_partir_de_cuando,
                $cuando_se_vence,
                $horas_definitivas,
                $horas_frente_grupo,
                $horarios_nombramiento,
                $telefono,
                $imss,
                $rfc,
                $curp,
                $correo
            );

            if (!$stmt->execute()) {
                $errores[] = "Error en la fila $row: " . $stmt->error;
            }
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
                $asunto = "Nuevos datos de Coordinación de Personal subidos por Jefe de Departamento";
                $cuerpo = "
                <html>
                <body>
                    <h2>Nuevos datos de Coordinación de Personal subidos</h2>
                    <p>El Jefe del Departamento de {$departamento['Departamentos']} ha subido nuevos datos de Coordinación de Personal.</p>
                    <p>Nombre del archivo: {$fileName}</p>
                    <p>Fecha de subida: " . date('d/m/Y H:i') . "</p>
                    <p>Por favor, ingrese al sistema para más detalles.</p>
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

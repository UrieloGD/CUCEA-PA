<?php
// extraerdatosexcel
session_start();
ini_set('memory_limit', '256M');
require './../../vendor/autoload.php';
include './../notificaciones-correos/email_functions.php';
include './../../config/db.php';

ob_start();

use PhpOffice\PhpSpreadsheet\IOFactory;

function convertExcelDate($value)
{
    if (!is_numeric($value)) {
        return $value;
    }
    $unix_date = ($value - 25569) * 86400;
    return date("d/m/Y", $unix_date);
}

function safeSubstr($string, $start, $length = null)
{
    if ($string === null) {
        return null;
    }
    return $length === null ? mb_substr($string, $start) : mb_substr($string, $start, $length);
}

if ($conexion->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $conexion->connect_error]);
    exit();
}

if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $file = $_FILES["file"]["tmp_name"];
    $fileName = $_FILES["file"]["name"];
    $fileSize = $_FILES["file"]["size"];

    $usuario_id = $_SESSION['Codigo'] ?? null;
    $rol_id = $_SESSION['Rol_ID'] ?? null;

    if ($usuario_id !== null) {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $sql = "INSERT INTO Coord_Per_Prof (
            Codigo, Paterno, Materno, Nombres, Nombre_completo, Sexo, Departamento,
            Categoria_actual, Categoria_actual_dos, Horas_frente_grupo, Division, Tipo_plaza, Cat_act,
            Carga_horaria, Horas_definitivas, Horario, Turno, Investigacion_nombramiento_cambio_funcion,
            SNI, SNI_desde, Cambio_dedicacion, Inicio, Fin, `2024A`, Telefono_particular, Telefono_oficina,
            Domicilio, Colonia, CP, Ciudad, Estado, No_imss, CURP, RFC, Lugar_nacimiento, Estado_civil,
            Tipo_sangre, Fecha_nacimiento, Edad, Nacionalidad, Correo, Correos_oficiales, Ultimo_grado,
            Programa, Nivel, Institucion, Estado_pais, Año, Gdo_exp, Otro_grado, Otro_programa,
            Otro_nivel, Otro_institucion, Otro_estado_pais, Otro_año, Otro_gdo_exp,
            Otro_grado_alternativo, Otro_programa_alternativo, Otro_nivel_altenrativo,
            Otro_institucion_alternativo, Otro_estado_pais_alternativo, Otro_año_alternativo,
            Otro_gdo_exp_alternativo, Proesde_24_25, A_partir_de, Fecha_ingreso, Antiguedad
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conexion->error]);
            exit();
        }

        $errores = array();

        for ($row = 2; $row <= $highestRow; $row++) {
            $codigo = safeSubstr($sheet->getCell('A' . $row)->getCalculatedValue(), 0, 12);
            $paterno = safeSubstr($sheet->getCell('B' . $row)->getCalculatedValue(), 0, 50);
            $materno = safeSubstr($sheet->getCell('C' . $row)->getCalculatedValue(), 0, 50);
            $nombres = safeSubstr($sheet->getCell('D' . $row)->getCalculatedValue(), 0, 70);
            $nombre_completo = safeSubstr($sheet->getCell('E' . $row)->getCalculatedValue(), 0, 80);
            $sexo = safeSubstr($sheet->getCell('F' . $row)->getCalculatedValue(), 0, 5);
            $departamento = safeSubstr($sheet->getCell('G' . $row)->getCalculatedValue(), 0, 50);
            $categoria_actual = safeSubstr($sheet->getCell('H' . $row)->getCalculatedValue(), 0, 60);
            $categoria_actual_dos = safeSubstr($sheet->getCell('I' . $row)->getCalculatedValue(), 0, 20);
            $horas_frente_grupo = intval($sheet->getCell('J' . $row)->getCalculatedValue());
            $division = safeSubstr($sheet->getCell('K' . $row)->getCalculatedValue(), 0, 70);
            $tipo_plaza = safeSubstr($sheet->getCell('L' . $row)->getCalculatedValue(), 0, 70);
            $cat_act = safeSubstr($sheet->getCell('M' . $row)->getCalculatedValue(), 0, 70);
            $carga_horaria = safeSubstr($sheet->getCell('N' . $row)->getCalculatedValue(), 0, 10);
            $horas_definitivas = intval($sheet->getCell('O' . $row)->getCalculatedValue());
            $horario = safeSubstr($sheet->getCell('P' . $row)->getCalculatedValue(), 0, 60);
            $turno = safeSubstr($sheet->getCell('Q' . $row)->getCalculatedValue(), 0, 5);
            $investigacion_nombramiento_cambio_funcion = safeSubstr($sheet->getCell('R' . $row)->getCalculatedValue(), 0, 50);
            $sni = safeSubstr($sheet->getCell('S' . $row)->getCalculatedValue(), 0, 10);
            $sin_desde = convertExcelDate($sheet->getCell('T' . $row)->getCalculatedValue());
            $cambio_dedicacion = safeSubstr($sheet->getCell('U' . $row)->getCalculatedValue(), 0, 40);
            $inicio = convertExcelDate($sheet->getCell('V' . $row)->getCalculatedValue());
            $fin = convertExcelDate($sheet->getCell('W' . $row)->getCalculatedValue());
            $a_2024 = safeSubstr($sheet->getCell('X' . $row)->getCalculatedValue(), 0, 50);
            $telefono_particular = safeSubstr($sheet->getCell('Y' . $row)->getCalculatedValue(), 0, 30);
            $telefono_oficina = safeSubstr($sheet->getCell('Z' . $row)->getCalculatedValue(),0 , 30);
            $domicilio = safeSubstr($sheet->getCell('AA' . $row)->getCalculatedValue(), 0, 70);
            $colonia = safeSubstr($sheet->getCell('AB' . $row)->getCalculatedValue(), 0, 60);
            $cp = intval($sheet->getCell('AC' . $row)->getCalculatedValue());
            $ciudad = safeSubstr($sheet->getCell('AD' . $row)->getCalculatedValue(), 0, 30);
            $estado = safeSubstr($sheet->getCell('AE' . $row)->getCalculatedValue(), 0, 30);
            $no_imss = safeSubstr($sheet->getCell('AF' . $row)->getCalculatedValue(),0, 12);
            $curp = safeSubstr($sheet->getCell('AG' . $row)->getCalculatedValue(), 0, 25);
            $rfc = safeSubstr($sheet->getCell('AH' . $row)->getCalculatedValue(), 0, 15);
            $lugar_nacimiento = safeSubstr($sheet->getCell('AI' . $row)->getCalculatedValue(), 0, 50);
            $estado_civil = safeSubstr($sheet->getCell('AJ' . $row)->getCalculatedValue(), 0, 5);
            $tipo_sangre = safeSubstr($sheet->getCell('AK' . $row)->getCalculatedValue(), 0, 5);
            $fecha_nacimiento = convertExcelDate($sheet->getCell('AL' . $row)->getCalculatedValue(), 0, 15);
            $edad = intval($sheet->getCell('AM' . $row)->getCalculatedValue());
            $nacionalidad = safeSubstr($sheet->getCell('AN' . $row)->getCalculatedValue(), 0, 40);
            $correo = safeSubstr($sheet->getCell('AO' . $row)->getCalculatedValue(), 0, 60);
            $correos_oficiales = safeSubstr($sheet->getCell('AP' . $row)->getCalculatedValue(), 0, 60);
            $ultimo_grado = safeSubstr($sheet->getCell('AQ' . $row)->getCalculatedValue(), 0, 5);
            $programa = safeSubstr($sheet->getCell('AR' . $row)->getCalculatedValue(), 0, 70);
            $nivel = safeSubstr($sheet->getCell('AS' . $row)->getCalculatedValue(), 0, 5);
            $institucion = safeSubstr($sheet->getCell('AT' . $row)->getCalculatedValue(), 0, 50);
            $estado_pais = safeSubstr($sheet->getCell('AU' . $row)->getCalculatedValue(), 0, 50);
            $año = intval($sheet->getCell('AV' . $row)->getCalculatedValue());
            $gdo_exp = safeSubstr($sheet->getCell('AW' . $row)->getCalculatedValue(), 0, 25);
            $otro_grado = safeSubstr($sheet->getCell('AX' . $row)->getCalculatedValue(), 0, 5);
            $otro_programa = safeSubstr($sheet->getCell('AY' . $row)->getCalculatedValue(), 0, 70);
            $otro_nivel = safeSubstr($sheet->getCell('AZ' . $row)->getCalculatedValue(), 0, 10);
            $otro_institucion = safeSubstr($sheet->getCell('BA' . $row)->getCalculatedValue(), 0, 30);
            $otro_estado_pais = safeSubstr($sheet->getCell('BB' . $row)->getCalculatedValue(), 0, 25);
            $otro_año = intval($sheet->getCell('BC' . $row)->getCalculatedValue());
            $otro_gdo_exp = safeSubstr($sheet->getCell('BD' . $row)->getCalculatedValue(), 0, 25);
            $otro_grado_alternativo = safeSubstr($sheet->getCell('BE' . $row)->getCalculatedValue(), 0, 5);
            $otro_programa_alternativo = safeSubstr($sheet->getCell('BF' . $row)->getCalculatedValue(), 0, 70);
            $otro_nivel_alternativo = safeSubstr($sheet->getCell('BG' . $row)->getCalculatedValue(), 0, 10);
            $otro_institucion_alternativo = safeSubstr($sheet->getCell('BH' . $row)->getCalculatedValue(), 0, 30);
            $otro_estado_pais_alternativo = safeSubstr($sheet->getCell('BI' . $row)->getCalculatedValue(), 0, 25);
            $otro_año_alternativo = safeSubstr($sheet->getCell('BJ' . $row)->getCalculatedValue(), 0, 10);
            $otro_gdo_exp_alternativo = safeSubstr($sheet->getCell('BK' . $row)->getCalculatedValue(), 0, 15);
            $proesde_24_25 = safeSubstr($sheet->getCell('BL' . $row)->getCalculatedValue(), 0, 15);
            $a_partir_de = convertExcelDate($sheet->getCell('BM' . $row)->getCalculatedValue(), 0, 10);
            $fecha_ingreso = convertExcelDate($sheet->getCell('BN' . $row)->getCalculatedValue(), 0, 10);
            $antiguedad = safeSubstr($sheet->getCell('BO' . $row)->getCalculatedValue(), 0, 5);

            $stmt->bind_param(
                "sssssssssissssisssssssssiissississssssissssssssissssssissssssisssss",
                $codigo,
                $paterno,
                $materno,
                $nombres,
                $nombre_completo,
                $sexo,
                $departamento,
                $categoria_actual,
                $categoria_actual_dos,
                $horas_frente_grupo,
                $division,
                $tipo_plaza,
                $cat_act,
                $carga_horaria,
                $horas_definitivas,
                $horario,
                $turno,
                $investigacion_nombramiento_cambio_funcion,
                $sni,
                $sin_desde,
                $cambio_dedicacion,
                $inicio,
                $fin,
                $a_2024,
                $telefono_particular,
                $telefono_oficina,
                $domicilio,
                $colonia,
                $cp,
                $ciudad,
                $estado,
                $no_imss,
                $curp,
                $rfc,
                $lugar_nacimiento,
                $estado_civil,
                $tipo_sangre,
                $fecha_nacimiento,
                $edad,
                $nacionalidad,
                $correo,
                $correos_oficiales,
                $ultimo_grado,
                $programa,
                $nivel,
                $institucion,
                $estado_pais,
                $año,
                $gdo_exp,
                $otro_grado,
                $otro_programa,
                $otro_nivel,
                $otro_institucion,
                $otro_estado_pais,
                $otro_año,
                $otro_gdo_exp,
                $otro_grado_alternativo,
                $otro_programa_alternativo,
                $otro_nivel_alternativo,
                $otro_institucion_alternativo,
                $otro_estado_pais_alternativo,
                $otro_año_alternativo,
                $otro_gdo_exp_alternativo,
                $proesde_24_25,
                $a_partir_de,
                $fecha_ingreso,
                $antiguedad
            );

            if (!$stmt->execute()) {
                $errores[] = "Error en la fila $row: " . $stmt->error . " SQL: " . $stmt->sqlstate . " Valor del Departamento: " . $departamento;
            }
        }

        $sqlInsertPlantillaCoordP = "INSERT INTO Plantilla_CoordP (Nombre_Archivo_CoordP, Tamaño_Archivo_CoordP, Usuario_ID) VALUES (?, ?, ?)";
        $stmtInsertPlantillaCoordP = $conexion->prepare($sqlInsertPlantillaCoordP);
        $stmtInsertPlantillaCoordP->bind_param("sii", $fileName, $fileSize, $usuario_id);

        if ($stmtInsertPlantillaCoordP->execute()) {
            /*
            // Obtener el nombre del departamento
            $sql_coordinacion = "SELECT Departamentos FROM Departamentos WHERE Departamento_ID = (SELECT Departamento_ID FROM Usuarios WHERE Codigo = ?)";
            $stmt_departamento = $conexion->prepare($sql_coordinacion);
            
            if ($stmt_departamento) {
                $stmt_departamento->bind_param("s", $usuario_id);
                $stmt_departamento->execute();
                $result_departamento = $stmt_departamento->get_result();
                
                if ($result_departamento->num_rows > 0) {
                    $departamento = $result_departamento->fetch_assoc();
        
                    // Obtener correos de los usuarios de secretaría administrativa
                    $sql_secretaria = "SELECT Correo FROM Usuarios WHERE Rol_ID = 2";
                    $result_secretaria = $conexion->query($sql_secretaria);
        
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
                    echo json_encode(["success" => false, "message" => "No se encontró el departamento del usuario."]);
                }
            
                $stmt_departamento->close();
            } else {
                echo json_encode(["success" => false, "message" => "Error al preparar la consulta del departamento: " . $conexion->error]);
            }
            */
            echo json_encode([
                "success" => true, 
                "message" => "Archivo cargado y datos insertados correctamente en la base de datos."
            ]);
        } else {
            // Si hubo un error en la inserción
            echo json_encode([
                "success" => false, 
                "message" => "Error al insertar en Plantilla_CoordP: " . $stmtInsertPlantillaCoordP->error
            ]);
        }
    }
}

$output = ob_get_clean();
if (!empty($output)) {
    if (json_decode($output) === null) {
        echo json_encode([
            "success" => false, 
            "message" => "Error en el proceso: " . $output
        ]);
    } else {
        echo $output;
    }
}

$conexion->close();

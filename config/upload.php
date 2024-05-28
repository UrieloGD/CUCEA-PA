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
                $tabla_destino = 'Data_Economia';
                break;
            case 7:
                $tabla_destino = 'Data_Recursos_Humanos';
                break;
            case 8:
                $tabla_destino = 'Data_Metodos_Cuantitativos';
                break;
            case 9:
                $tabla_destino = 'Data_Politicas_Publicas';
                break;
            case 10:
                $tabla_destino = 'Data_Administracion';
                break;
            default:
                echo json_encode(["success" => false, "message" => "Departamento no válido."]);
                exit();
        }

        // Preparar la consulta SQL
        $sql = "INSERT INTO $tabla_destino (DEPARTAMENTO_ID, CICLO, NRC, FECHA_INI, FECHA_FIN, L, M, I, J, V, S, D, HORA_INI, HORA_FIN, EDIF, AULA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $errores = array();

        // Insertar los datos en la tabla correspondiente
        for ($row = 2; $row <= $highestRow; $row++) {
            // Empezar desde la fila 2 para omitir los encabezados
            $departamento_id = $departamento_id ?? null;
            $ciclo = $sheet->getCell('A' . $row)->getValue() ?? null;
            $nrc = $sheet->getCell('B' . $row)->getValue() ?? null;
            $fecha_ini = $sheet->getCell('C' . $row)->getFormattedValue() ?? null;
            $fecha_fin = $sheet->getCell('D' . $row)->getFormattedValue() ?? null;
            $l = $sheet->getCell('E' . $row)->getValue() ?? null;
            $m = $sheet->getCell('F' . $row)->getValue() ?? null;
            $i = $sheet->getCell('G' . $row)->getValue() ?? null;
            $j = $sheet->getCell('H' . $row)->getValue() ?? null;
            $v = $sheet->getCell('I' . $row)->getValue() ?? null;
            $s = $sheet->getCell('J' . $row)->getValue() ?? null;
            $d = $sheet->getCell('K' . $row)->getValue() ?? null;
            $hora_ini = $sheet->getCell('L' . $row)->getValue() ?? null;
            $hora_fin = $sheet->getCell('M' . $row)->getValue() ?? null;
            $edif = $sheet->getCell('N' . $row)->getValue() ?? null;
            $aula = $sheet->getCell('O' . $row)->getValue() ?? null;

            // Validaciones
            // CICLO: debe contener no más de 6 caracteres
            if (strlen($ciclo) > 6) {
                $errores[] = "El valor '$ciclo' en la columna CICLO no debe tener más de 6 caracteres.";
            }

            // NRC: debe tener no más de 7 caracteres
            if (strlen($nrc) > 7) {
                $errores[] = "El valor '$nrc' en la columna NRC tiene más de 7 caracteres.";
            }

            // Días: Solo deben contener la letra correspondiente
            $dias = ['L', 'M', 'I', 'J', 'V', 'S', 'D'];
            foreach ($dias as $dia) {
                $valor = strtolower($dia);
                if ($$valor != $dia && !empty($$valor)) {
                    $errores[] = "El valor '$$valor' en la columna $dia no es válido. Debe contener solo la letra $dia.";
                }
            }

            if (count($errores) > 0) {
                // Mostrar errores y detener el script si hay errores
                echo json_encode(["success" => false, "message" => $errores]);
                exit();
            }

            // Convertir fechas al formato YYYY-MM-DD
            if ($fecha_ini) {
                $fecha_ini = DateTime::createFromFormat('d/m/Y', $fecha_ini);
                if ($fecha_ini) {
                    $fecha_ini = $fecha_ini->format('Y-m-d');
                } else {
                    $errores[] = "El valor '$fecha_ini' en la columna FECHA_INI no es una fecha válida.";
                }
            }
            if ($fecha_fin) {
                $fecha_fin = DateTime::createFromFormat('d/m/Y', $fecha_fin);
                if ($fecha_fin) {
                    $fecha_fin = $fecha_fin->format('Y-m-d');
                } else {
                    $errores[] = "El valor '$fecha_fin' en la columna FECHA_FIN no es una fecha válida.";
                }
            }

            if (count($errores) > 0) {
                // Mostrar errores y detener el script si hay errores
                echo json_encode(["success" => false, "message" => $errores]);
                exit();
            }

            $stmt->bind_param("isssssssssssssss", $departamento_id, $ciclo, $nrc, $fecha_ini, $fecha_fin, $l, $m, $i, $j, $v, $s, $d, $hora_ini, $hora_fin, $edif, $aula);
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

$conn->close();
?>
<?php
include '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departamento_id = $_POST['Departamento_ID'];
    $nombre_archivo_dep = $_POST['Nombre_Archivo_Dep'];
    $fecha_subida_dep = $_POST['Fecha_Subida_Dep'];

    // Manejo del archivo subido
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $nombre_archivo = basename($_FILES['file']['name']);
        $directorio_subida = '../uploads/'; // Asegúrate de que este directorio exista y tenga permisos de escritura
        $ruta_archivo = $directorio_subida . $nombre_archivo;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $ruta_archivo)) {
            // Insertar los datos en la base de datos
            $sql = "INSERT INTO subir_plantilla (Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep) 
                    VALUES ('$departamento_id', '$nombre_archivo_dep', '$fecha_subida_dep')";

<<<<<<< HEAD
            if (mysqli_query($conexion, $sql)) {
                // Redirigir a la página principal tras una subida exitosa
                header("Location: ../plantillasPA.php?success=true&nombre_archivo=$nombre_archivo_dep&fecha_subida=$fecha_subida_dep"); 
                exit();
            } else {
                echo '<script>alert("Error añadiendo registro: ' . mysqli_error($conexion) . '");</script>';
            }
=======
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
                $tabla_destino = 'Data_Administraciún';
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
>>>>>>> 2a03537f2fdae694844407480815cf52128b76fe
        } else {
            echo '<script>alert("Error al mover el archivo subido.");</script>';
        }
    } else {
        echo '<script>alert("Error en la subida del archivo.");</script>';
    }

<<<<<<< HEAD
    mysqli_close($conexion);
} else {
    echo '<script>alert("Método de solicitud no permitido.");</script>';
}
?>
=======
$conn->close();
>>>>>>> 2a03537f2fdae694844407480815cf52128b76fe

<?php
// Asegurarse de que los errores no se muestren en la salida
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // ./functions/basesdedatos/actualizar-celda.php
    // Función para manejar errores y devolverlos como JSON
    function handleError($errno, $errstr, $errfile, $errline)
    {
        $response = [
            'success' => false,
            'error' => "Error: [$errno] $errstr en $errfile en la línea $errline"
        ];
        echo json_encode($response);
        exit;
    }

    // Establecer el manejador de errores
    set_error_handler("handleError");

    // Asegurarse de que la salida sea JSON
    header('Content-Type: application/json');

    // Usar POST directamente sin urldecode
    $value = $_POST['value'];
    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

    // Iniciar la sesión
    session_start();

    include './../../config/db.php';

    $response = ['success' => false, 'oldValue' => '', 'error' => ''];

    if (isset($_POST['id']) && isset($_POST['column']) && isset($_POST['value'])) {
        $id = mysqli_real_escape_string($conexion, $_POST['id']);
        $column = mysqli_real_escape_string($conexion, $_POST['column']);
        $value = mysqli_real_escape_string($conexion, $_POST['value']);
        $user_role = isset($_POST['user_role']) ? intval($_POST['user_role']) : -1;

        // Mapear los nombres de las columnas si es necesario
        $columnMap = [
            'ID' => 'ID_Plantilla',
            'CICLO' => 'CICLO',
            'CRN' => 'CRN',
            'MATERIA' => 'MATERIA',
            'CVE MATERIA' => 'CVE_MATERIA',
            'SECCIÓN' => 'SECCION',
            'NIVEL' => 'NIVEL',
            'NIVEL TIPO' => 'NIVEL_TIPO',
            'TIPO' => 'TIPO',
            'C. MIN' => 'C_MIN',
            'H. TOTALES' => 'H_TOTALES',
            'STATUS' => 'ESTATUS',
            'TIPO CONTRATO' => 'TIPO_CONTRATO',
            'CÓDIGO' => 'CODIGO_PROFESOR',
            'NOMBRE PROFESOR' => 'NOMBRE_PROFESOR',
            'CATEGORIA' => 'CATEGORIA',
            'DESCARGA' => 'DESCARGA',
            'CÓDIGO DESCARGA' => 'CODIGO_DESCARGA',
            'NOMBRE DESCARGA' => 'NOMBRE_DESCARGA',
            'NOMBRE DEFINITIVO' => 'NOMBRE_DEFINITIVO',
            'TITULAR' => 'TITULAR',
            'HORAS' => 'HORAS',
            'CÓDIGO DEPENDENCIA' => 'CODIGO_DEPENDENCIA',
            'L' => 'L',
            'M' => 'M',
            'I' => 'I',
            'J' => 'J',
            'V' => 'V',
            'S' => 'S',
            'D' => 'D',
            'DÍA PRESENCIAL' => 'DIA_PRESENCIAL',
            'DÍA VIRTUAL' => 'DIA_VIRTUAL',
            'MODALIDAD' => 'MODALIDAD',
            'FECHA INICIAL' => 'FECHA_INICIAL',
            'FECHA FINAL' => 'FECHA_FINAL',
            'HORA INICIAL' => 'HORA_INICIAL',
            'HORA FINAL' => 'HORA_FINAL',
            'MÓDULO' => 'MODULO',
            'AULA' => 'AULA',
            'CUPO' => 'CUPO',
            'OBSERVACIONES' => 'OBSERVACIONES',
            'EXTRAORDINARIO' => 'EXAMEN_EXTRAORDINARIO'
        ];

        // Si el usuario es superadmin (rol 0), use el department_id del POST
        // De lo contrario, use el Departamento_ID de la sesión
        if ($user_role === 0 && isset($_POST['department_id'])) {
            $departamento_id = intval($_POST['department_id']);
        } else {
            // Para usuarios normales, seguir usando el ID de la sesión
            if (!isset($_SESSION['Departamento_ID'])) {
                $response['error'] = "No se ha establecido el Departamento_ID en la sesión";
                echo json_encode($response);
                exit;
            }
            $departamento_id = $_SESSION['Departamento_ID'];
        }

        if (isset($columnMap[$column])) {
            $column = $columnMap[$column];
        }

        // Sección de validaciones numéricas:
        $numericColumns = [
            'CICLO' => 10,        // Máximo 10 caracteres
            'C_MIN' => 2,          // Máximo 2 dígitos
            'H_TOTALES' => 2,      // Máximo 2 dígitos
            'CODIGO_PROFESOR' => 9,// Código de 9 dígitos
            'CODIGO_DESCARGA' => 9,
            'HORA_INICIAL' => 4,   // Formato 24h -> 1330
            'HORA_FINAL' => 4,
            'CUPO' => 3            // Máximo 3 dígitos
        ];

        if (isset($numericColumns[$column])) {
            // Permitir vacío o número válido
            if ($value !== '') {
                // Validar formato según columna
                switch ($column) {
                    case 'HORA_INICIAL':
                    case 'HORA_FINAL':
                    default:
                        // Validar números enteros
                        if (!preg_match('/^\d+$/', $value)) {
                            $response['error'] = "$column debe ser un número entero";
                            echo json_encode($response);
                            exit;
                        }
                        
                        // Validar longitud máxima
                        if (strlen($value) > $numericColumns[$column]) {
                            $response['error'] = "$column no puede exceder {$numericColumns[$column]} dígitos";
                            echo json_encode($response);
                            exit;
                        }
                }
            }
        } 
        elseif ($column === 'HORAS') {
            // Validación existente para horas decimales
            if ($value !== '' && !preg_match('/^\d+\.?\d*$/', $value)) {
                $response['error'] = "HORAS debe ser un número decimal (ej. 2.5)";
                echo json_encode($response);
                exit;
            }
        }

        // Usar el departamento_id determinado anteriormente
        $sql_departamento = "SELECT Nombre_Departamento FROM departamentos WHERE Departamento_ID = $departamento_id";
        $result_departamento = mysqli_query($conexion, $sql_departamento);
        if (!$result_departamento) {
            $response['error'] = "Error al obtener el departamento: " . mysqli_error($conexion);
            echo json_encode($response);
            exit;
        }
        $row_departamento = mysqli_fetch_assoc($result_departamento);
        $tabla_departamento = "data_" . $row_departamento['Nombre_Departamento'];

        // Resto del código igual...
        // Obtener el valor antiguo antes de actualizar
        $sql_old = "SELECT `$column` FROM `$tabla_departamento` WHERE ID_Plantilla = '$id'";
        $result_old = mysqli_query($conexion, $sql_old);
        if (!$result_old) {
            $response['error'] = "Error al obtener el valor antiguo: " . mysqli_error($conexion);
            echo json_encode($response);
            exit;
        }
        $row_old = mysqli_fetch_assoc($result_old);
        $response['oldValue'] = $row_old[$column];

        // Actualizar el valor en la base de datos
        $sql = "UPDATE `$tabla_departamento` SET `$column` = '$value' WHERE `ID_Plantilla` = '$id'";
        if (mysqli_query($conexion, $sql)) {
            $response['success'] = true;
        } else {
            $response['error'] = "Error al actualizar: " . mysqli_error($conexion);
        }
    } else {
        $response['error'] = "Faltan datos requeridos";
    }

    error_log("SQL Query: $sql");
    error_log("Error: " . mysqli_error($conexion));

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

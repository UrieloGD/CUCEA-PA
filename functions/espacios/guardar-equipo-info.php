<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

$response = ['debug' => [], 'success' => false, 'error' => null];

try {
    include __DIR__ . '/../../config/db.php';
    $response['debug'][] = 'Database included';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $modulo = $_POST['modulo'] ?? '';
        $espacio = $_POST['espacio'] ?? '';
        $equipo = isset($_POST['equipo']) ? implode(',', $_POST['equipo']) : '';
        $observaciones = isset($_POST['observaciones']) ? mysqli_real_escape_string($conexion, $_POST['observaciones']) : '';
        $reportes = isset($_POST['reportes']) ? mysqli_real_escape_string($conexion, $_POST['reportes']) : '';

        $response['debug'][] = 'POST data: ' . json_encode($_POST);

        $query = "UPDATE Espacios SET 
                  Equipo = '$equipo', 
                  Observaciones = '$observaciones', 
                  Reportes = '$reportes' 
                  WHERE Modulo = '$modulo' AND Espacio = '$espacio'";

        $response['debug'][] = 'Query: ' . $query;

        $resultado = mysqli_query($conexion, $query);
        $response['debug'][] = 'Query executed';

        if ($resultado) {
            $response['success'] = true;
            $response['debug'][] = 'Update successful';
        } else {
            $response['error'] = mysqli_error($conexion);
            $response['debug'][] = 'MySQL error: ' . mysqli_error($conexion);
        }
    } else {
        $response['error'] = 'Método no permitido';
    }
} catch (Exception $e) {
    error_log("Caught exception: " . $e->getMessage());
    $response['debug'][] = 'Exception caught: ' . $e->getMessage();
    $response['error'] = 'Internal server error';
}

$response['debug'][] = 'Script completed';

header('Content-Type: application/json');
echo json_encode($response);

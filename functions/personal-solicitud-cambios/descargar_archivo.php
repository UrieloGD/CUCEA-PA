<?php
// functions/personal-solicitud-cambios/descargar_archivo.php
include('./../../config/db.php');

if (!isset($_GET['folio']) || !isset($_GET['tipo'])) {
    http_response_code(400);
    echo "Parámetros faltantes";
    exit;
}

$folio = $_GET['folio'];
$tipo = $_GET['tipo'];

try {
    $sql = "";
    
    switch ($tipo) {
        case 'baja':
            $sql = "SELECT ARCHIVO_ADJUNTO_VALIDACION, NOMBRE_ARCHIVO_VALIDACION, TIPO_ARCHIVO_VALIDACION 
                    FROM solicitudes_baja 
                    WHERE OFICIO_NUM_BAJA = ?";
            break;
        case 'propuesta':
            // Agregar consulta para propuesta si es necesario
            break;
        case 'baja-propuesta':
            // Agregar consulta para baja-propuesta si es necesario
            break;
        default:
            http_response_code(400);
            echo "Tipo no válido";
            exit;
    }
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $folio);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($fila = $resultado->fetch_assoc()) {
        if ($fila['ARCHIVO_ADJUNTO_VALIDACION']) {
            // Configurar headers para la descarga
            header('Content-Type: ' . $fila['TIPO_ARCHIVO_VALIDACION']);
            header('Content-Disposition: inline; filename="' . $fila['NOMBRE_ARCHIVO_VALIDACION'] . '"');
            header('Content-Length: ' . strlen($fila['ARCHIVO_ADJUNTO_VALIDACION']));
            header('Cache-Control: no-cache, must-revalidate');
            
            // Enviar el archivo
            echo $fila['ARCHIVO_ADJUNTO_VALIDACION'];
        } else {
            http_response_code(404);
            echo "Archivo no encontrado";
        }
    } else {
        http_response_code(404);
        echo "Solicitud no encontrada";
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
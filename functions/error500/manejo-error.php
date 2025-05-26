<?php 
// Inicia buffer de salida al comienzo del script
ob_start();

// Función para detectar si es una petición AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTT_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Función para detectar si es una petición de modal
function isModalRequest() {
    // Detecta por parámetros específicos de modales
    return isset($_POST['modal_action']) || isset($_GET['modal_action']) || strpos($_SERVER['REQUEST_URI'], 'modal') !== false || strpos($_SERVER['HTTP_REFERER'] ?? '', 'modal') !== false;
}

// Configura manejo de errores para mostrar la página personalizada 500
function handleError500() {
    // Buffer de salida para evitar errores de "header already sent"
    ob_clean(); //Limpia cualquier buffer de salida existente

    // Si es una petición AJAX o de modal, devolver JSON
    if(isAjaxRequest() || isModalRequest()) {
        if(!headers_sent()) {
            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
        }

        echo json_encode([
            'success' => false,
            'error' => true,
            'message' => 'Ha ocurrido un error interno del servidor',
            'error_type' => 'server_error',
            'show_modal_error' => true
        ]);
        exit;
    }

    // Sies una petición normal, redirigir a la página de error
    $base_url = (isset($_SERVER['HTTP']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');

    header("Location: $base_url$path/500.php");
    exit;
}

function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // registra el error en un archivo log
    error_log("Error [$errno] $errstr - File: $errfile - Line: $errline", 0);

    //Para errores graves, manejar segúndo tipo de petición
    if($errno == E_ERROR || $errno == E_CORE_ERROR || $errno == E_COMPILE_ERROR || $errno == E_USER_ERROR || $errno == E_RECOVERABLE_ERROR) {
        handleError500();
    }   
    // Para errores mejos graves, simplemente devolver false para permitir que php maneje el error
    return false;
}

// Función para manejar excepciones
function customExceptionHandler($exception) {
    // Registra la excepción en un archivo log
    error_log("Excepción no capturada: " . $exception->getMessage() . " - File: " . $exception->getFile() . " - get Line: " . $exception -> getLine(), 0);

    // Maneja según el tipo de petición
    handleError500();
}

function shutdownHandler() {
    $error = error_get_last();

    // Si hay un error fatal que no fue capturado por el manejador de datos
    if($error && ($error['type'] === E_ERROR || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR || $error['type'] == E_PARSE)) {
        // Registra el error
        error_log("Error fatal: " . $error['message'] . " - File: " . $error['file'] . " - Line: " . $error['line'] , 0);
        
        // SI es una petición AJAX o modal
        if(isAjaxRequest() || isModalRequest()) {
            if(!headers_sent()) { 
                header('Content-Type application/json');
                header('HTTP/1.1 500 Internal Server Error');
            }

            echo json_encode([
                'success' => false,
                'error' => true,
                'message' => 'Error fatal de servidor',
                'error_type' => 'fatal_error',
                'show_modal_error' => true
            ]);
            exit;
        } 

        // Para peticiones normales
        if(!headers_sent()) {
            handleError500();
        } else {
            // Si los encabezados ya fueron enviados, mostrar un mensaje simple
            echo "<h1>Error Interno del Servidor</h1>";
            echo "<p>Lo sentimos, ha ocurrido un error inesperado.</p>";
            echo "<p><a href='./home.php'>Volver al inicio</a></p>";
        }
    }
}

// Función para trigger manual de errores en modales
function triggerModalError($message = "Ha ocurrido un error", $details = null) {
    if(isAjaxRequest() || isModalRequest()) {
        header('Content-Type: application/json');
        header('HPPT/1.1 500 Internal Server Error');

        echo json_encode([
            'success' => false,
            'error' => true,
            'message' => $message,
            'details' => $details,
            'error_type' => 'custom_error',
            'show_modal_error' => true
        ]);
        exit;
    } else {
        // Para peticiones normales, usar el método tradicional
        handleError500();
    }
}

// Configurar los manejadores de errores
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");
register_shutdown_handler("shutdownHandler");

// Configura el display_errors para entornos
$environment = 'development'; // Cambiar a production en producción
if($environment === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php-errors.log');
} else {
    ini_set('display_errors', 1);
}

// Función auxiliar para validar datos de entrada en modales 
function validateModalInput($data, $required_fields = []){
    foreach ($required_fields as $field) {
        if(!isset($data[$field]) || empty(trim($data[$field]))) {
            triggerModalError("Campo requerido faltante: $field");
        }
    }
    return true;
}

// Función para manejar errores de base de datos en modales
function handleDatabaseError($connection, $query = null) {
    $error_message = mysqli_error($connection);

    error_log("Database Error: $error_message" . ($query ? " - Query: $query" : ""));

    if(isAjaxRequest() || isModalRequest()) {
        triggerModalError("Error en la base de datos", $error_message);
    } else {
        handleError500();
    }
}
?>
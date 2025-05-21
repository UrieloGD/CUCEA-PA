<?php 
// Inicia buffer de salida al comienzo del script
ob_start();

// Configura manejo de errores para mostrar la página personalizada de error 500 
function handleError500(){
    // Buffer de salida para evitar errores de "headers already sent"
    ob_clean(); // Limpia cualquier buffer de salida existente

    // Redirige a la página de error 500
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');

    // Redirige a la pagina de error 
    header("Location: $base_url$path/500.php");
    exit;
}
// Función personalizada para manejar errores
function customErrorHandler($errno, $errstr, $errfile, $errline){
    // Registra el error en un archivo de log
    error_log("Error [$errno] $errstr - File: $$errfile - Line: $errline", 0);

    // Para errores graves, redirigir a la página de error 500
    if ($errno == E_ERROR || $errno == E_CORE_ERROR || $errno == E_COMPILE_ERROR || $errno == E_USER_ERROR || $errno == E_RECOVERABLE_ERROR) {
            handleError500();
    }
    // Para errores menos graves, simplemente devolver false para permitir que PHP maneje el error
    return false;
}

// Función para manejar excepciones
function customExceptionHandler($exception){
    // Registra la exceptión en un archivo de log
    error_log("Excepción no capturada: " . $exception->getMessage() .
        " - File: " . $exception->getFile() .
        " - Line: " . $exception->getLine(), 0);

    // muestra la página de error 500
    handleError500();
}

// Función para manejar errores fatales
function shutdownHandler(){
    $error = error_get_last();

    // Si hay un error fatal que no fue capturado por el manejador de errores
    if ($error && ($error['type'] === E_ERROR ||
                $error['type'] === E_CORE_ERROR ||
                $error['type'] === E_COMPILE_ERROR ||
                $error['type'] === E_PARSE)) {
        // Registra el error
        error_log("Error fatal: " . $error['message'].
        " - File: " . $error['file'] . 
        " - Line: " . $error['line'], 0);

        // Intentar redirigir a la página de error
        if (!headers_sent()) {
            handleError500();
        } else {
            // Si los encabezados ya fueron enviados, mostrar un mensaje simple
            echo "<h1>Error Interno del Servidor</h1>";
            echo "<p>Lo sentimos, ha ocurrido un error inesperado.</p>";
            echo "<p><a href='./home.php'>Volver al inicio</a></p>";
        }
    }
}

// Configurar los manejadores de errores
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");
register_shutdown_function("shutdownHandler");

// Configura el display_errors para entornos
$environment = 'development'; // Cambiar a 'development' durante pruebas
if($environment === 'production'){
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php-errors.log');
} else {
    ini_set('display_errors', 1);
}
?>
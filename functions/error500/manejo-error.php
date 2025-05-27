<?php 
// Inicia buffer de salida al comienzo del script
ob_start();

// Variable global para controlar si ya se mostró un error
$error_displayed = false;

// Función para renderizar el contronido de error 500 inline
function rendererror500Inline() {
    global $error_displayed;

    // Evita mostrar múltiples errores
    if($error_displayed){
        return;
    }

    // CSS específico para el error inline
    echo '<link rel="stylesheet" href="./CSS/errores/500.css" />';
    
    // Contenido del error
    echo '<div class="cuadro-principal">
            <div class="container-error">
                <div class="img-error">
                    <img src="./Img/img-errores/500.png" alt="Error 500" onerror="this.style.display=\'none\'">
                </div>
                <div class="text-error">
                    <p>
                        Error 500!<br>
                        Error en el servidor
                    </p>
                </div>
            </div>
            <div class="container-btn">
                <button class="boton-inicio">
                    <a href="./home.php">Regresar al inicio</a>
                </button>
            </div>
        </div>';
    
    // Detener la ejecución del resto del contenido
    exit;
}

// Configura manejo de errores para mostrar la página personalizada de error 500
function handleError500() {
    // Si estamos en una petición AJAX, devolver JSON
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower('HTTP_X_REQUESTED_EITH') == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Error interno del servidor',
            'code' => 500
        ]);
        exit;
    }

    // Buffer de salida para evitar errores de "headers already sent"

    // NE lugar de redireccionar, renderiza inline
    rendererror500Inline();
}

// Función personalizada para manejar errores
function customErrorHandler($errno, $errstr, $errfile, $errline){
    // Registra el error en un archivo log
    error_log("Error [$errno $errstr - File: $errfile - Line: $errline", 0);

    // Para errores graves, mostrar error inline
    if($errno == E_ERROR || $errno == E_CORE_ERROR || $errno == E_COMPILE_ERROR || 
    $errno == E_USER_ERROR || $errno == E_RECOVERABLE_ERROR) {
        handleError500();
    }

    // Para errores menos graves, permitir que PHP maneje el error normalmente
    return false;
}

// Función para manejar excepciones
function customExceptionHandler($exception){
    // Registra la exceptión en un archivo de log
    error_log("Excepción no capturada: " . $exception->getMessage() . " - File: " . $exception->getFile() . " - Line: " . $exception->getLine(), 0);

    // Muestra la pagina de error inline
    handleError500();
}

function shutdownHandler() {
    $error = error_get_last();

    // Si hay un error fatal que no fue capturado por el manejador de errores
    if($error && ($error['type'] === E_ERROR || $error['type'] === E__CORE_ERROR || $error['type'] === E__COMPILE_ERROR || $error['type'] === E_PARSE)) {
        // Registra el error
        error_log("Error fatal: " . $error['message'] . " - File: " . $error['file'] . " - Line: " . $error['line'], 0);

        // Intenta mostrar error inline
        if(!headers_sent()){
            handleError500();
        } else {
            // Si los encabezsados ya fueron enviados, mostrar un mensaje simple inline
            global $error_displayed;
            if (!$error_displayed) {
                echo '<div style="text-align: center; padding: 40px; background: #f8f9fa; border: 2px solid #dc3545; margin: 20px; border-radius: 10px;">
                        <h1 style="color: #dc3545;">Error Interno del Servidor</h1>
                        <p>Lo sentimos, ha ocurrido un error inesperado.</p>
                        <p><a href="./home.php" style="color: #007bff; text-decoration: none;">Volver al inicio</a></p>
                      </div>';
                $error_displayed = true;
            }
        }
    }
}

// Configura lo manejadores de errores
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");
register_shutdown_function("shutdownHandler");

// Configura el display_errors para entorno
$environment = 'development'; // Cambiar a 'production' en producción
if($environment === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/log/php-errors.log');
} else {
    ini_set('display_errors', 1);
}
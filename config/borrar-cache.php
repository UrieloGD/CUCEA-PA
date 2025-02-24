<?php
/**
 * Función para aplicar control de caché global
 * Crea una variable global con la marca de tiempo actual
 * que puede ser usada en toda la aplicación
 */
function initGlobalCacheBuster() {
    // Define una constante global con el timestamp actual
    if (!defined('CACHE_VERSION')) {
        define('CACHE_VERSION', time());
    }
    
    // También puedes guardarla en una variable de sesión si prefieres
    // $_SESSION['CACHE_VERSION'] = time();
}

/**
 * Obtiene la URL con el parámetro de versión ya añadido
 * para uso en etiquetas en el head o en templates
 */
function getCacheBusterURL() {
    return "?v=" . CACHE_VERSION;
}
?>
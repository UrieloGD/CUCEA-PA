<?php
/*
    # Configuración del sistema de mantenimiento por secciones
    # Este archivo contiene la configuración para el modo de mantenimiento de pestañas especificas
*/

// COnfiguración global
$global_maintenance = false; // Si es true, todo el sitio estará en mantenimiento

// IPs que siempre pueden acceder incluso durante el mantenimiento
$allowedIPs = array('127.0.0.1', '::1'); // '127.0.0.1', '::1' para local

// Configuración por secciones
$section_maintenance = array(
    'home' => true,
    'plantilla' => true,
    'admin-plantilla' => false,
    'data-departamentos' => false,
    'calendario' => true,
    'espacios' => true,
    'admin-home' => false,
    'admin-reportes' => false,
    'admin-eventos' => false,
    'admin-usuarios' => false,
    'basesdedatos' => true,
    'plantilla-coordpers' => false,
    'bd-coordpers' => true,
    'horas-comparacion' => true,
    'personal-solicitud-cambios' => true,
    'guiaPA' => true,
    'oferta-dash' => true
);

// Configuración de mensajes personalizados por sección 
$maintenance_messages = array(
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a',
    'a' => 'a'
);

// Mensages por defecto
$default_message = 'Esta sección se encuentra temporalmente en mantenimiento. Disculpe las molestias.';

// TImepo de recarga automática de la página (en segundos, 0 para desactivar
$refresh_time = 0;

function isAllowedIP($allowedIPs) {
    $userIP = $_SERVER['REMOTE_ADDR'];
    return in_array($userIP, $allowedIPs);
}

// Funcion para comprobar si una sección está en mantenimiento
function isSectionInMaintenance($section) {
    global $global_maintenance, $section_maintenance, $allowedIPs;

    // Si el mantenimiento global esta activado, tolas las secciones están en mantenimiento
    if($global_maintenance && !isAllowedIP($allowedIPs)){
        return true;
    }

    // Comprueba si la sección específica está en mantenimiento
    if (isset($section_maintenance[$section]) && $section_maintenance[$section] && !isAllowedIP($allowedIPs)) {
        return true;
    }
    return false;
}

// Función para obtener el mensaje de mantenimiento para una sección
function getMaintenanceMessage($section){
    global $maintenance_messages, $default_message;

    if (isset($maintenance_messages[$section])) {
        return $maintenance_messages[$section];
    }

    return $default_message;
}
?>
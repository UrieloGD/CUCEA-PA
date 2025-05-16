<?php
// Configuración del modo mantenimiento
$maintenance = true;  // Cambiar a false cuando el mantenimiento haya terminado
$allowedIPs = array('127.0.0.1', '::1'); // IPs que pueden acceder durante el mantenimiento 

// Comprobar si la IP del visitante está en la lista de permitidas
function isAllowedIP($allowedIPs) {
    $userIP = $_SERVER['REMOTE_ADDR'];
    return in_array($userIP, $allowedIPs);
}

// Si el sitio está en mantenimiento y la IP no está permitida, mostrar página de mantenimiento
if ($maintenance && !isAllowedIP($allowedIPs)) {
    // Establecer el código de estado HTTP correcto para mantenimiento
    header('HTTP/1.1 503 Service Unavailable');
    header('Retry-After: 3600'); // Sugiere al navegador volver a intentar en 1 hora
    
    // Incluir el archivo HTML de la página de mantenimiento
    include 'p-mantenimiento.php';
    exit(); // Detener la ejecución del resto del script
}

// Si no está en mantenimiento o la IP está permitida, continuar con la carga normal del sitio
?>
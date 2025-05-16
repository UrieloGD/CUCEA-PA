<?php 
/*
    Verificado de mantenimiento para secciones específicas
    Este archivo se eincluye al inicio de cada sección 
*/
require_once 'config-mantenimiento.php';

// Función para verificar y mostrar la página de mantenimiento si es necesario
function checkMaintenance($section){
    if(isSectionInMaintenance($section)){
        include 'mantenimiento-section.php';
        exit();
    }
}
?>
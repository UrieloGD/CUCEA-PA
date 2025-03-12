<?php
// ./personal-solicitud-cambios/obtener_oficio_baja.php
require_once './../../config/db.php';

$anio_actual = date('y');

$sql_ultimo = "SELECT OFICIO_NUM_BAJA FROM solicitudes_baja 
               WHERE OFICIO_NUM_BAJA LIKE 'SA/CP/%/$anio_actual' 
               ORDER BY ID_BAJA DESC LIMIT 1";
$result = $conexion->query($sql_ultimo);

$siguiente_numero = '0001'; // Valor inicial correcto

if ($result && $result->num_rows > 0) {
    $ultimo = $result->fetch_assoc()['OFICIO_NUM_BAJA'];
    preg_match('/SA\/CP\/(\d{4})\/'.$anio_actual.'/', $ultimo, $matches);
    $ultimo_numero = intval($matches[1]);
    $siguiente_numero = str_pad($ultimo_numero + 1, 4, '0', STR_PAD_LEFT);
}

$oficio_num = "SA/CP/$siguiente_numero/$anio_actual";

echo json_encode([
    'status' => 'success',
    'siguiente_numero' => $oficio_num
]);
?>
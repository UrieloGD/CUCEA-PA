<?php
include './../../config/db.php';

$modulo = $_GET['modulo'];
$dia = $_GET['dia'];
$hora_inicio = $_GET['hora_inicio'];
$hora_fin = $_GET['hora_fin'];

$departamentos = [
    'Estudios_Regionales', 'Finanzas', 'Ciencias_Sociales', 'PALE', 'Posgrados',
    'Economía', 'Recursos_Humanos', 'Métodos_Cuantitativos', 'Políticas_Públicas',
    'Administración', 'Auditoría', 'Mercadotecnia', 'Impuestos',
    'Sistemas_de_Información', 'Turismo', 'Contabilidad'
];

$espacios_ocupados = array();

foreach ($departamentos as $departamento) {
    $tabla = "Data_" . str_replace(' ', '_', $departamento);
    
    $query = "SELECT DISTINCT AULA FROM $tabla 
              WHERE MODULO = '$modulo' 
              AND $dia IS NOT NULL 
              AND HORA_INICIAL <= '$hora_fin' 
              AND HORA_FINAL >= '$hora_inicio'";

    $result = mysqli_query($conexion, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!in_array($row['AULA'], $espacios_ocupados)) {
                $espacios_ocupados[] = $row['AULA'];
            }
        }
    } else {
        // Opcional: registrar error si la consulta falla
        error_log("Error en la consulta para la tabla $tabla: " . mysqli_error($conexion));
    }
}

echo json_encode($espacios_ocupados);
?>
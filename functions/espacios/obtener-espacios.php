<?php
include './../../config/db.php';

function getEspaciosFiltrados($conexion, $edificio, $dia, $hora_inicio, $hora_fin, $ciclo) {
    $departamentos = ['Estudios_Regionales', 'Finanzas', 'Ciencias_Sociales', 'PALE', 'Posgrados', 'Economia', 'Recursos_Humanos', 'Metodos_Cuantitativos', 'Politicas_Publicas', 'Administracion', 'Auditoria', 'Mercadotecnia', 'Impuestos', 'Sistemas_de_Informacion', 'Turismo', 'Contabilidad'];
    
    $query = "SELECT DISTINCT e.Edificio, e.Espacio, e.Etiqueta, 
                     CASE WHEN d.Ocupado IS NOT NULL THEN 'Ocupado' ELSE 'Disponible' END AS Estado
              FROM Espacios e
              LEFT JOIN (";
    
    foreach ($departamentos as $index => $dep) {
        if ($index > 0) {
            $query .= " UNION ALL ";
        }
        $query .= "SELECT MODULO, AULA, '$dia' AS Ocupado
                   FROM Data_$dep
                   WHERE CICLO = '$ciclo'
                     AND HORA_INICIAL <= '$hora_fin'
                     AND HORA_FINAL >= '$hora_inicio'
                     AND $dia IS NOT NULL";
    }
    
    $query .= ") d ON e.Edificio = d.MODULO AND e.Espacio = d.AULA
                WHERE e.Edificio = '$edificio'
                ORDER BY e.Espacio";
    
    $result = mysqli_query($conexion, $query);
    $espacios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $espacios[] = $row;
    }
    return $espacios;
}

$edificio = $_POST['edificio'];
$dia = $_POST['dia'];
$horario_inicio = $_POST['horario_inicio'];
$horario_fin = $_POST['horario_fin'];
$ciclo = $_POST['ciclo'];

$espacios = getEspaciosFiltrados($conexion, $edificio, $dia, $horario_inicio, $horario_fin, $ciclo);

// Generar HTML para los espacios
$html = "<div class='cuadro-grande'>";
foreach ($espacios as $espacio) {
    $html .= "<div class='sala-container'>";
    $html .= "<span class='sala-texto'>" . $espacio['Espacio'] . "</span>";
    $html .= "<div class='sala " . strtolower($espacio['Etiqueta']) . " " . strtolower($espacio['Estado']) . "'>";
    $html .= "<img src='./Img/icons/iconos-espacios/icono-" . strtolower($espacio['Etiqueta']) . ".png'>";
    $html .= "</div>";
    $html .= "</div>";
}
$html .= "</div>";

echo $html;
?>
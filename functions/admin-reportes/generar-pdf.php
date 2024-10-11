<?php
require_once('./../../library/tcpdf.php'); // Ajusta la ruta según donde hayas instalado TCPDF
include './../../config/db.php';

// Crea un nuevo documento PDF - 'P' significa orientación vertical, 'mm' es la unidad de medida, 'A4' es el tamaño
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Establece los márgenes del documento (izquierda, arriba, derecha)
$pdf->SetMargins(5, 5, 15);

// Desactiva el encabezado y pie de página predeterminados
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Agrega una nueva página
$pdf->AddPage();

// Configura el color de relleno para el encabezado (RGB: 0,113,176 - el color azul #0071b0)
$pdf->SetFillColor(0, 113, 176);

// Crea un rectángulo redondeado para el título
// Parámetros: (posición X, posición Y, ancho, alto, radio de esquinas, esquinas a redondear, estilo)
// '0010' significa que solo redondea las esquinas inferiores
$pdf->RoundedRect(($pdf->GetPageWidth() - 90) / 2, 5, 90, 12, 3, '0110', 'F');

// Establece el color del texto a blanco
$pdf->SetTextColor(255, 255, 255);

// Configura la fuente (familia, estilo [B=negrita], tamaño)
$pdf->SetFont('helvetica', 'B', 16);

// Crea una celda con el título (ancho, alto, texto, borde, salto de línea, alineación)
$pdf->Cell(0, 12, 'Reporte de Entrega', 0, 1, 'C');

// Agrega espacio vertical
$pdf->Ln(10);

// Obtener datos
$sql_departamentos = "SELECT d.*, MAX(p.Fecha_Subida_Dep) AS Fecha_Subida_Dep
                      FROM Departamentos d
                      LEFT JOIN Plantilla_Dep p ON d.Departamento_ID = p.Departamento_ID
                      GROUP BY d.Departamento_ID";
$result_departamentos = mysqli_query($conexion, $sql_departamentos);

// Obtener fecha límite
$sql_fecha_limite = "SELECT Fecha_Limite FROM Fechas_Limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$fecha_limite = null;
if ($result_fecha_limite && mysqli_num_rows($result_fecha_limite) > 0) {
    $fecha_limite = mysqli_fetch_assoc($result_fecha_limite)['Fecha_Limite'];
}

$periodo_actual = "2025A";

// Función para crear una celda con fondo blanco y borde
function createWhiteCell($pdf, $text, $width, $align = 'L') {
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($width, 8, $text, 1, 0, $align, true);
}

while ($departamento = mysqli_fetch_assoc($result_departamentos)) {
    $fecha_subida = $departamento['Fecha_Subida_Dep'];
    $fecha_actual = date("d-m-Y");
    
    // Buscar justificación
    $sql_justificacion = "SELECT Justificacion FROM Justificaciones 
    WHERE Departamento_ID = {$departamento['Departamento_ID']}
    ORDER BY Fecha_Justificacion DESC LIMIT 1";
    $result_justificacion = mysqli_query($conexion, $sql_justificacion);
    $tiene_justificacion = mysqli_num_rows($result_justificacion) > 0;
    
    // Determinar estado y notas
    if ($fecha_subida !== null) {
        $fecha_entrega = date("d/m/Y H:i", strtotime($fecha_subida));
        
        if ($tiene_justificacion) {
            $estado_entrega = "Entregada";
            $notas_justificacion = "Entregado con retraso. ";
        } else {
            $estado_entrega = "Entregada";
            $notas_justificacion = "Entregado a tiempo. ";
        }
    } else {
        if ($fecha_limite && $fecha_actual > $fecha_limite) {
            $estado_entrega = "Atrasada";
            $fecha_entrega = "-";
            $notas_justificacion = "No entregado. Fecha límite excedida. ";
        } else {
            $estado_entrega = "Pendiente";
            $fecha_entrega = "-";
            $notas_justificacion = "Aún sin entregar. ";
        }
    }
    
    if ($tiene_justificacion) {
        $justificacion = mysqli_fetch_assoc($result_justificacion)['Justificacion'];
        $notas_justificacion .= "\nJustificación: " . $justificacion;
    }
    
    // Dibujar el cuadro del departamento
    $pdf->SetFillColor(231, 233, 242); // Color #E7E9F2
    $startY = $pdf->GetY();
    $pdf->RoundedRect($pdf->GetX(), $startY, 260, 50, 3, '1111', 'F');
    
    // Primera fila
    $pdf->SetXY($pdf->GetX() + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 5, 'Departamento', 0, 0);
    $pdf->SetX($pdf->GetX() + 45);
    $pdf->Cell(30, 5, 'Periodo', 0, 0);
    $pdf->SetX($pdf->GetX() + 45);
    $pdf->Cell(30, 5, 'Estado de la entrega', 0, 1);
    
    // Valores de la primera fila
    $pdf->SetX($pdf->GetX() + 5);
    createWhiteCell($pdf, $departamento['Departamentos'], 70);
    $pdf->SetX($pdf->GetX() + 5);
    createWhiteCell($pdf, $periodo_actual, 70);
    $pdf->SetX($pdf->GetX() + 5);
    
    // Estilo del estado según su valor
    switch(strtolower($estado_entrega)) {
        case 'entregada':
            $pdf->SetFillColor(223, 241, 216);
            $pdf->SetTextColor(59, 118, 61);
            break;
        case 'pendiente':
            $pdf->SetFillColor(253, 248, 228);
            $pdf->SetTextColor(139, 110, 59);
            break;
        case 'atrasada':
            $pdf->SetFillColor(249, 209, 211);
            $pdf->SetTextColor(149, 61, 66);
            break;
    }
    $pdf->Cell(70, 8, $estado_entrega, 1, 1, 'C', true);
    
    // Segunda fila
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetX($pdf->GetX() + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 5, 'Fecha límite', 0, 0);
    $pdf->SetX($pdf->GetX() + 45);
    $pdf->Cell(30, 5, 'Fecha de entrega', 0, 1);
    
    // Valores de la segunda fila
    $pdf->SetX($pdf->GetX() + 5);
    createWhiteCell($pdf, ($fecha_limite ? date("d/m/Y", strtotime($fecha_limite)) : "-"), 70);
    $pdf->SetX($pdf->GetX() + 5);
    createWhiteCell($pdf, $fecha_entrega, 70, 'L');
    $pdf->Ln();
    
    // Notas/Justificación
    $pdf->SetX($pdf->GetX() + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 5, 'Notas/Justificación', 0, 1);
    $pdf->SetX($pdf->GetX() + 5);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(250, 8, strip_tags($notas_justificacion), 1, 'L', true);
    
    $pdf->Ln(10);
}

// Generar el PDF
$pdf->Output('reporte_entrega.pdf', 'I');
?>
<?php
require_once('./../../library/tcpdf.php'); // Ajusta la ruta según donde hayas instalado TCPDF
include './../../config/db.php';

// Crear nuevo documento PDF con orientación horizontal
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Configurar márgenes (izquierda, arriba, derecha)
$pdf->SetMargins(15, 15, 15);

// Establecer información del documento
$pdf->SetCreator('Tu Sistema');
$pdf->SetAuthor('Nombre de tu organización');
$pdf->SetTitle('Reporte de Entrega');

// Eliminar encabezados y pies de página predeterminados
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Agregar una página
$pdf->AddPage();

// Título del reporte con estilo similar
$pdf->SetFillColor(0, 113, 176); // Color #0071b0
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 12, 'Reporte de Entrega', 0, 1, 'C', true);
$pdf->Ln(10);

// Restablecer color de texto
$pdf->SetTextColor(0, 0, 0);

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

while ($departamento = mysqli_fetch_assoc($result_departamentos)) {
    $fecha_subida = $departamento['Fecha_Subida_Dep'];
    $fecha_actual = date("Y-m-d");
    
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
    
    // Dibujar el "cuadro" del departamento
    $pdf->SetFillColor(231, 233, 242); // Color #E7E9F2
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 260, 50, 'F');
    
    // Información del departamento
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY($pdf->GetX() + 5, $pdf->GetY() + 5);
    
    // Primera fila de información
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 7, 'Departamento:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(80, 7, $departamento['Departamentos'], 0, 0);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 7, 'Periodo:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 7, $periodo_actual, 0, 0);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 7, 'Estado:', 0, 0);
    
    // Estilo del estado según su valor
    switch(strtolower($estado_entrega)) {
        case 'entregada':
            $pdf->SetFillColor(223, 241, 216); // Verde claro
            $pdf->SetTextColor(59, 118, 61);
            break;
        case 'pendiente':
            $pdf->SetFillColor(253, 248, 228); // Amarillo claro
            $pdf->SetTextColor(139, 110, 59);
            break;
        case 'atrasada':
            $pdf->SetFillColor(249, 209, 211); // Rojo claro
            $pdf->SetTextColor(149, 61, 66);
            break;
    }
    
    $pdf->Cell(40, 7, $estado_entrega, 0, 1, 'C', true);
    
    // Restablecer colores
    $pdf->SetTextColor(0, 0, 0);
    
    // Segunda fila
    $pdf->SetXY($pdf->GetX() + 5, $pdf->GetY() + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 7, 'Fecha límite:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(80, 7, ($fecha_limite ? date("d/m/Y", strtotime($fecha_limite)) : "-"), 0, 0);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 7, 'Fecha de entrega:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(80, 7, $fecha_entrega, 0, 1);
    
    // Notas/Justificación
    $pdf->SetXY($pdf->GetX() + 5, $pdf->GetY() + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 7, 'Notas/Justificación:', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetX($pdf->GetX() + 5);
    $pdf->MultiCell(250, 7, strip_tags($notas_justificacion), 0, 'L');
    
    $pdf->Ln(10);
}

// Generar el PDF
$pdf->Output('reporte_entrega.pdf', 'I');
?>
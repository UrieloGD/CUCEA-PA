<?php
require_once('./../../library/tcpdf.php');
include './../../config/db.php';

class MYPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'C', 0);
    }
}

$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 5, 15);
$pdf->setPrintHeader(false);
$pdf->setFooterFont(Array('helvetica', '', 8));
$pdf->setFontSubsetting(false);
$pdf->AddPage();

// Función para crear el encabezado azul
function createHeader($pdf) {
    $pdf->SetFillColor(0, 113, 176);
    $rectWidth = 120;
    $rectX = ($pdf->GetPageWidth() - $rectWidth) / 2;
    $pdf->RoundedRect($rectX, 5, $rectWidth, 12, 3, '0110', 'F'); // Posición Y cambiada a 5
    
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetXY($rectX, 5); // Posición Y cambiada a 5
    $pdf->Cell($rectWidth, 12, 'Reporte de Entrega', 0, 1, 'C');
    $pdf->Ln(5);
}

// Obtener datos
$sql_departamentos = "SELECT d.*, MAX(p.Fecha_Subida_Dep) AS Fecha_Subida_Dep
                      FROM departamentos d
                      LEFT JOIN plantilla_dep p ON d.Departamento_ID = p.Departamento_ID
                      GROUP BY d.Departamento_ID";
$result_departamentos = mysqli_query($conexion, $sql_departamentos);

// Obtener fecha límite
$sql_fecha_limite = "SELECT Fecha_Limite FROM fechas_limite ORDER BY Fecha_Actualizacion DESC LIMIT 1";
$result_fecha_limite = mysqli_query($conexion, $sql_fecha_limite);
$fecha_limite = null;
if ($result_fecha_limite && mysqli_num_rows($result_fecha_limite) > 0) {
    $fecha_limite = mysqli_fetch_assoc($result_fecha_limite)['Fecha_Limite'];
}

$periodo_actual = "2025B";
$recuadros_por_pagina = 0;
$max_recuadros_por_pagina = 4; // Cambiado a 4 recuadros por página

createHeader($pdf);

// Función modificada para crear celda sin bordes
function createWhiteCell($pdf, $text, $width, $align = 'L') {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Dibuja el fondo redondeado blanco
    $pdf->SetFillColor(255, 255, 255);
    $pdf->RoundedRect($startX, $startY, $width, 8, 1, '1111', 'F');
    
    // Coloca el texto
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY($startX, $startY);
    $pdf->Cell($width, 8, $text, 0, 0, $align, false);
}

while ($departamento = mysqli_fetch_assoc($result_departamentos)) {
    if ($recuadros_por_pagina >= $max_recuadros_por_pagina) {
        $pdf->AddPage();
        createHeader($pdf);
        $recuadros_por_pagina = 0;
    }

    $fecha_subida = $departamento['Fecha_Subida_Dep'];
    $fecha_actual = date("d-m-Y");
    
    // Buscar justificación
    $sql_justificacion = "SELECT Justificacion FROM justificaciones 
    WHERE Departamento_ID = {$departamento['Departamento_ID']}
    ORDER BY Fecha_Justificacion DESC LIMIT 1";
    $result_justificacion = mysqli_query($conexion, $sql_justificacion);
    $tiene_justificacion = mysqli_num_rows($result_justificacion) > 0;
    
    // Determinar estado y notas
    if ($fecha_subida !== null) {
        $fecha_entrega = date("d/m/Y H:i", strtotime($fecha_subida));
        $estado_entrega = "Entregada";
        $notas_justificacion = $tiene_justificacion ? "Entregado con retraso. " : "Entregado a tiempo. ";
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
    
    // Dibujar el recuadro principal
    $startY = $pdf->GetY();
    $pdf->SetFillColor(231, 233, 242);
    $pdf->RoundedRect(15, $startY, 180, 50, 3, '1111', 'F');
    
    // Primera fila
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 5, 'Departamento:', 0, 0);
    createWhiteCell($pdf, $departamento['Departamentos'], 135);
    $pdf->Ln();
    
    // Segunda fila
    $pdf->SetXY(20, $pdf->GetY() + 3);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 5, 'Periodo:', 0, 0);
    createWhiteCell($pdf, $periodo_actual, 60);
    
    $pdf->SetX($pdf->GetX() + 5);
    $pdf->Cell(20, 5, 'Estado:', 0, 0);
    
    // Estado con color y bordes redondeados (1mm)
    $estadoX = $pdf->GetX();
    $estadoY = $pdf->GetY();
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
    $pdf->RoundedRect($estadoX, $estadoY, 50, 8, 1, '1111', 'F'); // Radio reducido a 1mm
    $pdf->SetXY($estadoX, $estadoY);
    $pdf->Cell(50, 8, $estado_entrega, 0, 1, 'C', false);
    
    // Tercera fila
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(20, $pdf->GetY() + 3);
    $pdf->Cell(35, 5, 'Fecha límite:', 0, 0);
    createWhiteCell($pdf, ($fecha_limite ? date("d/m/Y", strtotime($fecha_limite)) : "-"), 60);
    
    $pdf->SetX($pdf->GetX() + 5);
    $pdf->Cell(20, 5, 'Entrega:', 0, 0);
    createWhiteCell($pdf, $fecha_entrega, 50);
    $pdf->Ln();
    
    // Justificación en línea
    $pdf->SetXY(20, $pdf->GetY() + 3);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(35, 5, 'Justificación:', 0, 0);
    
    // Recuadro blanco redondeado para la justificación (a la derecha del texto)
    $pdf->SetFillColor(255, 255, 255);
    $justX = $pdf->GetX();
    $justY = $pdf->GetY();
    $pdf->RoundedRect($justX, $justY, 135, 8, 1, '1111', 'F'); // Radio reducido a 1mm
    $pdf->SetXY($justX, $justY);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(135, 8, strip_tags($notas_justificacion), 0, 'L', false);
    
    $pdf->Ln(15);
    $recuadros_por_pagina++;
}

$pdf->Output('Reporte_Entrega.pdf', 'I');
?>
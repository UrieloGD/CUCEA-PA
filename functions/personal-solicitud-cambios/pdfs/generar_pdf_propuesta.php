<?php
session_start();
require_once './../../../config/db.php';
require_once './../../../library/tcpdf.php';

class PROPUESTA_PDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

function generarPDFPropuesta($conexion, $folio) {
    $sql = "SELECT 
            sp.*, 
            d.Nombre_Departamento 
        FROM solicitudes_propuesta sp 
        JOIN departamentos d ON sp.Departamento_ID = d.Departamento_ID
        WHERE sp.OFICIO_NUM_PROP = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $folio);
    
    if (!mysqli_stmt_execute($stmt)) {
        return ['success' => false, 'message' => 'Error al buscar solicitud: ' . mysqli_error($conexion)];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $solicitud = mysqli_fetch_assoc($result);
    
    if (!$solicitud) {
        return ['success' => false, 'message' => 'Solicitud no encontrada'];
    }

    try {
        // Estilo de bordes
        $border = 1;  // Borde completo (1 = todos los bordes)

        // Configuración general
        $pageWidth = 180; // Ancho útil considerando márgenes (210mm - 10mm*2)
        $fontSizeHeader = 8;  // Tamaño reducido para headers
        $fontSizeBody = 9;    // Tamaño cuerpo

        $pdf = new PROPUESTA_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 40, 10);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        // Logo y encabezado
        $logoPath = './../../../Img/logos/LogoUDG-Color.png';
        $pdf->Image($logoPath, 12, 10, 20, 0, 'PNG');

        // Encabezado institucional
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(37, 10);
        $pdf->Cell(0, 6, 'UNIVERSIDAD DE GUADALAJARA', 0, 1);
        $pdf->SetX(37);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'SOLICITUD DE PROPUESTA', 0, 1);
        $pdf->SetX(37);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, 'DEPENDENCIA', 0, 1);

        // Oficio y fecha
        $pdf->SetXY(125, 10);
        $pdf->SetFont('helvetica', 'B', 10);
        // Fila de títulos
        $pdf->Cell(35, 8, 'OFICIO NUM', $border, 0, 'L');
        $pdf->Cell(35, 8, 'FECHA', $border, 1, 'L');
        // Fila de valores
        $pdf->SetX(125);
        $pdf->SetFont('', '');
        $pdf->Cell(35, 8, $solicitud['OFICIO_NUM_PROP'], $border, 0, 'L');
        $pdf->Cell(35, 8, date('d/m/Y', strtotime($solicitud['FECHA_SOLICITUD_P'])), $border, 1, 'L');

        // Departamento
        $pdf->SetXY(37, 28);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, strtoupper('CENTRO UNIVERSITARIO DE CIENCIAS ECONÓMICO ADMINISTRATIVAS'), 0, 1, 'L');
        $pdf->SetXY(37, 33);
        $departamento = str_replace('_', ' ', $solicitud['Nombre_Departamento']);
        $pdf->Cell(0, 7, strtoupper('DEPARTAMENTO DE ' . $departamento), 0, 1, 'L');
        
        // Línea divisoria
        $pdf->Line(11, 45, 199, 45);
        
        // Contenido principal
        $pdf->SetY(50);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, 'C. RECTOR GENERAL DE LA UNIVERSIDAD DE GUADALAJARA', 0, 1);
        $pdf->Cell(0, 8, 'PRESENTE', 0, 1);
        $pdf->MultiCell(0, 8, "POR ESTE CONDUCTO ME PERMITO SOLICITAR DE USTED QUE EL NOMBRAMIENTO/CONTRATO/ASIGNACION IDENTIFICADO CON", 0, 'J');
        $pdf->Ln(4);        
        
        // Tabla de profesor actual
        $header1 = ['PROFESIÓN', 'AP. PATERNO', 'AP. MATERNO', 'NOMBRE(S)', 'CÓDIGO.', 'DÍA', 'MES', 'AÑO'];
        $widths1 = [22, 30, 25, 48, 20, 15, 15, 15]; // Total: 190mm
        $pdf->SetFont('helvetica', 'B', 9);

        // Header primera tabla
        foreach ($header1 as $i => $col) {
            $pdf->Cell($widths1[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos primera tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths1[0], 8, mb_strtoupper($solicitud['PROFESSION_PROFESOR_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[1], 8, mb_strtoupper($solicitud['APELLIDO_P_PROF_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[2], 8, mb_strtoupper($solicitud['APELLIDO_M_PROF_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[3], 8, mb_strtoupper($solicitud['NOMBRES_PROF_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[4], 8, mb_strtoupper($solicitud['CODIGO_PROF_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[5], 8, $solicitud['DIA_P'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths1[6], 8, $solicitud['MES_P'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths1[7], 8, $solicitud['ANO_P'] ?? '', $border, 1, 'L');
        $pdf->Ln(4);
        
        // Segunda tabla (Descripción puesto)
        $header2 = ['DESCRIPCIÓN DEL PUESTO QUE OCUPA', 'CÓDIGO', 'CLASIFICACIÓN'];
        $widths2 = [125, 30, 35]; // Total: 190mm
        $pdf->SetFont('helvetica', 'B', 9);
        
        foreach ($header2 as $i => $col) {
            $pdf->Cell($widths2[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos segunda tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths2[0], 8, mb_strtoupper($solicitud['DESCRIPCION_PUESTO_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths2[1], 8, $solicitud['CODIGO_PUESTO_P'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths2[2], 8, mb_strtoupper($solicitud['CLASIFICACION_PUESTO_P'] ?? ''), $border, 1, 'L');
        $pdf->Ln(4);
        
        // Tercera tabla (Detalles del puesto)
        $header3 = ['HRS SEM.', 'CATEGORIA', 'CARRERA', 'CRN', 'N° PUESTO', 'CARGO A.T.C.'];
        $widths3 = [20, 45, 60, 20, 20, 25]; // Total: 190mm (ajustado para margen)
        $pdf->SetFont('helvetica', 'B', 9);

        // Configurar autoajuste de texto
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->setCellHeightRatio(1.5); // Espaciado entre líneas
        
        foreach ($header3 as $i => $col) {
            $pdf->Cell($widths3[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos tercera tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths3[0], 8, $solicitud['HRS_SEMANALES_P'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths3[1], 8, mb_strtoupper($solicitud['CATEGORIA_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths3[2], 8, mb_strtoupper($solicitud['CARRIERA_PROF_P'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths3[3], 8, $solicitud['CRN_P'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths3[4], 8, $solicitud['NUM_PUESTO_P'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths3[5], 8, ($solicitud['CARGO_ATC_P'] ? 'SÍ' : 'NO'), $border, 1, 'L');
        $pdf->Ln(4);

        // Cuarta tabla (Motivo)
        $header4 = ['EN SUSTITUCIÓN:', 'CÓDIGO', 'NOMBRE PROFESOR SUSTITUTO', 'CAUSA'];
        $widths4 = [30, 20, 100, 40]; // Total: 190mm
        $pdf->SetFont('helvetica', 'B', 9);
        
        foreach ($header4 as $i => $col) {
            $pdf->Cell($widths4[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos cuarta tabla (Ajustar según necesidad real)
        $pdf->SetFont('', '', 9);
        $pdf->Cell($widths4[0], 8, '', $border, 0, 'L'); // Celda vacía para el título
        $pdf->Cell($widths4[1], 8, $solicitud['CODIGO_PROF_SUST'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths4[2], 8, mb_strtoupper(
            ($solicitud['APELLIDO_P_PROF_SUST'] ?? '') . ' ' . 
            ($solicitud['APELLIDO_M_PROF_SUST'] ?? '') . ' ' . 
            ($solicitud['NOMBRES_PROF_SUST'] ?? '')),
            $border, 0, 'L');
        $pdf->Cell($widths4[3], 8, mb_strtoupper($solicitud['CAUSA_P'] ?? ''), $border, 1, 'L');
        $pdf->Ln(4);

        // Periodo de asignación
        $periodo_desde = $solicitud['PERIODO_ASIG_DESDE_P'] ? date('d/m/Y', strtotime($solicitud['PERIODO_ASIG_DESDE_P'])) : 'SIN FECHA';
        $periodo_hasta = $solicitud['PERIODO_ASIG_HASTA_P'] ? date('d/m/Y', strtotime($solicitud['PERIODO_ASIG_HASTA_P'])) : 'SIN FECHA';
        
        // Anchos calculados para 190mm total
        $pdf->SetFont('', 'B', 9);
        $pdf->Cell(65, 8, 'PERIODO DE ASIGNACIÓN DESDE:', 0, 0, 'L');
        $pdf->SetFont('', '', 9);
        $pdf->Cell(50, 8, $periodo_desde, $border, 0, 'L');

        $pdf->SetFont('', 'B', 9);
        $pdf->Cell(30, 8, 'HASTA:', 0, 0, 'L');  // Ancho aumentado
        $pdf->SetFont('', '', 9);
        $pdf->Cell(35, 8, $periodo_hasta, $border, 1, 'L'); // Ancho aumentado

        
        // Firmas (igual que baja)
        $pdf->SetY(-100);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'ATENTAMENTE', 0, 1, 'C');
        $pdf->Cell(0, 8, 'PIENSA Y TRABAJA', 0, 1, 'C');
        $pdf->Ln(25);
        
        $pdf->SetX(25);
        $pdf->Cell(70, 8, '____________________________', 0, 0, 'C');
        $pdf->SetX(110);
        $pdf->Cell(70, 8, '____________________________', 0, 1, 'C');
        
        $pdf->SetX(25);
        $pdf->SetFont('', 'B', 10);
        $pdf->Cell(70, 8, 'LIC. DENISSE MURILLO GONZALEZ', 0, 0, 'C');
        $pdf->SetX(110);
        $pdf->Cell(70, 8, 'MTRO. LUIS GUSTAVO PADILLA MONTES', 0, 1, 'C');
        
        $pdf->SetX(25);
        $pdf->SetFont('', '', 9);
        $pdf->Cell(70, 8, 'EL SECRETARIO DE LA DEPENDENCIA', 0, 0, 'C');
        $pdf->SetX(110);
        $pdf->Cell(70, 8, 'EL TITULAR DE LA DEPENDENCIA', 0, 1, 'C');

        // Generar contenido
        $pdf_content = $pdf->Output('', 'S');

        // Actualizar base de datos con fecha de modificación
        $sql_update = "UPDATE solicitudes_propuesta 
                    SET PDF_BLOB = ?, 
                        ESTADO_P = 'En revision',
                        FECHA_MODIFICACION_REVISION = CURRENT_TIMESTAMP
                    WHERE OFICIO_NUM_PROP = ?";
            
        $stmt = mysqli_prepare($conexion, $sql_update);
        $null = NULL;
        mysqli_stmt_bind_param($stmt, "bs", $null, $folio);
        mysqli_stmt_send_long_data($stmt, 0, $pdf_content);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecutando consulta: " . mysqli_error($conexion));
        }

        return ['success' => true, 'folio' => $folio];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folio = filter_input(INPUT_POST, 'folio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
    if (empty($folio)) {
        echo json_encode(['success' => false, 'message' => 'Folio inválido']);
        exit;
    }
    
    $resultado = generarPDFPropuesta($conexion, $folio);
    echo json_encode($resultado);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
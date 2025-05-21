<?php
session_start();
require_once './../../../config/db.php';
require_once './../../../library/tcpdf.php';

class PROPUESTA_PDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

function generarPDFBajaPropuesta($conexion, $folio) {
    $sql = "SELECT 
            sbp.*, 
            d.Nombre_Departamento 
        FROM solicitudes_baja_propuesta sbp 
        JOIN departamentos d ON sbp.Departamento_ID = d.Departamento_ID
        WHERE sbp.OFICIO_NUM_BAJA_PROP = ?";
    
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

        $pdf = new PROPUESTA_PDF('P', 'mm', 'A4', true, 'UTF-8', TRUE);
        $pdf->SetMargins(10, 40, 10);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        // Logo y encabezado
        $logoPath = './../../../Img/logos/LogoUDG-Color.png';
        $pdf->Image($logoPath, 12, 10, 20, 0, 'PNG');

        // Encabezado institucional
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->SetXY(37, 10);
        $pdf->Cell(0, 6, 'UNIVERSIDAD DE GUADALAJARA', 0, 1);
        $pdf->SetX(37);
        $pdf->SetFont('dejavusans', 'B', 11);
        $pdf->Cell(0, 6, 'SOLICITUD DE BAJA Y PROPOSICION', 0, 1);
        $pdf->SetX(37);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->Cell(0, 6, 'DEPENDENCIA', 0, 1);

        // Oficio y fecha
        $pdf->SetXY(125, 10);
        $pdf->SetFont('dejavusans', 'B', 10);
        // Fila de títulos
        $pdf->Cell(35, 8, 'OFICIO NUM', $border, 0, 'L');
        $pdf->Cell(35, 8, 'FECHA', $border, 1, 'L');
        // Fila de valores
        $pdf->SetX(125);
        $pdf->SetFont('', '');
        $pdf->Cell(35, 8, $solicitud['OFICIO_NUM_BAJA_PROP'], $border, 0, 'L');
        $pdf->Cell(35, 8, date('d/m/Y', strtotime($solicitud['FECHA_SOLICITUD_BAJA_PROP'])), $border, 1, 'L');

        // Departamento
        $pdf->SetXY(37, 28);
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(0, 7, strtoupper('CENTRO UNIVERSITARIO DE CIENCIAS ECONOMICO ADMINISTRATIVAS'), 0, 1, 'L');
        $pdf->SetXY(37, 33);
        // Obtener el nombre del departamento
        $departamento = str_replace('_', ' ', $solicitud['Nombre_Departamento']);
        // Convertir a mayúsculas
        $departamento = mb_strtoupper($departamento, 'UTF-8');
        // Eliminar acentos y caracteres especiales
        $buscar = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'];
        $reemplazar = ['A', 'E', 'I', 'O', 'U', 'U', 'N'];
        $departamento = str_replace($buscar, $reemplazar, $departamento);
        $pdf->Cell(0, 7, 'DEPARTAMENTO DE ' . $departamento, 0, 1, 'L');
        
        // Línea divisoria
        $pdf->Line(11, 45, 199, 45);
        
        // Contenido principal
        $pdf->SetY(50);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->Cell(0, 8, 'C. RECTOR GENERAL DE LA UNIVERSIDAD DE GUADALAJARA', 0, 1);
        $pdf->Cell(0, 8, 'PRESENTE', 0, 1);
        $pdf->MultiCell(0, 8, "POR ESTE CONDUCTO ME PERMITO SOLICITAR DE USTED QUE EL NOMBRAMIENTO/CONTRATO/ASIGNACION", 0, 'L');
        $pdf->Cell(0, 8, 'IDENTIFICADO CON EL NUMERO ___________________________ DE FECHA ___________________________', 0, 1);
        $pdf->Cell(0, 8, 'A FAVOR DE', 0, 1);               
        
        // Tabla de profesor actual
        $header1 = ['PROFESIÓN', 'APELLIDO PATERNO', 'MATERNO', 'NOMBRE(S)', 'CODIGO'];
        $widths1 = [25, 45, 35, 55, 30]; // Total: 190mm
        $pdf->SetFont('dejavusans', 'B', 8);

        // Header primera tabla
        foreach ($header1 as $i => $col) {
            $pdf->Cell($widths1[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos primera tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths1[0], 8, mb_strtoupper($solicitud['PROFESSION_PROFESOR_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[1], 8, mb_strtoupper($solicitud['APELLIDO_P_PROF_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[2], 8, mb_strtoupper($solicitud['APELLIDO_M_PROF_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[3], 8, mb_strtoupper($solicitud['NOMBRES_PROF_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths1[4], 8, mb_strtoupper($solicitud['CODIGO_PROF_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Ln(12);
        
        // Segunda tabla (Descripción puesto)
        $header2 = ['NO. PUESTO (T)', 'NO. PUESTO (P)', 'CVE. MATERIA', 'NOMBRE DE LA MATERIA', 'CRN'];
        $widths2 = [30, 30, 30, 70, 30]; // Ajustado para que sume 180mm
        $pdf->SetFont('dejavusans', 'B', 8);
        
        for ($i = 0; $i < count($header2); $i++) {
            $pdf->Cell($widths2[$i], 8, $header2[$i], $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos segunda tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths2[0], 8, mb_strtoupper($solicitud['NUM_PUESTO_TEORIA_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths2[1], 8, $solicitud['NUM_PUESTO_PRACTICA_BAJA'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths2[2], 8, mb_strtoupper($solicitud['CVE_MATERIA_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths2[3], 8, mb_strtoupper($solicitud['NOMBRE_MATERIA_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths2[4], 8, mb_strtoupper($solicitud['CRN_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Ln(12);
        
        // Tercera tabla (Detalles del puesto)
        $header3 = ['HRS/SEM/MES (T)', 'HRS/SEM/MES (P)', 'CARRERA', 'GDO/GPO/TURNO', 'TIPO ASIGNACION'];
        $widths3 = [30, 30, 70, 30, 30]; // Total: 190mm (ajustado para margen)
        $pdf->SetFont('dejavusans', 'B', 8);

        // Configurar autoajuste de texto
        $pdf->SetFont('dejavusans', 'B', 8);
        $pdf->setCellHeightRatio(1.5); // Espaciado entre líneas
        
        foreach ($header3 as $i => $col) {
            $pdf->Cell($widths3[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos tercera tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths3[0], 8, $solicitud['HRS_SEM_MES_TEORIA_BAJA'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths3[1], 8, mb_strtoupper($solicitud['HRS_SEM_MES_TEORIA_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths3[2], 8, mb_strtoupper($solicitud['CARRERA_BAJA'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths3[3], 8, $solicitud['GDO_PO_TURNO_BAJA'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths3[4], 8, $solicitud['TIPO_ASIGNACION_BAJA'] ?? '', $border, 0, 'L');
        $pdf->Ln(12);

        // FILA DE SIN EFECTOS Y MOTIVO
        $sin_efectos = $solicitud['SIN_EFFECTOS_APARTH_BAJA'] ? date('d/m/Y', strtotime($solicitud['SIN_EFFECTOS_APARTH_BAJA'])) : 'SIN FECHA';
        $motivo = $solicitud['MOTIVO_BAJA'];
        
        // Anchos calculados para 190mm total
        $pdf->SetFont('', 'B', 8);
        $pdf->Cell(60, 8, 'QUEDE SIN EFECTOS A PARTIR DE:', 0, 0, 'L');
        $pdf->SetFont('', '', 8);
        $pdf->Cell(45, 8, $sin_efectos, $border, 0, 'L');

        $pdf->SetFont('', 'B', 8);
        $pdf->Cell(25, 8, 'MOTIVO:', 0, 0, 'L');  // Ancho aumentado
        $pdf->SetFont('', '', 8);
        $pdf->Cell(60, 8, $motivo, $border, 1, 'L'); // Ancho aumentado
        $pdf->Ln(4);

        // Cuarta tabla (Descripción puesto)
        $header4 = ['NO. PUESTO (T)', 'NO. PUESTO (P)', 'A. PATERNO', 'A. MATERNO', 'NOMBRE(S)', 'CODIGO'];
        $widths4 = [28, 28, 35, 35, 44, 20]; // Total: 190mm
        $pdf->SetFont('dejavusans', 'B', 8);
        
        foreach ($header4 as $i => $col) {
            $pdf->Cell($widths4[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos cuarta tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths4[0], 8, mb_strtoupper($solicitud['NUM_PUESTO_TEORIA_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths4[1], 8, $solicitud['NUM_PUESTO_PRACTICA_PROP'] ?? '', $border, 0, 'L');
        $pdf->Cell($widths4[2], 8, mb_strtoupper($solicitud['APELLIDO_P_PROF_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths4[3], 8, mb_strtoupper($solicitud['APELLIDO_M_PROF_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths4[4], 8, mb_strtoupper($solicitud['NOMBRES_PROF_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths4[5], 8, mb_strtoupper($solicitud['CODIGO_PROF_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Ln(12);

        // Quinta tabla (Descripción puesto)
        $header5 = ['HRS/SEM/MES (T)', 'HRS/SEM/MES (P)', 'INTERINO/TEMPORAL/DEFINITIVO', 'TIPO ASIGNACION'];
        $widths5 = [30, 30, 85, 45]; // Total: 190mm
        $pdf->SetFont('dejavusans', 'B', 8);
                
        foreach ($header5 as $i => $col) {
            $pdf->Cell($widths5[$i], 8, $col, $border, 0, 'L');
        }
        $pdf->Ln(8);

        // Datos quinta tabla
        $pdf->SetFont('', '', 8);
        $pdf->Cell($widths5[0], 8, mb_strtoupper($solicitud['HRS_SEM_MES_TEORIA_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths5[1], 8, mb_strtoupper($solicitud['HRS_SEM_MES_PRACTICA_PROP'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths5[2], 8, mb_strtoupper($solicitud['INTERINO_TEMPORAL_DEFINITIVO'] ?? ''), $border, 0, 'L');
        $pdf->Cell($widths5[3], 8, mb_strtoupper($solicitud['TIPO_ASIGNACION'] ?? ''), $border, 0, 'L');
        $pdf->Ln(12);

        // Periodo de asignación
        $periodo_desde = $solicitud['PERIODO_ASIG_DESDE_PROP'] ? date('d/m/Y', strtotime($solicitud['PERIODO_ASIG_DESDE_PROP'])) : 'SIN FECHA';
        $periodo_hasta = $solicitud['PERIODO_ASIG_HASTA_PROP'] ? date('d/m/Y', strtotime($solicitud['PERIODO_ASIG_HASTA_PROP'])) : 'SIN FECHA';
        
        // Anchos calculados para 190mm total
        $pdf->SetFont('', 'B', 8);
        $pdf->Cell(60, 8, 'PERIODO DE ASIGNACIÓN DESDE:', 0, 0, 'L');
        $pdf->SetFont('', '', 8);
        $pdf->Cell(45, 8, $periodo_desde, $border, 0, 'L');

        $pdf->SetFont('', 'B', 8);
        $pdf->Cell(25, 8, 'HASTA:', 0, 0, 'L');  // Ancho aumentado
        $pdf->SetFont('', '', 8);
        $pdf->Cell(60, 8, $periodo_hasta, $border, 1, 'L'); // Ancho aumentado

        
        // Firmas (igual que baja)
        $pdf->SetY(-80);
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 8, 'ATENTAMENTE', 0, 1, 'C');
        $pdf->Cell(0, 8, 'PIENSA Y TRABAJA', 0, 1, 'C');
        $pdf->Ln(15);
        
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
        $sql_update = "UPDATE solicitudes_baja_propuesta 
                    SET PDF_BLOB = ?, 
                        ESTADO_P = 'En revision',
                        FECHA_MODIFICACION_REVISION = CURRENT_TIMESTAMP
                    WHERE OFICIO_NUM_BAJA_PROP = ?";
            
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
    
    $resultado = generarPDFBajaPropuesta($conexion, $folio);
    echo json_encode($resultado);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
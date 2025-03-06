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
        $pdf = new PROPUESTA_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 40, 15);
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
        $pdf->SetXY(130, 10);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, 7, 'OFICIO NUM:', 0, 0, 'L');
        $pdf->SetFont('', '');
        $pdf->Cell(30, 7, $solicitud['OFICIO_NUM_PROP'], 0, 1);
        $pdf->SetX(130);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, 7, 'FECHA:', 0, 0, 'L');
        $pdf->SetFont('', '');
        $pdf->Cell(30, 7, date('d/m/Y', strtotime($solicitud['FECHA_SOLICITUD_P'])), 0, 1);

        // Departamento
        $pdf->SetXY(37, 28);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, strtoupper('CENTRO UNIVERSITARIO DE CIENCIAS ECONÓMICO ADMINISTRATIVAS'), 0, 1, 'L');
        $pdf->SetXY(37, 33);
        $departamento = str_replace('_', ' ', $solicitud['Nombre_Departamento']);
        $pdf->Cell(0, 7, strtoupper('DEPARTAMENTO DE ' . $departamento), 0, 1, 'L');
        
        // Línea divisoria
        $pdf->Line(15, 45, 195, 45);
        
        // Contenido principal
        $pdf->SetY(50);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, 'C. RECTOR GENERAL DE LA UNIVERSIDAD DE GUADALAJARA', 0, 1);
        $pdf->Cell(0, 8, 'PRESENTE', 0, 1);
        
        // Tabla de profesor actual
        $header = ['PROFESIÓN', 'APELLIDO PATERNO', 'MATERNO', 'NOMBRE(S)', 'CODIGO'];
        $widths = [25, 45, 35, 55, 30];
        $pdf->SetFont('helvetica', 'B', 10);
        foreach ($header as $i => $col) {
            $pdf->Cell($widths[$i], 8, $col, 0, 0, 'L');
        }
        $pdf->Ln(10);
        
        // Datos profesor actual
        $pdf->SetFont('', '');
        $dataActual = [
            mb_strtoupper($solicitud['PROFESSION_PROFESOR_P'] ?? ''),
            mb_strtoupper($solicitud['APELLIDO_P_PROF_P'] ?? ''),
            mb_strtoupper($solicitud['APELLIDO_M_PROF_P'] ?? ''),
            mb_strtoupper($solicitud['NOMBRES_PROF_P'] ?? ''),
            mb_strtoupper($solicitud['CODIGO_PROF_P'] ?? '')
        ];

        foreach ($dataActual as $i => $val) {
            $pdf->Cell($widths[$i], 8, $val, 0, 0, 'L');
        }
        $pdf->Ln(15);
        
        // Tabla profesor propuesto
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'PROFESOR PROPUESTO:', 0, 1);
        
        $dataPropuesto = [
            mb_strtoupper($solicitud['CODIGO_PROF_SUST'] ?? ''),  // Usamos código como identificador
            mb_strtoupper($solicitud['APELLIDO_P_PROF_SUST'] ?? ''),
            mb_strtoupper($solicitud['APELLIDO_M_PROF_SUST'] ?? ''),
            mb_strtoupper($solicitud['NOMBRES_PROF_SUST'] ?? ''),
            ''  // Espacio reservado
        ];

        foreach ($dataPropuesto as $i => $val) {
            $pdf->Cell($widths[$i], 8, $val, 0, 0, 'L');
        }
        $pdf->Ln(15);
        
        // Detalles de la propuesta
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(90, 8, 'DESCRIPCIÓN DEL PUESTO', 0, 0, 'L');
        $pdf->Cell(45, 8, 'CRN', 0, 0, 'L');
        $pdf->Cell(45, 8, 'CLASIFICACIÓN', 0, 1, 'L');
        
        $pdf->SetFont('', '');
        $pdf->Cell(90, 8, mb_strtoupper($solicitud['DESCRIPCION_PUESTO_P'] ?? ''), 0);
        $pdf->Cell(45, 8, $solicitud['CRN_P'] ?? '', 0);
        $pdf->Cell(45, 8, $solicitud['CLASIFICACION_PUESTO_P'] ?? '', 0, 1);
        $pdf->Ln(10);
        
        // Periodo y motivo
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(70, 8, 'PERIODO PROPUESTO: ', 0, 0, 'L');
        $pdf->SetFont('', '');
        $periodo_desde = $solicitud['PERIODO_ASIG_DESDE_P'] ? date('d/m/Y', strtotime($solicitud['PERIODO_ASIG_DESDE_P'])) : '';
        $periodo_hasta = $solicitud['PERIODO_ASIG_HASTA_P'] ? date('d/m/Y', strtotime($solicitud['PERIODO_ASIG_HASTA_P'])) : '';
        $pdf->Cell(50, 8, "$periodo_desde - $periodo_hasta", 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(20, 8, 'MOTIVO: ', 0, 0, 'L');
        $pdf->SetFont('', '');
        $pdf->Cell(0, 8, mb_strtoupper($solicitud['CAUSA_P'] ?? ''), 0, 1);
        $pdf->Ln(10);
        
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
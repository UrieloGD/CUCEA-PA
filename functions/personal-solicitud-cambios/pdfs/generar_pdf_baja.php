<?php
session_start();
require_once './../../../config/db.php';
require_once './../../../library/tcpdf.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

class BAJA_PDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'C');
    }
}

if (!isset($_SESSION['Codigo']) || ($_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 1)) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Acceso no autorizado']));
}

header('Content-Type: application/json');

function generarPDFyActualizarEstado($conexion, $folio) {
    $sql = "SELECT 
            sb.*, 
            d.Nombre_Departamento 
        FROM solicitudes_baja sb 
        JOIN departamentos d ON sb.Departamento_ID = d.Departamento_ID
        WHERE sb.OFICIO_NUM_BAJA = ?";
    
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
    // Configurar PDF
    $pdf = new BAJA_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetMargins(10, 40, 10); // Margen superior aumentado
    $pdf->setPrintHeader(false);
    $pdf->AddPage();

    $border = 1; // Borde completo para todas las celdas
    $pdf->SetFillColor(255, 255, 255); // Fondo blanco para celdas
    
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
    $pdf->Cell(35, 8, $solicitud['OFICIO_NUM_BAJA'], $border, 0, 'L');
    $pdf->Cell(35, 8, date('d/m/Y', strtotime($solicitud['FECHA_SOLICITUD_B'])), $border, 1, 'L');

    // Departamento
    $pdf->SetXY(37, 28);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 7, strtoupper('CENTRO UNIVERSITARIO DE CIENCIAS ECONÓMICO ADMINISTRATIVAS'), 0, 1, 'L');
    $pdf->SetXY(37, 33);
    $departamento = str_replace('_', ' ', $solicitud['Nombre_Departamento']);
    $pdf->Cell(0, 7, strtoupper('DEPARTAMENTO DE ' . $departamento), 0, 1, 'L');
    
    // Línea divisoria
    $pdf->Line(15, 45, 195, 45);;

    // Línea divisoria
    $pdf->Line(15, 45, 195, 45);
    
    // Destinatario
    $pdf->SetY(50);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, 'C. RECTOR GENERAL DE LA UNIVERSIDAD DE GUADALAJARA', 0, 1);
    $pdf->Cell(0, 8, 'PRESENTE', 0, 1);
    
    // Texto justificado
    $pdf->setCellHeightRatio(1.8); // Aumentar espacio entre líneas
    $pdf->MultiCell(0, 8, "POR ESTE CONDUCTO ME PERMITO SOLICITAR A USTED QUE EL NOMBRAMIENTO/CONTRATO/ASIGNACION IDENTIFICADO CON EL NUMERO ____________________ DE FECHA ____________________", 0, 'L');
    $pdf->MultiCell(0, 8, "A FAVOR DE", 0, 'L');
    $pdf->setCellHeightRatio(1); // Restaurar valor
    $pdf->Ln(3);
    
    // Tabla de profesor (100% ancho)
    $header = ['PROFESIÓN', 'APELLIDO PATERNO', 'MATERNO', 'NOMBRE(S)', 'CODIGO'];
    $widths = [25, 45, 35, 55, 30];
    // Header con bordes
    $pdf->SetFont('helvetica', 'B', 10);
    foreach ($header as $i => $col) {
        $pdf->Cell($widths[$i], 8, $col, $border, 0, 'L');
    }
    $pdf->Ln(8);;
    
    // Datos
    $pdf->SetFont('', '');
    $data = [
        mb_strtoupper($solicitud['PROFESSION_PROFESOR_B'], 'UTF-8'),
        mb_strtoupper($solicitud['APELLIDO_P_PROF_B'], 'UTF-8'),
        mb_strtoupper($solicitud['APELLIDO_M_PROF_B'], 'UTF-8'),
        mb_strtoupper($solicitud['NOMBRES_PROF_B'], 'UTF-8'),
        mb_strtoupper($solicitud['CODIGO_PROF_B'], 'UTF-8')
    ];

    $pdf->SetFont('', '');  // Asegurar fuente normal
    foreach ($data as $i => $val) {
        $pdf->Cell($widths[$i], 7, $val, $border, 0, 'L');
    }
    $pdf->Ln(15);
    
    // Tabla descripción (50%-25%-25%)
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(105, 8, 'DESCRIPCIÓN DEL PUESTO QUE OCUPA', $border, 0, 'L');
    $pdf->Cell(40, 8, 'CRN',$border, 0, 'L');
    $pdf->Cell(45, 8, 'CLASIFICACIÓN', $border, 1, 'L');
    
    $pdf->SetFont('', '');
    $pdf->Cell(105, 7, mb_strtoupper($solicitud['DESCRIPCION_PUESTO_B'], 'UTF-8'), $border, 0);
    $pdf->Cell(40, 7, $solicitud['CRN_B'], $border, 0);
    $pdf->Cell(45, 7, $solicitud['CLASIFICACION_BAJA_B'], $border, 1);
    $pdf->Ln(10);
    
    // Efectos y motivo en línea
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(70, 8, 'QUEDE SIN EFECTOS A PARTIR DE:', 0, 0, 'L');    $pdf->SetFont('', '');
    $pdf->Cell(45, 7, date('d/m/Y', strtotime($solicitud['SIN_EFFECTOS_DESDE_B'])), $border, 0, 'L');

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(25, 8, 'MOTIVO:', 0, 0, 'L');
    $pdf->SetFont('', '');
    $pdf->Cell(50, 7, mb_strtoupper($solicitud['MOTIVO_B'], 'UTF-8'), $border, 1, 'L');
    $pdf->Ln(10);
    
    // Firmas más arriba
    $pdf->SetY(-100);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'ATENTAMENTE', 0, 1, 'C');
    $pdf->Cell(0, 8, 'PIENSA Y TRABAJA', 0, 1, 'C');
    $pdf->Ln(25);
    
    // Líneas de firma
    $pdf->SetX(25);
    $pdf->Cell(70, 8, '____________________________', 0, 0, 'C');
    $pdf->SetX(110);
    $pdf->Cell(70, 8, '____________________________', 0, 1, 'C');
    
    // Nombres
    $pdf->SetX(25);
    $pdf->SetFont('', 'B', 10);
    $pdf->Cell(70, 8, 'LIC. DENISSE MURILLO GONZALEZ', 0, 0, 'C');
    $pdf->SetX(110);
    $pdf->Cell(70, 8, 'MTRO. LUIS GUSTAVO PADILLA MONTES', 0, 1, 'C');
    
    // Cargos
    $pdf->SetX(25);
    $pdf->SetFont('', '', 9);
    $pdf->Cell(70, 8, 'EL SECRETARIO DE LA DEPENDENCIA', 0, 0, 'C');
    $pdf->SetX(110);
    $pdf->Cell(70, 8, 'EL TITULAR DE LA DEPENDENCIA', 0, 1, 'C');

    // Generar contenido
    $pdf_content = $pdf->Output('', 'S');

    // Actualizar base de datos
    $sql_update = "UPDATE solicitudes_baja 
                SET PDF_BLOB = ?, 
                    ESTADO_B = 'En revision'                    
                WHERE OFICIO_NUM_BAJA = ?";
        
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
    
    $resultado = generarPDFyActualizarEstado($conexion, $folio);
    echo json_encode($resultado);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
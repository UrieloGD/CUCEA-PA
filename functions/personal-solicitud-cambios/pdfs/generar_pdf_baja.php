<?php
session_start();
require_once './../../../config/db.php';
require_once './../../../library/tcpdf.php';

// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

class BAJA_PDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'C');
    }
}

// Verificar sesión y rol ANTES de cualquier output
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Acceso no autorizado']));
}

header('Content-Type: application/json');

function generarPDFyActualizarEstado($conexion, $folio) {
    // Obtener datos completos de la solicitud
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
        // ========== GENERAR PDF ==========
        $pdf = new BAJA_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 25, 15);
        $pdf->setPrintHeader(false);
        $pdf->AddPage();

        // Encabezado azul
        $pdf->SetFillColor(0, 113, 176);
        $pdf->RoundedRect(15, 15, 180, 15, 3, '1111', 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(15, 17);
        $pdf->Cell(180, 12, 'SOLICITUD DE BAJA', 0, 1, 'C');

        // Datos generales
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetY(40);

        // Sección de información
        $pdf->Cell(95, 10, 'DEPENDENCIA: ' . $solicitud['Nombre_Departamento'], 0, 0);
        $pdf->Cell(85, 10, 'OFICIO NUM: ' . $solicitud['OFICIO_NUM_BAJA'], 0, 1);
        
        $pdf->Cell(95, 10, 'FECHA: ' . date('d/m/Y', strtotime($solicitud['FECHA_SOLICITUD_B'])), 0, 0);
        $pdf->Cell(85, 10, 'HORA: ' . $solicitud['HORA_CREACION'], 0, 1);
        $pdf->Ln(8);

        // Destinatario
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'C. RECTOR GENERAL DE LA UNIVERSIDAD DE GUADALAJARA', 0, 1);
        $pdf->Cell(0, 10, 'PRESENTE', 0, 1);
        $pdf->Ln(5);

        // Cuerpo de la solicitud
        $pdf->SetFont('helvetica', '', 11);
        $texto_solicitud = "POR ESTE CONDUCTO ME PERMITO SOLICITAR A USTED QUE EL NOMBRAMIENTO/CONTRATO/ASIGNACION IDENTIFICADO\n"
            . "CON EL NUMERO ______ DE FECHA ______\n\n"
            . "A FAVOR DE:";
        $pdf->MultiCell(0, 10, $texto_solicitud, 0, 'J');
        $pdf->Ln(3);

        // Tabla de datos del profesor
        $pdf->SetFont('helvetica', 'B', 10);
        $header = ['PROFESIÓN', 'APELLIDO PATERNO', 'MATERNO', 'NOMBRE(S)', 'CODIGO'];
        $widths = [30, 45, 35, 45, 25];
        $data = [
            $solicitud['PROFESSION_PROFESOR_B'],
            $solicitud['APELLIDO_P_PROF_B'],
            $solicitud['APELLIDO_M_PROF_B'],
            $solicitud['NOMBRES_PROF_B'],
            $solicitud['CODIGO_PROF_B']
        ];

        $pdf->SetFillColor(231, 233, 242);
        $pdf->Cell(array_sum($widths), 8, '', 0, 1);
        foreach ($header as $i => $col) {
            $pdf->Cell($widths[$i], 8, $col, 1, 0, 'C', true);
        }
        $pdf->Ln();
        foreach ($data as $i => $val) {
            $pdf->Cell($widths[$i], 8, $val, 1, 0, 'C');
        }
        $pdf->Ln(10);

        // Descripción del puesto
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 10, 'DESCRIPCIÓN DEL PUESTO QUE OCUPA:', 0, 1);
        $pdf->SetFont('', '', 10);
        $pdf->MultiCell(0, 8, $solicitud['DESCRIPCION_PUESTO_B'], 0, 'L');
        $pdf->Ln(5);

        // Efectos y motivo
        $pdf->Cell(0, 10, 'QUEDE SIN EFECTOS A PARTIR DE:', 0, 1);
        $pdf->Cell(50, 8, date('d/m/Y', strtotime($solicitud['SIN_EFFECTOS_DESDE_B'])), 0, 0);
        $pdf->Cell(0, 8, 'MOTIVO: ' . $solicitud['MOTIVO_B'], 0, 1);
        $pdf->Ln(15);

        // Firmas
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(0, 10, 'ATENTAMENTE', 0, 1, 'C');
        $pdf->Cell(0, 10, 'PIENSA Y TRABAJA', 0, 1, 'C');
        $pdf->Ln(20);

        $pdf->SetX(25);
        $pdf->Cell(80, 8, '____________________________', 0, 0, 'C');
        $pdf->SetX(110);
        $pdf->Cell(80, 8, '____________________________', 0, 1, 'C');

        $pdf->SetX(25);
        $pdf->Cell(80, 8, 'MTRO. JOSE DAVID FLORES UREÑA', 0, 0, 'C');
        $pdf->SetX(110);
        $pdf->Cell(80, 8, 'MTRO. JOSE ALBERTO CASTELLANOS', 0, 1, 'C');

        $pdf->SetX(25);
        $pdf->Cell(80, 8, 'EL SECRETARIO DE LA DEPENDENCIA', 0, 0, 'C');
        $pdf->SetX(110);
        $pdf->Cell(80, 8, 'EL TITULAR DE LA DEPENDENCIA', 0, 1, 'C');

        // Generar contenido binario del PDF
        $pdf_content = $pdf->Output('', 'S');

        // ========== ACTUALIZAR BASE DE DATOS ==========
        $sql_update = "UPDATE solicitudes_baja 
                    SET PDF_BLOB = ?, 
                        ESTADO_B = 'En revision'                    
                    WHERE OFICIO_NUM_BAJA = ?";
        
        $stmt = mysqli_prepare($conexion, $sql_update);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . mysqli_error($conexion));
        }
        
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
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString() // Solo para desarrollo
        ];
    }
}

// Manejar solicitud POST
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
<!-- ./functions/personal-solicitud-cambios/pdfs/generar_pdf.php -->
<?php
session_start();
require_once './../../../config/db.php'; 
require_once './../../../vendor/autoload.php'; 

// Función para actualizar el estado de la solicitud a "En revision"
function actualizarEstadoSolicitud($conexion, $folio) {
    $sql = "UPDATE solicitudes_baja SET ESTADO_B = 'En revision' WHERE OFICIO_NUM_BAJA = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $folio);
    $resultado = mysqli_stmt_execute($stmt);
    return $resultado;
}

// Función para generar el PDF y guardarlo en la base de datos
function generarPDF($conexion, $folio) {
    // Obtener la información de la solicitud
    $sql = "SELECT sb.*, d.Nombre_Departamento 
            FROM solicitudes_baja sb 
            JOIN departamentos d ON sb.Departamento_ID = d.Departamento_ID
            WHERE sb.OFICIO_NUM_BAJA = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $folio);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $solicitud = mysqli_fetch_assoc($result);
    
    if (!$solicitud) {
        return ['success' => false, 'message' => 'Solicitud no encontrada'];
    }
    
    // Aquí usamos la librería para generar el PDF
    // Ejemplo con FPDF
    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    
    // Encabezado
    $pdf->Cell(0, 10, 'UNIVERSIDAD DE GUADALAJARA', 0, 1, 'C');
    $pdf->Cell(0, 10, 'SOLICITUD DE BAJA', 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'DEPENDENCIA: ' . $solicitud['Nombre_Departamento'], 0, 1);
    $pdf->Cell(0, 10, 'OFICIO NUM: ' . $solicitud['OFICIO_NUM_BAJA'], 0, 1);
    $pdf->Cell(0, 10, 'FECHA: ' . $solicitud['FECHA_SOLICITUD_B'], 0, 1);
    
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'C. RECTOR GENERAL DE LA UNIVERSIDAD DE GUADALAJARA');
    $pdf->MultiCell(0, 10, 'PRESENTE');
    
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'POR ESTE CONDUCTO ME PERMITO SOLICITAR A USTED QUE EL NOMBRAMIENTO/CONTRATO/ASIGNACION IDENTIFICADO CON EL NUMERO DE FECHA QUEDE SIN EFECTOS A PARTIR DE ' . $solicitud['SIN_EFFECTOS_DESDE_B'] . ' MOTIVO: ' . $solicitud['MOTIVO_B']);
    
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'PROF(A). ' . $solicitud['APELLIDO_P_PROF_B'] . ' ' . $solicitud['APELLIDO_M_PROF_B'] . ' ' . $solicitud['NOMBRES_PROF_B'], 0, 1);
    $pdf->Cell(0, 10, 'CODIGO: ' . $solicitud['CODIGO_PROF_B'], 0, 1);
    $pdf->Cell(0, 10, 'DESCRIPCION DEL PUESTO QUE OCUPA: ' . $solicitud['DESCRIPCION_PUESTO_B'], 0, 1);
    $pdf->Cell(0, 10, 'CRN: ' . $solicitud['CRN_B'], 0, 1);
    
    $pdf->Ln(20);
    $pdf->Cell(0, 10, 'ATENTAMENTE', 0, 1, 'C');
    $pdf->Cell(0, 10, 'PIENSA Y TRABAJA', 0, 1, 'C');
    
    $pdf->Ln(20);
    $pdf->Cell(($pdf->GetPageWidth() / 2) - 10, 10, 'EL SECRETARIO DE LA DEPENDENCIA', 0, 0, 'C');
    $pdf->Cell(($pdf->GetPageWidth() / 2) - 10, 10, 'EL TITULAR DE LA DEPENDENCIA', 0, 1, 'C');
    
    // Capturar la salida del PDF como una cadena
    $pdf_content = $pdf->Output('S');
    
    // Actualizar el contenido del PDF en la base de datos
    $sql_update = "UPDATE solicitudes_baja SET PDF_BLOB = ? WHERE OFICIO_NUM_BAJA = ?";
    $stmt = mysqli_prepare($conexion, $sql_update);
    mysqli_stmt_bind_param($stmt, "ss", $pdf_content, $folio);
    $resultado = mysqli_stmt_execute($stmt);
    
    if ($resultado) {
        return [
            'success' => true, 
            'folio' => $folio
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Error al guardar el PDF en la base de datos'
        ];
    }
}

// Procesar la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $folio = $_POST['folio'] ?? '';
    
    if (empty($folio)) {
        echo json_encode(['success' => false, 'message' => 'Folio no proporcionado']);
        exit;
    }
    
    if ($accion === 'generar' && $_SESSION['Rol_ID'] == 3) {
        // Solo Coordinación de Personal puede generar
        $actualizado = actualizarEstadoSolicitud($conexion, $folio);
        if ($actualizado) {
            $resultado = generarPDF($conexion, $folio);
            echo json_encode($resultado);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    } elseif ($accion === 'descargar') {
        // Obtener el PDF de la base de datos
        $sql = "SELECT PDF_BLOB, OFICIO_NUM_BAJA FROM solicitudes_baja WHERE OFICIO_NUM_BAJA = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $folio);
        mysqli_stmt_execute($stmt);
        $stmt->bind_result($pdf_blob, $oficio_num);
        $stmt->fetch();
        $stmt->close();
        
        if ($pdf_blob) {
            echo json_encode([
                'success' => true, 
                'folio' => $folio
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No se ha generado el PDF para esta solicitud'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida o no autorizada']);
    }
}
?>
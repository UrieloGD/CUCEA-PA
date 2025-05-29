<?php
session_start();
require_once './../../../config/db.php';

if (!isset($_SESSION['Codigo']) || ($_SESSION['Rol_ID'] != 0 && $_SESSION['Rol_ID'] != 1 && $_SESSION['Rol_ID'] != 3)) {
    die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

if (isset($_GET['folio'])) {
    $folio = $_GET['folio'];
    
    ob_clean();
    ob_start();
    
    $sql = "SELECT PDF_BLOB FROM solicitudes_baja_propuesta WHERE OFICIO_NUM_BAJA_PROP = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $folio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $pdfBlob);
    mysqli_stmt_fetch($stmt);

    if ($pdfBlob) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Solicitud_Baja_Propuesta_' . $folio . '.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . strlen($pdfBlob));

        echo $pdfBlob;
        ob_end_flush();
        exit();
    } else {
        http_response_code(404);
        die('PDF no encontrado');
    }
} else {
    http_response_code(400);
    die('Folio no proporcionado');
}
?>
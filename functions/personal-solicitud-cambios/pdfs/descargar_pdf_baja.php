<!-- ./functions/personal-solicitud-cambios/pdfs/descargar_pdf.php -->
<?php
session_start();
require_once './../../../config/db.php'; 

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['Codigo']) || ($_SESSION['Rol_ID'] != 1 && $_SESSION['Rol_ID'] != 3)) {
    echo "No autorizado";
    exit;
}

if (isset($_GET['folio'])) {
    $folio = $_GET['folio'];
    
    // Obtener el PDF de la base de datos
    $sql = "SELECT PDF_BLOB, OFICIO_NUM_BAJA FROM solicitudes_baja WHERE OFICIO_NUM_BAJA = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $folio);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row && $row['PDF_BLOB']) {
        // Configurar las cabeceras para la descarga del PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Solicitud_Baja_' . $folio . '.pdf"');
        header('Content-Length: ' . strlen($row['PDF_BLOB']));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $row['PDF_BLOB'];
        exit;
    } else {
        echo "PDF no encontrado";
    }
} else {
    echo "Folio no proporcionado";
}
?>
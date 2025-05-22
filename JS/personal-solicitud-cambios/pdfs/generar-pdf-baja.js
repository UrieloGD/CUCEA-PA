function generarPDFBaja(folio) {
    Swal.fire({
        title: 'Confirmar generación',
        html: `¿Generar PDF?<br><small>El estado cambiará a "En revisión"</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Generar',
        confirmButtonColor: '#0071B0',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Loader centrado durante generación
            Swal.fire({
                title: 'Generando documento',
                html: '<div class="swal2-loader"></div>',
                showConfirmButton: false,
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal2-modal-custom',
                    loader: 'swal2-loader-center'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });    
            $.ajax({
                url: './functions/personal-solicitud-cambios/pdfs/generar_pdf_baja.php',
                type: 'POST',
                data: { folio: folio },
                dataType: 'json',
                success: (response) => {
                    Swal.close();
                    if (response.success) {
                        // Descargar automáticamente
                        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=${response.folio}`, '_blank');
                        
                        // Recargar después de 1s
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMsg = `Error ${xhr.status}: `;
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg += errorResponse.message || 'Error en el servidor';
                    } catch(e) {
                        errorMsg += xhr.statusText;
                    }
                    Swal.fire('Error de conexión', errorMsg, 'error');
                    console.error('Error completo:', xhr.responseText);
                }
            });
        }
    });
};

window.descargarPDF = function(folio) {
    $.ajax({
        url: './functions/personal-solicitud-cambios/pdfs/generar_pdf_baja.php',
        type: 'POST',
        data: {
            accion: 'descargar',
            folio: folio
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.open('./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=' + response.folio, '_blank');
            } else {
                if (rol_usuario === 3) {
                    alert('El PDF aún no ha sido generado. Por favor, genere primero la solicitud en PDF.');
                } else {
                    alert('El PDF aún no está disponible. La solicitud debe ser procesada por Coordinación de Personal.');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición AJAX:", status, error);
            console.log("Respuesta del servidor:", xhr.responseText);
            try {
                var errorResponse = JSON.parse(xhr.responseText);
                alert('Error: ' + errorResponse.message);
            } catch (e) {
                alert('Error en la comunicación con el servidor: ' + error);
            }
        }
    });
};
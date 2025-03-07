function generarPDFPropuesta(folio) {
    Swal.fire({
        title: 'Confirmar generación',
        html: `¿Generar PDF de propuesta?<br><small>El estado cambiará a "En revisión"</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Generar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generando documento',
                html: '<div class="swal2-loader"></div>',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            
            $.ajax({
                url: './functions/personal-solicitud-cambios/pdfs/generar_pdf_propuesta.php',
                type: 'POST',
                data: { folio: folio },
                dataType: 'json',
                success: (response) => {
                    Swal.close();
                    if (response.success) {
                        window.open(`./functions/personal-solicitud-cambios/pdfs/descargar_pdf_propuesta.php?folio=${response.folio}`, '_blank');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                },
                error: (xhr) => {
                    Swal.close();
                    let errorMsg = `Error ${xhr.status}: `;
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg += errorResponse.message || 'Error en el servidor';
                    } catch(e) {
                        errorMsg += xhr.statusText;
                    }
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });
}
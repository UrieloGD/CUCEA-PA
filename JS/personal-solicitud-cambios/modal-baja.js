document.addEventListener('DOMContentLoaded', function() {
    const formBaja = document.getElementById('form-baja');
    const modalBaja = document.getElementById('solicitud-modal-baja-academica');
    
    formBaja.addEventListener('submit', function(e) {
        e.preventDefault();

        // Sweet Alert de carga
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const formData = new FormData(this);

        fetch('./functions/personal-solicitud-cambios/procesar_baja.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Primero cerramos el modal
                modalBaja.style.display = 'none';
                formBaja.reset();
                
                // Después mostramos el Sweet Alert
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: data.message || 'Ocurrió un error al procesar la solicitud',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Error en la comunicación con el servidor',
                confirmButtonColor: '#d33'
            });
        });
    });

    // Botón de descartar
    document.getElementById('btn-descartar').addEventListener('click', function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se descartarán todos los datos ingresados",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formBaja.reset();
                modalBaja.style.display = 'none';
            }
        });
    });
});
//modal-baja.js
let modalBaja = null;
let formBaja = null;

function initModalBaja() {
    modalBaja = document.getElementById('solicitud-modal-baja-academica');
    formBaja = document.getElementById('form-baja');
    
    // Manejo del formulario
    formBaja?.addEventListener('submit', handleSubmit);
    
    // Botones de cerrar y descartar
    modalBaja.querySelector('.close-button')?.addEventListener('click', cerrarModal);
    document.getElementById('btn-descartar')?.addEventListener('click', confirmarDescartar);
    
    // Cerrar al hacer clic fuera
    window.addEventListener('click', (e) => {
        if (e.target === modalBaja) {
            cerrarModal();
        }
    });
}

function handleSubmit(e) {
    e.preventDefault();
    
    Swal.fire({
        title: '¿Confirmar guardado?',
        text: "¿Desea guardar esta solicitud de baja?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFormulario();
        }
    });
}

function enviarFormulario() {
    const formData = new FormData(formBaja);

    fetch('./functions/personal-solicitud-cambios/guardar_solicitud_baja.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Solicitud guardada correctamente',
                icon: 'success'
            }).then(() => {
                cerrarModal();
                // Recargar la lista si es necesario
                if (typeof cargarSolicitudes === 'function') {
                    cargarSolicitudes();
                }
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: error.message || 'Error al procesar la solicitud',
            icon: 'error'
        });
    });
}

function confirmarDescartar() {
    Swal.fire({
        title: '¿Descartar cambios?',
        text: "Se perderá la información ingresada",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, descartar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cerrarModal();
        }
    });
}

function cerrarModal() {
    if (formBaja) formBaja.reset();
    if (modalBaja) modalBaja.style.display = 'none';
}

function abrirModalBaja() {
    if (modalBaja) modalBaja.style.display = 'block';
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initModalBaja);
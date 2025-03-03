// ./JS/personal-solicitud-cambios/modal-baja.js
document.addEventListener('DOMContentLoaded', function() {
    const formBaja = document.getElementById('form-baja');
    const modalBaja = document.getElementById('solicitud-modal-baja-academica');
    let formData = new FormData(); // Almacena datos del formulario
    
    // Establecer límites máximos para inputs según la base de datos
    const maxLengths = {
        'oficio_num': 15,
        'profesion': 15,
        'apellido_paterno': 40,
        'apellido_materno': 40,
        'nombres': 60,
        'codigo_prof': 10,
        'descripcion': 100,
        'crn': 7,
        'clasificacion': 15,
        'motivo': 50
    };

    // Aplicar límites máximos a los inputs
    Object.keys(maxLengths).forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.setAttribute('maxlength', maxLengths[field]);
        }
    });

    // Obtener el siguiente número de oficio cuando se abre el modal
    function actualizarNumeroOficio() {
        fetch('./functions/personal-solicitud-cambios/obtener_siguiente_oficio.php')
            .then(response => response.json())
            .then(data => {
                if(data.siguiente_numero) {
                    document.getElementById('oficio_num').value = data.siguiente_numero;
                }
            });
    }

    // Guardar datos del formulario antes de cerrar el modal
    function guardarDatosFormulario() {
        formData = new FormData(formBaja);
    }

    // Restaurar datos del formulario cuando se reabre el modal
    function restaurarDatosFormulario() {
        formData.forEach((valor, clave) => {
            const input = formBaja.elements[clave];
            if(input) {
                input.value = valor;
            }
        });
    }

    formBaja.addEventListener('submit', function(e) {
        e.preventDefault();
        
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
        .then(response => response.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Respuesta del servidor:', text);
                throw new Error('La respuesta no es un JSON válido');
            }
            
            if (data.status === 'success') {
                modalBaja.style.display = 'none';
                formBaja.reset();
                
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
                text: 'Error en la comunicación con el servidor: ' + error.message,
                confirmButtonColor: '#d33'
            });
        });
    });

    // Manejador del botón descartar modificado
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
                formData = new FormData(); // Limpiar datos almacenados
                modalBaja.style.display = 'none';
            }
        });
    });

    // Eventos para abrir/cerrar modal
    document.querySelector('.close-button').addEventListener('click', function() {
        guardarDatosFormulario();
    });

    // Cuando se abre el modal
    modalBaja.addEventListener('show', function() {
        actualizarNumeroOficio();
        restaurarDatosFormulario();
    });
});
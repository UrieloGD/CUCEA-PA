// ./JS/personal-solicitud-cambios/modal-baja.js
document.addEventListener('DOMContentLoaded', function() {
    const formBaja = document.getElementById('form-baja');
    const modalBaja = document.getElementById('solicitud-modal-baja-academica');
    let formData = new FormData();
    
    // Función para mayúsculas con acentos
    const toUpperWithAccents = (str) => {
        return str.normalize('NFD')
            .toUpperCase()
            .replace(/¡/g, '¿') // Mantener símbolos en español
            .replace(/!/g, '?');
    };

    // Límites máximos para inputs
    const maxLengths = {
        'oficio_num_baja': 15,
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

    // Campos alfabéticos - permitir letras, espacios y caracteres especiales del español
    const camposAlfabeticos = [
        'nombres', 'apellido_paterno', 'apellido_materno'
    ];

    camposAlfabeticos.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.addEventListener('input', (e) => {
                // Primero aplicamos toUpperWithAccents para mantener la conversión a mayúsculas
                let valor = toUpperWithAccents(e.target.value);
                
                // Luego quitamos solo los números
                // Esto preserva letras, espacios, acentos, ñ, etc.
                valor = valor.replace(/[0-9]/g, '');
                
                e.target.value = valor;
            });
        }
    });

    // Para campos estrictamente numéricos (CRN, CODIGO)
    const camposEstrictamenteNumericos = [
        'codigo_prof', 'crn'
    ];

    camposEstrictamenteNumericos.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.addEventListener('input', (e) => {
                // Solo permitir dígitos
                e.target.value = e.target.value.replace(/\D/g, '');
                
                // Aplicar la longitud máxima correspondiente
                const maxLength = campo === 'codigo_prof' ? 8 : 7; // 8 para código, 7 para CRN
                if (e.target.value.length > maxLength) {
                    e.target.value = e.target.value.slice(0, maxLength);
                }
            });
        }
    });

    // Aplicar límites
    Object.keys(maxLengths).forEach(field => {
        const input = document.getElementById(field);
        input && input.setAttribute('maxlength', maxLengths[field]);
    });

    // Actualizar número de oficio
    const actualizarNumeroOficio = () => {
        fetch('./functions/personal-solicitud-cambios/obtener_oficio_baja.php')
            .then(response => response.json())
            .then(data => {
                data.siguiente_numero && 
                    (document.getElementById('oficio_num_baja').value = data.siguiente_numero);
            });
    };

    // Guardar/Restaurar datos del formulario
    const guardarDatosFormulario = () => formData = new FormData(formBaja);
    
    const restaurarDatosFormulario = () => {
        formData.forEach((valor, clave) => {
            const input = formBaja.elements[clave];
            input && (input.value = valor);
        });
    };

    // Observer para detectar apertura del modal
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && 
                modalBaja.style.display === 'block') {
                actualizarNumeroOficio();
                restaurarDatosFormulario();
            }
        });
    });

    observer.observe(modalBaja, { attributes: true, attributeFilter: ['style'] });

    // Manejar envío del formulario
    formBaja.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
        
        fetch('./functions/personal-solicitud-cambios/procesar_baja.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            if (data.status === 'success') {
                modalBaja.style.display = 'none';
                formBaja.reset();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonColor: '#3085d6'
                }).then(() => window.location.reload());
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: error.message || 'Error en la comunicación',
                confirmButtonColor: '#d33'
            });
        });
    });

    // Botón descartar
    document.getElementById('btn-descartar').addEventListener('click', () => {
        Swal.fire({
            title: '¿Descartar cambios?',
            text: "Se perderán todos los datos ingresados",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                formBaja.reset();
                formData = new FormData();
                modalBaja.style.display = 'none';
            }
        });
    });

    // Cierre del modal
    document.querySelector('.close-button').addEventListener('click', () => {
        guardarDatosFormulario();
        modalBaja.style.display = 'none';
    });

    // Convertir a mayúsculas con acentos al escribir
    document.querySelectorAll('.modal-content input[type="text"]').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = toUpperWithAccents(this.value);
        });
    });

    // Manejar clic fuera del modal
    modalBaja.addEventListener('click', function(e) {
        if (e.target === modalBaja) {
            guardarDatosFormulario();
            modalBaja.style.display = 'none';
        }
    });
});
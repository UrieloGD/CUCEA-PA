// Archivo nueva-solicitud.js
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevaSolicitud = document.getElementById('nueva-solicitud-btn');
    const listaOpciones = document.getElementById('lista-opciones');
    
    if (!btnNuevaSolicitud || !listaOpciones) return;

    btnNuevaSolicitud.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        listaOpciones.classList.toggle('show');
    });

    listaOpciones.addEventListener('click', function(e) {
        const opcion = e.target.innerText;
        listaOpciones.classList.remove('show');

        switch(opcion) {
            case 'Solicitud de baja':
                if (typeof abrirModalBaja === 'function') {
                    abrirModalBaja();
                }
                break;
            // Agregar otros casos según necesites
        }
    });

    // Cerrar lista al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!btnNuevaSolicitud.contains(e.target) && !listaOpciones.contains(e.target)) {
            listaOpciones.classList.remove('show');
        }
    });
});
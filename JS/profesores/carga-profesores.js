// Función para ocultar la pantalla de carga
function hideLoadingScreen() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
        // Eliminamos completamente después de la transición
        setTimeout(() => {
            loadingOverlay.remove();
        }, 300);
    }
}

// Mostrar la pantalla de carga por un máximo de 10 segundos
const maxLoadingTime = setTimeout(hideLoadingScreen, 10000);

// Ocultar la pantalla de carga cuando la tabla se haya cargado
document.addEventListener('DOMContentLoaded', function() {
    // Detectar cuando DataTables termina de inicializarse
    $(document).ready(function() {
        $('.profesores-table').on('init.dt', function() {
            clearTimeout(maxLoadingTime); // Cancelar el temporizador máximo
            
            // Dar un pequeño retraso para asegurar que todo está listo
            setTimeout(hideLoadingScreen, 500);
        });
    });
});

// Backup: si DataTables no se inicializa correctamente
window.addEventListener('load', function() {
    // Si la página termina de cargar y aún no se ha ocultado la pantalla
    setTimeout(function() {
        // Verificar si la pantalla de carga aún existe
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay && !loadingOverlay.classList.contains('hidden')) {
            hideLoadingScreen();
        }
    }, 1000);
});
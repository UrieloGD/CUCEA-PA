// Función de debounce
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// Función para realizar la búsqueda
function realizarBusqueda() {
    const inputBuscador = document.getElementById("input-buscador");
    const tablaDatos = document.getElementById("tabla-datos").getElementsByTagName("tr");
    const filtro = inputBuscador.value.toUpperCase();

    // Crear un blob con el código del worker
    const workerBlob = new Blob([`
        self.onmessage = function(e) {
            const { filtro, datos } = e.data;
            const resultados = datos.map(fila => 
                fila.some(celda => celda.toUpperCase().indexOf(filtro) > -1)
            );
            self.postMessage(resultados);
        };
    `], { type: 'application/javascript' });

    // Crear un worker a partir del blob
    const worker = new Worker(URL.createObjectURL(workerBlob));

    // Enviar datos al worker
    worker.postMessage({ 
        filtro: filtro, 
        datos: Array.from(tablaDatos).slice(1).map(row => Array.from(row.cells).map(cell => cell.textContent)) 
    });

    worker.onmessage = function(e) {
        const resultados = e.data;
        for (let i = 1; i < tablaDatos.length; i++) {
            tablaDatos[i].style.display = resultados[i - 1] ? "" : "none";
        }
        // Terminar el worker después de usarlo
        worker.terminate();
    };
}

// Aplicar debounce a la función de búsqueda
const busquedaDebounced = debounce(realizarBusqueda, 300);

document.addEventListener("DOMContentLoaded", function() {
    const iconoBuscador = document.getElementById("icono-buscador");
    const barraBuscador = document.getElementById("barra-buscador");
    const inputBuscador = document.getElementById("input-buscador");

    // Mostrar/ocultar la barra de búsqueda
    iconoBuscador.addEventListener("click", function() {
        if (barraBuscador.style.display === "none" || barraBuscador.style.display === "") {
            barraBuscador.style.display = "flex";
        } else {
            barraBuscador.style.display = "none";
        }
    });

    // Evento de búsqueda
    inputBuscador.addEventListener("keyup", busquedaDebounced);
});
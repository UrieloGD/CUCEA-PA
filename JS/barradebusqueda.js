document.addEventListener("DOMContentLoaded", function() {
    const iconoBuscador = document.getElementById("icono-buscador");
    const barraBuscador = document.getElementById("barra-buscador");

    iconoBuscador.addEventListener("click", function() {
        if (barraBuscador.style.display === "none" || barraBuscador.style.display === "") {
            barraBuscador.style.display = "flex";
        } else {
            barraBuscador.style.display = "none";
        }
    });
});

// Función de barra de búsqueda
document.addEventListener("DOMContentLoaded", function() {
    const inputBuscador = document.getElementById("input-buscador");
    const tablaDatos = document.getElementById("tabla-datos").getElementsByTagName("tr");

    inputBuscador.addEventListener("keyup", function() {
        const filtro = inputBuscador.value.toUpperCase();

        // Itera sobre las filas de la tabla y muestra solo las que coinciden con el filtro
        for (let i = 1; i < tablaDatos.length; i++) {
            const fila = tablaDatos[i];
            const datosFila = fila.getElementsByTagName("td");
            let mostrarFila = false;

            // Itera sobre las celdas de la fila y verifica si alguna coincide con el filtro
            for (let j = 0; j < datosFila.length; j++) {
                const dato = datosFila[j];
                if (dato) {
                    const textoDato = dato.textContent || dato.innerText;
                    if (textoDato.toUpperCase().indexOf(filtro) > -1) {
                        mostrarFila = true;
                        break;
                    }
                }
            }

            // Muestra u oculta la fila según el resultado de la búsqueda
            if (mostrarFila) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        }
    });
});
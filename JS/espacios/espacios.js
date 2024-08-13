document.addEventListener('DOMContentLoaded', function() {
    const edificioContainer = document.getElementById('edificio-container');
    const filtros = document.querySelectorAll('.filtros select');
    const tiempoRealBtn = document.getElementById('tiempoReal');

    // Función para generar las aulas
    function generarAulas() {
        const pisos = [3, 2, 1];
        edificioContainer.innerHTML = '';

        pisos.forEach(piso => {
            const pisoDiv = document.createElement('div');
            pisoDiv.className = `piso piso-${piso}`;

            for (let i = 1; i <= 9; i++) {
                const aula = document.createElement('div');
                aula.className = 'aula';
                aula.textContent = `A-${piso}0${i}`;
                
                // Asignar clases aleatorias para demostración
                if (Math.random() > 0.5) {
                    aula.classList.add('ocupada');
                }
                if (Math.random() > 0.8) {
                    aula.classList.add('laboratorio');
                }
                if (Math.random() > 0.9) {
                    aula.classList.add('bodega');
                }
                if (Math.random() > 0.9) {
                    aula.classList.add('administrativo');
                }

                pisoDiv.appendChild(aula);
            }

            edificioContainer.appendChild(pisoDiv);
        });
    }

    // Evento para los filtros
    filtros.forEach(filtro => {
        filtro.addEventListener('change', generarAulas);
    });

    // Evento para el botón de tiempo real
    tiempoRealBtn.addEventListener('click', () => {
        // Aquí iría la lógica para obtener datos en tiempo real
        generarAulas();
    });

    // Generar aulas iniciales
    generarAulas();
});
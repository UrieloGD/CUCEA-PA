
document.addEventListener("DOMContentLoaded", function () {
    // Funciones de modal para: crear nuevo evento.
    // Función para agregar participantes
    document.querySelector('.escribir-parts').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            const inputValue = this.value.trim();
            if (inputValue) {
                addTab(inputValue, 'tabs-participantes'); // Especifica el contenedor de tabs
                this.value = ''; // Limpiar el input
            }
            event.preventDefault(); // Evitar el comportamiento del enter
        }
    });
    
    // Función para agregar etiquetas
    document.querySelector('.escribir-etiquetas').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            const inputValue = this.value.trim();
            if (inputValue) {
                addTab(inputValue, 'tabs-etiquetas', true); // Especifica el contenedor de tabs y marca como etiqueta
                this.value = ''; // Limpiar el input
            }
            event.preventDefault(); // Evitar el comportamiento del enter
        }
    });
    
    // Obtener color aleatorio
    function getRandomColor() {
        const letters = '89ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 8)];
        }
        return color;
    }
    
    function addTab(text, containerId, isEtiqueta = false) {
        const tabContainer = document.getElementById(containerId);
        const newTab = document.createElement('div');
        newTab.className = 'tab';
    
        // Aplicar color de fondo solo si es etiqueta
        if (isEtiqueta) {
            const randomColor = getRandomColor();
            newTab.style.backgroundColor = randomColor; // Color de fondo para etiquetas
        }
  
    if (!isEtiqueta) {
        // Solo para participantes: crear un span con la primera letra
        const firstLetter = text.charAt(0).toUpperCase();
        const letterSpan = document.createElement('span');
        letterSpan.className = 'tab-letter';
        letterSpan.textContent = firstLetter; // Establecer la letra
  
        // Generar un color aleatorio y aplicarlo al círculo
        const letterColor = getRandomColor();
        letterSpan.style.backgroundColor = letterColor; // Solo para participantes
  
        newTab.appendChild(letterSpan); // Añadir la letra al tab
    }
  
    newTab.appendChild(document.createTextNode(text)); // Añadir el resto del texto
  
    // Añadir clase específica para etiquetas
    if (isEtiqueta) {
        newTab.classList.add('tab-etiqueta');
    }
  
    // Crear el botón de cerrar
    const closeButton = document.createElement('span');
    closeButton.className = 'close-button';
    closeButton.textContent = '✖'; 
    closeButton.addEventListener('click', function() {
        tabContainer.removeChild(newTab); // Eliminar el tab
    });
  
    newTab.appendChild(closeButton); // Añadir el botón a la pestaña
    tabContainer.insertBefore(newTab, tabContainer.firstChild);
    }
});

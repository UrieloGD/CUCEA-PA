function filtrarTodosProfesores() {
    // Obtener el valor de búsqueda y convertirlo a minúsculas
    const input = document.getElementById('buscar-todos-profesores');
    const filtro = input.value.toLowerCase();
    
    // Obtener todas las filas de la tabla
    const tabla = document.querySelector('.profesores-table');
    const filas = tabla.getElementsByTagName('tr');
    
    // Recorrer todas las filas, empezando desde 1 para saltar el encabezado
    for (let i = 1; i < filas.length; i++) {
        const fila = filas[i];
        let mostrarFila = false;
        
        // Obtener las celdas de código, nombre y categoría
        const codigo = fila.getElementsByTagName('td')[0]?.textContent || '';
        const nombre = fila.getElementsByTagName('td')[1]?.textContent || '';
        const categoria = fila.getElementsByTagName('td')[2]?.textContent || '';
        
        // Verificar si el texto de búsqueda aparece en alguna de las columnas
        if (codigo.toLowerCase().includes(filtro) ||
            nombre.toLowerCase().includes(filtro) ||
            categoria.toLowerCase().includes(filtro)) {
            mostrarFila = true;
        }
        
        // Mostrar u ocultar la fila según el resultado
        fila.style.display = mostrarFila ? '' : 'none';
    }
}

// Función para limpiar la búsqueda cuando se presione Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const input = document.getElementById('buscar-todos-profesores');
        input.value = '';
        filtrarTodosProfesores();
    }
});
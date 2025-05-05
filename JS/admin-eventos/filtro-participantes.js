function filtrarParticipantes() {
    // Obtener el valor de búsqueda y convertirlo a minúsculas
    const input = document.getElementById('filtrarParticipantes');
    const filtro = input.value.toLowerCase();
    
    // Obtener todas las filas de la tabla
    const tabla = document.querySelector('.part-table');
    const filas = tabla.getElementsByTagName('tr');
    
    // Recorrer todas las filas, empezando desde 1 para saltar el encabezado
    for (let i = 1; i < filas.length; i++) {
        const fila = filas[i];
        let mostrarFila = false;
        
        // Obtener las celdas de código, nombre y categoría
        const nombre = fila.getElementsByTagName('td')[0]?.textContent || '';
        const correo = fila.getElementsByTagName('td')[1]?.textContent || '';
        const rol = fila.getElementsByTagName('td')[2]?.textContent || '';
        
        // Verificar si el texto de búsqueda aparece en alguna de las columnas
        if (nombre.toLowerCase().includes(filtro) ||
            correo.toLowerCase().includes(filtro) ||
            rol.toLowerCase().includes(filtro)) {
            mostrarFila = true;
        }
        
        // Mostrar u ocultar la fila según el resultado
        fila.style.display = mostrarFila ? '' : 'none';
    }
}
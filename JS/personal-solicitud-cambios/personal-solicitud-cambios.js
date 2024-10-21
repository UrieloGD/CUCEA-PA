// Función para mostrar el despliegue de información en pagina principal (personal-solicitud-cambios.php)
function mostrarInformacion(contenedorId, icono) {
    const nuevoContenedor = document.getElementById(contenedorId);

    if (nuevoContenedor.style.display === '' || nuevoContenedor.style.display === 'none') {
        nuevoContenedor.style.display = 'block'; // Cambiar a bloque
        icono.classList.add('rotar'); // Rotar el icono
    } else {
        nuevoContenedor.style.display = 'none'; // Ocultar el contenedor
        icono.classList.remove('rotar'); // Quitar la rotación del icono
    }
}


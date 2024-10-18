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

// Función para mostrar el modal
function mostrarModal() {
    const modal = document.getElementById('solicitud-modal'); 
    modal.style.display = 'block'; // Muestra el modal
}

// Agregar eventos de clic a cada opción de la lista
document.querySelectorAll('#lista-opciones li').forEach(item => {
    item.addEventListener('click', (event) => {
        const opcionSeleccionada = event.target.textContent; // Obtiene el texto de la opción seleccionada
        console.log(`Opción seleccionada: ${opcionSeleccionada}`);
        
        mostrarModal(); // Llama a la función para mostrar el modal
    });
});

// Función para cerrar el modal
document.getElementById('boton-cancelar').addEventListener('click', () => {
    const modal = document.getElementById('solicitud-modal'); // Asegúrate de que el ID del modal sea correcto
    modal.style.display = 'none'; // Oculta el modal
});

// Obtener el modal
var miModal = document.getElementById("solicitud-modal");

// Obtener las opciones de la lista
var opciones = document.querySelectorAll(".opcion-solicitud");

// Obtener el elemento <span> que cierra el modal
var span = document.getElementsByClassName("close-button")[0];

// Añadir un evento de clic a cada opción
opciones.forEach(function(opcion) {
    opcion.onclick = function() {
        miModal.style.display = "block";
    }
});

// Cuando el usuario hace clic en el botón de cerrar, cerrar el modal
span.onclick = function() {
    miModal.style.display = "none";
}

// Cuando el usuario hace clic fuera del modal, también se cierra
window.onclick = function(event) {
    if (event.target == miModal) {
        miModal.style.display = "none";
    }
}

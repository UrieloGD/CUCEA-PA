// Actualizar texto de desplegable segun responsividad.
function actualizarTextoOpcion() {
    const ancho = window.innerWidth;
    const opcion = document.getElementById("disabled-option");

    if (ancho <= 768) {
      opcion.textContent = "Estado";
    } else {
      opcion.textContent = "Selecciona un estado";
    }
  }

// Ejecutar al cargar y al redimensionar
window.addEventListener("load", actualizarTextoOpcion);
window.addEventListener("resize", actualizarTextoOpcion);
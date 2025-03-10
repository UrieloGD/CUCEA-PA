// Get the notification icon and menu
const notificationIcon = document.getElementById("notification-icon");
const notificationMenu = document.getElementById("notification-menu");

function actualizarTitulo() {
  var tituloContainer = document.querySelector(".titulo");
  if (!tituloContainer) return; // Evitar errores si no existe el elemento

  if (window.innerWidth <= 768) {
    tituloContainer.innerHTML = "";
  } else {
    tituloContainer.innerHTML = "<h3>Programación Académica</h3>";
  }
}

// Ejecutar la función al cargar la página
window.addEventListener("DOMContentLoaded", actualizarTitulo);
window.addEventListener("load", actualizarTitulo);

// También ejecutarla al redimensionar la ventana
window.addEventListener("resize", actualizarTitulo);

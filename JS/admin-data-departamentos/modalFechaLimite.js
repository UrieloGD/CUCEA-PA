// Función para abrir el modal
function openModal() {
  document.getElementById("modalFechaLimite").style.display = "block";
}

// Función para cerrar el modal
function closeModal() {
  document.getElementById("modalFechaLimite").style.display = "none";
}

// Cerrar el modal si se hace clic fuera del contenido del modal
window.onclick = function (event) {
  var modal = document.getElementById("modalFechaLimite");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
// Obtener el modal
var modal = document.getElementById("modal");

// Obtener el elemento span que cierra el modal
var span = document.getElementsByClassName("close")[0];

// Función para abrir el modal
function openModal() {
  modal.style.display = "block";
}

// Función para cerrar el modal
function closeModal() {
  modal.style.display = "none";
}

// Cuando el usuario hace clic en la "x", cierra el modal
span.onclick = function() {
  closeModal();
}

// Cuando el usuario hace clic fuera del modal, se cierra
window.onclick = function(event) {
  if (event.target == modal) {
    closeModal();
  }
}

// Obtener el botón de guardar fecha
var guardarFechaBtn = document.getElementById("guardar-fecha");

// Función para guardar la nueva fecha límite
guardarFechaBtn.onclick = function() {
  var nuevaFechaLimite = document.getElementById("fecha-limite").value;
  // Aquí puedes realizar las acciones necesarias para guardar la nueva fecha límite
  // Por ejemplo, enviar una solicitud AJAX al servidor o actualizar la variable $fecha_limite en PHP

  // Cerrar el modal después de guardar
  closeModal();
}

function guardarFechaLimite() {
  var nuevaFechaLimite = document.getElementById("fecha-limite").value;
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "actualizar_fecha_limite.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      if (xhr.responseText === "true") {
        alert("La fecha límite se ha actualizado correctamente.");
        closeModal();
        actualizarFechaLimiteVisual(nuevaFechaLimite);
      } else {
        alert("Error al actualizar la fecha límite.");
      }
    }
  };
  xhr.send("nueva_fecha_limite=" + encodeURIComponent(nuevaFechaLimite));
}

function actualizarFechaLimiteVisual(nuevaFechaLimite) {
  var fechaLimiteSpan = document.querySelector(".fechalimite span");
  var fechaLimiteObj = new Date(nuevaFechaLimite);
  var opciones = { 
    year: 'numeric', 
    month: 'numeric', 
    day: 'numeric', 
    hour: 'numeric', 
    minute: 'numeric',
    second: 'numeric'
  };
  var fechaLimiteFormateada = fechaLimiteObj.toLocaleString('es-ES', opciones);
  fechaLimiteSpan.textContent = "La fecha límite actual es " + fechaLimiteFormateada;
}
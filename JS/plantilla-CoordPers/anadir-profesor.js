function mostrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "block";
}

function cerrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "none";
  document.getElementById("form-añadir-registro").reset(); // Limpiar el formulario al cerrar
}

// Cerrar el modal al hacer clic en la X
document.querySelector(".close").onclick = function () {
  cerrarFormularioAñadir();
};

// Cerrar el modal al hacer clic fuera de él
window.onclick = function (event) {
  if (event.target == document.getElementById("modal-añadir")) {
    cerrarFormularioAñadir();
  }
};

function añadirRegistro() {
  // Mostrar loader o deshabilitar botón
  const submitButton = document.querySelector(
    '#form-añadir-registro button[onclick="añadirRegistro()"]'
  );
  submitButton.disabled = true;
  submitButton.textContent = "Guardando...";

  var form = document.getElementById("form-añadir-registro");
  var formData = new FormData(form);

  // Validar campos requeridos
  const requiredFields = ["codigo", "paterno", "materno", "nombre"];
  for (let field of requiredFields) {
    if (!formData.get(field)) {
      Swal.fire({
        title: "Error",
        text: `El campo ${field} es requerido`,
        icon: "error",
      });
      submitButton.disabled = false;
      submitButton.textContent = "Guardar";
      return;
    }
  }

  fetch("./functions/coord-personal-plantilla/anadir-profesor.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.text(); // Primero obtener el texto de la respuesta
    })
    .then((text) => {
      try {
        return JSON.parse(text); // Intentar parsear el JSON
      } catch (e) {
        console.error("Error parsing JSON:", text);
        throw new Error("Respuesta del servidor no válida");
      }
    })
    .then((data) => {
      if (data.success) {
        Swal.fire({
          title: "¡Éxito!",
          text: data.message,
          icon: "success",
        }).then(() => {
          cerrarFormularioAñadir();
          location.reload();
        });
      } else {
        throw new Error(data.message || "Error al guardar el registro");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        title: "Error",
        text: error.message || "Error al procesar la solicitud",
        icon: "error",
      });
    })
    .finally(() => {
      submitButton.disabled = false;
      submitButton.textContent = "Guardar";
    });
}

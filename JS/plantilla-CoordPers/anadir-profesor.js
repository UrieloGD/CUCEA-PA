// Función para inicializar la conversión de mayúsculas y minúsculas
function inicializarFormatoTexto() {
  // Obtenemos todos los campos de entrada de texto del formulario
  const inputs = document.querySelectorAll('#form-añadir-registro input[type="text"]');
  
  // Para cada campo de texto, agregamos un evento input
  inputs.forEach(input => {
    input.addEventListener('input', function() {
      // Verificamos que no sea un campo numérico por su nombre o ID
      const camposNumericos = ['codigo', 'cp', 'edad', 'año', 'otro_año', 'otro_año_alternativo', 'horas_frente_grupo', 'horas_definitivas'];
      if (!camposNumericos.includes(input.id)) {
        // Convertimos el valor a mayúsculas
        this.value = this.value.toUpperCase();
      }
    });
  });
  
  // Obtenemos todos los campos de correo electrónico
  const emailInputs = document.querySelectorAll('#form-añadir-registro input[type="email"]');
  
  // Para cada campo de correo, agregamos un evento input
  emailInputs.forEach(input => {
    input.addEventListener('input', function() {
      // Convertimos el valor a minúsculas
      this.value = this.value.toLowerCase();
    });
  });
}

// Modificamos la función añadirRegistro para asegurar el formato correcto antes de enviar
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

  // Asegurar formato correcto de todos los campos
  const camposNumericos = ['codigo', 'cp', 'edad', 'año', 'otro_año', 'otro_año_alternativo', 'horas_frente_grupo', 'horas_definitivas'];
  const camposEmail = ['correo', 'correos_oficiales'];
  
  for (let pair of formData.entries()) {
    const [key, value] = pair;
    if (typeof value === 'string') {
      if (camposEmail.includes(key)) {
        // Emails en minúsculas
        formData.set(key, value.toLowerCase());
      } else if (!camposNumericos.includes(key)) {
        // Texto en mayúsculas
        formData.set(key, value.toUpperCase());
      }
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

// Llamamos a la función cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', inicializarFormatoTexto);

// Resto de funciones del archivo
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
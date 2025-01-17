// Solicitud Ajax para guardar la justificación en la base de datos
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formulario-justificacion");
  const textarea = form.querySelector('textarea[name="justificacion"]');
  const charCount = document.getElementById("char-count");
  const continueButton = form.querySelector('button[type="submit"]');

  // Deshabilitar el botón inicialmente y añadir clase disabled
  continueButton.disabled = true;
  continueButton.classList.add("disabled");

  function countChars(text) {
    return text.replace(/\s/g, "").length;
  }

  textarea.addEventListener("input", function () {
    const charCountValue = countChars(this.value);
    charCount.textContent = charCountValue + " / 60 caracteres";

    // Habilitar el botón si hay al menos 60 caracteres, deshabilitar de lo contrario
    if (charCountValue >= 60) {
      continueButton.disabled = false;
      continueButton.classList.remove("disabled");
    } else {
      continueButton.disabled = true;
      continueButton.classList.add("disabled");
    }
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    console.log("Formulario enviado"); // Depuración

    const justificacion = textarea.value;
    const charCountValue = countChars(justificacion);
    console.log("Número de caracteres:", charCountValue); // Depuración

    if (charCountValue < 60) {
      console.log("Menos de 60 caracteres"); // Depuración
      Swal.fire(
        "Error",
        "La justificación debe tener al menos 60 caracteres sin contar espacios.",
        "error"
      );
      return;
    }

    console.log("Más de 60 caracteres, procediendo con el envío"); // Depuración

    // Mostrar Sweet Alert de carga
    Swal.fire({
      title: "Procesando...",
      html: "Por favor espere mientras se envía su justificación.",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
        console.log("Sweet Alert de carga mostrado");
      },
    }).then(() => {
      console.log("Sweet Alert de carga cerrado");
    });

    console.log("Iniciando fetch");

    const formData = new FormData(this);

    fetch("./functions/plantilla/guardar_justificacion.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("Respuesta del servidor:", data); // Depuración
        // Cerrar el Sweet Alert de carga
        Swal.close();

        if (data.success) {
          Swal.fire({
            title: "Éxito",
            text: "Justificación enviada correctamente",
            icon: "success",
            confirmButtonText: "Ok",
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            }
          });
        } else {
          Swal.fire("Error", data.message, "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.close();
        Swal.fire(
          "Error",
          "Hubo un problema al enviar la justificación",
          "error"
        );
      });
  });
});

// Poner 60 caracteres como mínimo para escribir la solicitud
document
  .querySelector('textarea[name="justificacion"]')
  .addEventListener("input", function () {
    var charCount = this.value.length;
    document.getElementById("char-count").textContent =
      charCount + " / 60 caracteres mínimo";
  });

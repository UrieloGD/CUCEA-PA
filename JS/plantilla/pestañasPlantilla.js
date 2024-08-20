const tabButtons = document.querySelectorAll(".tab-button");
const tabPanes = document.querySelectorAll(".tab-pane");

tabButtons.forEach((button, index) => {
  button.addEventListener("click", () => {
    // Remover la clase 'active' de todos los botones y paneles
    tabButtons.forEach((btn) => btn.classList.remove("active"));
    tabPanes.forEach((pane) => pane.classList.remove("active"));

    // Agregar la clase 'active' al botón y panel correspondiente
    button.classList.add("active");
    tabPanes[index].classList.add("active");
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const formJustificacion = document.getElementById("formulario-justificacion");
  if (formJustificacion) {
    formJustificacion.addEventListener("submit", function (event) {
      event.preventDefault();
      const formData = new FormData(this);

      fetch("./actions/plantilla/guardar_justificacion.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (response.ok) {
            Swal.fire({
              icon: "success",
              title: "Justificación enviada",
              text: "Puedes subir tu archivo ahora.",
              confirmButtonText: "Aceptar",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Hubo un error al enviar la justificación.",
              confirmButtonText: "Aceptar",
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    });
  }
});

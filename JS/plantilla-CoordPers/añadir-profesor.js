function mostrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "block";
}

function cerrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "none";
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
  var form = document.getElementById("form-añadir-registro");
  var datos = new FormData(form);

  var xhr = new XMLHttpRequest();
  xhr.open(
    "POST",
    "./functions/plantilla-CoordPers/añadir-profesor.php",
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        try {
          var respuesta = JSON.parse(xhr.responseText);
          if (respuesta.success) {
            Swal.fire({
              title: "¡Éxito!",
              text: respuesta.message,
              icon: "success",
            }).then(() => {
              cerrarFormularioAñadir();
              location.reload();
            });
          } else {
            Swal.fire({
              title: "Error",
              text: respuesta.message,
              icon: "error",
            });
          }
        } catch (e) {
          Swal.fire({
            title: "Error",
            text: "Error al procesar la respuesta del servidor",
            icon: "error",
          });
        }
      } else {
        Swal.fire({
          title: "Error",
          text: "Error de conexión con el servidor",
          icon: "error",
        });
      }
    }
  };
  xhr.send(datos);
}

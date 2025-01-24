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

  var departamento_id = document.getElementById("departamento_id").value;
  datos.append("departamento_id", departamento_id);

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./functions/basesdedatos/añadir-registro.php", true);
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

// Definir los dias completos
var lunes = document.getElementById("l");
var martes = document.getElementById("m");
var miercoles = document.getElementById("i");
var jueves = document.getElementById("j");
var viernes = document.getElementById("v");
var sabado = document.getElementById("s");
var domingo = document.getElementById("d");

// Funcion para manejar la modalidad seleccionada
document.getElementById("modalidad").addEventListener("change", function () {
  const modalidad = this.value;
  if (modalidad === "PRESENCIAL ENRIQUECIDA") {
    document.getElementById("title_dia_presencial").style.display = "block";
    document.getElementById("dia_presencial").style.display = "block";
    document.getElementById("title_dia_virtual").style.display = "none";
    document.getElementById("dia_virtual").style.display = "none";
  } else if (modalidad === "VIRTUAL") {
    document.getElementById("title_dia_virtual").style.display = "block";
    document.getElementById("dia_virtual").style.display = "block";
    document.getElementById("title_dia_presencial").style.display = "none";
    document.getElementById("dia_presencial").style.display = "none";
  } else if (modalidad === "MIXTA") {
    document.getElementById("title_dia_presencial").style.display = "block";
    document.getElementById("dia_presencial").style.display = "block";
    document.getElementById("title_dia_virtual").style.display = "block";
    document.getElementById("dia_virtual").style.display = "block";
  }

  function actualizarDia() {
    var diaPresencial = "";
    var diaVirtual = "";

    if (modalidad === "PRESENCIAL ENRIQUECIDA" || modalidad === "VIRTUAL") {
      if (lunes.value === "L") {
        diaPresencial = "LUNES";
        diaVirtual = "LUNES";
      }
      if (martes.value === "M") {
        diaPresencial = "MARTES";
        diaVirtual = "MARTES";
      }
      if (miercoles.value === "I") {
        diaPresencial = "MIERCOLES";
        diaVirtual = "MIERCOLES";
      }
      if (jueves.value === "J") {
        diaPresencial = "JUEVES";
        diaVirtual = "JUEVES";
      }
      if (viernes.value === "V") {
        diaPresencial = "VIERNES";
        diaVirtual = "VIERNES";
      }
      if (sabado.value === "S") {
        diaPresencial = "SABADO";
        diaVirtual = "SABADO";
      }
      if (domingo.value === "D") {
        diaPresencial = "DOMINGO";
        diaVirtual = "DOMINGO";
      }
    }

    var totalDias = 0;
    totalDias += lunes.value === "L" ? 1 : 0;
    totalDias += martes.value === "M" ? 1 : 0;
    totalDias += miercoles.value === "I" ? 1 : 0;
    totalDias += jueves.value === "J" ? 1 : 0;
    totalDias += viernes.value === "V" ? 1 : 0;
    totalDias += sabado.value === "S" ? 1 : 0;
    totalDias += domingo.value === "D" ? 1 : 0;

    if (
      totalDias > 1 &&
      (modalidad === "PRESENCIAL ENRIQUECIDA" || modalidad === "VIRTUAL")
    ) {
      document.getElementById("dia_presencial").value = "AMBOS";
      document.getElementById("dia_virtual").value = "AMBOS";
    } else if (diaPresencial && diaVirtual) {
      document.getElementById("dia_presencial").value = diaPresencial;
      document.getElementById("dia_virtual").value = diaVirtual;
    } else {
      document.getElementById("dia_presencial").value = "";
      document.getElementById("dia_virtual").value = "";
    }
  }

  // Agregar event listeners a cada campo de texto de los dias (L, M, I, J, V, S, D)
  lunes.addEventListener("input", actualizarDia);
  martes.addEventListener("input", actualizarDia);
  miercoles.addEventListener("input", actualizarDia);
  jueves.addEventListener("input", actualizarDia);
  viernes.addEventListener("input", actualizarDia);
  sabado.addEventListener("input", actualizarDia);
  domingo.addEventListener("input", actualizarDia);
});

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

function añadirRegistro() {
  // Mostrar SweetAlert de espera inicial
  Swal.fire({
    title: "Procesando...",
    text: "Añadiendo registro, por favor espere.",
    icon: "info",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

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
            // Verificar si se debe duplicar el registro con tipo opuesto
            const duplicarCheckbox = document.getElementById("duplicar-tipo");
            const tipoSeleccionado = document.getElementById("tipo").value;

            if (
              duplicarCheckbox &&
              duplicarCheckbox.checked &&
              (tipoSeleccionado === "P" || tipoSeleccionado === "T")
            ) {
              // Actualizar el SweetAlert para indicar que se está procesando el registro duplicado
              Swal.update({
                title: "Procesando...",
                text: "Primer registro añadido. Añadiendo registro duplicado, por favor espere.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                  Swal.showLoading();
                },
              });

              // Crear un nuevo FormData para el registro duplicado
              var datosDuplicados = new FormData(form);
              datosDuplicados.append("departamento_id", departamento_id);

              // Cambiar el tipo al opuesto
              const tipoOpuesto = tipoSeleccionado === "P" ? "T" : "P";
              datosDuplicados.set("tipo", tipoOpuesto);

              // Enviar la solicitud para el registro duplicado
              var xhr2 = new XMLHttpRequest();
              xhr2.open(
                "POST",
                "./functions/basesdedatos/añadir-registro.php",
                true
              );
              xhr2.onreadystatechange = function () {
                if (xhr2.readyState === XMLHttpRequest.DONE) {
                  if (xhr2.status === 200) {
                    try {
                      var respuesta2 = JSON.parse(xhr2.responseText);
                      if (respuesta2.success) {
                        // Actualizar el SweetAlert para indicar que se están enviando notificaciones
                        Swal.update({
                          title: "Procesando...",
                          text: "Ambos registros añadidos.",
                          confirmButtonColor: "#0071B0",
                        });

                        // Simular un breve retraso para que el usuario vea el mensaje de notificaciones
                        setTimeout(() => {
                          Swal.fire({
                            title: "¡Éxito!",
                            text: "Ambos registros fueron añadidos correctamente.",
                            icon: "success",
                            confirmButtonColor: "#0071B0",
                          }).then(() => {
                            cerrarFormularioAñadir();
                            location.reload();
                          });
                        }, 1000);
                      } else {
                        Swal.fire({
                          title: "Advertencia",
                          text:
                            "El primer registro se añadió correctamente, pero hubo un error al añadir el registro duplicado: " +
                            respuesta2.message,
                          icon: "warning",
                          confirmButtonColor: "#0071B0",
                        }).then(() => {
                          cerrarFormularioAñadir();
                          location.reload();
                        });
                      }
                    } catch (e) {
                      Swal.fire({
                        title: "Advertencia",
                        text: "El primer registro se añadió correctamente, pero hubo un error al procesar la respuesta para el registro duplicado.",
                        icon: "warning",
                        confirmButtonColor: "#0071B0",
                      }).then(() => {
                        cerrarFormularioAñadir();
                        location.reload();
                      });
                    }
                  } else {
                    Swal.fire({
                      title: "Advertencia",
                      text: "El primer registro se añadió correctamente, pero hubo un error de conexión al intentar añadir el registro duplicado.",
                      icon: "warning",
                      confirmButtonColor: "#0071B0",
                    }).then(() => {
                      cerrarFormularioAñadir();
                      location.reload();
                    });
                  }
                }
              };
              xhr2.send(datosDuplicados);
            } else {
              // Si no hay duplicación, actualizar el SweetAlert para indicar que se están enviando notificaciones
              Swal.update({
                title: "Procesando...",
                text: "Registro añadido.",
                confirmButtonColor: "#0071B0",
              });

              // Simular un breve retraso para que el usuario vea el mensaje de notificaciones
              setTimeout(() => {
                // Si no hay duplicación, mostrar mensaje normal
                Swal.fire({
                  title: "¡Éxito!",
                  text: respuesta.message,
                  icon: "success",
                  confirmButtonColor: "#0071B0",
                }).then(() => {
                  cerrarFormularioAñadir();
                  location.reload();
                });
              }, 1000);
            }
          } else {
            Swal.fire({
              title: "Error",
              text: respuesta.message,
              icon: "error",
              customClass: {
                confirmButton: "OK-boton",
              }
            });
          }
        } catch (e) {
          Swal.fire({
            title: "Error",
            text: "Error al procesar la respuesta del servidor",
            icon: "error",
            confirmButtonText: "OK",
            customClass: {
              confirmButton: "OK-boton",
            }
          });
        }
      } else {
        Swal.fire({
          title: "Error",
          text: "Error de conexión con el servidor",
          icon: "error",
          confirmButtonText: "OK",
          customClass: {
            confirmButton: "OK-boton",
          }
        });
      }
    }
  };
  xhr.send(datos);
}

// Condicionales y estilos del modal.
// --------------------------------- //

// Variables para presenciales y virtuales
var lunes = document.getElementById("l");
var martes = document.getElementById("m");
var miercoles = document.getElementById("i");
var jueves = document.getElementById("j");
var viernes = document.getElementById("v");
var sabado = document.getElementById("s");
var domingo = document.getElementById("d");

// Variables para mixta (desplegable presencial)
var lun = document.getElementById("lun");
var mar = document.getElementById("mar");
var mie = document.getElementById("mie");
var jue = document.getElementById("jue");
var vie = document.getElementById("vie");
var sab = document.getElementById("sab");
var dom = document.getElementById("dom");

// Variables para mixta (desplegable virtual)
var lun2 = document.getElementById("lun2");
var mar2 = document.getElementById("mar2");
var mie2 = document.getElementById("mie2");
var jue2 = document.getElementById("jue2");
var vie2 = document.getElementById("vie2");
var sab2 = document.getElementById("sab2");
var dom2 = document.getElementById("dom2");

// Funcion para manejar la modalidad seleccionada
document.getElementById("modalidad").addEventListener("change", function () {
  const modalidad = this.value;
  if (modalidad === "PRESENCIAL ENRIQUECIDA") {
    // Vaciar el valor de los input
    document.getElementById("dia_presencial").value = "";
    lunes.value = "";
    martes.value = "";
    miercoles.value = "";
    jueves.value = "";
    viernes.value = "";
    sabado.value = "";
    domingo.value = "";

    // Para que aparezca el contenedor de dias presenciales y virtuales
    document.getElementById("presencial-virtual").style.display = "flex";

    // Titulos e inputs para dias presenciales y ocultar los virtuales
    document.getElementById("title_dia_presencial").style.display = "block";
    document.getElementById("title_dia_presencial2").style.display = "none";
    document.getElementById("dia_presencial").style.display = "block";
    document.getElementById("title_dia_virtual").style.display = "none";
    document.getElementById("title_dia_virtual2").style.display = "none";
    document.getElementById("dia_virtual").style.display = "none";

    // Ocultar las opciones de desplegables de dias mixtos.
    document.getElementById("mixta").style.display = "none";
    document.getElementById("dia_presencial2").style.display = "none";
    document.getElementById("dia_virtual2").style.display = "none";
  } else if (modalidad === "VIRTUAL") {
    // Vaciar el valor de los input
    document.getElementById("dia_virtual").value = "";
    lunes.value = "";
    martes.value = "";
    miercoles.value = "";
    jueves.value = "";
    viernes.value = "";
    sabado.value = "";
    domingo.value = "";

    // Para que aparezca el contenedor de dias presenciales y virtuales
    document.getElementById("presencial-virtual").style.display = "flex";

    // Titulos e inputs para dias virtuales y ocultar los presenciales
    document.getElementById("title_dia_virtual").style.display = "block";
    document.getElementById("title_dia_virtual2").style.display = "none";
    document.getElementById("dia_virtual").style.display = "block";
    document.getElementById("title_dia_presencial").style.display = "none";
    document.getElementById("title_dia_presencial2").style.display = "none";
    document.getElementById("dia_presencial").style.display = "none";

    // Ocultar las opciones de desplegables de dias mixtos.
    document.getElementById("mixta").style.display = "none";
    document.getElementById("dia_presencial2").style.display = "none";
    document.getElementById("dia_virtual2").style.display = "none";
  } else if (modalidad === "MIXTA") {
    // Vaciar el valor de los input
    document.getElementById("dia_presencial2").value = "";
    document.getElementById("dia_virtual2").value = "";
    lunes.value = "";
    martes.value = "";
    miercoles.value = "";
    jueves.value = "";
    viernes.value = "";
    sabado.value = "";
    domingo.value = "";

    // Para que aparezca el contenedor de dias mixtos
    document.getElementById("mixta").style.display = "flex";

    // Titulos y <select> para dias presenciales y virtuales
    document.getElementById("title_dia_presencial2").style.display = "block";
    document.getElementById("title_dia_presencial").style.display = "none";
    document.getElementById("dia_presencial2").style.display = "block";
    document.getElementById("title_dia_virtual2").style.display = "block";
    document.getElementById("title_dia_virtual").style.display = "none";
    document.getElementById("dia_virtual2").style.display = "block";

    // Ocultar inputs de presenciales y virtuales
    document.getElementById("dia_presencial").style.display = "none";
    document.getElementById("dia_virtual").style.display = "none";
  }

  function actualizarDia() {
    if (modalidad === "PRESENCIAL ENRIQUECIDA") {
      // Aparece el dia en automatico en el campo de dia_presencial.
      if (lunes.value === "L") {
        document.getElementById("dia_presencial").value = "LUNES";
      }
      if (martes.value === "M") {
        document.getElementById("dia_presencial").value = "MARTES";
      }
      if (miercoles.value === "I") {
        document.getElementById("dia_presencial").value = "MIERCOLES";
      }
      if (jueves.value === "J") {
        document.getElementById("dia_presencial").value = "JUEVES";
      }
      if (viernes.value === "V") {
        document.getElementById("dia_presencial").value = "VIERNES";
      }
      if (sabado.value === "S") {
        document.getElementById("dia_presencial").value = "SABADO";
      }
      if (domingo.value === "D") {
        document.getElementById("dia_presencial").value = "DOMINGO";
      }
    }

    if (modalidad === "VIRTUAL") {
      // Aparece el dia en automatico en el campo de dia_virtual.
      if (lunes.value === "L") {
        document.getElementById("dia_virtual").value = "LUNES";
      }
      if (martes.value === "M") {
        document.getElementById("dia_virtual").value = "MARTES";
      }
      if (miercoles.value === "I") {
        document.getElementById("dia_virtual").value = "MIERCOLES";
      }
      if (jueves.value === "J") {
        document.getElementById("dia_virtual").value = "JUEVES";
      }
      if (viernes.value === "V") {
        document.getElementById("dia_virtual").value = "VIERNES";
      }
      if (sabado.value === "S") {
        document.getElementById("dia_virtual").value = "SABADO";
      }
      if (domingo.value === "D") {
        document.getElementById("dia_virtual").value = "DOMINGO";
      }
    }

    if (modalidad === "MIXTA") {
      document.getElementById("dia_presencial").value =
        document.getElementById("dia_presencial2").value;
      document.getElementById("dia_virtual").value =
        document.getElementById("dia_virtual2").value;

      // Que aparezca en lista el dia.
      if (lunes.value === "L") {
        document.getElementById("lun").style.display = "block";
        document.getElementById("lun2").style.display = "block";
      } else {
        document.getElementById("lun").style.display = "none";
        document.getElementById("lun2").style.display = "none";
      }
      if (martes.value === "M") {
        document.getElementById("mar").style.display = "block";
        document.getElementById("mar2").style.display = "block";
      } else {
        document.getElementById("mar").style.display = "none";
        document.getElementById("mar2").style.display = "none";
      }
      if (miercoles.value === "I") {
        document.getElementById("mie").style.display = "block";
        document.getElementById("mie2").style.display = "block";
      } else {
        document.getElementById("mie").style.display = "none";
        document.getElementById("mie2").style.display = "none";
      }
      if (jueves.value === "J") {
        document.getElementById("jue").style.display = "block";
        document.getElementById("jue2").style.display = "block";
      } else {
        document.getElementById("jue").style.display = "none";
        document.getElementById("jue2").style.display = "none";
      }
      if (viernes.value === "V") {
        document.getElementById("vie").style.display = "block";
        document.getElementById("vie2").style.display = "block";
      } else {
        document.getElementById("vie").style.display = "none";
        document.getElementById("vie2").style.display = "none";
      }
      if (sabado.value === "S") {
        document.getElementById("sab").style.display = "block";
        document.getElementById("sab2").style.display = "block";
      } else {
        document.getElementById("sab").style.display = "none";
        document.getElementById("sab2").style.display = "none";
      }
      if (domingo.value === "D") {
        document.getElementById("dom").style.display = "block";
        document.getElementById("dom2").style.display = "block";
      } else {
        document.getElementById("dom").style.display = "none";
        document.getElementById("dom2").style.display = "none";
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

    // Si es un dia, el dia, si son dos dias, ambos.
    if (totalDias > 1 && modalidad === "PRESENCIAL ENRIQUECIDA") {
      document.getElementById("dia_presencial").value = "AMBOS";
      document.getElementById("dia_virtual").value = "";
    } else if (totalDias > 1 && modalidad === "VIRTUAL") {
      document.getElementById("dia_virtual").value = "AMBOS";
      document.getElementById("dia_presencial").value = "";
    } else {
      // Si no hay ninguna letra, regresar el valor de dia presencial y virtual a vacio.
      if (
        lunes.value === "" &&
        martes.value === "" &&
        miercoles.value === "" &&
        jueves.value === "" &&
        viernes.value === "" &&
        sabado.value === "" &&
        domingo.value === ""
      ) {
        document.getElementById("dia_presencial").value = "";
        document.getElementById("dia_virtual").value = "";
      }
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
  document
    .getElementById("dia_presencial2")
    .addEventListener("change", actualizarDia);
  document
    .getElementById("dia_virtual2")
    .addEventListener("change", actualizarDia);
});

// Actualizar el valor de dia_virtual2 cuando se cambia dia_presencial2
function actualizarDiaVirtual2() {
  const dia_presencial = document.getElementById("dia_presencial2").value;
  const dia_virtual_option = document.getElementById("dia_virtual2");

  // Automatizacion del dia virtual
  if (dia_presencial === "LUNES" && miercoles.value === "I") {
    dia_virtual_option.value = "MIERCOLES";
  } else if (dia_presencial === "MARTES" && jueves.value === "J") {
    dia_virtual_option.value = "JUEVES";
  } else if (dia_presencial === "MIERCOLES" && lunes.value === "L") {
    dia_virtual_option.value = "LUNES";
  } else if (dia_presencial === "JUEVES" && martes.value === "M") {
    dia_virtual_option.value = "MARTES";
  }
  dia_virtual_option.style.color = "#000000";
  dia_virtual_option.style.fontStyle = "normal";
}

// Actualizar el valor de dia_presencial2 cuando se cambia dia_virtual2
function actualizarDiaPresencial2() {
  const dia_virtual = document.getElementById("dia_virtual2").value;
  const dia_presencial_select = document.getElementById("dia_presencial2");

  // Automatizacion del dia presencial
  if (dia_virtual === "LUNES" && miercoles.value === "I") {
    dia_presencial_select.value = "MIERCOLES";
  } else if (dia_virtual === "MARTES" && jueves.value === "J") {
    dia_presencial_select.value = "JUEVES";
  } else if (dia_virtual === "MIERCOLES" && lunes.value === "L") {
    dia_presencial_select.value = "LUNES";
  } else if (dia_virtual === "JUEVES" && martes.value === "M") {
    dia_presencial_select.value = "MARTES";
  }
  dia_presencial_select.style.color = "#000000";
  dia_presencial_select.style.fontStyle = "normal";
}

// Variables para cada ID de <select>
var nivel = document.getElementById("nivel");
var tipo = document.getElementById("tipo");
var nivel_tipo = document.getElementById("nivel_tipo");
var estatus = document.getElementById("estatus");
var modalidad_option = document.getElementById("modalidad");
var dia_presencial_option = document.getElementById("dia_presencial2");
var dia_virtual_option = document.getElementById("dia_virtual2");
var examen_extraordinario = document.getElementById("examen_extraordinario");
var tipo_contrato = document.getElementById("tipo_contrato");
var categoria = document.getElementById("categoria");
var descarga = document.getElementById("descarga");
var titular = document.getElementById("titular");
var hora_inicial = document.getElementById("hora_inicial");
var hora_final = document.getElementById("hora_final");

// Cambiar el color del texto al ingresar datos en los inputs...
function setupSelectStyleHandler(selectElement, defaultText) {
  selectElement.addEventListener("change", function () {
    const isDefault = this.value === defaultText;
    this.style.color = isDefault ? "" : "#000000";
    this.style.fontStyle = isDefault ? "" : "normal";
  });
}
const selectConfigs = [
  { element: nivel, defaultText: "Seleccione el nivel correspondiente..." },
  { element: tipo, defaultText: "Seleccione la opción correspondiente..." },
  {
    element: nivel_tipo,
    defaultText: "Seleccione la opción correspondiente...",
  },
  { element: estatus, defaultText: "Seleccione la opción correspondiente..." },
  {
    element: modalidad_option,
    defaultText: "Seleccione la modalidad correspondiente...",
  },
  {
    element: dia_presencial_option,
    defaultText: "Seleccione el dia presencial...",
  },
  { element: dia_virtual_option, defaultText: "Seleccione el dia virtual..." },
  {
    element: examen_extraordinario,
    defaultText: "Seleccione la opción correspondiente...",
  },
  {
    element: tipo_contrato,
    defaultText: "Seleccione el tipo de contrato correspondiente...",
  },
  {
    element: categoria,
    defaultText: "Seleccione la categoria correspondiente...",
  },
  { element: descarga, defaultText: "Seleccione la opción correspondiente..." },
  { element: titular, defaultText: "Seleccione la opción correspondiente..." },
];
const timeInputs = [
  { element: hora_inicial, defaultText: "" },
  { element: hora_final, defaultText: "" },
];
selectConfigs.forEach((config) =>
  setupSelectStyleHandler(config.element, config.defaultText)
);
timeInputs.forEach((config) =>
  setupSelectStyleHandler(config.element, config.defaultText)
);

// Cambiar el texto a color #000000 cuando se selecciona alguna fecha.
const fechaInicial = document.getElementById("fecha_inicial");
const fechaFinal = document.getElementById("fecha_final");

function cambiarColorTexto(event) {
  const input = event.target;
  if (input.value) {
    input.style.color = "#000000";
    input.style.fontStyle = "normal";
  } else {
    input.style.color = "";
    input.style.fontStyle = "italic";
  }
}

fechaInicial.addEventListener("change", cambiarColorTexto);
fechaFinal.addEventListener("change", cambiarColorTexto);

// Cerrar modal dando click afuera de el
$(window).click(function (event) {
  if (event.target.className === "modal-R") {
    $(".modal-R").hide();
  }
});

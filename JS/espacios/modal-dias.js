function mostrarModal(espacio, horarios) {
  $("#modalTitle").text("Características del espacio");
  $("#moduloInfo").text(horarios.modulo);
  $("#espacioInfo").text(espacio);
  $("#tipoInfo").text(horarios.tipo);
  $("#cupoInfo").text(horarios.cupo);

  var equipoList = $("#equipoList");
  equipoList.empty();
  var equipos = [
    "Computadora",
    "Proyector",
    "Cortina Proyector",
    "Cortinas",
    "Doble Pizarrón",
    "Pantalla",
    "Cámaras",
  ];
  equipos.forEach(function (equipo) {
    equipoList.append(
      `<li><input type="checkbox" id="${equipo.replace(
        " ",
        "_"
      )}" name="${equipo}"><label for="${equipo.replace(
        " ",
        "_"
      )}">${equipo}</label></li>`
    );
  });

  // Agregar campos de texto para observaciones y reportes
  $("#observacionesArea").html(
    '<textarea id="observaciones" rows="3" cols="30"></textarea>'
  );
  $("#reportesArea").html(
    '<textarea id="reportes" rows="3" cols="30"></textarea>'
  );

  // Cargar la información guardada
  $.ajax({
    url: "./functions/espacios/obtener-equipo-info.php",
    method: "GET",
    data: {
      modulo: horarios.modulo,
      espacio: espacio,
    },
    dataType: "json",
    success: function (data) {
      if (data.success) {
        // Marcar los checkboxes del equipo
        var equipoArray = data.equipo.split(",");
        equipoArray.forEach(function (item) {
          $(`#${item.trim().replace(" ", "_")}`).prop("checked", true);
        });

        // Llenar los campos de texto
        $("#observaciones").val(data.observaciones);
        $("#reportes").val(data.reportes);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar la información:", error);
    },
  });

  $("#tabContent").empty(); // Limpiar el contenido anterior

  var dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"];

  dias.forEach(function (dia) {
    var contenido = '<table class="horario-table">';
    contenido +=
      "<thead><tr><th>Hora</th><th>Clase</th><th>Profesor</th></tr></thead><tbody>";

    if (horarios[dia] && horarios[dia].length > 0) {
      horarios[dia].forEach(function (clase) {
        contenido += `<tr>
                        <td>${clase.hora_inicial} - ${clase.hora_final}</td>
                        <td>${clase.materia}</td>
                        <td>${clase.profesor}</td>
                    </tr>`;
      });
    } else {
      contenido +=
        '<tr><td colspan="3">No hay clases programadas para este día.</td></tr>';
    }

    contenido += "</tbody></table>";
    $(`#tabContent`).append(
      `<div id="${dia}" class="tabcontent">${contenido}</div>`
    );
  });

  $("#claseModal").show();
  openDay(null, "Lunes"); // Mostrar el lunes por defecto
  $(".tablinks").first().addClass("active"); // Activar el tab de Lunes
}

function openDay(evt, dayName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(dayName).style.display = "block";
  if (evt) evt.currentTarget.className += " active";

  $(document).ready(function () {
    function guardarInfoEspacio() {
      var modulo = $("#moduloInfo").text();
      var espacio = $("#espacioInfo").text();
      var equipo = [];
      $("#equipoList input:checked").each(function () {
        equipo.push($(this).attr("name"));
      });
      var observaciones = $("#observaciones").val();
      var reportes = $("#reportes").val();

      $.ajax({
        url: "./functions/espacios/guardar-equipo-info.php",
        method: "POST",
        data: {
          modulo: modulo,
          espacio: espacio,
          equipo: equipo,
          observaciones: observaciones,
          reportes: reportes,
        },
        dataType: "json",
        success: function (result) {
          if (result.success) {
            alert("Información guardada con éxito");
          } else {
            alert(
              "Error al guardar la información: " +
                (result.error || "Unknown error")
            );
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
          alert(
            "Error al guardar la información. Por favor, revise la consola para más detalles."
          );
        },
      });
    }

    // Asignar el evento de clic una sola vez
    $(document).ready(function () {
      $("#guardarCambios")
        .off("click")
        .on("click", function () {
          guardarInfoEspacio();
        });
    });

    $("#cerrarModal").click(function () {
      $("#claseModal").hide();
    });

    // Opcional: guardar al cerrar el modal si hay cambios sin guardar
    var cambiosSinGuardar = false;

    $(".modal-content input, .modal-content textarea").on(
      "change",
      function () {
        cambiosSinGuardar = true;
      }
    );

    $("#cerrarModal").click(function () {
      if (cambiosSinGuardar) {
        if (
          confirm("Hay cambios sin guardar. ¿Desea guardarlos antes de cerrar?")
        ) {
          guardarInfoEspacio();
        }
      }
      $("#claseModal").hide();
      cambiosSinGuardar = false;
    });
  });
}

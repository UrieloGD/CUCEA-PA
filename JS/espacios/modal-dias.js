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

  $("#observacionesArea").html(
    '<textarea id="observaciones" rows="3" cols="30"></textarea>'
  );
  $("#reportesArea").html(
    '<textarea id="reportes" rows="3" cols="30"></textarea>'
  );

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
        if (data.equipo && data.equipo.trim() !== "") {
          var equipoArray = data.equipo.split(",");
          equipoArray.forEach(function (item) {
            var trimmedItem = item.trim();
            if (trimmedItem !== "") {
              $(`#${trimmedItem.replace(" ", "_")}`).prop("checked", true);
            }
          });
        }
        $("#observaciones").val(data.observaciones || "");
        $("#reportes").val(data.reportes || "");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar la información:", error);
    },
  });

  $("#tabContent").empty();

  var dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"];

  dias.forEach(function (dia) {
    var contenido = '<table class="horario-table">';
    // Agregamos departamento a los headers
    contenido += "<thead><tr><th>Hora</th><th>Clase</th><th>Profesor</th><th>Departamento</th></tr></thead><tbody>";

    if (horarios[dia] && horarios[dia].length > 0) {
        var horariosMap = new Map();

        horarios[dia].forEach(function (clase) {
            const claveHorario = `${clase.hora_inicial}-${clase.hora_final}`;

            if (!horariosMap.has(claveHorario)) {
                horariosMap.set(claveHorario, 1);
            } else {
                horariosMap.set(claveHorario, horariosMap.get(claveHorario) + 1);
            }
        });

        horarios[dia].forEach(function (clase) {
            const claveHorario = `${clase.hora_inicial}-${clase.hora_final}`;
            const esConflicto = horariosMap.get(claveHorario) > 1;
            const estiloConflicto = esConflicto ? ' style="color: red;"' : "";

            contenido += `<tr${estiloConflicto}>
              <td>${clase.hora_inicial} - ${clase.hora_final}</td>
              <td>${clase.materia}</td>
              <td>${clase.profesor}</td>
              <td>${clase.departamento || 'Sin información'}</td>
            </tr>`;
        });
    } else {
        contenido += '<tr><td colspan="4">No hay clases programadas para este día.</td></tr>';
    }

    contenido += "</tbody></table>";
    $(`#tabContent`).append(
      `<div id="${dia}" class="tabcontent">${contenido}</div>`
    );
});

  // Determinar la clase del espacio
  function determinarClaseEspacio(tipo) {
    tipo = tipo.toLowerCase();
    if (tipo.includes('aula')) return 'aula';
    if (tipo.includes('laboratorio')) return 'laboratorio';
    if (tipo.includes('administrativo') || tipo.includes('oficina administrativa')) return 'oficina-administrativa';
    if (tipo.includes('bodega')) return 'bodega';
    return 'espacio-generico';
  }

  // Actualizar la clase e imagen del espacio
  var claseEspacio = determinarClaseEspacio(horarios.tipo);
  $(".sala-modal")
    .removeClass()
    .addClass(`sala-modal ${claseEspacio}`);
  
  $(".sala-modal img")
    .attr({
      src: `./Img/Icons/iconos-espacios/icono-${claseEspacio}.png`,
      alt: horarios.tipo
    });

  $("#claseModal").show();
  openDay(null, "Lunes");
  $(".tablinks").first().addClass("active");
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
}

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
          console.log("Información guardada con éxito");
          console.log("Respuesta del servidor:", result);
        } else {
          console.error(
            "Error al guardar la información: " +
              (result.error || "Unknown error")
          );
          console.log("Respuesta del servidor:", result);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.log("Respuesta del servidor:", xhr.responseText);
      },
    });
  }

  $(".close")
    .off("click")
    .on("click", function () {
      guardarInfoEspacio();
      $("#claseModal").hide();
    });

  $(window)
    .off("click")
    .on("click", function (event) {
      if (event.target == document.getElementById("claseModal")) {
        guardarInfoEspacio();
        $("#claseModal").hide();
      }
    });

  // Eliminamos los eventos que ya no son necesarios
  $("#guardarCambios").off("click");
  $("#cerrarModal").off("click");
  $(".modal-content input, .modal-content textarea").off("change");
});

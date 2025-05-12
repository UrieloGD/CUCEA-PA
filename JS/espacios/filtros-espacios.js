$(document).ready(function () {
  $("#modulo").change(function () {
    var modulo = $(this).val();
    window.location.href = "espacios.php?modulo=" + modulo;
  });

  function getDiaActual() {
    var dias = ["D", "L", "M", "I", "J", "V", "S"];
    return dias[new Date().getDay()];
  }

  function getHoraActual() {
    var hora = new Date().getHours();
    return (hora < 10 ? "0" : "") + hora + ":00";
  }

  function calcularHoraFin(horaInicio) {
    var hora = parseInt(horaInicio.split(":")[0]);
    var horaFin = (hora + 1) % 24;
    return (horaFin < 10 ? "0" : "") + horaFin + ":55";
  }

  $("#horario_inicio").change(function () {
    var horaInicio = $(this).val();
    var $horaFin = $("#horario_fin");
    $horaFin.empty();
    $horaFin.append('<option value="">Hora fin</option>');

    if (horaInicio) {
      var horaInicioNum = parseInt(horaInicio.split(":")[0]);
      for (var i = horaInicioNum + 1; i <= 21; i++) {
        var hour = (i < 10 ? "0" : "") + i + ":55";
        $horaFin.append('<option value="' + hour + '">' + hour + "</option>");
      }
    }
  });

  $("#tiempo-real").change(function () {
    if ($(this).is(":checked")) {
      var diaActual = getDiaActual();
      var horaActual = getHoraActual();
      var horaFin = calcularHoraFin(horaActual);

      $("#dia").val(diaActual).prop("disabled", true);
      $("#horario_inicio").val(horaActual).prop("disabled", true);
      $("#horario_fin").val(horaFin).prop("disabled", true);

      $("#filtrar").click();
    } else {
      $("#dia, #horario_inicio, #horario_fin").prop("disabled", false);
    }
  });

  // Función mejorada para actualizar las aulas amplias
  function actualizarAulasAmplias(espacios_ocupados) {
    console.log("Actualizando aulas amplias con datos:", espacios_ocupados);
    
    // Mapeo de los IDs de espacios que llegan del servidor a los elementos visuales
    // Se asume que 0001, 0001A o 0001B corresponden al aula 1, 0002 al aula 2, etc.
    const mapping = {
      // Mapeo para números simples (0001, 0002, etc.)
      "0001": 6, "0002": 5, "0003": 4, 
      "0004": 3, "0005": 2, "0006": 1,
      // Mapeo para variantes con letras (0001A, 0001B, etc.)
      "0001A": 6, "0001B": 6, "0001C": 6, 
      "0002A": 5, "0002B": 5, "0002C": 5, 
      "0003A": 4, "0003B": 4, "0003C": 4, 
      "0004A": 3, "0004B": 3, "0004C": 3, 
      "0005A": 2, "0005B": 2, "0005C": 2,
      "0006A": 1, "0006B": 1, "0006C": 1 
    };
    
    // Resetear todas las aulas amplias a su estado original
    for (let i = 1; i <= 6; i++) {
      $(`#azul-${i}`).removeClass("aula-azul-ocupada").addClass("aula-azul");
      $(`#AA${i}`).removeAttr("data-info");
    }
    
    // Marcar las aulas amplias que estén ocupadas
    Object.keys(espacios_ocupados).forEach(function(espacio) {
      // Verificar si el espacio está en nuestro mapeo
      const aulaNum = mapping[espacio];
      
      if (aulaNum) {
        console.log(`Marcando ${espacio} como ocupada (aula ${aulaNum})`);
        $(`#azul-${aulaNum}`).removeClass("aula-azul").addClass("aula-azul-ocupada");
        
        // Añadir los datos para mostrar en el hover
        $(`#AA${aulaNum}`).attr("data-info", JSON.stringify(espacios_ocupados[espacio]));
      }
    });
  }

  $("#filtrar").click(function () {
    var modulo = $("#modulo").val();
    var dia = $("#dia").val();
    var hora_inicio = $("#horario_inicio").val();
    var hora_fin = $("#horario_fin").val();
    var tiempoReal = $("#tiempo-real").is(":checked");

    if (tiempoReal) {
      dia = getDiaActual();
      hora_inicio = getHoraActual();
      hora_fin = calcularHoraFin(hora_inicio);
    }

    // Validar que se hayan seleccionado todos los campos necesarios
    if (!modulo) {
      console.error("Módulo no seleccionado");
      return;
    }
    
    // En módulo CEDAA permitir filtrado aunque falten algunos campos
    if (modulo !== "CEDAA" && (!dia || !hora_inicio || !hora_fin)) {
      alert("Por favor complete todos los campos de filtrado");
      return;
    }

    console.log("Filtrando espacios:", {
      modulo: modulo,
      dia: dia,
      hora_inicio: hora_inicio,
      hora_fin: hora_fin
    });

    $.ajax({
      url: "./functions/espacios/obtener-espacios.php",
      method: "GET",
      data: {
        modulo: modulo,
        dia: dia,
        hora_inicio: hora_inicio,
        hora_fin: hora_fin,
      },
      success: function (response) {
        console.log("Respuesta del servidor:", response);
        var espacios_ocupados = JSON.parse(response);

        // Restablecer todas las salas a su estado original
        $(".sala")
          .removeClass("aula-ocupada laboratorio-ocupado ocupado")
          .removeAttr("data-info");

        // Marcar las salas ocupadas
        Object.keys(espacios_ocupados).forEach(function (espacio) {
          var salaElement = $('[data-espacio="' + espacio + '"]');
          var info = espacios_ocupados[espacio];

          if (salaElement.hasClass("aula")) {
            salaElement.addClass("aula-ocupada");
          } else if (salaElement.hasClass("laboratorio")) {
            salaElement.addClass("laboratorio-ocupado");
          } else {
            salaElement.addClass("ocupado");
          }

          salaElement.attr("data-info", JSON.stringify(info));
        });
        
        // Actualizar aulas amplias si estamos en el módulo CEDAA
        if (modulo === "CEDAA") {
          console.log("Actualizando aulas amplias");
          actualizarAulasAmplias(espacios_ocupados);
        }
      },
      error: function(xhr, status, error) {
        console.error("Error en la solicitud AJAX:", error);
        console.error("Respuesta del servidor:", xhr.responseText);
        alert("Hubo un error al filtrar los espacios. Por favor, intente de nuevo.");
      }
    });
  });

  // Manejador de eventos para mostrar información en hover
  $(document)
    .on("mouseenter", ".sala[data-info], [id^='azul-'], [id^='AA']", function (e) {
      // Para aulas amplias, buscar info en el elemento AA correspondiente
      var info;
      if (this.id && this.id.startsWith('azul-')) {
        var aulaNum = this.id.split('-')[1];
        var $aaElement = $(`#AA${aulaNum}`);
        if ($aaElement.attr('data-info')) {
          info = JSON.parse($aaElement.attr('data-info'));
        } else {
          return; // No hay info que mostrar
        }
      } else if (this.id && this.id.startsWith('AA')) {
        if ($(this).attr('data-info')) {
          info = JSON.parse($(this).attr('data-info'));
        } else {
          return; // No hay info que mostrar
        }
      } else if ($(this).attr('data-info')) {
        info = JSON.parse($(this).attr('data-info'));
      } else {
        return; // No hay info que mostrar
      }
      
      var infoHtml =
        '<div class="info-hover">' +
        "<p><strong>CVE Materia:</strong> " +
        info.cve_materia +
        "</p>" +
        "<p><strong>Materia:</strong> " +
        info.materia +
        "</p>" +
        "<p><strong>Profesor:</strong> " +
        info.profesor +
        "</p>" +
        "</div>";
      var $infoElement = $(infoHtml).appendTo("body");

      var salaRect = this.getBoundingClientRect();
      var infoRect = $infoElement[0].getBoundingClientRect();

      var top = salaRect.top - infoRect.height - 10;
      var left = salaRect.left + salaRect.width / 2 - infoRect.width / 2;

      $infoElement.css({
        position: "fixed",
        top: Math.max(0, top) + "px",
        left: Math.max(0, left) + "px",
      });

      $(this).data("infoElement", $infoElement);
    })
    .on("mouseleave", ".sala[data-info], [id^='azul-'], [id^='AA']", function () {
      var $infoElement = $(this).data("infoElement");
      if ($infoElement) {
        $infoElement.remove();
      }
    });

  // Evento de clic para abrir el modal en espacios normales
  $(document).on("click", ".sala", function () {
    var espacio = $(this).data("espacio");
    var modulo = $("#modulo").val();

    $.ajax({
      url: "./functions/espacios/obtener-horario-aula.php",
      method: "GET",
      data: { espacio: espacio, modulo: modulo },
      dataType: "json",
      success: function (horarios) {
        console.log("Respuesta del servidor:", horarios);
        if (typeof horarios === "object" && horarios !== null) {
          mostrarModal(espacio, horarios);
        } else {
          console.error("La respuesta no es un objeto válido:", horarios);
          alert(
            "Hubo un error al cargar los horarios. Por favor, intente de nuevo."
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la solicitud AJAX:", error);
        console.error("Respuesta del servidor:", xhr.responseText);
        alert(
          "Hubo un error al cargar los horarios. Por favor, intente de nuevo."
        );
      },
    });
  });

  // Evento de clic para AA1-AA6
  $(document).on("click", "[id^='AA']", function() {
    var aulaNum = this.id.substring(2);
    var espacio = "000" + aulaNum;
    var modulo = "CEDAA";

    console.log("Clic en aula amplia:", espacio);

    $.ajax({
      url: "./functions/espacios/obtener-horario-aula.php",
      method: "GET",
      data: { espacio: espacio, modulo: modulo },
      dataType: "json",
      success: function(horarios) {
        console.log("Respuesta del servidor para aula amplia:", horarios);
        if (typeof horarios === "object" && horarios !== null) {
          mostrarModal(espacio, horarios);
        } else {
          console.error("La respuesta no es un objeto válido:", horarios);
          alert("Hubo un error al cargar los horarios. Por favor, intente de nuevo.");
        }
      },
      error: function(xhr, status, error) {
        console.error("Error en la solicitud AJAX:", error);
        console.error("Respuesta del servidor:", xhr.responseText);
        alert("Hubo un error al cargar los horarios. Por favor, intente de nuevo.");
      }
    });
  });

  // También permitir clic en los elementos visuales azul-N
  $(document).on("click", "[id^='azul-']", function() {
    var aulaNum = this.id.split('-')[1];
    $(`#AA${aulaNum}`).trigger('click');
  });

  // Cerrar el modal
  $(".close").click(function () {
    $("#claseModal").hide();
  });

  // Cerrar el modal si se hace clic fuera de él
  $(window).click(function (event) {
    if (event.target == document.getElementById("claseModal")) {
      $("#claseModal").hide();
    }
  });
  
  // Limpiar filtros
  $("#limpiar").click(function() {
    $("#dia").val("").prop("disabled", false);
    $("#horario_inicio").val("").prop("disabled", false);
    $("#horario_fin").val("").prop("disabled", false);
    $("#tiempo-real").prop("checked", false);
    
    // Restablecer todas las salas a su estado original
    $(".sala")
      .removeClass("aula-ocupada laboratorio-ocupado ocupado")
      .removeAttr("data-info");
    
    // Restablecer todas las aulas amplias
    for (let i = 1; i <= 6; i++) {
      $(`#azul-${i}`).removeClass("aula-azul-ocupada").addClass("aula-azul");
      $(`#AA${i}`).removeAttr("data-info");
    }
  });
  
  // Ejecutar filtrado inicial si estamos en el módulo CEDAA y los filtros están completos
  if ($("#modulo").val() === "CEDAA") {
    // Comprobar si hay filtros seleccionados
    var dia = $("#dia").val();
    var hora_inicio = $("#horario_inicio").val();
    var hora_fin = $("#horario_fin").val();
    
    if (dia && hora_inicio && hora_fin) {
      console.log("Ejecutando filtrado inicial para CEDAA");
      $("#filtrar").click();
    }
  }
});
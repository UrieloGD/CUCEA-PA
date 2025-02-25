$(document).ready(function () {
  // Función para normalizar los identificadores de las aulas
  function normalizarIdAula(aaNum) {
    // Convertir a string si es número
    aaNum = String(aaNum);

    // Quitar cualquier letra al final (A, B, C)
    aaNum = aaNum.replace(/[A-Za-z]$/, "");

    // Normalizar al formato de 4 dígitos
    if (aaNum.length === 1) {
      return "000" + aaNum;
    } else if (aaNum.length === 2) {
      // Si es 03, 05, 06, etc.
      return "00" + aaNum;
    } else {
      return aaNum;
    }
  }

  function cargarDatosEspacio(espacio) {
    // Normalizar el ID del aula
    espacio = normalizarIdAula(espacio);
    console.log(`Cargando datos para espacio: ${espacio}`);

    $.ajax({
      url: "./functions/espacios/obtener-horario-aula.php",
      method: "GET",
      data: {
        modulo: "CEDAA",
        espacio: espacio,
      },
      dataType: "json",
      success: function (data) {
        if (data) {
          console.log("Datos recibidos:", data);
          // Asegurarse de que data.tipo tenga un valor
          data.tipo = data.tipo || "Aula"; // Valor por defecto si es nulo

          // Agregar información adicional si es un aula con divisiones
          if (["0001", "0002", "0004"].includes(espacio)) {
            data.tipoCompleto = data.tipo + " (incluye divisiones A, B y C)";
          } else {
            data.tipoCompleto = data.tipo;
          }

          mostrarModal(espacio, data);
        } else {
          console.error("No se encontró información para el espacio:", espacio);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar los horarios:", error);
        console.log("Respuesta recibida:", xhr.responseText);
      },
    });
  }

  // Configurar eventos para los elementos del modo normal de Aulas Amplias
  function configurarEventosAulasAmplias() {
    // Mapeo correcto de círculos azules a aulas
    const mapeoCirculosAulas = {
      "azul-1": "0006", // Círculo 0001 abre aula 0006
      "azul-2": "0005", // Círculo 0002 abre aula 0005
      "azul-3": "0004", // Círculo 0003 abre aula 0004
      "azul-4": "0003", // Círculo 0004 abre aula 0003
      "azul-5": "0002", // Círculo 0005 abre aula 0002
      "azul-6": "0001", // Círculo 0006 abre aula 0001
    };

    // Para los elementos span que tienen los IDs AA1, AA2, etc.
    $("[id^='AA']")
      .off("click")
      .on("click", function () {
        const aaNum = $(this).attr("id").replace("AA", "");
        cargarDatosEspacio(aaNum);
      });

    // Para los círculos azules que representan las aulas
    $("[id^='azul-']")
      .off("click")
      .on("click", function () {
        const idCirculo = $(this).attr("id");
        // Usar el mapeo para encontrar el aula correcta
        const aaNum =
          mapeoCirculosAulas[idCirculo] || idCirculo.replace("azul-", "");
        cargarDatosEspacio(aaNum);
      });

    console.log("Eventos configurados para Aulas Amplias en modo normal");
  }

  // Verificar si estamos en el módulo de Aulas Amplias y configurar eventos
  if ($("#modulo").val() === "CEDAA") {
    configurarEventosAulasAmplias();
  }

  // Volver a agregar eventos cuando cambia de módulo
  $("#modulo").on("change", function () {
    if ($(this).val() === "CEDAA") {
      // Dar tiempo a que se cargue la vista
      setTimeout(configurarEventosAulasAmplias, 500);
    }
  });

  // Modificar función mostrarModal para manejar información adicional
  const originalMostrarModal = window.mostrarModal;
  window.mostrarModal = function (espacio, horarios) {
    // Si el espacio tiene información de tipo completo, usarla
    if (horarios.tipoCompleto) {
      horarios.tipo = horarios.tipoCompleto;
    }

    // Llamar a la función original
    originalMostrarModal(espacio, horarios);

    // Actualizar el título si es un aula con divisiones
    if (["0001", "0002", "0004"].includes(espacio)) {
      $("#tipoInfo").text(horarios.tipo + " (incluye divisiones A, B y C)");
    }
  };
});

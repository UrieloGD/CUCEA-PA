document.addEventListener("DOMContentLoaded", function () {
  // Obtener elementos del DOM
  const departamentoCards = document.querySelectorAll(".desglose-button-dpto");

  // Variables para almacenar los datos de cada tipo de hora por departamento
  let datosDepartamentos = {};

  // Función para cargar los datos de un departamento
  function cargarDatosDepartamento(departamento) {
    let endpoint = "./functions/horas-comparacion/obtener-personal.php";

    fetch(endpoint, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `departamento=${encodeURIComponent(departamento)}`,
    })
      .then((response) => response.text())
      .then((text) => {
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("Error parsing JSON:", text);
          throw new Error("Error al procesar la respuesta del servidor");
        }
      })
      .then((data) => {
        if (data.error) {
          throw new Error(data.error);
        }

        // Inicializar datos para este departamento si no existen
        if (!datosDepartamentos[departamento]) {
          datosDepartamentos[departamento] = {};
        }

        // Calcular totales para cada tipo de hora
        let totalFrenteGrupo = 0;
        let totalMaxFrenteGrupo = 0;
        let totalDefinitivas = 0;
        let totalMaxDefinitivas = 0;
        let totalTemporales = 0;

        data.forEach((persona) => {
          // Horas Frente Grupo
          totalFrenteGrupo += parseInt(persona.suma_cargo_plaza) || 0;
          totalMaxFrenteGrupo += parseInt(persona.Horas_frente_grupo) || 0;

          // Horas Definitivas
          totalDefinitivas += parseInt(persona.suma_horas_definitivas) || 0;
          totalMaxDefinitivas += parseInt(persona.Horas_definitivas) || 0;

          // Horas Temporales
          totalTemporales += parseInt(persona.suma_horas_temporales) || 0;
        });

        // Guardar los datos calculados
        datosDepartamentos[departamento] = {
          frenteGrupo: {
            total: Math.round(totalFrenteGrupo),
            max: Math.round(totalMaxFrenteGrupo),
          },
          definitivas: {
            total: Math.round(totalDefinitivas),
            max: Math.round(totalMaxDefinitivas),
          },
          temporales: {
            total: Math.round(totalTemporales),
            max: 0, // Las horas temporales no tienen máximo
          },
        };

        // Actualizar visualización según el departamento
        if (departamento === "todos") {
          actualizarVisualizacionGeneral("frente-grupo");
        } else {
          actualizarVisualizacionHoras(departamento, "frente-grupo");
        }
      })
      .catch((error) => {
        console.error(`Error cargando datos para ${departamento}:`, error);
        if (departamento === "todos") {
          actualizarSinDatos();
        } else {
          actualizarSinDatos(departamento);
        }
      });
  }

  // Actualizar la visualización general
  function actualizarVisualizacionGeneral(tipoHora = "frente-grupo") {
    const datos = datosDepartamentos["todos"];
    if (!datos) return;

    let tipoData;
    let porcentaje;
    let textoHoras;

    switch (tipoHora) {
      case "frente-grupo":
        tipoData = datos.frenteGrupo;
        porcentaje =
          tipoData.max > 0
            ? Math.round((tipoData.total / tipoData.max) * 100)
            : 0;
        textoHoras = `${tipoData.total.toLocaleString()} / <strong>${tipoData.max.toLocaleString()}</strong>`;
        break;
      case "definitivas":
        tipoData = datos.definitivas;
        porcentaje =
          tipoData.max > 0
            ? Math.round((tipoData.total / tipoData.max) * 100)
            : 0;
        textoHoras = `${tipoData.total.toLocaleString()} / <strong>${tipoData.max.toLocaleString()}</strong>`;
        break;
      case "temporales":
        tipoData = datos.temporales;
        porcentaje = 100; // Siempre 100% para temporales
        textoHoras = `${tipoData.total.toLocaleString()}`;
        break;
      default:
        tipoData = datos.frenteGrupo;
        porcentaje =
          tipoData.max > 0
            ? Math.round((tipoData.total / tipoData.max) * 100)
            : 0;
        textoHoras = `${tipoData.total.toLocaleString()} / <strong>${tipoData.max.toLocaleString()}</strong>`;
    }

    // Actualizar elementos del DOM
    const horasCompGeneral = document.getElementById("horas-comp-general");
    const porcentajeElement = document.getElementById("porcentaje-general");
    const circuloProgreso = document.querySelector(".circulo-progreso");
    const buttons = document.querySelectorAll(
      ".total-general-hrs_container .tipo-hora-btn"
    );

    if (horasCompGeneral) horasCompGeneral.innerHTML = textoHoras;
    if (porcentajeElement) porcentajeElement.textContent = `${porcentaje}%`;
    if (circuloProgreso) {
      circuloProgreso.style.background = `conic-gradient(#0071b0 ${
        porcentaje * 3.6
      }deg, #f0f0f0 ${porcentaje * 3.6}deg)`;
    }

    buttons.forEach((btn) => {
      btn.classList.toggle(
        "active",
        btn.getAttribute("data-tipo") === tipoHora
      );
    });
  }

  // Manejar el caso sin datos para el total general
  function actualizarSinDatos(departamento) {
    if (departamento !== "todos") {
      // Encontrar todos los elementos que podrían corresponder a este departamento
      const deptoElements = document.querySelectorAll(".desglose-button-dpto");
      deptoElements.forEach((btn) => {
        if (btn.getAttribute("data-departamento") === departamento) {
          // Encontrar el elemento que contiene las horas en el contenedor padre
          const deptoContainer = btn.closest(".contenedor-informacion");
          const horasCompElement =
            deptoContainer.querySelector(".horas-comp-dpto");

          if (horasCompElement) {
            // Actualizar con mensaje de sin datos
            horasCompElement.innerHTML = `<span style="color: #2684D5;">No se encontraron datos</span>`;

            // Actualizar la barra de progreso a 0%
            const barraProgreso =
              deptoContainer.querySelector(".barra-stats-hrs");
            const porcentajeElement =
              deptoContainer.querySelector(".porcentaje-dpto");

            if (barraProgreso && porcentajeElement) {
              barraProgreso.style.width = "0%";
              porcentajeElement.textContent = "0%";
            }

            // Asegurarse de que el botón de Frente Grupo esté activo al principio
            const botonesHora =
              deptoContainer.querySelectorAll(".tipo-hora-btn");
            botonesHora.forEach((btn) => {
              if (btn.getAttribute("data-tipo") === "frente-grupo") {
                btn.classList.add("active");
              } else {
                btn.classList.remove("active");
              }
            });
          }
        }
      });
    } else {
      // Si es "todos", llamar a la función específica para actualizar el contador general
      actualizarSinDatos();
    }
  }
  // Función para actualizar la visualización con el tipo de hora seleccionado
  function actualizarVisualizacionHoras(departamento, tipoHora) {
    if (!datosDepartamentos[departamento]) {
      return; // No tenemos datos para este departamento
    }

    let datos;
    let porcentaje;
    let textoHoras;

    switch (tipoHora) {
      case "frente-grupo":
        datos = datosDepartamentos[departamento].frenteGrupo;
        porcentaje =
          datos.max > 0 ? Math.round((datos.total / datos.max) * 100) : 0;
        textoHoras = `${datos.total.toLocaleString()} / <strong>${datos.max.toLocaleString()}</strong>`;
        break;
      case "definitivas":
        datos = datosDepartamentos[departamento].definitivas;
        porcentaje =
          datos.max > 0 ? Math.round((datos.total / datos.max) * 100) : 0;
        textoHoras = `${datos.total.toLocaleString()} / <strong>${datos.max.toLocaleString()}</strong>`;
        break;
      case "temporales":
        datos = datosDepartamentos[departamento].temporales;
        // Para horas temporales, mostramos solo el total sin máximo
        porcentaje = 100; // Siempre 100% para horas temporales
        textoHoras = `${datos.total.toLocaleString()}`;
        break;
    }

    // Encontrar el contenedor de departamento
    const btnDesglose = document.querySelector(
      `.desglose-button-dpto[data-departamento="${departamento}"]`
    );
    if (!btnDesglose) return;

    const contenedorInfo = btnDesglose.closest(".contenedor-informacion");
    if (!contenedorInfo) return;

    // Actualizar texto de horas
    const horasCompElement = contenedorInfo.querySelector(".horas-comp-dpto");
    if (horasCompElement) {
      horasCompElement.innerHTML = textoHoras;
    }

    // Actualizar barra de progreso
    const barraProgreso = contenedorInfo.querySelector(".barra-stats-hrs");
    const porcentajeElement = contenedorInfo.querySelector(".porcentaje-dpto");

    if (barraProgreso && porcentajeElement) {
      barraProgreso.style.width = `${porcentaje}%`;
      porcentajeElement.textContent = `${porcentaje}%`;
    }

    // Actualizar botones de tipo de hora
    const botonesHora = contenedorInfo.querySelectorAll(".tipo-hora-btn");
    botonesHora.forEach((btn) => {
      if (btn.getAttribute("data-tipo") === tipoHora) {
        btn.classList.add("active");
      } else {
        btn.classList.remove("active");
      }
    });
  }

  // Función para cargar datos de todos los departamentos al inicio
  function cargarDatosDepartamentos() {
    // Obtener todos los departamentos
    const departamentoCards = document.querySelectorAll(
      ".desglose-button-dpto"
    );

    // Cargar datos para cada departamento
    departamentoCards.forEach((card) => {
      const departamento = card.getAttribute("data-departamento");
      cargarDatosDepartamento(departamento);
    });

    // Cargar datos para el total general
    cargarDatosDepartamento("todos");
  }

  // Añadir event listeners para los botones de tipo de hora
  document.addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("tipo-hora-btn")) {
      const tipoHora = e.target.getAttribute("data-tipo");
      const contenedorInfo = e.target.closest(".contenedor-informacion");
      const generalContainer = e.target.closest(".total-general-hrs_container");

      if (contenedorInfo) {
        // Botones de departamento individual
        const btnDesglose = contenedorInfo.querySelector(
          ".desglose-button-dpto"
        );
        if (btnDesglose) {
          const departamento = btnDesglose.getAttribute("data-departamento");
          actualizarVisualizacionHoras(departamento, tipoHora);
        }
      } else if (generalContainer) {
        // Botones de "Todos los departamentos"
        actualizarVisualizacionGeneral(tipoHora);
      }
    }
  });

  // Función para cargar los totales de un departamento específico
  function cargarTotalesDepartamento(departamento) {
    fetch("./functions/horas-comparacion/obtener-personal.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `departamento=${encodeURIComponent(departamento)}`,
    })
      .then((response) => response.text())
      .then((text) => {
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("Error parsing JSON:", text);
          throw new Error("Error al procesar la respuesta del servidor");
        }
      })
      .then((data) => {
        if (data.error) {
          throw new Error(data.error);
        }

        if (!Array.isArray(data) || data.length === 0) {
          // No hay datos para este departamento
          actualizarSinDatos(departamento);
          return;
        }

        // Calcular totales de horas frente grupo
        let totalFrenteGrupo = 0;
        let totalMaxFrenteGrupo = 0;

        data.forEach((persona) => {
          const horasCargoActual = parseInt(persona.suma_cargo_plaza) || 0;
          const horasFrenteRequeridas =
            parseInt(persona.Horas_frente_grupo) || 0;

          totalFrenteGrupo += horasCargoActual;
          totalMaxFrenteGrupo += horasFrenteRequeridas;
        });

        // Actualizar los valores según el departamento
        if (departamento !== "todos") {
          // Encontrar todos los elementos que podrían corresponder a este departamento
          const deptoElements = document.querySelectorAll(
            ".desglose-button-dpto"
          );
          deptoElements.forEach((btn) => {
            if (btn.getAttribute("data-departamento") === departamento) {
              // Encontrar el elemento que contiene las horas en el contenedor padre
              const deptoContainer = btn.closest(".contenedor-informacion");
              const horasCompElement =
                deptoContainer.querySelector(".horas-comp-dpto");

              if (horasCompElement) {
                // Actualizar con los valores calculados
                horasCompElement.innerHTML = `${Math.round(
                  totalFrenteGrupo
                ).toLocaleString()} / <strong>${Math.round(
                  totalMaxFrenteGrupo
                ).toLocaleString()}</strong>`;

                // Actualizar también la barra de progreso
                const porcentaje =
                  totalMaxFrenteGrupo > 0
                    ? Math.round((totalFrenteGrupo / totalMaxFrenteGrupo) * 100)
                    : 0;
                const barraProgreso =
                  deptoContainer.querySelector(".barra-stats-hrs");
                const porcentajeElement =
                  deptoContainer.querySelector(".porcentaje-dpto");

                if (barraProgreso && porcentajeElement) {
                  barraProgreso.style.width = `${porcentaje}%`;
                  porcentajeElement.textContent = `${porcentaje}%`;
                }
              }
            }
          });
        } else {
          // Si es "todos", actualizar el contador general
          const horasCompGeneral =
            document.getElementById("horas-comp-general");
          if (horasCompGeneral) {
            horasCompGeneral.innerHTML = `${Math.round(
              totalFrenteGrupo
            ).toLocaleString()} / <strong>${Math.round(
              totalMaxFrenteGrupo
            ).toLocaleString()}</strong>`;

            // Actualizar también el porcentaje en el círculo
            const porcentaje =
              totalMaxFrenteGrupo > 0
                ? Math.round((totalFrenteGrupo / totalMaxFrenteGrupo) * 100)
                : 0;
            const porcentajeElement =
              document.getElementById("porcentaje-general");
            if (porcentajeElement) {
              porcentajeElement.textContent = `${porcentaje}%`;
            }

            // Actualizar el círculo de progreso

            const circuloProgreso = document.querySelector(".circulo-progreso");
            if (circuloProgreso) {
              // Aseguramos que el centro permanece blanco y solo se actualiza el borde
              circuloProgreso.style.backgroundImage = `conic-gradient(#0071b0 ${
                porcentaje * 3.6
              }deg, transparent ${porcentaje * 3.6}deg)`;
            }
          }
        }
      })
      .catch((error) => {
        console.error(`Error cargando datos para ${departamento}:`, error);
        actualizarSinDatos(departamento);
      });
  }

  // Nueva función para actualizar cuando no hay datos
  function actualizarSinDatos(departamento) {
    if (departamento !== "todos") {
      // Encontrar todos los elementos que podrían corresponder a este departamento
      const deptoElements = document.querySelectorAll(".desglose-button-dpto");
      deptoElements.forEach((btn) => {
        if (btn.getAttribute("data-departamento") === departamento) {
          // Encontrar el elemento que contiene las horas en el contenedor padre
          const deptoContainer = btn.closest(".contenedor-informacion");
          const horasCompElement =
            deptoContainer.querySelector(".horas-comp-dpto");

          if (horasCompElement) {
            // Actualizar con mensaje de sin datos
            horasCompElement.innerHTML = `<span style="color: #2684D5;">No se encontraron datos</span>`;

            // Actualizar la barra de progreso a 0%
            const barraProgreso =
              deptoContainer.querySelector(".barra-stats-hrs");
            const porcentajeElement =
              deptoContainer.querySelector(".porcentaje-dpto");

            if (barraProgreso && porcentajeElement) {
              barraProgreso.style.width = "0%";
              porcentajeElement.textContent = "0%";
            }

            // Asegurarse de que el botón de Frente Grupo esté activo
            const botonesHora =
              deptoContainer.querySelectorAll(".tipo-hora-btn");
            botonesHora.forEach((btn) => {
              if (btn.getAttribute("data-tipo") === "frente-grupo") {
                btn.classList.add("active");
              } else {
                btn.classList.remove("active");
              }
            });
          }
        }
      });
    } else {
      // Si es "todos", actualizar el contador general
      const horasCompGeneral = document.getElementById("horas-comp-general");
      if (horasCompGeneral) {
        horasCompGeneral.innerHTML = `<span style="color: #999;">No se encontraron datos</span>`;

        // Actualizar el porcentaje en el círculo
        const porcentaje =
          totalMaxFrenteGrupo > 0
            ? Math.round((totalFrenteGrupo / totalMaxFrenteGrupo) * 100)
            : 0;
        const porcentajeElement = document.getElementById("porcentaje-general");
        if (porcentajeElement) {
          porcentajeElement.textContent = `${porcentaje}%`;
        }

        // Actualizar el círculo de progreso
        const circuloProgreso = document.querySelector(".circulo-progreso");
        if (circuloProgreso) {
          // Aseguramos que el centro permanece blanco y solo se actualiza el borde
          circuloProgreso.style.backgroundImage = `conic-gradient(#0071b0 ${
            porcentaje * 3.6
          }deg, transparent ${porcentaje * 3.6}deg)`;
        }
      }
    }
  }

  // Llamar a la función para cargar todos los departamentos al inicio
  cargarDatosDepartamentos();

  // Función para mostrar mensaje de error
  function showError(message) {
    tablaBody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: red;">
            ${message}</td></tr>`;
  }

  // Funcionalidad de búsqueda
  const searchInput = document.getElementById("searchInput");

  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();
    const rows = tablaBody.getElementsByTagName("tr");

    Array.from(rows).forEach((row) => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? "" : "none";
    });
  });

  // Event Listeners
  departamentoCards.forEach((card) => {
    card.addEventListener("click", function () {
      const departamento = this.getAttribute("data-departamento"); // Obtener el atributo correctamente
      openModal(departamento); // Pasar el valor al modal
    });
  });

  const botonTodos = document.getElementById("desglose-todos");
  botonTodos.addEventListener("click", function () {
    openModal("todos"); // Llama a la función con 'todos' como parámetro
  });

  span.onclick = closeModal;

  window.onclick = function (event) {
    if (event.target == modal) {
      closeModal();
    }
  };
});

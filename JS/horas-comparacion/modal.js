const modal = document.getElementById("modalPersonal");
const span = document.getElementsByClassName("close")[0];
const modalTitle = document.getElementById("modalTitle");
const tablaBody = document.getElementById("tablaBody");
const searchInput = document.getElementById("searchInput");

// Función para abrir el modal
function openModal(departamento) {
  modal.style.display = "block";
  modalTitle.textContent =
    departamento === "todos"
      ? "Personal de Todos los Departamentos"
      : `Personal del Departamento ${departamento}`;
  fetchPersonalData(departamento);
}

// Función para cerrar el modal
function closeModal() {
  modal.style.display = "none";
}

// Función para obtener los datos del personal
function fetchPersonalData(departamento) {
  // Limpiar la búsqueda al cargar nuevos datos
  searchInput.value = "";

  // Mostrar mensaje de carga
  const colSpan = 11; // Ajustado para incluir la nueva columna de horas totales
  tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">Cargando...</td></tr>`;

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
        tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">No se encontraron datos para mostrar</td></tr>`;
        return;
      }

      // Limpiar la tabla y agregar los datos
      tablaBody.innerHTML = "";

      //Encabezados de la tabla
      const thead = document.querySelector(".tabla-personal thead tr");
      thead.innerHTML = `
                    <th>Código</th>
                    <th>Nombre Completo</th>
                    <th>Departamento</th>
                    <th>Categoría Actual</th>
                    <th>Tipo Plaza</th>
                    <th>Carga Horaria</th>
                    <th>Horas Frente Grupo</th>
                    <th>Horas Definitivas</th>
                    <th>Horas Temporales</th>
                `;
      tablaBody.innerHTML = ""; // Limpiar tabla

      // Obtener el color del departamento
      function getDepartmentColor(departamento) {
        const normalizedDept = departamento
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/[^a-z\s]/g, "")
          .trim();

        // Mapping de departamentos a clases CSS
        const classMapping = {
          administracion: "dept-administracion",
          "programa de aprendizaje de lengua extranjera": "dept-pale",
          pale: "dept-pale",
          auditoria: "dept-auditoria",
          "secretaria administrativa": "dept-auditoria",
          "ciencias sociales": "dept-ciencias-sociales",
          "politicas publicas": "dept-politicas-publicas",
          contabilidad: "dept-contabilidad",
          economia: "dept-economia",
          "estudios regionales": "dept-estudios-regionales",
          finanzas: "dept-finanzas",
          impuestos: "dept-impuestos",
          mercadotecnia: "dept-mercadotecnia",
          "metodos cuantitativos": "dept-metodos-cuantitativos",
          "recursos humanos": "dept-recursos-humanos",
          "sistemas de informacion": "dept-sistemas",
          turismo: "dept-turismo",
        };

        // Buscar coincidencia
        for (let [key, value] of Object.entries(classMapping)) {
          if (normalizedDept.includes(key)) {
            return value;
          }
        }

        return "dept-otros"; // Valor por defecto
      }

      data.forEach((persona) => {
        const row = document.createElement("tr");

        // Función para obtener la clase del departamento
        function getDepartamentoClass(departamento) {
          // Normalizar el texto del departamento
          const normalizedDept = departamento
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z\s]/g, "")
            .trim();

          const mapping = {
            administracion: "administracion",
            "programa de aprendizaje de lengua extranjera": "pale",
            pale: "pale",
            "administracion/programa de aprendizaje de lengua extranjera":
              "pale",
            auditoria: "auditoria",
            "secretaria administrativa": "auditoria",
            "ciencias sociales": "ciencias-sociales",
            "politicas publicas": "politicas-publicas",
            contabilidad: "contabilidad",
            economia: "economia",
            "estudios regionales": "estudios-regionales",
            finanzas: "finanzas",
            impuestos: "impuestos",
            mercadotecnia: "mercadotecnia",
            "metodos cuantitativos": "metodos-cuantitativos",
            "recursos humanos": "recursos-humanos",
            "sistemas de informacion": "sistemas-informacion",
            turismo: "turismo",
          };

          // Buscar coincidencia exacta primero
          for (let [key, value] of Object.entries(mapping)) {
            if (normalizedDept === key) {
              return value;
            }
          }

          // Si no hay coincidencia exacta, buscar coincidencia parcial
          for (let [key, value] of Object.entries(mapping)) {
            // Para PALE, buscar coincidencias específicas
            if (
              value === "pale" &&
              (normalizedDept.includes("pale") ||
                normalizedDept.includes("programa de aprendizaje") ||
                normalizedDept.includes("lengua extranjera"))
            ) {
              return "pale";
            }
            if (normalizedDept.includes(key)) {
              return value;
            }
          }

          return "default";
        }

        function formatTooltipContent(departmentData) {
          if (!departmentData || departmentData.trim() === "") {
            return "No hay información de departamentos disponible";
          }

          // Split by newlines and format each line
          const departments = departmentData
            .split("\n")
            .filter((line) => line.trim());
          return departments.join("<br>");
        }

        // Función para formatear las horas por departamento
        function formatearHorasDepartamento(horasString, tipoHoras) {
          if (!horasString || horasString.trim() === "") {
            return "";
          }

          let formattedHoras = "";
          let horasArray = horasString.split("\n");

          for (let i = 0; i < horasArray.length; i++) {
            let linea = horasArray[i].trim();
            if (linea === "") continue; // Saltar líneas vacías

            // Dividir por el primer ':' solamente
            const [dept, horas] = linea
              .split(/:(.+)/)
              .map((s) => s?.trim())
              .filter(Boolean);
            if (!dept || !horas) continue; // Saltar si falta departamento u horas

            const [horasActual, horasRequeridas] = horas
              .split("/")
              .map((h) => parseInt(h.trim()));

            // Si las horas son 0/0, no mostrar la burbuja
            if (horasActual === 0 && horasRequeridas === 0) {
              continue;
            }

            const horasClass = getHorasClass(
              horasActual,
              tipoHoras === "definitivas"
                ? parseInt(persona.Horas_definitivas)
                : parseInt(persona.Horas_frente_grupo)
            );

            formattedHoras += `
                            <div class="departamento-tag tag-${getDepartamentoClass(
                              dept
                            )} ${horasClass}" style="position: relative; display: inline-block; max-width: 100%;">
                                ${dept}: ${horas}
                            </div>
                        `;
          }

          return formattedHoras;
        }

        // Procesar horas frente a grupo
        const horasCargoActual = persona.suma_cargo_plaza || 0;
        const horasFrenteRequeridas = persona.Horas_frente_grupo || 0;
        const claseFrenteGrupo = getHorasClass(
          horasCargoActual,
          horasFrenteRequeridas
        );

        // Procesar horas definitivas
        const horasDefActual = persona.suma_horas_definitivas || 0;
        const horasDefRequeridas = persona.Horas_definitivas || 0;
        const claseDefinitivas = getHorasClass(
          horasDefActual,
          horasDefRequeridas
        );

        // Procesar horas temporales
        const horasTemporales = persona.suma_horas_temporales || 0;

        // Determinar la clase del departamento para asignar el color a horas temporales
        const deptClass = getDepartmentColor(persona.Departamento || "otros");

        const horasFrenteGrupoHTML = `
                    <div class="tooltip">
                        <span class="${claseFrenteGrupo}">${horasCargoActual}/${horasFrenteRequeridas}</span>
                        <div class="tooltiptext">${
                          persona.horas_cargo_por_departamento
                            ? persona.horas_cargo_por_departamento
                                .replace(/\n/g, "<br>")
                                .replace(/<br>/g, "<br>")
                            : ""
                        }
                        </div>
                    </div>
                `;

        const horasDefinitivasHTML = `
                    <div class="tooltip">
                        <span class="${claseDefinitivas}">${horasDefActual}/${horasDefRequeridas}</span>
                        <div class="tooltiptext">${
                          persona.horas_definitivas_por_departamento
                            ? persona.horas_definitivas_por_departamento
                                .replace(/\n/g, "<br>")
                                .replace(/<br>/g, "<br>")
                            : ""
                        }
                        </div>
                    </div>
                `;

        // Horas temporales con el color del departamento
        const horasTemporalesHTML = `
                    <div class="tooltip" style="text-align: center; width: 100%;">
                        <span class="${deptClass}" style="display: inline-block;">${horasTemporales}</span>
                        <div class="tooltiptext">${
                          persona.horas_temporales_por_departamento || ""
                        }</div>
                    </div>
                `;

        // Generar el contenido del tooltip para horas totales
        function generarDesgloseTotalHoras(persona) {
          // Combinar todas las horas por departamento
          const departamentos = new Map();

          // Procesar horas de cargo
          if (persona.horas_cargo_por_departamento) {
            persona.horas_cargo_por_departamento
              .split("\n")
              .forEach((linea) => {
                if (linea.trim() === "") return;
                const [dept, horas] = linea
                  .split(/:(.+)/)
                  .map((s) => s?.trim())
                  .filter(Boolean);
                if (!dept || !horas) return;

                const horasActual = parseInt(horas.split("/")[0]);
                if (departamentos.has(dept)) {
                  departamentos.set(
                    dept,
                    departamentos.get(dept) + horasActual
                  );
                } else {
                  departamentos.set(dept, horasActual);
                }
              });
          }

          // Procesar horas definitivas
          if (persona.horas_definitivas_por_departamento) {
            persona.horas_definitivas_por_departamento
              .split("\n")
              .forEach((linea) => {
                if (linea.trim() === "") return;
                const [dept, horas] = linea
                  .split(/:(.+)/)
                  .map((s) => s?.trim())
                  .filter(Boolean);
                if (!dept || !horas) return;

                const horasActual = parseInt(horas.split("/")[0]);
                if (departamentos.has(dept)) {
                  departamentos.set(
                    dept,
                    departamentos.get(dept) + horasActual
                  );
                } else {
                  departamentos.set(dept, horasActual);
                }
              });
          }

          // Procesar horas temporales
          if (persona.horas_temporales_por_departamento) {
            persona.horas_temporales_por_departamento
              .split("\n")
              .forEach((linea) => {
                if (linea.trim() === "") return;
                const [dept, horas] = linea
                  .split(/:(.+)/)
                  .map((s) => s?.trim())
                  .filter(Boolean);
                if (!dept || !horas) return;

                const horasActual = parseInt(horas);
                if (departamentos.has(dept)) {
                  departamentos.set(
                    dept,
                    departamentos.get(dept) + horasActual
                  );
                } else {
                  departamentos.set(dept, horasActual);
                }
              });
          }

          // Generar el texto del tooltip
          let tooltipText = "";
          departamentos.forEach((horas, dept) => {
            if (horas > 0) {
              tooltipText += `${dept}: ${horas}\n`;
            }
          });

          return tooltipText || "No hay desglose disponible";
        }

        const tooltipDesgloseTotalHoras = generarDesgloseTotalHoras(persona);

        const tdContent = `
                    <td>${persona.Codigo || ""}</td>
                    <td>${persona.Nombre_completo || ""}</td>
                    <td>${persona.Departamento || ""}</td>
                    <td>${persona.Categoria_actual || ""}</td>
                    <td>${persona.Tipo_plaza || ""}</td>
                    <td>${persona.Carga_horaria || ""}</td>
                    <td>${horasFrenteGrupoHTML}</td>
                    <td>${horasDefinitivasHTML}</td>
                    <td>${horasTemporalesHTML}</td>
                `;

        row.innerHTML = tdContent;
        tablaBody.appendChild(row);
      });

      let totalFrenteGrupo = 0;
      let totalMaxFrenteGrupo = 0;

      // Calcular totales de horas frente grupo
      data.forEach((persona) => {
        const horasCargoActual = parseInt(persona.suma_cargo_plaza) || 0;
        const horasFrenteRequeridas = parseInt(persona.Horas_frente_grupo) || 0;

        totalFrenteGrupo += horasCargoActual;
        totalMaxFrenteGrupo += horasFrenteRequeridas;
      });

      // Actualizar el valor en la tarjeta del departamento correspondiente
      if (departamento !== "todos") {
        // Encontrar el elemento para el departamento actual
        const deptoElements = document.querySelectorAll(
          ".desglose-button-dpto"
        );
        deptoElements.forEach((btn) => {
          if (btn.getAttribute("data-departamento") === departamento) {
            // Encontrar el elemento que contiene "5,117 / 10,234" en el contenedor padre
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
        const horasCompGeneral = document.getElementById("horas-comp-general");
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

          // Actualizar el círculo de progreso correctamente (vacío)
          const circuloProgreso = document.querySelector(".circulo-progreso");
          if (circuloProgreso) {
            circuloProgreso.style.backgroundImage =
              "conic-gradient(transparent 360deg, transparent 360deg)";
          }

          // Actualizar el círculo de progreso
          document.querySelector(
            ".circulo-progreso"
          ).style.background = `conic-gradient(#0071b0 ${
            porcentaje * 3.6
          }deg, #f0f0f0 ${porcentaje * 3.6}deg)`;
        }
      }

      agregarFilaTotales();
    })
    .catch((error) => {
      console.error("Error:", error);
      tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center; color: red;">
            ${error.message || "Error al cargar los datos"}</td></tr>`;
    });
}

// Función para determinar la clase de las horas
function getHorasClass(actual, requerido) {
  actual = parseInt(actual) || 0;
  requerido = parseInt(requerido) || 0;

  if (actual === 0 && requerido === 0) return "horas-cero";
  if (actual < requerido) return "horas-faltantes";
  if (actual === requerido) return "horas-correctas";
  return "horas-excedidas";
}

// Función para calcular y mostrar los totales
function agregarFilaTotales() {
  const tablaBody = document.getElementById("tablaBody");
  const thead = document.querySelector(".tabla-personal thead");

  // Eliminar la fila de totales existente si hay una
  const filaTotalesExistente = document.querySelector(".fila-totales");
  if (filaTotalesExistente) {
    filaTotalesExistente.remove();
  }

  // Crear una nueva fila para los totales
  const filaTotales = document.createElement("tr");
  filaTotales.className = "fila-totales";

  // Calcular totales de las columnas numéricas
  let totalFrenteGrupo = 0;
  let totalMaxFrenteGrupo = 0;
  let totalDefinitivas = 0;
  let totalMaxDefinitivas = 0;
  let totalTemporales = 0;

  // Obtener todas las filas de datos (excluyendo la fila de totales)
  const filas = Array.from(tablaBody.querySelectorAll("tr")).filter(
    (row) => !row.classList.contains("fila-totales")
  );

  filas.forEach((fila) => {
    // Obtener los valores de las celdas relevantes
    const celdaFrenteGrupo = fila.querySelector("td:nth-child(7)"); // Horas Frente Grupo
    const celdaDefinitivas = fila.querySelector("td:nth-child(8)"); // Horas Definitivas
    const celdaTemporales = fila.querySelector("td:nth-child(9)"); // Horas Temporales

    // Extraer los valores numéricos
    if (celdaFrenteGrupo) {
      const [actual, maximo] = celdaFrenteGrupo.textContent
        .split("/")
        .map((v) => parseFloat(v) || 0);
      totalFrenteGrupo += actual;
      totalMaxFrenteGrupo += maximo;
    }

    if (celdaDefinitivas) {
      const [actual, maximo] = celdaDefinitivas.textContent
        .split("/")
        .map((v) => parseFloat(v) || 0);
      totalDefinitivas += actual;
      totalMaxDefinitivas += maximo;
    }

    if (celdaTemporales) {
      const valorTemporales = parseFloat(celdaTemporales.textContent) || 0;
      totalTemporales += valorTemporales;
    }
  });

  // Construir la fila de totales con los totales y máximos redondeados
  filaTotales.innerHTML = `
                        <td colspan="6" style="text-align: right; font-weight: bold;">Totales:</td>
                        <td style="text-align: center; font-weight: bold;">${Math.round(
                          totalFrenteGrupo
                        )}/${Math.round(totalMaxFrenteGrupo)}</td>
                        <td style="text-align: center; font-weight: bold;">${Math.round(
                          totalDefinitivas
                        )}/${Math.round(totalMaxDefinitivas)}</td>
                        <td style="text-align: center; font-weight: bold;">${Math.round(
                          totalTemporales
                        )}</td>
                    `;

  // Insertar la fila de totales después del encabezado
  thead.parentNode.insertBefore(filaTotales, thead.nextSibling);
}

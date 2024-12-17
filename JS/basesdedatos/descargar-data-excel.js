function mostrarPopupColumnas() {
  var popup = document.getElementById("popup-columnas");
  var opcionesColumnas = document.getElementById("opciones-columnas");
  opcionesColumnas.innerHTML = ""; // Limpiar opciones existentes

  // Añadir botón para seleccionar/deseleccionar todas
  var selectAllDiv = document.createElement("div");
  selectAllDiv.className = "select-all-div";
  var selectAllCheckbox = document.createElement("input");
  selectAllCheckbox.type = "checkbox";
  selectAllCheckbox.id = "select-all";
  var selectAllLabel = document.createElement("label");
  selectAllLabel.htmlFor = "select-all";
  selectAllLabel.className = "select-all-div-label";
  selectAllLabel.appendChild(
    document.createTextNode("Seleccionar/Deseleccionar Todas")
  );
  selectAllDiv.appendChild(selectAllCheckbox);
  selectAllDiv.appendChild(selectAllLabel);
  opcionesColumnas.appendChild(selectAllDiv);

  // Obtener los encabezados de la tabla
  var headers_materia_1 = Array.from(document.querySelectorAll("#tabla-datos th")).slice(
    2, 11
  );  // Ignorar la columna de checkbox y ID

  var headers_materia_2 = Array.from(document.querySelectorAll("#tabla-datos th")).slice(
    12, 13
  );

  var headers_materia_3 = Array.from(document.querySelectorAll("#tabla-datos th")).slice(
    24, 44
  );

  const headers_materia = headers_materia_1.concat(headers_materia_2, headers_materia_3);

  // Crear checkbox para cada columna
  var columnDiv1 = document.createElement("div");
  columnDiv1.className = "columns-container-js";
  var columnDiv2 = document.createElement("div");
  columnDiv2.className = "label-materia";

  // Crea una etiqueta de diferenciación
  var materia = document.createElement("label");
  materia.appendChild(
    document.createTextNode("Materia")
  );
  materia.className = "encabezado-js";
  columnDiv2.appendChild(materia);
  columnDiv1.appendChild(columnDiv2);

  // Añadir botón para seleccionar/deseleccionar todas
  var selectDiv = document.createElement("div");
  selectDiv.className = "select-all-materia";
  var selectCheckboxMateria = document.createElement("input");
  selectCheckboxMateria.type = "checkbox";
  selectCheckboxMateria.id = "select-all";
  var selectLabel = document.createElement("label");
  selectLabel.htmlFor = "select-all";
  selectLabel.appendChild(
    document.createTextNode("Seleccionar/Deseleccionar Todas - Materia")
  );
  selectDiv.appendChild(selectCheckboxMateria);
  selectDiv.appendChild(selectLabel);
  columnDiv1.appendChild(selectDiv);
  
  var columnDiv = document.createElement("div");
  columnDiv.className = "columns-container-materia";
  headers_materia.forEach(function (header, index) {
    var checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.id = "col-" + header.textContent.toLowerCase().replace(/ /g, "-");
    checkbox.name = "columnas[]";
    checkbox.value = header.textContent;

    var label = document.createElement("label");
    label.htmlFor = checkbox.id;
    label.appendChild(document.createTextNode(header.textContent));

    var div = document.createElement("div");
    div.className = "Check-js";
    div.appendChild(checkbox);
    div.appendChild(label);

    columnDiv.appendChild(div);
    columnDiv1.appendChild(columnDiv);
  });
  selectAllDiv.appendChild(columnDiv1);

  // Evento para seleccionar/deseleccionar todas
  selectAllCheckbox.addEventListener("change", function () {
    var checkboxes = document.querySelectorAll(
      '.columns-container-js input[type="checkbox"]'
    );
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = selectAllCheckbox.checked;
    });
  });

  // Evento para seleccionar/deseleccionar todas - Materia
  selectCheckboxMateria.addEventListener("change", function () {
    var checkboxes = document.querySelectorAll(
      '.columns-container-materia input[type="checkbox"]'
    );
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = selectCheckboxMateria.checked;
    });
  });

  // Obtener los encabezados de la tabla
  var headers_profesor_1 = Array.from(document.querySelectorAll("#tabla-datos th")).slice(
    13, 24
  );

  var headers_profesor_2 = Array.from(document.querySelectorAll("#tabla-datos th")).slice(
    11, 12
  );

  const headers_profesor = headers_profesor_1.concat(headers_profesor_2);

  // Crear checkbox para cada columna
  var columnDiv1 = document.createElement("div");
  columnDiv1.className = "columns-container-js";

  var columnDiv2 = document.createElement("div");
  columnDiv2.className = "label-profesorado";

  // Crea una etiqueta de diferenciación
  var profesor = document.createElement("label");
  profesor.appendChild(
    document.createTextNode("Profesorado")
  );
  profesor.className = "encabezado-js";

  columnDiv2.appendChild(profesor);
  columnDiv1.appendChild(columnDiv2);

  var columnDiv = document.createElement("div");
  columnDiv.className = "columns-container-profesor";

  // Añadir botón para seleccionar/deseleccionar todas
  var selectDiv2 = document.createElement("div");
  selectDiv2.className = "select-all-profesor";
  var selectCheckboxProfesor = document.createElement("input");
  selectCheckboxProfesor.type = "checkbox";
  selectCheckboxProfesor.id = "select-all";
  var selectLabel = document.createElement("label");
  selectLabel.htmlFor = "select-all";
  selectLabel.appendChild(
    document.createTextNode("Seleccionar/Deseleccionar Todas - Profesorado")
  );
  selectDiv2.appendChild(selectCheckboxProfesor);
  selectDiv2.appendChild(selectLabel);
  columnDiv1.appendChild(selectDiv2);
  
  headers_profesor.forEach(function (header, index) {
    var checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.id = "col-" + header.textContent.toLowerCase().replace(/ /g, "-");
    checkbox.name = "columnas[]";
    checkbox.value = header.textContent;

    var label = document.createElement("label");
    label.htmlFor = checkbox.id;
    label.appendChild(document.createTextNode(header.textContent));

    var div = document.createElement("div");
    div.className = "Check-js";
    div.appendChild(checkbox);
    div.appendChild(label);

    columnDiv.appendChild(div);
    columnDiv1.appendChild(columnDiv);
  });
  selectAllDiv.appendChild(columnDiv1);

  // Evento para seleccionar/deseleccionar todas
  selectAllCheckbox.addEventListener("change", function () {
    var checkboxes = document.querySelectorAll(
      '.columns-container-js input[type="checkbox"]'
    );
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = selectAllCheckbox.checked;
    });
  });

  // Evento para seleccionar/deseleccionar todas
  selectCheckboxProfesor.addEventListener("change", function () {
    var checkboxes = document.querySelectorAll(
      '.columns-container-profesor input[type="checkbox"]'
    );
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = selectCheckboxProfesor.checked;
    });
  });

  // Agregar botón de cierre
  var closeButton = document.createElement("button");
  closeButton.className = "close-btn";
  closeButton.textContent = "✕";
  closeButton.addEventListener("click", cerrarPopupColumnas);
  popup.appendChild(closeButton);

  popup.style.display = "block";
  document.body.classList.add("popup-active");
}

function cerrarPopupColumnas() {
  document.getElementById("popup-columnas").style.display = "none";
  document.body.classList.remove("popup-active");
}

function descargarExcelSeleccionado() {
  var columnasSeleccionadas = Array.from(
    document.querySelectorAll(
      '#opciones-columnas .columns-container-js input[type="checkbox"]:checked'
    )
  ).map((checkbox) => checkbox.value);

  if (columnasSeleccionadas.length === 0) {
    alert("Por favor, selecciona al menos una columna.");
    return;
  }

  var departamento_id = document.getElementById("departamento_id").value;
  var url =
    "./functions/basesdedatos/descargar-data-excel.php?departamento_id=" +
    departamento_id +
    "&columnas=" +
    JSON.stringify(columnasSeleccionadas);
  window.location.href = url;

  cerrarPopupColumnas();
}

function descargarExcelCotejado() {
  var departamento_id = document.getElementById("departamento_id").value;
  var url =
    "./functions/basesdedatos/descargar-cotejo.php?departamento_id=" +
    departamento_id;
  window.location.href = url;
  cerrarPopupColumnas();
}
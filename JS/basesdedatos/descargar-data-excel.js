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
  selectAllLabel.appendChild(
    document.createTextNode("Seleccionar/Deseleccionar Todas")
  );
  selectAllDiv.appendChild(selectAllCheckbox);
  selectAllDiv.appendChild(selectAllLabel);
  opcionesColumnas.appendChild(selectAllDiv);

  // Obtener los encabezados de la tabla
  var headers = Array.from(document.querySelectorAll("#tabla-datos th")).slice(
    2
  ); // Ignorar la columna de checkbox y ID

  // Crear checkbox para cada columna
  var columnDiv = document.createElement("div");
  columnDiv.className = "columns-container";
  headers.forEach(function (header, index) {
    var checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.id = "col-" + header.textContent.toLowerCase().replace(/ /g, "-");
    checkbox.name = "columnas[]";
    checkbox.value = header.textContent;

    var label = document.createElement("label");
    label.htmlFor = checkbox.id;
    label.appendChild(document.createTextNode(header.textContent));

    var div = document.createElement("div");
    div.appendChild(checkbox);
    div.appendChild(label);

    columnDiv.appendChild(div);
  });
  opcionesColumnas.appendChild(columnDiv);

  // Evento para seleccionar/deseleccionar todas
  selectAllCheckbox.addEventListener("change", function () {
    var checkboxes = document.querySelectorAll(
      '.columns-container input[type="checkbox"]'
    );
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = selectAllCheckbox.checked;
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
      '#opciones-columnas .columns-container input[type="checkbox"]:checked'
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
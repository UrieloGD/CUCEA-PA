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
  headers.forEach(function (header) {
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

  // Crear un formulario temporal
  var form = document.createElement("form");
  form.method = "POST";
  form.action = "./functions/basesdedatos/descargar-data-excel.php";

  // Añadir el Departamento_ID
  var inputDepartamento = document.createElement("input");
  inputDepartamento.type = "hidden";
  inputDepartamento.name = "Departamento_ID";
  inputDepartamento.value = departamento_id;
  form.appendChild(inputDepartamento);

  // Añadir las columnas seleccionadas
  var inputColumnas = document.createElement("input");
  inputColumnas.type = "hidden";
  inputColumnas.name = "columnas";
  inputColumnas.value = JSON.stringify(columnasSeleccionadas);
  form.appendChild(inputColumnas);

  // Añadir el formulario al documento y enviarlo
  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);

  cerrarPopupColumnas();
}

function descargarExcelCotejado() {
  // Obtener el ID del departamento
  var departamento_id = document.getElementById("departamento_id").value;

  if (!departamento_id) {
    alert("Error: No se pudo obtener el ID del departamento");
    return;
  }

  // Crear un formulario temporal
  var form = document.createElement("form");
  form.method = "POST";
  form.action = "./functions/basesdedatos/descargar-cotejo.php";

  // Añadir el Departamento_ID como campo oculto
  var inputDepartamento = document.createElement("input");
  inputDepartamento.type = "hidden";
  inputDepartamento.name = "Departamento_ID";
  inputDepartamento.value = departamento_id;
  form.appendChild(inputDepartamento);

  // Añadir el formulario al documento y enviarlo
  document.body.appendChild(form);

  // Manejar errores
  try {
    form.submit();
  } catch (error) {
    console.error("Error al enviar el formulario:", error);
    alert("Ocurrió un error al intentar descargar el archivo");
  } finally {
    // Limpiar el formulario temporal
    setTimeout(() => {
      document.body.removeChild(form);
    }, 1000);
  }

  // Cerrar el popup
  cerrarPopupColumnas();
}

function subirArchivo(id) {
  Swal.fire({
    title: "Selecciona un archivo",
    input: "file",
    inputAttributes: {
      accept: "*/*",
      "aria-label": "Selecciona un archivo",
    },
    showCancelButton: true,
    confirmButtonText: "Guardar",
    cancelButtonText: "Cancelar",
    preConfirm: (file) => {
      if (file) {
        const inputFileElement = document.querySelector(`#input-file-${id}`);

        if (!inputFileElement) {
          throw new Error(
            `No se encontró el elemento de entrada de archivo para el departamento ${id}`
          );
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        inputFileElement.files = dt.files;

        actualizarNombreArchivo(inputFileElement, id);
        actualizarFechaSubida(id);
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      const inputFileElement = document.querySelector(`#input-file-${id}`);
      if (!inputFileElement || !inputFileElement.files[0]) {
        Swal.fire("Error", "No se ha seleccionado un archivo.", "error");
        return;
      }

      // Mostrar pantalla de carga
      Swal.fire({
        title: "Subiendo archivo...",
        html: "Por favor espere mientras se sube el archivo.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const formData = new FormData();
      formData.append("file", inputFileElement.files[0]);
      formData.append("Departamento_ID", id);

      const nombreArchivoElement = document.querySelector(`#nombre-archivo-${id}`);
      const fechaSubidaElement = document.querySelector(`#fecha-subida-${id}`);

      if (nombreArchivoElement) {
        formData.append("Nombre_Archivo_Dep", nombreArchivoElement.textContent);
      }
      if (fechaSubidaElement) {
        formData.append("Fecha_Subida_Dep", fechaSubidaElement.textContent);
      }

      fetch("./functions/admin-plantilla/upload-plantilla.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.text())
        .then((data) => {
          console.log("Respuesta del servidor:", data);
          if (data.includes("success")) {
            Swal.fire({
              title: "¡Éxito!",
              text: "El archivo se ha subido correctamente.",
              icon: "success",
              showConfirmButton: true
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              title: "Error",
              text: "Ocurrió un error al subir el archivo. Por favor, inténtalo de nuevo.",
              icon: "error"
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            title: "Error",
            text: "Ocurrió un error inesperado. Por favor, inténtalo de nuevo.",
            icon: "error"
          });
        });
    }
  });
}

function actualizarNombreArchivo(input, id) {
  // Check if input and input.files exist
  if (input && input.files && input.files.length > 0) {
    var nombreArchivo = input.files[0].name;

    // Use optional chaining and null checks
    const nombreArchivoElement = document.getElementById(
      `nombre-archivo-${id}`
    );
    const nombreArchivoDep = document.getElementById(
      `nombre_archivo_dep-${id}`
    );

    if (nombreArchivoElement) {
      nombreArchivoElement.innerText = nombreArchivo;
    }

    if (nombreArchivoDep) {
      nombreArchivoDep.value = nombreArchivo;
    }

    console.log(
      `Nombre de archivo actualizado para departamento ${id}: ${nombreArchivo}`
    );
  } else {
    const nombreArchivoElement = document.getElementById(
      `nombre-archivo-${id}`
    );
    if (nombreArchivoElement) {
      nombreArchivoElement.innerText = "No se ha subido un archivo";
    }
    console.log(`No se ha subido un archivo para departamento ${id}`);
  }
}

function actualizarFechaSubida(id) {
  var fechaFormateada = obtenerFechaHoraActual();

  // Add null checks with console warnings
  const fechaSubidaElement = document.getElementById(`fecha-subida-${id}`);
  const fechaSubidaDepElement = document.getElementById(
    `Fecha_Subida_Dep-${id}`
  );

  if (fechaSubidaElement) {
    fechaSubidaElement.innerText = fechaFormateada;
  } else {
    console.warn(`Elemento fecha-subida-${id} no encontrado`);
  }

  if (fechaSubidaDepElement) {
    fechaSubidaDepElement.value = fechaFormateada;
  } else {
    console.warn(`Elemento Fecha_Subida_Dep-${id} no encontrado`);
  }

  console.log(
    `Fecha de subida actualizada para departamento ${id}: ${fechaFormateada}`
  );
}

function obtenerFechaHoraActual() {
  var fecha = new Date();
  var dia = fecha.getDate().toString().padStart(2, "0");
  var mes = (fecha.getMonth() + 1).toString().padStart(2, "0");
  var año = fecha.getFullYear();
  var horas = fecha.getHours().toString().padStart(2, "0");
  var minutos = fecha.getMinutes().toString().padStart(2, "0");
  return `${dia}/${mes}/${año} ${horas}:${minutos}`;
}

function handleFileChange(event, id) {
  var inputFileElement = event.target;
  actualizarNombreArchivo(inputFileElement, id);
  actualizarFechaSubida(id);
  console.log(`Cambio de archivo manejado para departamento ${id}`);
  subirArchivo(id);
}

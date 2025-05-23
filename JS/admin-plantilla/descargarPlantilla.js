function descargarPlantilla(id) {
  const nombreArchivo = document.getElementById(
    `nombre-archivo-${id}`
  ).innerText;

  if (nombreArchivo === "No hay archivo asignado") {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No hay una plantilla asignada para descargar.",
      customClass: {
        confirmButton: "OK-boton",
      }
    });
    return;
  }

  $.ajax({
    url: "./functions/admin-plantilla/descargar-plantilla.php",
    method: "GET",
    data: { departamento_id: id },
    xhrFields: {
      responseType: "blob",
    },
    success: function (data, status, xhr) {
      // Verificar si la respuesta es JSON (indica un error)
      const contentType = xhr.getResponseHeader("Content-Type");
      if (contentType && contentType.indexOf("application/json") !== -1) {
        // Si la respuesta es JSON, convertirla a objeto y mostrar el error
        const reader = new FileReader();
        reader.onload = function () {
          const errorResponse = JSON.parse(reader.result);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: errorResponse.message,
            customClass: {
              confirmButton: "OK-boton",
            }
          });
        };
        reader.readAsText(data);
      } else {
        // Si la respuesta es un archivo, proceder con la descarga
        let filename = nombreArchivo; // Usar el nombre del archivo mostrado en la tabla
        
        // Intentar obtener el nombre del archivo del header Content-Disposition
        const contentDisposition = xhr.getResponseHeader("Content-Disposition");
        if (contentDisposition) {
          const matches = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
          if (matches && matches[1]) {
            filename = matches[1].replace(/['"]/g, ''); // Remover comillas
            filename = filename.replace(/^_+|_+$/g, ''); // Remover guiones bajos al inicio y final
          }
        }
        
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(data);
        link.download = filename;
        link.click();
        
        // Limpiar el objeto URL después de un breve delay
        setTimeout(() => {
          window.URL.revokeObjectURL(link.href);
        }, 100);
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un problema al intentar descargar el archivo. Por favor, inténtalo de nuevo más tarde.",
        customClass: {
          confirmButton: "OK-boton",
        }
      });
    },
  });
}
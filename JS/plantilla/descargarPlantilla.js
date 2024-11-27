function descargarArchivo(departamentoId) {
  // Validación inicial
  if (!departamentoId) {
    Swal.fire({
      icon: "warning",
      title: "Departamento no identificado",
      text: "No se puede determinar el departamento. Por favor, inicie sesión nuevamente.",
      confirmButtonText: "Entendido",
    });
    return;
  }

  // Iniciar descarga
  Swal.fire({
    title: "Descargando plantilla",
    text: "Por favor, espere...",
    didOpen: () => {
      Swal.showLoading();

      // Realizar la solicitud de descarga
      fetch(
        `./functions/plantilla/descargarPlantilla.php?departamento_id=${departamentoId}`
      )
        .then((response) => {
          if (!response.ok) {
            // Manejar errores de respuesta
            return response.json().then((errorData) => {
              throw new Error(errorData.message || "Error desconocido");
            });
          }
          return response.blob();
        })
        .then((blob) => {
          // Crear enlace de descarga
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement("a");
          link.href = url;
          link.download = "plantilla.xlsx";
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          window.URL.revokeObjectURL(url);

          // Cerrar el mensaje de carga
          Swal.close();

          // Mostrar mensaje de éxito
          Swal.fire({
            icon: "success",
            title: "Descarga completada",
            text: "La plantilla se ha descargado correctamente.",
            timer: 2000,
            timerProgressBar: true,
          });
        })
        .catch((error) => {
          // Manejar cualquier error de descarga
          Swal.fire({
            icon: "error",
            title: "Error en la descarga",
            text:
              error.message ||
              "No se pudo descargar la plantilla. Intente nuevamente.",
            confirmButtonText: "Cerrar",
          });
        });
    },
  });
}

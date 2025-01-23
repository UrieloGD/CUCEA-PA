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

  // Consultar si existe plantilla para este departamento
  $.ajax({
    url: "./functions/plantilla/verificarPlantilla.php",
    type: "GET",
    data: { departamento_id: departamentoId },
    dataType: "json",
    success: function (response) {
      if (response.existe) {
        // Si existe plantilla, proceder con la descarga
        window.location.href = `./functions/plantilla/descargarPlantilla.php?departamento_id=${departamentoId}`;
      } else {
        // Si no existe plantilla, mostrar alerta
        Swal.fire({
          icon: "warning",
          title: "Plantilla no disponible",
          text: "No se ha asignado una plantilla para este departamento.",
          confirmButtonText: "Entendido",
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo verificar la disponibilidad de la plantilla.",
        confirmButtonText: "Cerrar",
      });
    },
  });
}

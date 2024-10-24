function deleteEvent(eventId) {
  Swal.fire({
    title: "Estás a punto de eliminar el evento",
    text: "¿Estás seguro?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Confirmar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loading mientras se procesa
      Swal.fire({
        title: "Procesando...",
        text: "Por favor espere",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      fetch("./functions/admin-eventos/eliminarEvento.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: eventId,
        }),
      })
        .then((response) => {
          // Primero vamos a ver el texto de la respuesta
          return response.text().then((text) => {
            console.log("Respuesta raw del servidor:", text); // Esto nos mostrará el error de PHP si existe

            try {
              // Intentamos parsear como JSON
              const data = JSON.parse(text);
              return data;
            } catch (e) {
              // Si no es JSON válido, lanzamos un error con el texto de la respuesta
              throw new Error(`Respuesta no válida del servidor: ${text}`);
            }
          });
        })
        .then((data) => {
          console.log("Datos procesados:", data);
          if (data.success) {
            Swal.fire(
              "Eliminado",
              "El evento ha sido eliminado.",
              "success"
            ).then(() => {
              window.location.reload();
            });
          } else {
            throw new Error(data.message || "Error desconocido en el servidor");
          }
        })
        .catch((error) => {
          console.error("Error completo:", error);
          Swal.fire(
            "Error!",
            "Error al eliminar el evento. Detalles: " + error.message,
            "error"
          );
        });
    }
  });
}

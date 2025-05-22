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
    customClass: {
      confirmButton: "aceptar-eliminacion",
      cancelButton: "cancelar-boton",
    }
    
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loading con configuración mejorada
      const loadingAlert = Swal.fire({
        title: "Eliminando evento...",
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
        body: JSON.stringify({ id: eventId }),
      })
        .then((response) => {
          return response.text().then((text) => {
            try {
              return JSON.parse(text);
            } catch (e) {
              loadingAlert.close(); // Cerrar loading antes de mostrar error
              throw new Error(`Respuesta no válida: ${text}`);
            }
          });
        })
        .then((data) => {
          loadingAlert.close(); // Cerrar loading al recibir respuesta
          if (data.success) {
            Swal.fire({
              title: "¡Eliminado!",
              text: "El evento ha sido eliminado correctamente",
              icon: "success",
              customClass: {
                confirmButton: "OK-boton",
              }
            }).then(() => {
              window.location.reload();
            });
          } else {
            throw new Error(data.message || "Error en el servidor");
          }
        })
        .catch((error) => {
          loadingAlert.close(); // Asegurar cierre del loading en errores
          console.error("Error completo:", error);
          Swal.fire({
            title: "Error",
            text: `No se pudo eliminar el evento: ${error.message}`,
            icon: "error",
            customClass: {
              confirmButton: "OK-boton",
            }
          });
        });
    }
  });
}

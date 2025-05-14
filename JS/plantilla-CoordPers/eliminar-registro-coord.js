function eliminarRegistrosSeleccionados() {
  const checkboxes = document.querySelectorAll(
    'input[name="registros_seleccionados[]"]:checked'
  );
  const ids = Array.from(checkboxes).map((checkbox) => checkbox.value);

  // Eliminar toda la base de datos
  if (ids.length === 0) {
    Swal.fire({
      title: "¿Está seguro?",
      text: "¿Está seguro que deseas borrar toda la base de datos?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, borrar todo",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
      customClass: {
        confirmButton: "swal2-confirm swal2-styled swal2-danger",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        // Mostrar SweetAlert de carga
        Swal.fire({
          title: "Eliminando base de datos",
          html: "Este proceso puede tardar varios segundos, por favor espere...",
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        // Solicitud AJAX
        fetch(
          "./functions/coord-personal-plantilla/eliminar-registros-coord.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "truncate=1",
          }
        )
          .then((response) => {
            if (!response.ok) {
              throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.text();
          })
          .then(() => {
            Swal.fire({
              title: "¡Éxito!",
              text: "La base de datos ha sido borrada correctamente.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
            }).then(() => {
              location.reload();
            });
          })
          .catch((error) => {
            console.error("Error:", error);
            Swal.fire({
              title: "Error",
              text: "Ocurrió un error al eliminar los datos. Por favor intenta de nuevo.",
              icon: "error",
            });
          });
      }
    });
    return;
  }

  // Eliminar registros seleccionados
  Swal.fire({
    title: "¿Desea continuar?",
    text: `Se eliminarán ${ids.length} registro(s)`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
    customClass: {
      confirmButton: "swal2-confirm swal2-styled swal2-danger",
    },
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar SweetAlert de carga
      Swal.fire({
        title: "Eliminando registros",
        html: `Procesando la eliminación de registro(s)...`,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      // Solicitud AJAX con fetch
      fetch(
        "./functions/coord-personal-plantilla/eliminar-registros-coord.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `ids=${encodeURIComponent(ids.join(","))}`,
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
          }
          return response.text();
        })
        .then(() => {
          Swal.fire({
            title: "¡Éxito!",
            text: "Los registros se han eliminado correctamente.",
            icon: "success",
            timer: 2000,
            timerProgressBar: true,
          }).then(() => {
            location.reload();
          });
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            title: "Error",
            text: "Ocurrió un error al eliminar los registros. Por favor intenta de nuevo.",
            icon: "error",
          });
        });
    }
  });
}

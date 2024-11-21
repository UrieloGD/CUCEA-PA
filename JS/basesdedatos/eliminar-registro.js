function eliminarRegistrosSeleccionados() {
  const checkboxes = document.querySelectorAll(
    'input[name="registros_seleccionados[]"]:checked'
  );
  const ids = Array.from(checkboxes).map((checkbox) => checkbox.value);
  const departamento_id = document.getElementById("departamento_id").value;

  if (ids.length === 0) {
    // Confirmar eliminación completa
    Swal.fire({
      title: "¿Estás seguro?",
      text: "¿Estás seguro que deseas borrar toda la base de datos?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, borrar todo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        enviarSolicitudEliminacion({
          truncate: "1",
          departamento_id: departamento_id,
        });
      }
    });
    return;
  }

  // Confirmar eliminación de registros seleccionados
  Swal.fire({
    title: "¿Desea continuar?",
    text: `Se eliminarán ${ids.length} registro(s)`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      enviarSolicitudEliminacion({
        ids: ids.join(","),
        departamento_id: departamento_id,
      });
    }
  });
}

function enviarSolicitudEliminacion(datos) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "./functions/basesdedatos/eliminar-registros.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      try {
        const response = JSON.parse(xhr.responseText);

        if (response.error) {
          Swal.fire({
            title: "Error",
            text: response.error,
            icon: "error",
          });
          return;
        }

        if (response.success) {
          Swal.fire({
            title: "¡Éxito!",
            text: response.message,
            icon: "success",
          }).then(() => {
            location.reload();
          });
        }
      } catch (e) {
        Swal.fire({
          title: "Error",
          text: "Ocurrió un error al procesar la respuesta del servidor",
          icon: "error",
        });
      }
    }
  };

  xhr.send(new URLSearchParams(datos).toString());
}

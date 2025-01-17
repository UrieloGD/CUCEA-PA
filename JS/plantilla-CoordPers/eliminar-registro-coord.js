function eliminarRegistrosSeleccionados() {
  var checkboxes = document.querySelectorAll(
    'input[name="registros_seleccionados[]"]:checked'
  );
  var ids = [];

  checkboxes.forEach(function (checkbox) {
    ids.push(checkbox.value);
  });

  if (ids.length === 0) {
    Swal.fire({
      title: "¿Estás seguro?",
      text: "¿Estás seguro que deseas borrar toda la base de datos?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, borrar todo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        var xhr = new XMLHttpRequest();
        xhr.open(
          "POST",
          "./functions/coord-personal-plantilla/eliminar-registros-coord.php",
          true
        );
        xhr.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            Swal.fire({
              title: "¡Éxito!",
              text: "La base de datos ha sido borrada correctamente.",
              icon: "success",
            }).then(() => {
              location.reload();
            });
          }
        };

        xhr.send("truncate=1");
      }
    });
    return;
  }

  Swal.fire({
    title: "¿Desea continuar?",
    text: "Se eliminarán " + ids.length + " registro(s)",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      var xhr = new XMLHttpRequest();
      xhr.open(
        "POST",
        "./functions/coord-personal-plantilla/eliminar-registros-coord.php",
        true
      );
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          Swal.fire({
            title: "¡Éxito!",
            text: "Los registros se han eliminado correctamente.",
            icon: "success",
          }).then(() => {
            location.reload();
          });
        }
      };

      xhr.send("ids=" + encodeURIComponent(ids.join(",")));
    }
  });
}

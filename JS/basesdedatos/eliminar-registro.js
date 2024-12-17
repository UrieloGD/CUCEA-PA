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
      text: "Estás seguro que deseas borrar toda la base de datos?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, borrar todo",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "./functions/basesdedatos/eliminar-registros.php", true);
        xhr.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              Swal.fire({
                title: "¡Éxito!",
                text: "La base de datos ha sido borrada correctamente.",
                icon: "success",
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                title: "Error",
                text: "Hubo un problema al borrar la base de datos.",
                icon: "error"
              });
            }
          }
        };

        var departamento_id = document.getElementById("departamento_id").value;
        xhr.send(
          "departamento_id=" + encodeURIComponent(departamento_id) + 
          "&truncate=1"
        );
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
      xhr.open("POST", "./functions/basesdedatos/eliminar-registros.php", true);
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

      var departamento_id = document.getElementById("departamento_id").value;
      var datos = { departamento_id: departamento_id };
      xhr.send(
        "ids=" +
          encodeURIComponent(ids.join(",")) +
          "&" +
          convertirObjeto(datos)
      );
    }
  });
}

function convertirObjeto(obj) {
  var str = [];
  for (var p in obj) {
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  }
  return str.join("&");
}

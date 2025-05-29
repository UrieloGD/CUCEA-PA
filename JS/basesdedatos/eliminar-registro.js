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
      title: "¿Está seguro?",
      text: "¿Está seguro que desea borrar toda la base de datos?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, borrar todo",
      cancelButtonText: "Cancelar",
      customClass: {
        confirmButton: 'aceptar-eliminarRegistros',
        cancelButton: 'cancelar-eliminarRegistros',
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Mostrar SweetAlert de espera
        Swal.fire({
          title: "Procesando...",
          text: "Eliminando registros, por favor espere.",
          icon: "info",
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        var xhr = new XMLHttpRequest();
        xhr.open(
          "POST",
          "./functions/basesdedatos/eliminar-registros.php",
          true
        );
        xhr.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              // Cerrar SweetAlert de espera y mostrar éxito
              Swal.fire({
                title: "¡Éxito!",
                text: "La base de datos ha sido borrada correctamente.",
                icon: "success",
                timer: 2000,
                timerProgressBar: true,
                customClass: {
                  confirmButton: 'OK-boton',
                }
              }).then(() => {
                location.reload();
              });
            } else {
              // Cerrar SweetAlert de espera y mostrar error
              Swal.fire({
                title: "Error",
                text: "Hubo un problema al borrar la base de datos.",
                icon: "error",
                customClass: {
                  confirmButton: 'OK-boton',
                }
              });
            }
          }
        };

        var departamento_id = document.getElementById("departamento_id").value;
        xhr.send(
          "departamento_id=" +
            encodeURIComponent(departamento_id) +
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
    customClass: {
      confirmButton: 'aceptar-eliminarRegistros',
      cancelButton: 'cancelar-eliminarRegistros',
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar SweetAlert de espera
      Swal.fire({
        title: "Procesando...",
        text: "Eliminando registros, por favor espere.",
        icon: "info",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      var xhr = new XMLHttpRequest();
      xhr.open("POST", "./functions/basesdedatos/eliminar-registros.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          // Cerrar SweetAlert de espera y mostrar éxito
          Swal.fire({
            title: "¡Éxito!",
            text: "Los registros se han eliminado correctamente.",
            icon: "success",
            confirmButtonColor: "#0071B0",
            timer: 2000,
            timerProgressBar: true,
          }).then(() => {
            location.reload();
          });
        } else if (xhr.readyState === XMLHttpRequest.DONE) {
          // Cerrar SweetAlert de espera y mostrar error en caso de fallo
          Swal.fire({
            title: "Error",
            text: "Hubo un problema al eliminar los registros.",
            icon: "error",
            confirmButtonColor: "#0071B0",
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

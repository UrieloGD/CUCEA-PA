document
  .getElementById("formulario-subida")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

    let formData = new FormData();
    let fileInput = document.getElementById("input-file");

    // Verificar si hay archivos seleccionados
    if (fileInput.files.length > 0) {
      let file = fileInput.files[0];

      // Validar el tipo de archivo y el tamaño
      const validExtensions = ["xlsx", "xls"];
      const maxFileSize = 2 * 1024 * 1024; // 2MB
      const fileExtension = file.name.split(".").pop().toLowerCase();

      if (validExtensions.includes(fileExtension) && file.size <= maxFileSize) {
        formData.append("file", file);
      } else {
        Swal.fire({
          icon: "error",
          title: "Archivo no válido",
          text: "Asegúrate de subir solamente archivos con extensión .xls y .xlsx, y que no excedan los 2MB.",
        });
        return;
      }
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Por favor, selecciona un archivo.",
      });
      return;
    }

    // Mostrar indicador de carga
    Swal.fire({
      title: "Subiendo archivo...",
      html: "Por favor, espere. Esto puede tardar varios segundos.",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Crear una solicitud XMLHttpRequest
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "./functions/plantilla/extraccion_dataExcel.php", true);

    xhr.onload = function () {
      Swal.close(); // Cerrar el indicador de carga
      if (xhr.status === 200) {
        try {
          let response = JSON.parse(xhr.responseText);
          if (response.success) {
            Swal.fire({
              icon: "success",
              title: "Éxito",
              text: response.message,
              didClose: () => {
                window.location.reload();
              },
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.message,
            });
          }
        } catch (e) {
          console.error("Error parsing JSON:", xhr.responseText);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un problema al procesar la respuesta del servidor.",
          });
        }
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al cargar el archivo.",
        });
      }
    };

    xhr.onerror = function () {
      Swal.close(); // Cerrar el indicador de carga en caso de error
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un problema al intentar cargar el archivo.",
      });
    };

    xhr.send(formData);
  });

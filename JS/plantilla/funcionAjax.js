document
  .getElementById("formulario-subida")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData();
    let fileInput = document.getElementById("input-file");

    if (fileInput.files.length > 0) {
      let file = fileInput.files[0];

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

    Swal.fire({
      title: "Subiendo archivo...",
      html: "Por favor, espere. Esto puede tardar varios segundos.",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "./functions/plantilla/extraccion_dataExcel.php", true);

    xhr.onload = function () {
      Swal.close();
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
            if (
              response.message.includes(
                "Los siguientes profesores exceden su carga horaria permitida"
              )
            ) {
              Swal.fire({
                icon: "warning",
                title: "Advertencia",
                html: response.message.replace(/\n/g, "<br>"),
                width: "800px", // Aumentamos el ancho para que quepa toda la información
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message,
              });
            }
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
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un problema al intentar cargar el archivo.",
      });
    };

    xhr.send(formData);
  });

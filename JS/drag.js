const dropArea = document.querySelector(".drop-area");
const dragText = dropArea.querySelector("p");
const button = dropArea.querySelector("button");
const input = dropArea.querySelector("#input-file");
let files;

button.addEventListener("click", (e) => {
  input.click();
});

input.addEventListener("change", (e) => {
  files = input.files;
  dropArea.classList.add("active");
  showFile(files);
  dropArea.classList.remove("active");
});

dropArea.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropArea.classList.add("active");
  dragText.textContent = "Suelta para subir tus archivos";
});

dropArea.addEventListener("dragleave", (e) => {
  e.preventDefault();
  dropArea.classList.remove("active");
  dragText.textContent = "Arrastra tus archivos a subir aquí";
});

dropArea.addEventListener("drop", (e) => {
  e.preventDefault();
  files = e.dataTransfer.files;
  showFile(files);
  dropArea.classList.remove("active");
  dragText.textContent = "Arrastra tus archivos a subir aquí";
});

function showFile(files) {
  if (files.length == undefined) {
    processFile(files);
  } else {
    for (const file of files) {
      processFile(file);
    }
  }
}

function processFile(file) {
  const fileExtension = file.name.split(".").pop().toLowerCase();
  const validExtensions = ["xlsx", "xls"];

  if (validExtensions.includes(fileExtension)) {
    //archivo válido
    const fileReader = new FileReader();
    const id = `file-${Math.random().toString(32).substring(7)}`;

    fileReader.addEventListener("load", (e) => {
      const filePreview = `
                <div id="${id}" class="file-container">
                    <div class="status">
                        <span>${file.name}</span>
                        <span class="status-text">
                            Cargando...
                        </span>
                    </div>
                </div>
            `;
      const html = document.querySelector("#preview").innerHTML;
      document.querySelector("#preview").innerHTML = filePreview + html;
    });

    fileReader.readAsDataURL(file);
    uploadFile(file, id);
  } else {
    //No válido
    alert(
      "No es un archivo válido. Asegúrate de subir solamente archivos con extensión .xls y .xlsx"
    );
  }
}

let filesToUpload = [];

function processFile(file) {
  const fileExtension = file.name.split(".").pop().toLowerCase();
  const validExtensions = ["xlsx", "xls"];

  if (validExtensions.includes(fileExtension)) {
    const fileReader = new FileReader();
    const id = `file-${Math.random().toString(32).substring(7)}`;

    fileReader.addEventListener("load", (e) => {
      const filePreview = `
        <div id="${id}" class="file-container">
          <div class="status">
            <span>${file.name}</span>
            <span class="status-text">Listo para subir</span>
          </div>
          <button class="cancel-btn" onclick="cancelUpload('${id}')">Cancelar</button>
        </div>
      `;
      const html = document.querySelector("#preview").innerHTML;
      document.querySelector("#preview").innerHTML = filePreview + html;
    });

    fileReader.readAsDataURL(file);
    filesToUpload.push({ file, id });
  } else {
    Swal.fire({
      icon: "error",
      title: "Archivo no válido",
      text: "Asegúrate de subir solamente archivos con extensión .xls y .xlsx",
    });
  }
}

function cancelUpload(id) {
  // Eliminar el archivo de filesToUpload
  filesToUpload = filesToUpload.filter(item => item.id !== id);
  
  // Eliminar la vista previa del archivo
  const fileContainer = document.getElementById(id);
  if (fileContainer) {
    fileContainer.remove();
  }
}

function uploadFiles() {
  if (filesToUpload.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No hay archivos para subir",
      text: "Por favor, seleccione al menos un archivo.",
    });
    return;
  }

  // Mostrar indicador de carga inmediatamente
  Swal.fire({
    title: "Subiendo archivo...",
    html: "Por favor, espere. Esto puede tardar varios segundos.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const promises = filesToUpload.map(({ file, id }) => {
    return new Promise((resolve, reject) => {
      const formData = new FormData();
      formData.append("file", file);

      fetch("./config/upload.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.text())
        .then((result) => {
          console.log(result);
          resolve(result);
        })
        .catch((error) => {
          console.error("Error:", error);
          reject(error);
        });
    });
  });

  Promise.all(promises)
    .then((results) => {
      Swal.close();
      const success = results.every((result) =>
        result.includes("cargado y guardado")
      );
      if (success) {
        Swal.fire({
          icon: "success",
          title: "Archivos subidos exitosamente",
          text: "Todos los archivos han sido cargados y guardados en la base de datos.",
        });
      } else {
        Swal.fire({
          icon: "warning",
          title: "Algunos archivos no se pudieron subir",
          text: "Por favor, revise los mensajes de estado para cada archivo.",
        });
      }
      // Limpiar la lista de archivos y la previsualización
      filesToUpload = [];
      document.querySelector("#preview").innerHTML = "";
    })
    .catch((error) => {
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error al subir los archivos",
        text: "Ocurrió un error durante la subida de archivos.",
      });
    });
}
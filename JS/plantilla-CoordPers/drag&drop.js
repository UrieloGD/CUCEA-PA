// Selección de elementos del DOM que vamos a necesitar
const dropArea = document.querySelector(".drop-area");
const dragText = dropArea.querySelector("p");
const button = dropArea.querySelector("button");
const input = dropArea.querySelector("#input-file");
const form = document.getElementById("formulario-subida");
// Array para almacenar los archivos pendientes de subir
let filesToUpload = [];

// Objeto de configuración central - Facilita cambios futuros
const CONFIG = {
  maxFileSize: 2 * 1024 * 1024, // 2MB en bytes
  validExtensions: ["xlsx", "xls"], // Tipos de archivo permitidos
  uploadUrl: "./functions/coord-personal-plantilla/extraer_datosExcel.php", // URL del servidor
};

// ============ CONFIGURACIÓN DE EVENT LISTENERS ============ //
// Cuando se hace clic en el botón, simula un clic en el input file oculto
button.addEventListener("click", () => input.click());
// Cuando se selecciona un archivo mediante el diálogo
input.addEventListener("change", handleFileSelect);
// Eventos para el drag & drop
dropArea.addEventListener("dragover", handleDragOver);
dropArea.addEventListener("dragleave", handleDragLeave);
dropArea.addEventListener("drop", handleDrop);
// Evento de envío del formulario
form.addEventListener("submit", handleSubmit);

// ============ MANEJADORES DE EVENTOS ============ //
// Maneja la selección de archivos mediante el diálogo
function handleFileSelect(e) {
  const files = e.target.files;
  handleFiles(files);
}

// Maneja el evento cuando se arrastra un archivo sobre el área
function handleDragOver(e) {
  e.preventDefault(); // Previene el comportamiento por defecto del navegador
  dropArea.classList.add("active"); // Añade clase para feedback visual
  dragText.textContent = "Suelta para subir tu archivo";
}

// Maneja el evento cuando el archivo arrastrado sale del área
function handleDragLeave(e) {
  e.preventDefault();
  dropArea.classList.remove("active"); // Elimina el feedback visual
  dragText.textContent = "Arrastra tu archivo a subir aquí";
}

// Maneja el evento cuando se sueltan los archivos en el área
function handleDrop(e) {
  e.preventDefault();
  dropArea.classList.remove("active");
  dragText.textContent = "Arrastra tu archivo a subir aquí";
  const files = e.dataTransfer.files; // Obtiene los archivos soltados
  handleFiles(files);
}

// ============ PROCESAMIENTO DE ARCHIVOS ============ //
// Procesa la lista de archivos seleccionados
function handleFiles(files) {
  Array.from(files).forEach((file) => {
    if (validateFile(file)) {
      processFile(file);
    }
  });
}

// Valida que el archivo cumpla con los requisitos
function validateFile(file) {
  const fileExtension = file.name.split(".").pop().toLowerCase();

  // Verifica la extensión del archivo
  if (!CONFIG.validExtensions.includes(fileExtension)) {
    showError("Archivo no válido", "Solo se permiten archivos .xls y .xlsx");
    return false;
  }

  // Verifica el tamaño del archivo
  if (file.size > CONFIG.maxFileSize) {
    showError("Archivo muy grande", "El archivo no debe exceder los 2MB");
    return false;
  }

  return true;
}

// Procesa un archivo individual y muestra su preview
function processFile(file) {
  const id = `file-${Math.random().toString(32).substring(7)}`; // ID único para el archivo
  const fileReader = new FileReader();

  // Cuando el archivo se carga, crea y muestra su preview
  fileReader.onload = () => {
    const filePreview = `
            <div id="${id}" class="file-container">
                <div class="status">
                    <span>${file.name}</span>
                    <span class="status-text">Listo para subir</span>
                </div>
                <button class="cancel-btn" onclick="cancelUpload('${id}')">Cancelar</button>
            </div>
        `;
    document
      .querySelector("#preview")
      .insertAdjacentHTML("afterbegin", filePreview);
  };

  fileReader.readAsDataURL(file);
  filesToUpload.push({ file, id }); // Agrega el archivo a la lista de pendientes
}

// ============ MANEJO DE SUBIDA DE ARCHIVOS ============ //
// Maneja el evento de envío del formulario
async function handleSubmit(e) {
  e.preventDefault(); // Previene el envío tradicional del formulario

  // Verifica que haya archivos para subir
  if (filesToUpload.length === 0) {
    showError("No hay archivos", "Por favor, seleccione al menos un archivo.");
    return;
  }

  showLoading(); // Muestra indicador de carga

  try {
    // Intenta subir todos los archivos simultáneamente
    const results = await Promise.all(
      filesToUpload.map(({ file }) => uploadFile(file))
    );

    // Verifica si todos los archivos se subieron exitosamente
    const success = results.every((result) => result.success);

    if (success) {
      await showSuccess(
        "Archivo subido exitosamente",
        "El archivo ha sido procesado correctamente."
      );
      resetUploadState();
      window.location.reload(); // Recarga la página para mostrar los cambios
    } else {
      showError(
        "Error en la subida",
        "Algunos archivos no se pudieron procesar correctamente."
      );
    }
  } catch (error) {
    showError("Error", "Hubo un problema al subir los archivos.");
    console.error("Error en la subida:", error);
  }
}

// Función que realiza la subida de un archivo individual
async function uploadFile(file) {
  const formData = new FormData();
  formData.append("file", file);

  const response = await fetch(CONFIG.uploadUrl, {
    method: "POST",
    body: formData,
  });

  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }

  const result = await response.json();
  return result;
}

// ============ FUNCIONES AUXILIARES ============ //
// Elimina un archivo de la lista de pendientes
function cancelUpload(id) {
  filesToUpload = filesToUpload.filter((item) => item.id !== id);
  document.getElementById(id).remove();
}

// Resetea el estado del formulario
function resetUploadState() {
  filesToUpload = [];
  document.querySelector("#preview").innerHTML = "";
  input.value = "";
}

// Muestra el indicador de carga usando SweetAlert2
function showLoading() {
  return Swal.fire({
    title: "Subiendo archivo...",
    html: "Por favor, espere. Esto puede tardar varios segundos.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });
}

// Muestra mensajes de error usando SweetAlert2
function showError(title, text) {
  return Swal.fire({
    icon: "error",
    title,
    text,
  });
}

// Muestra mensajes de éxito usando SweetAlert2
function showSuccess(title, text) {
  return Swal.fire({
    icon: "success",
    title,
    text,
  });
}

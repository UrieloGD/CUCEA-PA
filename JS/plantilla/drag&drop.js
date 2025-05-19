// ============ CONFIGURACIÓN INICIAL ============ //
const dropArea = document.querySelector(".drop-area");
const dragText = dropArea.querySelector("p");
const button = dropArea.querySelector("button");
const input = dropArea.querySelector("#input-file");
const form = document.getElementById("formulario-subida");
let filesToUpload = []; // Almacenará solo un archivo

// Configuración centralizada
const CONFIG = {
  maxFileSize: 2 * 1024 * 1024, // 2MB en bytes
  validExtensions: ["xlsx", "xls"],
  uploadUrl: "./functions/plantilla/extraccion_dataExcel.php",
};

// ============ EVENT LISTENERS ============ //
button.addEventListener("click", () => input.click());
input.addEventListener("change", (e) => handleFiles(e.target.files));
dropArea.addEventListener("dragover", handleDragOver);
dropArea.addEventListener("dragleave", handleDragLeave);
dropArea.addEventListener("drop", handleDrop);
form.addEventListener("submit", handleSubmit);

// ============ MANEJADORES DE EVENTOS ============ //
function handleDragOver(e) {
  e.preventDefault();
  updateDropArea(true, "Suelta para subir tu archivo");
}

function handleDragLeave(e) {
  e.preventDefault();
  updateDropArea(false, "Arrastra tu archivo a subir aquí");
}

function handleDrop(e) {
  e.preventDefault();
  const files = e.dataTransfer.files;
  handleFiles(files);
  updateDropArea(false, "Arrastra tu archivo a subir aquí");
}

// Actualiza el estado visual del área de drop
function updateDropArea(active, text) {
  dropArea.classList.toggle("active", active);
  dragText.textContent = text;
}

// ============ PROCESAMIENTO DE ARCHIVOS ============ //
function handleFiles(files) {
  // if (files.length === 0) {
  //   showError(
  //     "No se ha seleccionado ningún archivo",
  //     "Por favor, selecciona un archivo para subir."
  //   );
  //   return;
  // }
  // Procesar solo el primer archivo
  processFile(files[0]);
}

function processFile(file) {
  if (!validateFile(file)) return;

  const id = `file-${Math.random().toString(32).substring(7)}`;
  filesToUpload = [{ file, id }]; // Reemplaza cualquier archivo anterior
  updateFilePreview(file, id);
}

function validateFile(file) {
  const fileExtension = file.name.split(".").pop().toLowerCase();

  if (!CONFIG.validExtensions.includes(fileExtension)) {
    Swal.fire({
      icon: "error",
      title: "Archivo no válido",
      text: "Asegúrate de subir solamente archivos con extensión .xls y .xlsx",
      confirmButtonText: "OK",
      confirmButtonColor: "#0071B0",
    });
    return false;
  }

  if (file.size > CONFIG.maxFileSize) {
    showError( 
      "Archivo demasiado grande",
      "El archivo excede el tamaño máximo permitido de 2MB"
    );
    return false;
  }

  return true;
}

function updateFilePreview(file, id) {
  const filePreview = `
        <div id="${id}" class="file-container">
            <div class="status">
                <span>${file.name}</span>
                <span class="status-text">Listo para subir</span>
            </div>
            <button type="button" class="cancel-btn" onclick="cancelUpload('${id}')">Cancelar</button>
        </div>
    `;
  document.querySelector("#preview").innerHTML = filePreview;
}

// ============ MANEJO DE SUBIDA ============ //
async function handleSubmit(e) {
  e.preventDefault();

  if (filesToUpload.length === 0) {
    Swal.fire({
      icon: "error",
      title: "No hay archivos para subir",
      text: "Por favor, selecciona un archivo para subir.",
      confirmButtonText: "OK",
      confirmButtonColor: "#0071B0",
    });
    return;
  }

  // Obtener el ID del departamento usando un endpoint PHP
  try {
    // Primer paso: Obtener el departamento_id actual de la sesión
    const sesionResponse = await fetch('./functions/plantilla/obtener-departamento.php');
    if (!sesionResponse.ok) {
      throw new Error(`Error HTTP: ${sesionResponse.status}`);
    }
    
    const sesionData = await sesionResponse.json();
    
    if (!sesionData.success || !sesionData.departamento_id) {
      throw new Error("No se pudo recuperar el ID del departamento");
    }
    
    const departamentoId = sesionData.departamento_id;
    
    // Ahora con el departamentoId, verificamos si existe la plantilla
    const verificacionResponse = await fetch(`./functions/plantilla/verificarPlantillaExistente.php?departamento_id=${departamentoId}`);
    
    if (!verificacionResponse.ok) {
      throw new Error(`Error HTTP: ${verificacionResponse.status}`);
    }
    
    const verificacionResult = await verificacionResponse.json();
    
    if (verificacionResult.existe) {
      // Mostrar alertas de confirmación con SweetAlert2
      const confirmResult = await Swal.fire({
        title: 'Plantilla existente',
        html: 'Ya existe una plantilla subida para este departamento.<br>¿Deseas reemplazarla?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Reemplazar datos',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: "#0071B0",
        cancelButtonColor: '#d33',
        width: '600px',
      });
      
      if (confirmResult.isDismissed) {
        // Usuario canceló la operación
        return;
      }
      
      // El usuario eligió reemplazar (única opción disponible si no canceló)
      showLoading("Eliminando datos existentes...");
      
      try {
        const reemplazarResponse = await fetch('./functions/plantilla/reemplazarPlantilla.php', {
          method: 'POST'
        });
        
        if (!reemplazarResponse.ok) {
          throw new Error(`Error HTTP: ${reemplazarResponse.status}`);
        }
        
        const reemplazarResult = await reemplazarResponse.json();
        
        if (!reemplazarResult.success) {
          throw new Error(reemplazarResult.message || "Error al eliminar datos existentes");
        }
        
        // Después de eliminar, proceder con la subida normal
        await realizarSubida();
        
      } catch (error) {
        console.error("Error al reemplazar:", error);
        showError("Error", "No se pudieron eliminar los datos existentes. " + error.message);
      }
    } else {
      // No existe plantilla previa, subir normalmente
      await realizarSubida();
    }
  } catch (error) {
    console.error("Error:", error);
    showError(
      "Error",
      "No se pudo completar la operación: " + error.message
    );
  }
}

// Función para realizar la subida del archivo
async function realizarSubida() {
  showLoading("Subiendo archivo...");

  try {
    const { file } = filesToUpload[0];
    const formData = new FormData();
    formData.append("file", file);

    const response = await fetch(CONFIG.uploadUrl, {
      method: "POST",
      body: formData,
    });

    // Verificar si la respuesta HTTP es exitosa
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const result = await response.json();

    // Si la respuesta incluye un mensaje de carga horaria, mostrar como advertencia
    if (
      result.message &&
      result.message.includes(
        "Los siguientes profesores exceden su carga horaria permitida"
      )
    ) {
      await showWarning("Advertencia", result.message);
      // A pesar de la advertencia, el archivo se subió correctamente
      resetUploadState();
      window.location.reload();
      return;
    }

    // Si hay un mensaje de éxito explícito o la operación fue exitosa
    if (result.success || response.ok) {
      await showSuccess(
        "Archivo subido exitosamente",
        result.message || "El archivo ha sido procesado correctamente"
      );
      resetUploadState();
      window.location.reload();
    } else {
      showError(
        "Error",
        result.message || "Hubo un error al procesar el archivo"
      );
    }
  } catch (error) {
    console.error("Error:", error);
    showError(
      "Error al subir el archivo",
      "Ocurrió un error durante la subida del archivo."
    );
  }
}

// ============ FUNCIONES AUXILIARES ============ //
function cancelUpload(id) {
  filesToUpload = filesToUpload.filter((item) => item.id !== id);
  document.getElementById(id).remove();
  input.value = ""; // Limpiar el input file
}

function resetUploadState() {
  filesToUpload = [];
  document.querySelector("#preview").innerHTML = "";
  input.value = "";
}

function showLoading(message = "Subiendo archivo...") {
  return Swal.fire({
    title: message,
    html: "Por favor, espere. Esto puede tardar varios segundos.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });
}

function showError(title, text) {
  return Swal.fire({
    icon: "error",
    title,
    text,
  });
}

function showSuccess(title, text) {
  return Swal.fire({
    icon: "success",
    title,
    text,
  });
}

function showWarning(title, message) {
  return Swal.fire({
    icon: "warning",
    title,
    html: message.replace(/\n/g, "<br>"),
    width: "800px", // Para mensajes largos de advertencia
  });
}
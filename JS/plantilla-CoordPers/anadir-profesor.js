// Función para inicializar la conversión de mayúsculas y minúsculas
function inicializarFormatoTexto() {
  // Obtenemos todos los campos de entrada de texto del formulario
  const inputs = document.querySelectorAll('#form-añadir-registro input[type="text"]');
  
  // Para cada campo de texto, agregamos un evento input
  inputs.forEach(input => {
    input.addEventListener('input', function() {
      // Verificamos que no sea un campo numérico por su nombre o ID
      const camposNumericos = ['codigo', 'cp', 'edad', 'año', 'otro_año', 'otro_año_alternativo', 'horas_frente_grupo', 'horas_definitivas'];
      if (!camposNumericos.includes(input.id)) {
        // Convertimos el valor a mayúsculas
        this.value = this.value.toUpperCase();
      }
    });
  });
  
  // Obtenemos todos los campos de correo electrónico
  const emailInputs = document.querySelectorAll('#form-añadir-registro input[type="email"]');
  
  // Para cada campo de correo, agregamos un evento input
  emailInputs.forEach(input => {
    input.addEventListener('input', function() {
      // Convertimos el valor a minúsculas
      this.value = this.value.toLowerCase();
    });
  });
}

// Modificamos la función añadirRegistro para asegurar el formato correcto antes de enviar
function añadirRegistro() {
  // Mostrar loader o deshabilitar botón
  const submitButton = document.querySelector(
    '#form-añadir-registro button[onclick="añadirRegistro()"]'
  );
  submitButton.disabled = true;
  submitButton.textContent = "Guardando...";

  var form = document.getElementById("form-añadir-registro");
  var formData = new FormData(form);

  // Validar campos requeridos
  const requiredFields = ["codigo", "paterno", "materno", "nombres"];
  for (let field of requiredFields) {
    if (!formData.get(field)) {
      Swal.fire({
        title: "Error",
        text: `El campo ${field} es requerido`,
        icon: "error",
      });
      submitButton.disabled = false;
      submitButton.textContent = "Guardar";
      return;
    }
  }

  // Concatenar fechas de cambio de dedicación
  const fechaInicio = formData.get('cambio_dediacion_inicio') || '';
  const fechaFinal = formData.get('cambio_dediacion_final') || '';
  
  // Si al menos una de las fechas tiene valor, creamos el campo combinado
  if (fechaInicio || fechaFinal) {
    const cambioCompleto = `${fechaInicio}${fechaInicio && fechaFinal ? ' - ' : ''}${fechaFinal}`;
    formData.set('cambio_dedicacion', cambioCompleto);
  }
  
  // Eliminar los campos individuales para que no se procesen por separado
  formData.delete('cambio_dediacion_inicio');
  formData.delete('cambio_dediacion_final');

  // Asegurar formato correcto de todos los campos
  const camposNumericos = ['codigo', 'cp', 'edad', 'año', 'otro_año', 'otro_año_alternativo', 'horas_frente_grupo', 'horas_definitivas'];
  const camposEmail = ['correo', 'correos_oficiales'];
  
  for (let pair of formData.entries()) {
    const [key, value] = pair;
    if (typeof value === 'string') {
      if (camposEmail.includes(key)) {
        // Emails en minúsculas
        formData.set(key, value.toLowerCase());
      } else if (!camposNumericos.includes(key)) {
        // Texto en mayúsculas
        formData.set(key, value.toUpperCase());
      }
    }
  }

  fetch("./functions/coord-personal-plantilla/anadir-profesor.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.text(); // Primero obtener el texto de la respuesta
    })
    .then((text) => {
      try {
        return JSON.parse(text); // Intentar parsear el JSON
      } catch (e) {
        console.error("Error parsing JSON:", text);
        throw new Error("Respuesta del servidor no válida");
      }
    })
    .then((data) => {
      if (data.success) {
        Swal.fire({
          title: "¡Éxito!",
          text: data.message,
          icon: "success",
        }).then(() => {
          cerrarFormularioAñadir();
          location.reload();
        });
      } else {
        throw new Error(data.message || "Error al guardar el registro");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        title: "Error",
        text: error.message || "Error al procesar la solicitud",
        icon: "error",
      });
    })
    .finally(() => {
      submitButton.disabled = false;
      submitButton.textContent = "Guardar";
    });
}

// Llamamos a la función cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
  // Inicializa el formulario
  inicializarFormatoTexto();

  // Asigna el evento para cerrar al hacer clic en la X
  var closeButton = document.querySelector('.close-modal-anadir');
  if (closeButton) {
    closeButton.addEventListener('click', cerrarFormularioAñadir);
  }
  
  // Asigna el evento para cerrar al hacer clic fuera del modal
  var modal = document.getElementById('modal-añadir');
  if (modal) {
    window.addEventListener('click', function(event) {
      if (event.target === modal) {
        cerrarFormularioAñadir();
      }
    });
  }
});

// Resto de funciones del archivo
function mostrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "block";
}

function cerrarFormularioAñadir() {
  document.getElementById("modal-añadir").style.display = "none";
  document.getElementById("form-añadir-registro").reset(); // Limpiar el formulario al cerrar
}

// Cambia el color a #000000 cuando se selecciona una opción en los select
$(document).ready(function(){

  $("select").change(function(){
    if ($(this).val()=="") $(this).css({color: "#aaa"});
    else $(this).css({color: "#000"});
  });
  
});	

// Cambiar el texto a color #000000 cuando se selecciona alguna fecha.
const fechaSnidesde = document.getElementById('sni_desde');
const fechaCambioDI = document.getElementById('cambio_dediacion_inicio');
const fechaCambioDF = document.getElementById('cambio_dediacion_final');
const fechaNacimiento = document.getElementById('fecha_nacimiento');
const fechaAnio = document.getElementById('año');
const fechaAnioOtro = document.getElementById('otro_año');
const fechaAnioOtroAlternativo = document.getElementById('otro_año_alternativo');
const fechaApartirDe = document.getElementById('a_partir_de');
const fechaIngreso = document.getElementById('fecha_ingreso');

function cambiarColorTexto(event) {
    const input = event.target; 
    if (input.value) {
      input.style.color = '#000000'; 
      input.style.fontStyle = 'normal';
    } else {
      input.style.color = ''; 
      input.style.fontStyle = 'italic';
    }
}

fechaSnidesde.addEventListener('change', cambiarColorTexto);
fechaCambioDI.addEventListener('change', cambiarColorTexto);
fechaCambioDF.addEventListener('change', cambiarColorTexto);
fechaNacimiento.addEventListener('change', cambiarColorTexto);
fechaAnio.addEventListener('change', cambiarColorTexto);
fechaAnioOtro.addEventListener('change', cambiarColorTexto);
fechaAnioOtroAlternativo.addEventListener('change', cambiarColorTexto);
fechaApartirDe.addEventListener('change', cambiarColorTexto);
fechaIngreso.addEventListener('change', cambiarColorTexto);
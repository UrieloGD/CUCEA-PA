document.addEventListener('DOMContentLoaded', function() {
  // Funcionalidad del botón nueva solicitud y lista de opciones
  const btn = document.getElementById('nueva-solicitud-btn');
  const lista = document.getElementById('lista-opciones');
  const modales = {
      'Solicitud de baja': document.getElementById('solicitud-modal-baja-academica'),
      'Solicitud de propuesta': document.getElementById('solicitud-modal-propuesta-academica'),
      'Solicitud de baja-propuesta': document.getElementById('solicitud-modal-baja-propuesta')
  };

  // Asegurarse de que los modales estén ocultos al inicio
  Object.values(modales).forEach(modal => {
      if (modal) modal.style.display = 'none';
  });

  // Manejo del botón de nueva solicitud
  btn.addEventListener('click', function(e) {
      e.preventDefault();
      lista.classList.toggle('show');
  });

  // Manejo de la selección de opciones
  lista.addEventListener('click', function(e) {
      const opcionSeleccionada = e.target.innerText;
      if (modales[opcionSeleccionada]) {
          lista.classList.remove('show');
          modales[opcionSeleccionada].style.display = 'block';
      }
  });

  // Función para mostrar/ocultar información al hacer clic
  window.mostrarInformacion = function(contenedorId, icono) {
      const contenedor = document.getElementById(contenedorId);
      if (contenedor) {
          if (contenedor.style.maxHeight) {
              contenedor.style.maxHeight = null;
              icono.classList.remove('rotated');
          } else {
              contenedor.style.maxHeight = contenedor.scrollHeight + "px";
              icono.classList.add('rotated');
          }
      }
  };

  // Cerrar la lista de opciones si se hace clic fuera
  document.addEventListener('click', function(e) {
      if (!btn.contains(e.target) && !lista.contains(e.target)) {
          lista.classList.remove('show');
      }
  });

  // Validaciones de campos de texto
  const validaciones = {
      'texto-CRN': {
          pattern: /\D/g,
          maxLength: 6,
          type: 'numeric'
      },
      'texto-materia': {
          pattern: /[^a-zA-Z\s]/g,
          type: 'text'
      },
      'texto-clave': {
          pattern: /[^a-zA-Z0-9]/g,
          maxLength: 5,
          type: 'alphanumeric'
      },
      'texto-SEC': {
          pattern: /[^a-zA-Z0-9]/g,
          maxLength: 3,
          type: 'alphanumeric'
      },
      'texto-folio': {
          pattern: /[^a-zA-Z0-9]/g,
          maxLength: 10,
          type: 'alphanumeric'
      },
      'texto-apellido-paterno': {
          pattern: /[^a-zA-Z\s]/g,
          maxLength: 50,
          type: 'text'
      },
      'texto-apellido-materno': {
          pattern: /[^a-zA-Z\s]/g,
          maxLength: 50,
          type: 'text'
      },
      'texto-nombres': {
          pattern: /[^a-zA-Z\s]/g,
          maxLength: 50,
          type: 'text'
      },
      'texto-codigo': {
          pattern: /\D/g,
          maxLength: 10,
          type: 'numeric'
      },
      'texto-otro': {
          pattern: /[^a-zA-Z\s]/g,
          maxLength: 120,
          type: 'text'
      }
  };

  // Aplicar validaciones a los campos
  Object.entries(validaciones).forEach(([className, rules]) => {
      document.querySelectorAll(`.${className}`).forEach(input => {
          input.addEventListener('input', function(e) {
              let value = e.target.value;
              value = value.replace(rules.pattern, '');
              if (rules.maxLength) {
                  value = value.slice(0, rules.maxLength);
              }
              e.target.value = value;
          });
      });
  });
});
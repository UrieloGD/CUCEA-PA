// ./JS/personal-solicitud-cambios/personal-solicitud-cambios.js
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el usuario tiene el rol de Coordinación de personal
    const esCoordinacionPersonal = rol_usuario === 3;
    
    // Función mejorada para mostrar/ocultar información al hacer clic 
    // Disponible para todos los roles, incluyendo Coordinación de personal
    window.mostrarInformacion = function(contenedorId, icono) {
      const contenedor = document.getElementById(contenedorId);
      
      if (contenedor) {
          // Alternar la clase 'active' en el contenedor
          contenedor.classList.toggle('active');
          
          // Si el contenedor está activo, mostrarlo
          if (contenedor.classList.contains('active')) {
              contenedor.style.display = 'block';
              // Pequeño retraso para asegurar que la transición funcione
              setTimeout(() => {
                  contenedor.style.maxHeight = contenedor.scrollHeight + "px";
              }, 10);
              icono.classList.add('rotated');
          } else {
              // Si el contenedor no está activo, ocultarlo
              contenedor.style.maxHeight = '0';
              icono.classList.remove('rotated');
              // Agregar un retraso antes de ocultar completamente
              setTimeout(() => {
                  if (!contenedor.classList.contains('active')) {
                      contenedor.style.display = 'none';
                  }
              }, 200);
          }
      }
    };
    
    // Inicializar todos los contenedores como ocultos (para todos los roles)
    document.querySelectorAll('.contenedor-informacion').forEach(container => {
      container.style.display = 'none';
      container.style.maxHeight = '0';
    });
  
    // Solo inicializar la funcionalidad del botón si no es Coordinación de personal
    if (!esCoordinacionPersonal) {
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
              openModal(modales[opcionSeleccionada]);
          }
      });
  
      // Cerrar la lista de opciones si se hace clic fuera
      document.addEventListener('click', function(e) {
          if (!btn.contains(e.target) && !lista.contains(e.target)) {
              lista.classList.remove('show');
          }
      });
  
      // Función para abrir modales
      function openModal(modal) {
          if (!modal) return;
          
          modal.style.display = 'block';
          
          const closeButton = modal.querySelector('.close-button');
          const modalContent = modal.querySelector('.modal-content-propuesta') || 
                             modal.querySelector('.modal-content-baja');
          
          if (closeButton) {
              closeButton.addEventListener('click', function() {
                  modal.style.display = 'none';
              });
          }
  
          if (modalContent) {
              modalContent.addEventListener('click', function(e) {
                  e.stopPropagation();
              });
          }
  
          window.addEventListener('click', function closeOutside(e) {
              if (e.target === modal) {
                  modal.style.display = 'none';
                  window.removeEventListener('click', closeOutside);
              }
          });
      }
  
      // Solo inicializar validaciones de campos si no es Coordinación de personal
      // Validaciones de campos de texto
      const validaciones = {
          'texto-CRN': {
              pattern: /\D/g,
              maxLength: 6,
              type: 'numeric'
          },
          'texto-materia': {
              pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,
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
              pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,
              maxLength: 50,
              type: 'text'
          },
          'texto-apellido-materno': {
              pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,
              maxLength: 50,
              type: 'text'
          },
          'texto-nombres': {
              pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,
              maxLength: 50,
              type: 'text'
          },
          'texto-codigo': {
              pattern: /\D/g,
              maxLength: 10,
              type: 'numeric'
          },
          'texto-otro': {
              pattern: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,
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
    }
});

    // Función para generar PDF (solo para rol 3 - Coordinación de Personal)
    function generarPDF(folio) {
        Swal.fire({
            title: '¿Generar PDF?',
            text: 'La solicitud pasará a estado "En revisión"',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`./functions/procesar_baja.php?folio=${folio}&accion=generar`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.open(data.pdf_url, '_blank');
                        window.location.reload(); // Actualizar estado
                    } else {
                        Swal.fire('Error', data.error, 'error');
                    }
                });
            }
        });
    }

    // Para Ambos Roles
    function descargarPDF(folio) {
        window.open(`./functions/descargar_pdf.php?folio=${folio}`, '_blank');
    }
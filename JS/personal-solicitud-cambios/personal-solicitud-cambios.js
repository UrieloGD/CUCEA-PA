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
        if (rol_usuario !== 3) {
            alert('No tienes permisos para generar solicitudes en PDF.');
            return;
        }
        
        if (confirm('¿Estás seguro de generar esta solicitud? El estado cambiará a "En revisión".')) {
            $.ajax({
                url: './functions/personal-solicitud-cambios/pdfs/generar_pdf_baja.php',
                type: 'POST',
                data: {
                    accion: 'generar',
                    folio: folio
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Solicitud generada correctamente y estado actualizado a "En revisión".');
                        // Redirigir a la página de descarga del PDF
                        window.open('./functions/personal-solicitud-cambios/pdfs/descargar_pdf_baja.php?folio=' + response.folio, '_blank');
                        // Recargar la página para reflejar los cambios
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error en la comunicación con el servidor.');
                }
            });
        }
    }

    // Función para descargar PDF (para ambos roles)
    function descargarPDF(folio) {
        $.ajax({
            url: './functions/personal-solicitud-cambios/pdfs/generar_pdf.php',
            type: 'POST',
            data: {
                accion: 'descargar',
                folio: folio
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.open('./functions/personal-solicitud-cambios/pdfs/descargar_pdf.php?folio=' + response.folio, '_blank');
                } else {
                    if (rol_usuario === 3) {
                        alert('El PDF aún no ha sido generado. Por favor, genere primero la solicitud en PDF.');
                    } else {
                        alert('El PDF aún no está disponible. La solicitud debe ser procesada por Coordinación de Personal.');
                    }
                }
            },
            error: function() {
                alert('Error en la comunicación con el servidor.');
            }
        });
    }
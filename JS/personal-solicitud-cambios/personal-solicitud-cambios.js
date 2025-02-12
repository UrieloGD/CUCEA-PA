document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('nueva-solicitud-btn');
  const lista = document.getElementById('lista-opciones');
  const modalBaja = document.getElementById('solicitud-modal-baja-academica');
  
  // Evitar que el modal se abra automáticamente
  modalBaja.style.display = 'none';
  
  btn.addEventListener('click', function(e) {
      e.preventDefault();
      lista.classList.toggle('show');
  });

  lista.addEventListener('click', function(e) {
      const opcionSeleccionada = e.target.innerText;
      if (opcionSeleccionada === 'Solicitud de baja') {
          lista.classList.remove('show');
          abrirModal();
      }
  });

  // Función para abrir el modal
  function abrirModal() {
      modalBaja.style.display = 'block';
      // Inicializar el formulario
      const form = modalBaja.querySelector('form');
      if (form) form.reset();
  }

  // Manejar el cierre del modal
  const closeButton = modalBaja.querySelector('.close-button');
  const btnDescartar = modalBaja.querySelector('#btn-descartar');
  
  if (closeButton) {
      closeButton.addEventListener('click', cerrarModal);
  }
  
  if (btnDescartar) {
      btnDescartar.addEventListener('click', cerrarModal);
  }
  
  // Cerrar modal al hacer clic fuera
  window.addEventListener('click', function(e) {
      if (e.target === modalBaja) {
          cerrarModal();
      }
  });

  function cerrarModal() {
      modalBaja.style.display = 'none';
      const form = modalBaja.querySelector('form');
      if (form) form.reset();
  }

  // Cerrar la lista de opciones si se hace clic fuera
  document.addEventListener('click', function(e) {
      if (!btn.contains(e.target) && !lista.contains(e.target)) {
          lista.classList.remove('show');
      }
  });
});
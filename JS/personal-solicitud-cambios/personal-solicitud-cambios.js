//personal-solicitud-cambios.js
document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('nueva-solicitud-btn');
  const lista = document.getElementById('lista-opciones');
  const modalBaja = document.getElementById('solicitud-modal-baja-academica');
  
  btn.addEventListener('click', function(e) {
      e.preventDefault();
      lista.classList.toggle('show');
  });

  lista.addEventListener('click', function(e) {
      const opcionSeleccionada = e.target.innerText;
      if (opcionSeleccionada === 'Solicitud de baja') {
          lista.classList.remove('show');
          modalBaja.style.display = 'block';
      }
  });

  // Manejar el cierre del modal
  const closeButton = modalBaja.querySelector('.close-button');
  const btnDescartar = modalBaja.querySelector('#btn-descartar');
  
  closeButton.addEventListener('click', cerrarModal);
  btnDescartar.addEventListener('click', cerrarModal);
  
  // Cerrar modal al hacer clic fuera
  window.addEventListener('click', function(e) {
      if (e.target === modalBaja) {
          cerrarModal();
      }
  });

  // Manejar el guardado
  const btnGuardar = modalBaja.querySelector('#btn-guardar');
  btnGuardar.addEventListener('click', function() {
      // Aquí va la lógica de guardado
      // Después de guardar, cerrar el modal
      cerrarModal();
  });

  function cerrarModal() {
      modalBaja.style.display = 'none';
      // Opcional: limpiar el formulario
      const form = modalBaja.querySelector('form');
      if (form) form.reset();
  }
});
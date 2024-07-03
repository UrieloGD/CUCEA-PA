// Get the notification icon and menu
const notificationIcon = document.getElementById('notification-icon');
const notificationMenu = document.getElementById('notification-menu');

// ... (código existente) ...

// Función para crear una notificación
function createNotification(user, description, date) {
  const notificationElement = document.createElement('div');
  notificationElement.className = 'contenedor-notificacion';
  notificationElement.innerHTML = `
    <div class="imagen"></div>
    <div class="info-notificacion">
      <div class="usuario">${user}</div>
      <div class="descripcion">${description}</div>
      <div class="fecha-hora">${date}</div>
    </div>
  `;
  return notificationElement;
}

// Función para agregar notificaciones
function addNotifications() {
  const notificationsContainer = document.getElementById('notifications-container');
  const notifications = [
    { user: 'Coordinación de Personal', description: 'Ha realizado una solicitud de cambios', date: '3 de junio, 14:20 horas' },
    { user: 'Estudios regionales', description: 'La base de datos no ha sido actualizada', date: '3 de junio, 11:30 horas' },
    { user: 'Políticas públicas', description: 'La base de datos ha sido actualizada correctamente', date: '3 de junio, 11:30 horas' },
    { user: 'Control Escolar', description: 'Ha enviado una solicitud de apertura de sección', date: '2 de junio, 11:30 horas' }
  ];

  notifications.forEach(notif => {
    const notificationElement = createNotification(notif.user, notif.description, notif.date);
    notificationsContainer.appendChild(notificationElement);
  });
}

// Llama a la función para agregar notificaciones cuando se abre el menú
function openNav() {
  notificationMenu.style.width = '400px';
  addNotifications();
}

// ... (resto del código existente) ...

// Actualiza la fecha y hora cada segundo
setInterval(() => {
  const now = new Date();
  const options = { day: 'numeric', month: 'long', year: 'numeric' };
  currentDate.textContent = now.toLocaleDateString('es-MX', options);
  currentTime.textContent = now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
}, 1000);

// Agrega un evento al botón "Marcar como leído"
const markAsReadButton = document.querySelector('.marcar-leido');
markAsReadButton.addEventListener('click', () => {
  // Aquí puedes agregar la lógica para marcar las notificaciones como leídas
  console.log('Notificaciones marcadas como leídas');
});

// Código para el menú hamburguesa (sin cambios)
document.addEventListener('DOMContentLoaded', function() {
  // ... (código existente para el menú hamburguesa) ...
});

// Función para cerrar la barra de navegación al hacer clic fuera de ella
window.onclick = function(event) {
  if (event.target != notificationMenu && event.target != notificationIcon) {
    closeNav();
  }
}
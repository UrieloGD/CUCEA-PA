 // Get the notification icon and menu
 const notificationIcon = document.getElementById('notification-icon');
 const notificationMenu = document.getElementById('notification-menu');

 // Add a click event listener to the notification icon
 notificationIcon.addEventListener('click', () => {
   // Toggle the visibility of the notification menu
   notificationMenu.style.display = (notificationMenu.style.display === 'none') ? 'block' : 'none';
 });

 // Get the current date and time
 const currentDate = document.getElementById('current-date');
 const currentTime = document.getElementById('current-time');

 // Update the current date and time every second
 setInterval(() => {
   const now = new Date();
   const options = {
     month: 'long',
     day: 'numeric',
     year: 'numeric'
   }; // Use 'long' for month name
   currentDate.textContent = now.toLocaleDateString('es-MX', options);
   currentTime.textContent = now.toLocaleTimeString('es-MX');
 }, 1000); // Update every second

 // Add notifications here
 const notifications = document.getElementById('notifications');

 // Example notifications

 const notification1 = document.createElement('li');
 notification1.textContent = 'Aldo Ceja está enojao pq no le has mandado la Base de Datos';
 notification1.classList.add('urgente'); // Agregar la clase 'urgente' para notificaciones urgentes (rojo)
 notifications.appendChild(notification1);

 const notification2 = document.createElement('li');
 notification2.textContent = 'Notificación 2';
 notification2.classList.add('urgente'); // Agregar la clase 'urgente' para notificaciones urgentes (rojo)
 notifications.appendChild(notification2);

 const notification3 = document.createElement('li');
 notification3.textContent = 'Notificación 3';
 notification3.classList.add('normal'); // Agregar la clase 'normal' para notificaciones normales (verde)
 notifications.appendChild(notification3);

 const notification4 = document.createElement('li');
 notification4.textContent = 'Notificación 4';
 notification4.classList.add('advertencia'); // Agregar la clase 'advertencia' para notificaciones de advertencia (amarillo)
 notifications.appendChild(notification4);

 //Java Script: Convierte "Programación Académica" a "PA" 

/* window.addEventListener('resize', function() {
 var titulo = document.querySelector('.titulo h3');
 if (window.innerWidth <= 768) {
   titulo.textContent = 'PA';
 } else {
   titulo.textContent = 'Programación Académica';
 }
}); */

//Java Script: Convierte "Programación Académica" a "PA" alineado con menú hamburguesa

window.addEventListener('resize', function() {
var tituloContainer = document.querySelector('.titulo');
if (window.innerWidth <= 768) {
 tituloContainer.innerHTML = '<h3>PA</h3>';
} else {
 tituloContainer.innerHTML = '<h3>Programación Académica</h3>';
}
});


//Java Script: Click para el boton hamburguesa

/* document.addEventListener('DOMContentLoaded', function() {
 // Selecciona el botón del menú hamburguesa y el menú móvil
 var menuToggle = document.querySelector('.menu-toggle');
 var mobileMenu = document.querySelector('.mobile-menu');

 // Agrega un evento clic al botón del menú hamburguesa
 menuToggle.addEventListener('click', function() {
     // Cambia la visibilidad del menú móvil al hacer clic en el botón
     mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
 });
}); */

document.addEventListener('DOMContentLoaded', function() {
 // Selecciona el botón del menú hamburguesa y el menú móvil
 var menuToggle = document.querySelector('.menu-toggle');
 var mobileMenu = document.querySelector('.mobile-menu');

 // Función para manejar la visibilidad del menú hamburguesa
 function toggleMobileMenu() {
     var screenWidth = window.innerWidth;
     if (screenWidth <= 768) {
         mobileMenu.style.display = 'none';
     } else {
         mobileMenu.style.display = 'none';
     }
 }

 // Agrega un evento clic al botón del menú hamburguesa
 menuToggle.addEventListener('click', function() {
     // Cambia la visibilidad del menú móvil al hacer clic en el botón
     mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
 });

 // Agrega un evento de cambio de tamaño de ventana
 window.addEventListener('resize', toggleMobileMenu);

 // Oculta el menú hamburguesa inicialmente
 mobileMenu.style.display = 'none';
});

// Get the notification icon and menu
const notificationIcon = document.getElementById("notification-icon");
const notificationMenu = document.getElementById("notification-menu");

//Java Script: Convierte "Programación Académica" a "PA"

/* window.addEventListener('resize', function() {
 var titulo = document.querySelector('.titulo h3');
 if (window.innerWidth <= 768) {
   titulo.textContent = 'PA';
 } else {
   titulo.textContent = 'Programación Académica';
 }
}); */

//Java Script: Convierte "Programación Académica" a "PA"

window.addEventListener("resize", function () {
  var tituloContainer = document.querySelector(".titulo");
  if (window.innerWidth <= 768) {
    tituloContainer.innerHTML = "";
  } else if (window.innerWidth >= 768) {
    tituloContainer.innerHTML = "<h3>Programación Académica</h3>";
  } else {
    tituloContainer.innerHTML = "";
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
/*
document.addEventListener("DOMContentLoaded", function () {
  // Selecciona el botón del menú hamburguesa y el menú móvil
  var menuToggle = document.querySelector(".menu-toggle");
  var mobileMenu = document.querySelector(".mobile-menu");

  // Función para manejar la visibilidad del menú hamburguesa
  function toggleMobileMenu() {
    var screenWidth = window.innerWidth;
    if (screenWidth <= 768) {
      mobileMenu.style.display = "none";
    } else {
      mobileMenu.style.display = "none";
    }
  }

  // Agrega un evento clic al botón del menú hamburguesa
  menuToggle.addEventListener("click", function () {
    // Cambia la visibilidad del menú móvil al hacer clic en el botón
    mobileMenu.style.display =
      mobileMenu.style.display === "block" ? "none" : "block";
  });

  // Agrega un evento de cambio de tamaño de ventana
  window.addEventListener("resize", toggleMobileMenu);

  // Oculta el menú hamburguesa inicialmente
  mobileMenu.style.display = "none";
});*/

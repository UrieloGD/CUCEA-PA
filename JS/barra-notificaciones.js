let isNavOpen = false;

function toggleNav() {
  const sidebar = document.getElementById("mySidebar");
  if (isNavOpen) {
    sidebar.style.width = "0";
    sidebar.style.right = "-400px";
  } else {
    sidebar.style.width = "400px";
    sidebar.style.right = "0";
  }
  isNavOpen = !isNavOpen;
}

function marcarNotificacionesComoVistas() {
  const notificaciones = document.querySelectorAll(".contenedor-notificacion");
  const ids = [];
  const tipos = [];

  notificaciones.forEach((notificacion) => {
    if (!notificacion.classList.contains("vista")) {
      ids.push(notificacion.dataset.id);
      tipos.push(notificacion.dataset.tipo);
      notificacion.classList.add("vista");
    }
  });

  if (ids.length > 0) {
    fetch("marcar_vistas.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `ids=${JSON.stringify(ids)}&tipos=${JSON.stringify(tipos)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          console.log("Notificaciones marcadas como vistas");
        }
      });
  }
}

// Función para cerrar la barra de navegación al hacer clic fuera de ella
document.addEventListener("click", function (event) {
  const sidebar = document.getElementById("mySidebar");
  const notificationIcon = document.getElementById("notification-icon");

  if (
    isNavOpen &&
    event.target !== sidebar &&
    !sidebar.contains(event.target) &&
    event.target !== notificationIcon
  ) {
    toggleNav();
  }
});

// Prevenir que el clic en el icono de notificaciones propague el evento
document
  .getElementById("notification-icon")
  .addEventListener("click", function (event) {
    event.stopPropagation();
  });

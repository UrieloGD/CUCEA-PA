let isNavOpen = false;

window.actualizarBadgeNotificaciones = function () {
  const notificacionesSinVer = document.querySelectorAll(
    ".contenedor-notificacion:not(.vista)"
  );
  const badge = document.getElementById("notification-badge");

  if (notificacionesSinVer.length > 0) {
    badge.style.display = "block";
  } else {
    badge.style.display = "none";
  }
};

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

function actualizarBadgeNotificaciones() {
  const notificacionesSinVer = document.querySelectorAll(
    ".contenedor-notificacion:not(.vista)"
  );
  const badge = document.getElementById("notification-badge");

  if (notificacionesSinVer.length > 0) {
    badge.style.display = "block";
  } else {
    badge.style.display = "none";
  }
}

function manejarClicNotificacion(event) {
  const notificacion = event.currentTarget;

  if (!notificacion.classList.contains("vista")) {
    notificacion.classList.add("vista");
    marcarNotificacionComoVista(
      notificacion.dataset.id,
      notificacion.dataset.tipo
    );
    actualizarBadgeNotificaciones();

    // Si es una notificación de plantilla, podrías redirigir al usuario a la página de plantillas
    if (notificacion.dataset.tipo === "plantilla") {
      window.location.href = "./plantilla.php";
    }
  }
}

function marcarNotificacionComoVista(id, tipo) {
  fetch("./config/marcar_vista.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${id}&tipo=${tipo}`,
  })
    .then((response) => response.text())
    .then((text) => {
      console.log("Respuesta completa del servidor:", text);
      try {
        return JSON.parse(text);
      } catch (error) {
        throw new Error(`Respuesta no válida del servidor: ${text}`);
      }
    })
    .then((data) => {
      if (data.success) {
        console.log("Notificación marcada como vista");
      } else {
        console.error(
          "Error al marcar la notificación como vista:",
          data.error
        );
      }
    })
    .catch((error) => {
      console.error("Error en la solicitud:", error);
    });
}

function marcarNotificacionesComoVistas() {
  const notificaciones = document.querySelectorAll(
    ".contenedor-notificacion:not(.vista)"
  );

  notificaciones.forEach((notificacion) => {
    notificacion.classList.add("vista");
    marcarNotificacionComoVista(
      notificacion.dataset.id,
      notificacion.dataset.tipo
    );
  });

  actualizarBadgeNotificaciones();

  if (ids.length > 0) {
    fetch("./config/marcar_vista.php", {
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

document.addEventListener("DOMContentLoaded", function () {
  const notificaciones = document.querySelectorAll(".contenedor-notificacion");
  notificaciones.forEach((notificacion) => {
    notificacion.addEventListener("click", manejarClicNotificacion);
  });

  const botonMarcarLeido = document.querySelector(".marcar-leido");
  if (botonMarcarLeido) {
    botonMarcarLeido.addEventListener("click", marcarNotificacionesComoVistas);
  }
  actualizarBadgeNotificaciones();
});

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

function actualizarNotificaciones() {
  fetch("./functions/notificaciones/obtener-notificaciones.php")
    .then((response) => response.text())
    .then((html) => {
      const sidebar = document.getElementById("mySidebar");
      const contenedorFechaHora = sidebar.querySelector(
        ".contenedor-fecha-hora"
      );

      // Crear un contenedor temporal para el nuevo contenido
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      // Reemplazar solo la sección de notificaciones
      const viejoContenido = sidebar.querySelectorAll(
        ".grupo-fecha, .mensaje-sin-notificaciones"
      );
      viejoContenido.forEach((element) => element.remove());

      // Insertar nuevo contenido después del contenedor de fecha/hora
      tempDiv.childNodes.forEach((node) => {
        if (node.nodeType === 1) {
          // Solo elementos HTML
          sidebar.insertBefore(node, contenedorFechaHora.nextSibling);
        }
      });

      // Actualizar el contenido de las notificaciones
      sidebar.innerHTML = html;

      // Volver a insertar el contenedor de fecha y hora al principio
      if (contenedorFechaHora) {
        sidebar.insertBefore(contenedorFechaHora, sidebar.firstChild);
      }

      // Volver a añadir los event listeners
      const notificaciones = document.querySelectorAll(
        ".contenedor-notificacion"
      );
      notificaciones.forEach((notificacion) => {
        notificacion.addEventListener("click", manejarClicNotificacion);
      });

      actualizarBadgeNotificaciones();
    })
    .catch((error) =>
      console.error("Error al actualizar notificaciones:", error)
    );
}

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
    if (window.innerWidth <= 768) {
      sidebar.style.width = "0";
      sidebar.style.right = "-250px";
    }
  } else {
    sidebar.style.width = "400px";
    sidebar.style.right = "0";
    if (window.innerWidth <= 768) {
      sidebar.style.width = "250px";
      sidebar.style.right = "0";
    }
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
  fetch("./functions/notificaciones/marcar-notificacion-vista.php", {
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
    fetch("./functions/notificaciones/marcar-notificacion-vista.php", {
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
  // Iniciar la actualización automática cada 30 segundos
  setInterval(actualizarNotificaciones, 30000);
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

function descartarNotificacion(event, id, tipo) {
  event.stopPropagation();

  const notificacion = event.target.closest(".contenedor-notificacion");

  // Ocultar inmediatamente la notificación
  notificacion.style.display = "none";

  // Marcar como descartada en la base de datos
  fetch("./functions/notificaciones/descartar-notificacion.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${id}&tipo=${tipo}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        console.error("Error al descartar notificación");
        notificacion.style.display = "flex";
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      notificacion.style.display = "flex";
    });
}

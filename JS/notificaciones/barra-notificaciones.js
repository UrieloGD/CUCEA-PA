function actualizarNotificaciones() {
  fetch("./functions/notificaciones/obtener-notificaciones.php")
    .then((response) => response.text())
    .then((html) => {
      const sidebar = document.getElementById("mySidebar");

      // Conservar el contenedor de fecha/hora si existe
      const contenedorFechaHora = sidebar.querySelector(
        ".contenedor-fecha-hora"
      );

      // Limpiar el sidebar pero mantener el contenedor de fecha/hora
      while (sidebar.firstChild) {
        sidebar.removeChild(sidebar.firstChild);
      }

      // Agregar el contenedor de fecha/hora de vuelta si existía
      if (contenedorFechaHora) {
        sidebar.appendChild(contenedorFechaHora);
      }

      // Añadir el nuevo contenido
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      while (tempDiv.firstChild) {
        sidebar.appendChild(tempDiv.firstChild);
      }

      // Volver a añadir los event listeners
      const notificaciones = document.querySelectorAll(
        ".contenedor-notificacion"
      );
      notificaciones.forEach((notificacion) => {
        notificacion.addEventListener("click", manejarClicNotificacion);
      });

      // Actualizar el badge inmediatamente después de cargar las notificaciones
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
    badge.textContent = "";
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
    // Actualizar notificaciones cuando se abre el panel
    actualizarNotificaciones();
  }
  isNavOpen = !isNavOpen;
}

function manejarClicNotificacion(event) {
  // No procesar si se hizo clic en el botón de descartar
  if (event.target.classList.contains("boton-descartar")) {
    return;
  }

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
      try {
        return JSON.parse(text);
      } catch (error) {
        console.error(`Respuesta no válida del servidor: ${text}`);
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

  if (notificaciones.length === 0) return;

  const ids = [];
  const tipos = [];

  notificaciones.forEach((notificacion) => {
    notificacion.classList.add("vista");
    ids.push(notificacion.dataset.id);
    tipos.push(notificacion.dataset.tipo);

    marcarNotificacionComoVista(
      notificacion.dataset.id,
      notificacion.dataset.tipo
    );
  });

  actualizarBadgeNotificaciones();
}

function descartarNotificacion(event, id, tipo) {
  event.stopPropagation();

  const notificacion = event.target.closest(".contenedor-notificacion");

  // Ocultar inmediatamente la notificación con animación
  notificacion.style.opacity = "0";
  notificacion.style.transition = "opacity 0.3s ease";

  setTimeout(() => {
    // CORRECCIÓN: No usar display:none sino remover el elemento del DOM
    const grupoFecha = notificacion.closest(".grupo-fecha");
    notificacion.remove(); // Eliminar la notificación del DOM

    // CORRECCIÓN: Verificar si era la última notificación de su grupo
    // Ahora contamos correctamente las notificaciones visibles en el grupo
    const notificacionesRestantes = grupoFecha.querySelectorAll(
      ".contenedor-notificacion"
    );

    if (notificacionesRestantes.length === 0) {
      // Si no quedan notificaciones en el grupo, ocultar el grupo
      grupoFecha.style.display = "none";
    }

    // CORRECCIÓN: Verificar si no quedan más notificaciones
    // Contamos todos los grupos de fechas visibles
    const gruposVisibles = document.querySelectorAll(
      ".grupo-fecha:not([style*='display: none'])"
    );

    if (gruposVisibles.length === 0) {
      // Si no quedan grupos visibles, mostrar el mensaje de "No hay notificaciones"
      const sinNotificaciones = document.createElement("div");
      sinNotificaciones.className = "mensaje-sin-notificaciones";
      sinNotificaciones.innerHTML = `
        <div class="info-notificacion">
          <div class="descripcion">No hay nuevas notificaciones</div>
        </div>
      `;

      const sidebar = document.getElementById("mySidebar");
      // Asegurarse de que el mensaje no está ya presente
      const mensajeExistente = sidebar.querySelector(
        ".mensaje-sin-notificaciones"
      );
      if (!mensajeExistente) {
        sidebar.appendChild(sinNotificaciones);
      }
    }
  }, 300);

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
        console.error("Error al descartar notificación:", data.error);
        actualizarNotificaciones(); // Si hay un error, recargar todas las notificaciones
      } else {
        actualizarBadgeNotificaciones();
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      actualizarNotificaciones(); // Si hay un error, recargar todas las notificaciones
    });
}

document.addEventListener("DOMContentLoaded", function () {
  // Ejecutar la actualización de notificaciones inmediatamente al cargar la página
  actualizarNotificaciones();

  // Continuar con las actualizaciones automáticas cada 30 segundos
  setInterval(actualizarNotificaciones, 30000);

  // Añadir event listeners a las notificaciones
  const notificaciones = document.querySelectorAll(".contenedor-notificacion");
  notificaciones.forEach((notificacion) => {
    notificacion.addEventListener("click", manejarClicNotificacion);
  });

  // Añadir event listener al botón de marcar todo como leído
  const botonMarcarLeido = document.querySelector(".marcar-leido");
  if (botonMarcarLeido) {
    botonMarcarLeido.addEventListener("click", marcarNotificacionesComoVistas);
  }

  // Asignar eventos a los botones de descartar
  document.querySelectorAll(".boton-descartar").forEach((boton) => {
    boton.addEventListener("click", function (e) {
      const notificacion = this.closest(".contenedor-notificacion");
      descartarNotificacion(
        e,
        notificacion.dataset.id,
        notificacion.dataset.tipo
      );
    });
  });

  // Asignar evento al icono de notificaciones
  const notificationIcon = document.getElementById("notification-icon");
  if (notificationIcon) {
    notificationIcon.addEventListener("click", function (e) {
      e.stopPropagation();
      toggleNav();
    });
  }
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

// Asignar evento al icono de notificaciones
document.addEventListener("DOMContentLoaded", function () {
  const notificationIcon = document.getElementById("notification-icon");
  if (notificationIcon) {
    notificationIcon.addEventListener("click", function (event) {
      event.stopPropagation();
      toggleNav();
    });
  }
});

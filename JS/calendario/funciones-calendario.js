// Declaramos las funciones en el ámbito global
let updateCalendar, expandEvents, showEventsModal, openEventModal, closeModal;

document.addEventListener("DOMContentLoaded", function () {
  const monthYearDisplay = document.getElementById("monthYearDisplay");
  const monthYearPicker = document.getElementById("monthYearPicker");
  const leftArrow = document.querySelector(".arrow.left");
  const rightArrow = document.querySelector(".arrow.right");
  let currentMonth = new Date().getMonth() + 1;
  let currentYear = new Date().getFullYear();

  const monthNames = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];

  // Función para cerrar el selector
  function closeMonthYearPicker() {
    monthYearPicker.style.display = "none";
  }

  // Función para abrir el selector con animación
  function openMonthYearPicker() {
    monthYearPicker.style.display = "inline-block";
    monthYearPicker.classList.remove("fadeOut");
    monthYearPicker.classList.add("fadeIn");
    monthYearPicker.focus();
  }

  // Función para cerrar el selector con animación
  function closeMonthYearPicker() {
    monthYearPicker.classList.remove("fadeIn");
    monthYearPicker.classList.add("fadeOut");
    setTimeout(() => {
      monthYearPicker.style.display = "none";
      monthYearPicker.classList.remove("fadeOut");
    }, 300); // Este tiempo debe coincidir con la duración de la animación en CSS
  }

  monthYearDisplay.addEventListener("click", (e) => {
    e.stopPropagation();
    openMonthYearPicker();
  });

  document.addEventListener("click", (e) => {
    if (e.target !== monthYearPicker && e.target !== monthYearDisplay) {
      closeMonthYearPicker();
    }
  });

  monthYearPicker.addEventListener("change", () => {
    const [year, month] = monthYearPicker.value.split("-");
    updateCalendar(parseInt(month), parseInt(year));
    closeMonthYearPicker();
  });

  updateCalendar = function (month, year) {
    fetch(`./calendario.php?month=${month}&year=${year}&ajax=true`)
      .then((response) => response.text())
      .then((html) => {
        document.querySelector(".calendar").innerHTML = html;
        monthYearDisplay.textContent = new Date(year, month - 1).toLocaleString(
          "es-ES",
          { month: "long", year: "numeric" }
        );
        updateEventListeners();
        highlightCurrentDay(month, year);
        filterEvents();
      });
  };

  function highlightCurrentDay(month, year) {
    const today = new Date();
    if (today.getMonth() + 1 === month && today.getFullYear() === year) {
      const currentDay = today.getDate();
      const dayElement = document.querySelector(
        `.calendar-table td:not(:empty):nth-child(7n+${
          currentDay % 7 || 7
        }):nth-of-type(${Math.ceil(currentDay / 7)})`
      );
      if (dayElement) {
        dayElement.classList.add("current-day");
      }
    }
  }

  function updateEventListeners() {
    document.querySelectorAll(".event-indicator").forEach((indicator) => {
      indicator.addEventListener("click", (e) => {
        const eventId = e.target.getAttribute("data-event-id");
        openEventModal(eventId);
      });
    });

    document.querySelectorAll(".event-more").forEach((moreLink) => {
      moreLink.addEventListener("click", (e) => {
        e.preventDefault();
        const date = e.target.getAttribute("data-date");
        expandEvents(date, e.target);
      });
    });
  }

  expandEvents = function (date, moreLink) {
    fetch(
      `./functions/calendario/obtener-eventos-calendario.php?date=${date}&user_id=${userId}&limit=0`
    )
      .then((response) => response.json())
      .then((events) => {
        showEventsModal(date, events);
      })
      .catch((error) => {
        console.error("Error al cargar los eventos:", error);
        alert(
          "Hubo un error al cargar los eventos. Por favor, intenta de nuevo más tarde."
        );
      });
  };

  showEventsModal = function (date, events) {
    const modal = document.getElementById("eventsModal");
    const modalContent = modal.querySelector(".modal-content");
    const modalTitle = modal.querySelector(".modal-title");
    const modalBody = modal.querySelector(".modal-body");

    // Convertir la fecha al formato deseado
    const dateObj = new Date(date);
    const formattedDate = dateObj.toLocaleDateString("es-ES", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });

    modalTitle.textContent = `Eventos para ${formattedDate}`;
    modalBody.innerHTML =
      events.length === 0
        ? "<p>No hay eventos para este día.</p>"
        : events
            .map(
              (event) => `
        <div class="modal-event">
          <h3>${event.Nombre_Evento}</h3>
          <p><strong>Hora:</strong> ${event.Hora_Inicio}</p>
          <p><strong>Etiqueta:</strong> <span class="event-tag">${event.Etiqueta}</span></p>
          <p><strong>Descripción:</strong> ${event.Descripcion_Evento}</p>
        </div>
      `
            )
            .join("");

    modal.style.display = "block";
    // Forzar un reflow antes de añadir la clase 'show'
    void modal.offsetWidth;
    modal.classList.add("show");
  };

  openEventModal = function (eventId) {
    fetch(`./functions/calendario/detalles-eventos.php?event_id=${eventId}`)
      .then(response => response.text())  // Cambia esto de response.json() a response.text()
      .then(text => {
        console.log("Respuesta del servidor:", text);  // Registra la respuesta completa
        try {
          return JSON.parse(text);
        } catch (error) {
          console.error("Error al analizar JSON:", error);
          throw error;
        }
      })
      .then((eventDetails) => {
        document.getElementById("eventTitle").textContent =
          eventDetails.Nombre_Evento;
        document.getElementById("eventTag").textContent = eventDetails.Etiqueta;
        document.getElementById("eventDescription").textContent =
          eventDetails.Descripcion_Evento;
        document.getElementById("eventDate").textContent =
          eventDetails.Fecha_Evento;
        document.getElementById("eventTime").textContent =
          eventDetails.Hora_Inicio;

        const modal = document.getElementById("eventModal");
        modal.style.display = "block";
        setTimeout(() => modal.classList.add("show"), 10);
      })

      .catch(error => {
        console.error("Error fetching event details:", error);
      });
  };

  closeModal = function (modal) {
    modal.classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
    }, 300);
  };

  // Event listeners
  leftArrow.addEventListener("click", () => {
    currentMonth--;
    if (currentMonth === 0) {
      currentMonth = 12;
      currentYear--;
    }
    updateCalendar(currentMonth, currentYear);
  });

  rightArrow.addEventListener("click", () => {
    currentMonth++;
    if (currentMonth === 13) {
      currentMonth = 1;
      currentYear++;
    }
    updateCalendar(currentMonth, currentYear);
  });

  // Modal functionality
  const eventModal = document.getElementById("eventModal");
  const eventsModal = document.getElementById("eventsModal");

  [eventModal, eventsModal].forEach((modal) => {
    const closeBtn = modal.querySelector(".close");
    closeBtn.onclick = () => closeModal(modal);
  });

  window.onclick = (event) => {
    if (event.target === eventModal) closeModal(eventModal);
    if (event.target === eventsModal) closeModal(eventsModal);
  };

  // Inicializar el calendario
  updateCalendar(currentMonth, currentYear);

  //Agregar filtros de etiquetas

  let selectedFilters = new Set();
  let isFilterMenuVisible = false;
  let searchTerm = "";

  function filterEvents() {
    const eventItems = document.querySelectorAll(".event-item");
    const eventIndicators = document.querySelectorAll(".event-indicator");

    // Filtrar los "Eventos próximos"
    eventItems.forEach((item) => {
      const eventTag = item.querySelector(".event-tag").textContent;
      const eventName = item.querySelector("strong").textContent.toLowerCase();

      if (
        (selectedFilters.size === 0 || selectedFilters.has(eventTag)) &&
        eventName.includes(searchTerm.toLowerCase())
      ) {
        item.style.display = "flex";
      } else {
        item.style.display = "none";
      }
    });

    // No filtrar los eventos en los recuadros del calendario
    eventIndicators.forEach((indicator) => {
      indicator.style.display = "block";
    });
  }

  document.querySelector(".search-input").addEventListener("input", (e) => {
    searchTerm = e.target.value;
    filterEvents();
  });

  document.querySelector(".search-icon").addEventListener("click", () => {
    const searchInput = document.querySelector(".search-input");
    searchInput.focus();
  });

  document.querySelector(".list-icon").addEventListener("click", () => {
    const filterMenu = document.querySelector(".filter-menu");
    if (isFilterMenuVisible) {
      filterMenu.classList.remove("show");
      setTimeout(() => {
        filterMenu.style.display = "none";
      }, 200);
    } else {
      filterMenu.style.display = "flex";
      setTimeout(() => {
        filterMenu.classList.add("show");
      }, 10);
    }
    isFilterMenuVisible = !isFilterMenuVisible;
  });

  document.querySelector(".close-filter").addEventListener("click", () => {
    const filterMenu = document.querySelector(".filter-menu");
    filterMenu.classList.remove("show");
    setTimeout(() => {
      filterMenu.style.display = "none";
    }, 200);
    isFilterMenuVisible = false;
  });

  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const filter = e.target.getAttribute("data-filter");
      if (filter === "all") {
        selectedFilters.clear();
        document
          .querySelectorAll(".filter-btn")
          .forEach((b) => b.classList.remove("active"));
        e.target.classList.add("active");
      } else {
        if (selectedFilters.has(filter)) {
          selectedFilters.delete(filter);
          e.target.classList.remove("active");
        } else {
          selectedFilters.add(filter);
          e.target.classList.add("active");
        }
      }
      filterEvents();
    });
  });
});
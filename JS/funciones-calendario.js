document.addEventListener("DOMContentLoaded", function () {
  const monthYearDisplay = document.querySelector(".month-year");
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

  function updateCalendar(month, year) {
    const xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      `calendario.php?month=${month}&year=${year}&ajax=true`,
      true
    );
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        document.querySelector(".calendar").innerHTML = xhr.responseText;
        monthYearDisplay.textContent = `${monthNames[month - 1]} ${year}`;
        updateEventListeners();
        highlightCurrentDay();
      }
    };
    xhr.send();
  }

  function highlightCurrentDay() {
    const today = new Date();
    if (
      today.getMonth() + 1 === currentMonth &&
      today.getFullYear() === currentYear
    ) {
      const currentDay = today.getDate();
      const dayElement = document.querySelector(
        `.calendar-table td:not(:empty):nth-child(${
          ((currentDay + firstDayOfMonth - 1) % 7) + 1
        }):nth-of-type(${
          Math.floor((currentDay + firstDayOfMonth - 1) / 7) + 1
        })`
      );
      if (dayElement) {
        dayElement.classList.add("current-day");
      }
    }
  }

  function updateEventListeners() {
    document.querySelectorAll(".event-indicator").forEach((indicator) => {
      indicator.addEventListener("click", function (e) {
        const eventId = e.target.getAttribute("data-event-id");
        openEventModal(eventId);
      });
    });
  }

  leftArrow.addEventListener("click", function () {
    if (currentMonth === 1) {
      currentMonth = 12;
      currentYear--;
    } else {
      currentMonth--;
    }
    updateCalendar(currentMonth, currentYear);
  });

  rightArrow.addEventListener("click", function () {
    if (currentMonth === 12) {
      currentMonth = 1;
      currentYear++;
    } else {
      currentMonth++;
    }
    updateCalendar(currentMonth, currentYear);
  });

  // Función para abrir el modal y cargar la información del evento
  function openEventModal(eventId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `get_event_details.php?event_id=${eventId}`, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        const eventDetails = JSON.parse(xhr.responseText);
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
        setTimeout(function () {
          modal.classList.add("show");
        }, 10);
      }
    };
    xhr.send();
  }

  // Inicializar el calendario
  updateCalendar(currentMonth, currentYear);

  // Modal functionality
  const modal = document.getElementById("eventModal");
  const span = document.getElementsByClassName("close")[0];

  span.onclick = function () {
    closeModal();
  };

  window.onclick = function (event) {
    if (event.target == modal) {
      closeModal();
    }
  };

  function closeModal() {
    modal.classList.remove("show");
    modal.classList.add("hide");
    setTimeout(function () {
      modal.style.display = "none";
      modal.classList.remove("hide");
    }, 300);
  }
});

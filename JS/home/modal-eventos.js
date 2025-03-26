document.addEventListener('DOMContentLoaded', () => {
    // Crear el modal en el DOM con la estructura utilizada para el modal de eventos
    const modalHTML = `
        <div id="eventModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2 id="eventTitle"></h2>
                </div>
                <div class="modal-body">
                    <div class="event-time">
                        <span id="eventDate"></span> • <span id="eventTime"></span>
                    </div>
                    <div class="event-location">
                        <img src="./Img/Icons/iconos-calendario/etiqueta.png" alt="Icono de etiqueta" class="event-icon">
                        <span id="eventTag"></span>
                    </div>
                    <div class="event-description">
                        <p id="eventDescription"></p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Insertar el modal en el body del documento
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Seleccionar elementos clave del modal
    const modal = document.getElementById('eventModal');
    const closeBtn = modal.querySelector('.close');

    // Evento para cerrar el modal al hacer click en la X
    closeBtn.addEventListener('click', cerrarModalEvento);
    
    // Evento para cerrar el modal al hacer clic fuera del modal
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            cerrarModalEvento();
        }
    });

    // Buscar todos los elementos de eventos en la página
    const eventosItems = document.querySelectorAll('.evento-item');
    console.log('Eventos encontrados:', eventosItems.length);

    eventosItems.forEach(item => {
        // Depuración: Verificar el contenido del atributo de datos
        const eventoAttr = item.getAttribute('data-evento');
        console.log('Atributo de evento:', eventoAttr); // Depuración

        // Agregar eventos de click a cada elemento de evento
        item.addEventListener('click', function() {
            try {
                // Intentar convertir el atributo `data-evento` de JSON a objeto
                const evento = JSON.parse(this.getAttribute('data-evento'));
                console.log('Evento parseado:', evento); // Depuración: Intentar parsear el JSON
                
                abrirModalEvento(evento);
            } catch (error) {
                console.error('Error al parsear el evento:', error);
            }
        });
    });
});

// Función para abrir el modal y mostrar la información del evento
function abrirModalEvento(evento) {
    console.log('Datos completos del evento:', evento); // Depuración
    
    // Seleccionar elementos del modal
    const modal = document.getElementById('eventModal');
    const eventTitle = document.getElementById('eventTitle');
    const eventDate = document.getElementById('eventDate');
    const eventTime = document.getElementById('eventTime');
    const eventTag = document.getElementById('eventTag');
    const eventDescription = document.getElementById('eventDescription');

    // Verificación de seguridad para evitar errores si `evento` es null o undefined
    if (!evento) {
        console.error('Evento es nulo o undefined');
        return;
    }

    // Llenar los elementos del modal con la información del evento
    eventTitle.textContent = evento.titulo || 'Sin título'; // Si no hay título, mostrar 'Sin título'
    eventDate.textContent = evento.fecha_inicio || 'Fecha no disponible'; // Si no hay fecha, mostrar 'Fecha no disponible'
    eventTime.textContent = evento.Hora_Inicio || 'Hora no disponible';
    eventTag.textContent = evento.Etiqueta || 'Sin etiqueta';
    eventDescription.textContent = evento.descripcion || 'Sin descripción';

    // Mostrar el modal
    modal.style.display = 'block';
}

// Función para cerrar el modal
function cerrarModalEvento() {
    const modal = document.getElementById('eventModal');
    modal.style.display = 'none';
}
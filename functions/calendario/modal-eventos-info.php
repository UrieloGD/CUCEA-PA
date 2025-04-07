<!-- Modales -->
<!-- Modal para visualizar detalles del evento -->
<div id="eventModal" class="modal">
    <div class="modal-content-visualizarEvento">
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

<!-- Modal para visualizar todos los eventos -->
<div id="eventsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2 class="modal-title"></h2>
        </div>
        <div class="modal-body"></div>
    </div>
</div>
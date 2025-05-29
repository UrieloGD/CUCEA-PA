<!-- Modal para comentario de rechazo -->
<div id="modal-rechazo" class="modal-rechazo" style="display: none;">
    <div class="modal-content-rechazo">
        <div class="modal-header">
            <h3>Motivo de rechazo</h3>
            <span class="close-rechazo">&times;</span>
        </div>
        <div class="modal-body">
            <p>Por favor, especifica el motivo por el cual se rechaza esta solicitud:</p>
            <textarea id="comentario-rechazo" 
                      placeholder="Escribe aquí el motivo del rechazo..." 
                      maxlength="150" 
                      rows="4"></textarea>
            <div class="contador-caracteres">
                <span id="contador">0</span>/500 caracteres
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancelar">Cancelar</button>
            <button type="button" class="btn-confirmar-rechazo">Confirmar rechazo</button>
        </div>
    </div>
</div>
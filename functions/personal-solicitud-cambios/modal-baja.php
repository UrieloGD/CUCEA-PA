<div id="solicitud-modal-baja-academica" class="modal">
    <div class="modal-content">
        <h2>Profesor con efecto a baja</h2>
        
        <!-- Primera fila -->
        <div class="form-row">
            <div class="form-group">
                <label for="profesion">Profesión</label>
                <select id="profesion" name="profesion">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="profesor">Profesor</option>
                    <!-- Agregar más opciones según necesites -->
                </select>
            </div>
            <div class="form-group">
                <label for="apellido_paterno">Apellido paterno</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno">
            </div>
            <div class="form-group">
                <label for="apellido_materno">Apellido materno</label>
                <input type="text" id="apellido_materno" name="apellido_materno">
            </div>
            <div class="form-group">
                <label for="nombres">Nombre(s)</label>
                <input type="text" id="nombres" name="nombres">
            </div>
            <div class="form-group">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" name="codigo">
            </div>
        </div>

        <!-- Segunda fila -->
        <div class="form-row">
            <div class="form-group large">
                <label for="descripcion">Descripción del puesto que ocupa</label>
                <input type="text" id="descripcion" name="descripcion">
            </div>
            <div class="form-group">
                <label for="clasificacion">Clasificación</label>
                <input type="text" id="clasificacion" name="clasificacion">
            </div>
            <div class="form-group">
                <label for="motivo">Motivo</label>
                <input type="text" id="motivo" name="motivo">
            </div>
            <div class="form-group small">
                <label for="crn">CRN</label>
                <input type="text" id="crn" name="crn">
            </div>
        </div>

        <!-- Tercera fila -->
        <div class="form-row">
            <div class="form-group">
                <label for="fecha_efectos">Queda sin efectos a partir de</label>
                <input type="date" id="fecha_efectos" name="fecha_efectos" value="2025-02-05">
            </div>
            <div class="form-group">
                <label for="oficio_num">Oficio Num.</label>
                <input type="text" id="oficio_num" name="oficio_num" value="SA/CP/0000/25">
            </div>
            <div class="form-group">
                <label for="fecha">Fecha D/M/A</label>
                <input type="date" id="fecha" name="fecha" value="2025-02-05">
            </div>
        </div>
    </div>
</div>
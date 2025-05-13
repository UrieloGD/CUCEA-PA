<!-- modal solicitud baja - modal-baja.php -->
<div id="solicitud-modal-baja-academica" class="modal">
    <div class="modal-content">
        <button class="close-button">&times;</button>
        <form id="form-baja" method="POST" action="procesar_baja.php">
            <h2>Profesor con efecto a baja</h2>
        
            <!-- Primera fila -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nombres">Nombre(s)</label>
                    <input type="text" id="nombres" name="nombres">
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
                    <label for="codigo_prof">Código</label>
                    <input type="text" id="codigo_prof" name="codigo_prof">
                </div>
                <div class="form-group">
                    <label for="profesion">Profesión</label>
                    <select id="profesion" name="profesion">
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="LIC.">LIC.</option>
                        <option value="LIC(A).">LIC(A).</option>
                        <option value="PROF.">PROF.</option>
                        <option value="PROF(A).">PROF(A).</option>
                        <option value="MTRO.">MTRO.</option>
                        <option value="MTRA.">MTRA.</option>
                        <option value="DR.">DR.</option>
                        <option value="DRA.">DRA.</option>
                    </select>
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
                    <input type="date" id="fecha_efectos" name="fecha_efectos">
                </div>
                <div class="form-group">
                    <label for="oficio_num_baja">Oficio Num.</label>
                    <input type="text" id="oficio_num_baja" name="oficio_num_baja" readonly class="form-control">
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha D/M/A</label>
                    <input type="date" id="fecha" name="fecha" readonly value="<?php echo date('Y-m-d'); ?>">
                    </div>
            </div>
            <!-- Botones de acción al final del modal -->
            <div class="contenedor-botones-baja">
                <button type="submit" class="btn-guardar" id="btn-guardar">
                    <i class="fa fa-check-circle"></i>
                    Guardar
                </button>
                <button type="button" class="btn-descartar" id="btn-descartar">
                    <i class="fa fa-times-circle"></i>
                    Descartar
                </button>
            </div>
        </form>
    </div>
</div>


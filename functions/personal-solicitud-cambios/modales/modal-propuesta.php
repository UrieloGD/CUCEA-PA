<div id="solicitud-modal-propuesta-academica" class="modal-propuesta">
    <div class="modal-content-propuesta">
        <button class="close-button">&times;</button>
        <form id="form-propuesta" method="POST" action="procesar_propuesta.php">
            <h2>Propuesta de Profesor</h2>
            
            <!-- Primera fila - Datos del profesor propuesto -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta">
                    <label for="nombres_p">Nombre(s)</label>
                    <input type="text" id="nombres_p" name="nombres_p" maxlength="60" class="text-especial" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_paterno_p">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_p" name="apellido_paterno_p" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_materno_p">Apellido materno</label>
                    <input type="text" id="apellido_materno_p" name="apellido_materno_p" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="codigo_prof_p">Código</label>
                    <input type="text" id="codigo_prof_p" name="codigo_prof_p" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="profesion_p">Profesión</label>
                    <select id="profesion_p" name="profesion_p" required>
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

            <!-- Segunda fila - Fecha y descripción -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta smaller">
                    <label for="dia_p">Día</label>
                    <input type="text" id="dia_p" name="dia_p" maxlength="2" pattern="[0-9]*" required>
                </div>
                <div class="form-group-propuesta smaller">
                    <label for="mes_p">Mes</label>
                    <input type="text" id="mes_p" name="mes_p" maxlength="2" pattern="[0-9]*" required>
                </div>
                <div class="form-group-propuesta smaller">
                    <label for="ano_p">Año</label>
                    <input type="text" id="ano_p" name="ano_p" maxlength="4" pattern="[0-9]*" required>
                </div>
                <div class="form-group-propuesta large">
                    <label for="descripcion_p">Descripción del puesto</label>
                    <input type="text" id="descripcion_p" name="descripcion_p" maxlength="100" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="codigo_puesto_p">Código</label>
                    <input type="text" id="codigo_puesto_p" name="codigo_puesto_p" maxlength="10" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="clasificacion_p">Clasificación</label>
                    <input type="text" id="clasificacion_p" name="clasificacion_p" maxlength="15" required>
                </div>
            </div>

            <!-- Tercera fila - Horas y detalles -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta small">
                    <label for="hrs_semanales">Horas Semanales</label>
                    <input type="number" id="hrs_semanales" name="hrs_semanales" min="0" max="99999" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="categoria">Categoría</label>
                    <input type="text" id="categoria" name="categoria" maxlength="20" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="carrera">Carrera</label>
                    <input type="text" id="carrera" name="carrera" maxlength="50" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="crn_p">CRN</label>
                    <input type="text" id="crn_p" name="crn_p" maxlength="7" pattern="[0-9]*" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="num_puesto">Num. Puesto</label>
                    <input type="number" id="num_puesto" name="num_puesto" min="0" max="99999" required>
                </div>
                <div class="form-group-propuesta smaller">
                    <label for="cargo_atc">Cargo A.T.C.</label>
                    <select id="cargo_atc" name="cargo_atc" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>

            <h3>En sustitución de:</h3>

            <!-- Cuarta fila - Datos del profesor a sustituir -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta">
                    <label for="nombres_sust">Nombre(s)</label>
                    <input type="text" id="nombres_sust" name="nombres_sust" maxlength="60" class="text-especial" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_paterno_sust">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_sust" name="apellido_paterno_sust" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_materno_sust">Apellido materno</label>
                    <input type="text" id="apellido_materno_sust" name="apellido_materno_sust" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-propuesta small">
                    <label for="codigo_prof_sust">Código</label>
                    <input type="text" id="codigo_prof_sust" name="codigo_prof_sust" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="causa">Causa</label>
                    <input type="text" id="causa" name="causa" maxlength="50" required>
                </div>
            </div>

            <!-- Quinta fila - Fechas y oficios -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta">
                    <label for="fecha_inicio">Periodo de asignación desde</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="fecha_fin">Hasta</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>
                <div class="form-group-propuesta">
                    <label for="oficio_num">Oficio Num.</label>
                    <input type="text" id="oficio_num" name="oficio_num" readonly>
                </div>
                <div class="form-group-propuesta laboratorio">
                    <label for="fecha">Fecha D/M/A</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="contenedor-botones-baja">
                <button type="submit" class="boton-guardar" id="btn-guardar">
                    <i class="fa fa-check-circle"></i>
                    Guardar
                </button>
                <button type="button" class="boton-descartar" id="btn-descartar">
                    <i class="fa fa-times-circle"></i>
                    Descartar
                </button>
            </div>
        </form>
    </div>
</div>
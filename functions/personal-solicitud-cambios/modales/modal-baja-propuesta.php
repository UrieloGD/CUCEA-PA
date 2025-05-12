<!-- modal-baja-propuesta.php -->
<div id="solicitud-modal-baja-propuesta" class="modal-baja-propuesta">
    <div class="modal-content-baja-propuesta">
        <button class="close-button">&times;</button>
        <form id="form-baja-propuesta" method="POST">
            <h2>Solicitud de Baja-Propuesta</h2>
            
            <!-- Sección de Baja -->
            <h3>Información de Baja</h3>
            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="nombres_baja">Nombre(s)</label>
                    <input type="text" id="nombres_baja" name="nombres_baja" maxlength="60" class="text-especial" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="apellido_paterno_baja">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_baja" name="apellido_paterno_baja" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="apellido_materno_baja">Apellido materno</label>
                    <input type="text" id="apellido_materno_baja" name="apellido_materno_baja" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="codigo_prof_baja">Código</label>
                    <input type="text" id="codigo_prof_baja" name="codigo_prof_baja" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="profesion_baja">Profesión</label>
                    <select id="profesion_baja" name="profesion_baja" required>
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

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_teoria_baja">Num. Puesto (Teoría)</label>
                    <input type="text" id="num_puesto_teoria_baja" name="num_puesto_teoria_baja" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_practica_baja">Num. Puesto (Práctica)</label>
                    <input type="text" id="num_puesto_practica_baja" name="num_puesto_practica_baja" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="cve_materia_baja">CVE Materia</label>
                    <input type="text" id="cve_materia_baja" name="cve_materia_baja" maxlength="10" required>
                </div>
                <div class="form-group-baja-propuesta large">
                    <label for="nombre_materia_baja">Nombre de la Materia</label>
                    <input type="text" id="nombre_materia_baja" name="nombre_materia_baja" maxlength="100" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="crn_baja">CRN</label>
                    <input type="text" id="crn_baja" name="crn_baja" maxlength="7" pattern="[0-9]*" required>
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_teoria_baja">Hrs/Sem/Mes (Teoría)</label>
                    <input type="number" id="hrs_teoria_baja" name="hrs_teoria_baja" min="0" max="99" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_practica_baja">Hrs/Sem/Mes (Práctica)</label>
                    <input type="number" id="hrs_practica_baja" name="hrs_practica_baja" min="0" max="99" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="carrera_baja">Carrera</label>
                    <input type="text" id="carrera_baja" name="carrera_baja" maxlength="50" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="gdo_gpo_turno_baja">GDO/GPO/TURNO</label>
                    <input type="text" id="gdo_gpo_turno_baja" name="gdo_gpo_turno_baja" maxlength="20" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="tipo_asignacion_baja">Tipo Asignación</label>
                    <input type="text" id="tipo_asignacion_baja" name="tipo_asignacion_baja" maxlength="10" required>
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="sin_efectos_baja">Sin efectos a partir de:</label>
                    <input type="date" id="sin_efectos_baja" name="sin_efectos_baja" required>
                </div>
                <div class="form-group-baja-propuesta large">
                    <label for="motivo_baja">Motivo</label>
                    <select id="motivo_baja" name="motivo_baja" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Renuncia">Renuncia</option>
                        <option value="Licencia">Licencia</option>
                        <option value="Descarga">Descarga</option>
                        <option value="Cancelación de grupo">Cancelación de grupo</option>
                        <option value="Fallecimiento">Fallecimiento</option>
                        <option value="Jubilación/Pensión">Jubilación/Pensión</option>
                        <option value="Incapacidad">Incapacidad</option>
                    </select>
                </div>
            </div>

            <!-- Sección de Propuesta -->
            <h3>Información de Propuesta</h3>
            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="nombres_prop">Nombre(s)</label>
                    <input type="text" id="nombres_prop" name="nombres_prop" maxlength="60" class="text-especial" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="apellido_paterno_prop">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_prop" name="apellido_paterno_prop" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="apellido_materno_prop">Apellido materno</label>
                    <input type="text" id="apellido_materno_prop" name="apellido_materno_prop" maxlength="40" class="text-especial" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="codigo_prof_prop">Código</label>
                    <input type="text" id="codigo_prof_prop" name="codigo_prof_prop" maxlength="10" pattern="[0-9]*" required>
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_teoria_prop">Hrs/Sem/Mes (Teoría)</label>
                    <input type="number" id="hrs_teoria_prop" name="hrs_teoria_prop" min="0" max="99" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_practica_prop">Hrs/Sem/Mes (Práctica)</label>
                    <input type="number" id="hrs_practica_prop" name="hrs_practica_prop" min="0" max="99" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_teoria_prop">Num. Puesto (Teoría)</label>
                    <input type="text" id="num_puesto_teoria_prop" name="num_puesto_teoria_prop" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_practica_prop">Num. Puesto (Práctica)</label>
                    <input type="text" id="num_puesto_practica_prop" name="num_puesto_practica_prop" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="inter_temp_def_prop">Interino/Temporal/Def.</label>
                    <select id="inter_temp_def_prop" name="inter_temp_def_prop" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Interino">Interino</option>
                        <option value="Temporal">Temporal</option>
                        <option value="Definitivo">Definitivo</option>
                    </select>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="tipo_asignacion_prop">Tipo Asignación</label>
                    <input type="text" id="tipo_asignacion_prop" name="tipo_asignacion_prop" maxlength="10" required>
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="periodo_desde_prop">Periodo de asignación desde</label>
                    <input type="date" id="periodo_desde_prop" name="periodo_desde_prop" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="periodo_hasta_prop">Hasta</label>
                    <input type="date" id="periodo_hasta_prop" name="periodo_hasta_prop" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="oficio_num">Oficio Num.</label>
                    <input type="text" id="oficio_num" name="oficio_num" readonly>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="fecha">Fecha D/M/A</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
            </div>

            <div class="contenedor-botones-baja-propuesta">
                <button type="submit" class="btn-guardar" id="btn-guardar-baja-propuesta">
                    <i class="fa fa-check-circle"></i>
                    Guardar
                </button>
                <button type="button" class="btn-descartar" id="btn-descartar-baja-propuesta">
                    <i class="fa fa-times-circle"></i>
                    Descartar
                </button>
            </div>
        </form>
    </div>
</div>
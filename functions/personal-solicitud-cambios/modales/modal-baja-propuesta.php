<!-- modal-baja-propuesta.php -->
<div id="solicitud-modal-baja-propuesta" class="modal-baja-propuesta">
    <div class="modal-content-baja-propuesta">
        <button class="close-button-baja-propuesta">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
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
                    <label for="codigo_prof_baja">Código Profesor</label>
                    <input type="text" id="codigo_prof_baja" name="codigo_prof_baja" maxlength="10" pattern="[0-9]*" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="profesion_baja">Profesión</label>
                    <select id="profesion_baja" name="profesion_baja">
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
                    <input type="text" id="num_puesto_teoria_baja" name="num_puesto_teoria_baja" maxlength="10" pattern="[0-9]*">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_practica_baja">Num. Puesto (Práctica)</label>
                    <input type="text" id="num_puesto_practica_baja" name="num_puesto_practica_baja" maxlength="10" pattern="[0-9]*">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="cve_materia_baja">CVE Materia</label>
                    <input type="text" id="cve_materia_baja" name="cve_materia_baja" maxlength="10">
                </div>
                <div class="form-group-baja-propuesta large">
                    <label for="nombre_materia_baja">Nombre de la Materia</label>
                    <input type="text" id="nombre_materia_baja" name="nombre_materia_baja" maxlength="100">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="crn_baja">CRN</label>
                    <input type="text" id="crn_baja" name="crn_baja" maxlength="7" pattern="[0-9]*">
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_teoria_baja">Hrs/Sem/Mes (Teoría)</label>
                    <input type="number" id="hrs_teoria_baja" name="hrs_teoria_baja" min="0" max="99999">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_practica_baja">Hrs/Sem/Mes (Práctica)</label>
                    <input type="number" id="hrs_practica_baja" name="hrs_practica_baja" min="0" max="99999">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="carrera_baja">Carrera</label>
                    <input type="text" id="carrera_baja" name="carrera_baja" maxlength="50">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="gdo_gpo_turno_baja">GDO/GPO/TURNO</label>
                    <input type="text" id="gdo_gpo_turno_baja" name="gdo_gpo_turno_baja" maxlength="20">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="tipo_asignacion_baja">Tipo Asignación</label>
                    <input type="text" id="tipo_asignacion_baja" name="tipo_asignacion_baja" maxlength="10">
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="sin_efectos_baja">Sin efectos a partir de:</label>
                    <input type="date" id="sin_efectos_baja" name="sin_efectos_baja" required>
                </div>
                <div class="form-group-baja-propuesta large">
                    <label for="motivo_baja">Motivo</label>
                    <input type="text" id="motivo_baja" name="motivo_baja" maxlength="50" required>
                </div>
            </div>

            <!-- Sección de Propuesta -->
            <h3>Información de Propuesta</h3>
            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="nombres_prop">Nombre(s)</label>
                    <input type="text" id="nombres_prop" name="nombres_prop" maxlength="60" class="text-especial">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="apellido_paterno_prop">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_prop" name="apellido_paterno_prop" maxlength="40" class="text-especial">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="apellido_materno_prop">Apellido materno</label>
                    <input type="text" id="apellido_materno_prop" name="apellido_materno_prop" maxlength="40" class="text-especial">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="codigo_prof_prop">Código Profesor</label>
                    <input type="text" id="codigo_prof_prop" name="codigo_prof_prop" maxlength="10" pattern="[0-9]*">
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_teoria_prop">Hrs/Sem/Mes (Teoría)</label>
                    <input type="number" id="hrs_teoria_prop" name="hrs_teoria_prop" min="0" max="99999">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="hrs_practica_prop">Hrs/Sem/Mes (Práctica)</label>
                    <input type="number" id="hrs_practica_prop" name="hrs_practica_prop" min="0" max="99999">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_teoria_prop">Num. Puesto (Teoría)</label>
                    <input type="text" id="num_puesto_teoria_prop" name="num_puesto_teoria_prop" maxlength="10" pattern="[0-9]*">
                </div>
                <div class="form-group-baja-propuesta small">
                    <label for="num_puesto_practica_prop">Num. Puesto (Práctica)</label>
                    <input type="text" id="num_puesto_practica_prop" name="num_puesto_practica_prop" maxlength="10" pattern="[0-9]*">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="inter_temp_def_prop">Interino/Temporal/Def.</label>
                    <select id="inter_temp_def_prop" name="inter_temp_def_prop">
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Interino">Interino</option>
                        <option value="Temporal">Temporal</option>
                        <option value="Definitivo">Definitivo</option>
                    </select>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="tipo_asignacion_prop">Tipo Asignación</label>
                    <input type="text" id="tipo_asignacion_prop" name="tipo_asignacion_prop" maxlength="10">
                </div>
            </div>

            <div class="form-row-baja-propuesta">
                <div class="form-group-baja-propuesta">
                    <label for="periodo_desde_prop">Periodo de asignación desde</label>
                    <input type="date" id="periodo_desde_prop" name="periodo_desde_prop" required>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="periodo_hasta_prop">Hasta</label>
                    <input type="date" id="periodo_hasta_prop" name="periodo_hasta_prop">
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="oficio_num_baja_prop">Oficio Num.</label>
                    <input type="text" id="oficio_num_baja_prop" name="oficio_num_baja_prop" maxlength="15" readonly disabled>
                </div>
                <div class="form-group-baja-propuesta">
                    <label for="fecha">Fecha D/M/A</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
            </div>

            <!-- Sexta fila - Archivos adjuntos -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta large archivo-adjunto-container">
                    <!-- Input para nuevos archivos -->
                    <div id="nuevo-archivo-section">
                        <label for="archivo_adjunto">Archivo adjunto (PDF o Imagen)</label>
                        <input type="file" id="archivo_adjunto" name="archivo_adjunto" accept=".pdf,.jpg,.jpeg,.png,.gif">
                        <small class="file-info">Formatos permitidos: PDF, JPG, PNG. Tamaño máximo: 5MB</small>
                        <div id="preview-container-propuesta" class="preview-container" style="display: none;">
                            <div class="preview-header">
                                <span id="file-name-propuesta"></span>
                                <button type="button" id="remove-file-propuesta" class="remove-file-btn">&times;</button>
                            </div>
                            <div id="file-preview-propuesta"></div>
                        </div>
                    </div>

                    <!-- Visualización de archivo existente -->
                    <div id="existing-archivo-section" class="archivo-adjunto" style="display: none;">
                        <label>Archivo adjunto:</label>
                        <div id="archivo-adjunto-contenido" class="preview-container">
                            <!-- Contenido dinámico se insertará aquí -->
                        </div>
                    </div>
                    
                    <input type="hidden" id="archivo_nombre_existente_propuesta" name="archivo_nombre_existente">
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
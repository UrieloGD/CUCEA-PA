<div id="solicitud-modal-propuesta-academica" class="modal-propuesta">
    <div class="modal-content-propuesta">
        <button class="close-button">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
        <form id="form-propuesta" method="POST" action="procesar_propuesta.php">
            <h2>Propuesta de Profesor</h2>
            
            <!-- Primera fila - Datos del profesor propuesto -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta">
                    <label for="nombres_p">Nombre(s)</label>
                    <input type="text" id="nombres_p" name="nombres_p">
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_paterno_p">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_p" name="apellido_paterno_p">
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_materno_p">Apellido materno</label>
                    <input type="text" id="apellido_materno_p" name="apellido_materno_p">
                </div>
                <div class="form-group-propuesta small">
                    <label for="codigo_prof_p">Código Profesor</label>
                    <input type="text" id="codigo_prof_p" name="codigo_prof_p" class="numeric-only">
                </div>
                <div class="form-group-propuesta">
                    <label for="profesion_p">Profesión</label>
                    <select id="profesion_p" name="profesion_p">
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
                    <select id="dia_p" name="dia_p">
                        <option value="" disabled selected>Día</option>
                        <!-- Los días se llenarán con JavaScript -->
                    </select>
                </div>
                <div class="form-group-propuesta smaller">
                    <label for="mes_p">Mes</label>
                    <select id="mes_p" name="mes_p">
                        <option value="" disabled selected>Mes</option>
                        <!-- Los meses se llenarán con JavaScript -->
                    </select>
                </div>
                <div class="form-group-propuesta smaller">
                    <label for="ano_p">Año</label>
                    <select id="ano_p" name="ano_p">
                        <option value="" disabled selected>Año</option>
                        <!-- Los años se llenarán con JavaScript -->
                    </select>
                </div>
                <div class="form-group-propuesta large">
                    <label for="descripcion_p">Descripción del puesto</label>
                    <input type="text" id="descripcion_p" name="descripcion_p">
                </div>
                <div class="form-group-propuesta small">
                    <label for="codigo_puesto_p">Código del Puesto</label>
                    <input type="text" id="codigo_puesto_p" name="codigo_puesto_p" class="alpha-numeric">
                </div>
                <div class="form-group-propuesta small">
                    <label for="clasificacion_p">Clasificación</label>
                    <input type="text" id="clasificacion_p" name="clasificacion_p" class="alpha-numeric">
                </div>
            </div>

            <!-- Tercera fila - Horas y detalles -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta small">
                    <label for="hrs_semanales">Horas Semanales</label>
                    <input type="text" id="hrs_semanales" name="hrs_semanales"class="numeric-only">
                </div>
                <div class="form-group-propuesta small">
                    <label for="categoria">Categoría</label>
                    <input type="text" id="categoria" name="categoria" class="alpha-numeric">
                </div>
                <div class="form-group-propuesta">
                    <label for="carrera">Carrera</label>
                    <input type="text" id="carrera" name="carrera">
                </div>
                <div class="form-group-propuesta small">
                    <label for="crn_p">CRN</label>
                    <input type="text" id="crn_p" name="crn_p"class="numeric-only">
                </div>
                <div class="form-group-propuesta small">
                    <label for="num_puesto">Num. Puesto</label>
                    <input type="text" id="num_puesto" name="num_puesto"class="numeric-only">
                </div>
                <div class="form-group-propuesta smaller">
                    <label for="cargo_atc">Cargo A.T.C.</label>
                    <select id="cargo_atc" name="cargo_atc">
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
                    <input type="text" id="nombres_sust" name="nombres_sust">
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_paterno_sust">Apellido paterno</label>
                    <input type="text" id="apellido_paterno_sust" name="apellido_paterno_sust">
                </div>
                <div class="form-group-propuesta">
                    <label for="apellido_materno_sust">Apellido materno</label>
                    <input type="text" id="apellido_materno_sust" name="apellido_materno_sust">
                </div>
                <div class="form-group-propuesta small">
                    <label for="codigo_prof_sust">Código Profesor</label>
                    <input type="text" id="codigo_prof_sust" name="codigo_prof_sust" class="numeric-only">
                </div>
                <div class="form-group-propuesta">
                    <label for="causa">Causa</label>
                    <input type="text" id="causa" name="causa">
                </div>
            </div>

            <!-- Quinta fila - Fechas y oficios -->
            <div class="form-row-propuesta">
                <div class="form-group-propuesta">
                    <label for="fecha_inicio">Periodo de asignación desde</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio">
                </div>
                <div class="form-group-propuesta">
                    <label for="fecha_fin">Hasta</label>
                    <input type="date" id="fecha_fin" name="fecha_fin">
                </div>
                <div class="form-group-propuesta">
                    <label for="oficio_num_prop">Oficio Num.</label>
                    <input type="text" id="oficio_num_prop" name="oficio_num_prop" readonly disabled>
                </div>
                <div class="form-group-propuesta laboratorio">
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

            <!-- Botones de acción -->
            <div class="contenedor-botones-baja">
                <button type="submit" class="btn-guardar" id="btn-guardar">
                    <i class="fa fa-check-circle"></i>
                    Guardar
                </button>
                <button type="button" class="btn-descartar" id="btn-descartar-propuesta">
                    <i class="fa fa-times-circle"></i>
                    Descartar
                </button>
            </div>
        </form>
    </div>
</div>
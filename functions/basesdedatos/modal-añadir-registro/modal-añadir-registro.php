<!-- Modal para añadir registros -->
<div id="modal-añadir" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Añadir nuevo registro</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-añadir-registro">
            <div class="form-container">
                <div class="form-section">
                    <h3>Materia</h3>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Ciclo" disabled>
                        <input type="text" placeholder="CRN" disabled>
                        <input type="text" placeholder="CVE Materia" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="ciclo" name="ciclo" placeholder="202520" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                        <input type="text" id="crn" name="crn" placeholder="128633" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="I5095" oninput="this.value = this.value.replace(/[^A-Z0-9]/g, '').slice(0, 5);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Materia" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="materia" name="materia" placeholder="TEORIA Y DESARROLLO ORGANIZACIONAL" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 100);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Nivel" disabled>
                    </div>
                    <div class="form-row">
                        <select id="nivel" name="nivel">
                            <option value="" disabled selected>Seleccione el nivel correspondiente...</option>
                            <option value="licenciatura">LICENCIATURA</option>
                            <option value="tecnico">TECNICO SUP</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Tipo" disabled>
                        <input type="text" placeholder="Nivel tipo" disabled>
                        <input type="text" placeholder="Sección" disabled>
                    </div>
                    <div class="form-row">
                        <select id="tipo" name="tipo">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="p">P</option>
                            <option value="t">T</option>
                        </select>
                        <select id="nivel_tipo" name="nivel_tipo">
                            <option value="" disabled selected>Seleccione la nivel-tipo correspondiente...</option>
                            <option value="BLEARNING">BLEARNING</option>
                            <option value="REINGRESO">REINGRESO</option>
                            <option value="PRIMERO">PRIMERO</option>
                            <option value="TLAQUEP">TLAQUEP</option>
                        </select>
                        <input type="text" id="seccion" name="seccion" placeholder="C01" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 3);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="C. Min" disabled>
                        <input type="text" placeholder="Horas totales" disabled>
                        <input type="text" placeholder="Status" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="c_min" name="c_min" placeholder="15" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                        <input type="text" id="h_totales" name="h_totales" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                        <select id="estatus" name="estatus">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="activar">Activar</option>
                            <option value="inactivar">Inactivar</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Modalidad" disabled>
                    </div>
                    <div class="form-row">
                        <select id="modalidad" name="modalidad">
                            <option value="" disabled selected>Seleccione la modalidad correspondiente...</option>
                            <option value="PRESENCIAL ENRIQUECIDA">PRESENCIAL ENRIQUECIDA</option>
                            <option value="VIRTUAL">VIRTUAL</option>
                            <option value="MIXTA">MIXTA</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Días" class="full-width" disabled>
                    </div>
                    <div class="form-row weekdays">
                        <input type="text" id="l" name="l" placeholder="L" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'L') this.value = '';">
                        <input type="text" id="m" name="m" placeholder="M" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'M') this.value = '';">
                        <input type="text" id="i" name="i" placeholder="I" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'I') this.value = '';">
                        <input type="text" id="j" name="j" placeholder="J" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'J') this.value = '';">
                        <input type="text" id="v" name="v" placeholder="V" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'V') this.value = '';">
                        <input type="text" id="s" name="s" placeholder="S" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'S') this.value = '';">
                        <input type="text" id="d" name="d" placeholder="D" maxlength="1" oninput="this.value = this.value.toUpperCase(); if(this.value != 'D') this.value = '';">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="title_dia_presencial" placeholder="Día presencial" disabled>
                        <input type="text" id="title_dia_virtual" placeholder="Día virtual" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="MIERCOLES" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 10);">
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="LUNES" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 10);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Fecha inicial" disabled>
                        <input type="text" placeholder="Fecha final" disabled>
                    </div>
                    <div class="form-row">
                        <input type="date" id="fecha_inicial" name="fecha_inicial">
                        <input type="date" id="fecha_final" name="fecha_final">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Hora inicial" disabled>
                        <input type="text" placeholder="Hora final" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="1600" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="hora_final" name="hora_final" placeholder="1855" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Módulo" disabled>
                        <input type="text" placeholder="Aula" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="modulo" name="modulo" placeholder="CEDC" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 7);">
                        <input type="text" id="aula" name="aula" placeholder="207" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 6);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Cupo" disabled>
                        <input type="text" placeholder="Examen extraordinario" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="cupo" name="cupo" placeholder="38" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);">
                        <select id="examen_extraordinario" name="examen_extraordinario">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="si">SI</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Observaciones" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Ingrese sus observaciones aquí..." class="full-width" oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '').slice(0, 150);">
                    </div>
                </div>
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Código" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="2537999" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Nombre completo del profesor" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Tipo contrato" disabled>
                        <input type="text" placeholder="Categoría" disabled>
                    </div>
                    <div class="form-row">
                        <select id="tipo_contrato" name="tipo_contrato">
                            <option value="" disabled selected>Seleccione el tipo de contrato correspondiente...</option>
                            <option value="asignatura">Asignatura</option>
                            <option value="cargo">Cargo a Plaza</option>
                            <option value="horas">Horas Definitivas</option>
                        </select>
                        <select id="categoria" name="categoria">
                            <option value="" disabled selected>Seleccione la categoria correspondiente...</option>
                            <optgroup label="PROFESOR DE ASIGNATURA...">
                                <option value="PROFESOR DE ASIGNATURA 'A'">PROFESOR DE ASIGNATURA "A"</option>
                                <option value="PROFESOR DE ASIGNATURA 'B'">PROFESOR DE ASIGNATURA "B"</option>
                            </optgroup>
                            <optgroup label="PROFESOR DOCENTE...">
                                <option value="PROFESOR DOCENTE ASISTENTE 'A'">PROFESOR DOCENTE ASISTENTE "A"</option>
                                <option value="PROFESOR DOCENTE ASISTENTE 'B'">PROFESOR DOCENTE ASISTENTE "B"</option>
                                <option value="PROFESOR DOCENTE ASISTENTE 'C'">PROFESOR DOCENTE ASISTENTE "C"</option>
                                <option value="PROFESOR DOCENTE ASOCIADO 'A'">PROFESOR DOCENTE ASOCIADO "A"</option>
                                <option value="PROFESOR DOCENTE ASOCIADO 'B'">PROFESOR DOCENTE ASOCIADO "B"</option>
                                <option value="PROFESOR DOCENTE ASOCIADO 'C'">PROFESOR DOCENTE ASOCIADO "C"</option>
                                <option value="PROFESOR DOCENTE TITULAR 'A'">PROFESOR DOCENTE TITULAR "A"</option>
                                <option value="PROFESOR DOCENTE TITULAR 'B'">PROFESOR DOCENTE TITULAR "B"</option>
                                <option value="PROFESOR DOCENTE TITULAR 'C'">PROFESOR DOCENTE TITULAR "C"</option>
                            </optgroup>
                            <optgroup label="PROFESOR INVESTIGADOR...">
                                <option value="PROFESOR INVESTIGADOR ASOCIADO 'A'">PROFESOR INVESTIGADOR ASOCIADO "A"</option>
                                <option value="PROFESOR INVESTIGADOR ASOCIADO 'B'">PROFESOR INVESTIGADOR ASOCIADO "B"</option>
                                <option value="PROFESOR INVESTIGADOR ASOCIADO 'C'">PROFESOR INVESTIGADOR ASOCIADO "C"</option>
                                <option value="PROFESOR INVESTIGADOR TITULAR 'A'">PROFESOR INVESTIGADOR TITULAR "A"</option>
                                <option value="PROFESOR INVESTIGADOR TITULAR 'B'">PROFESOR INVESTIGADOR TITULAR "B"</option>
                                <option value="PROFESOR INVESTIGADOR TITULAR 'C'">PROFESOR INVESTIGADOR TITULAR "C"</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Descarga" class="full-width" disabled>
                        <input type="text" placeholder="Código descarga" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <select id="descarga" name="descarga">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="no">NO</option>
                            <option value="ok">OK</option>
                        </select>
                        <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="2967799" class="full-width" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Nombre descarga" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Nombre definitivo" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Horas totales" disabled>
                        <input type="text" placeholder="Titular" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas_totales" name="horas" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                        <select id="titular" name="titular">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="si">SI</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <input type="text" placeholder="Horas" disabled>
                        <input type="text" placeholder="Código dependencia" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas" name="horas" placeholder="2" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);">
                        <input type="text" id="codigo_dependencia" name="codigo_dependencia" placeholder="1110" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" onclick="añadirRegistro()">Guardar</button>
                <button type="button" onclick="cerrarFormularioAñadir()">Descartar</button>
            </div>
        </form>
    </div>
</div>
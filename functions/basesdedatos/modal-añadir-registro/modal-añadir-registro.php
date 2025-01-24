<!-- jQuery (requerido para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                        <span>Ciclo</span>
                        <span>CRN</span>
                        <span>CVE Materia</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="ciclo" name="ciclo" placeholder="202520" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                        <input type="text" id="crn" name="crn" placeholder="128633" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="I5095" oninput="this.value = this.value.replace(/[^A-Z0-9]/g, '').slice(0, 5);">
                    </div>
                    <div class="form-row-titles">
                        <span>Materia</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="materia" name="materia" placeholder="TEORIA Y DESARROLLO ORGANIZACIONAL" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 100);">
                    </div>
                    <div class="form-row-titles">
                        <span>Nivel</span>
                    </div>
                    <div class="form-row">
                        <select id="nivel" name="nivel">
                            <option value="" disabled selected>Seleccione el nivel correspondiente...</option>
                            <option value="licenciatura">LICENCIATURA</option>
                            <option value="tecnico">TECNICO SUP</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span>Tipo</span>
                        <span>Nivel tipo</span>
                        <span>Sección</span>
                    </div>
                    <div class="form-row">
                        <select id="tipo" name="tipo">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="p">P</option>
                            <option value="t">T</option>
                        </select>
                        <select id="nivel_tipo" name="nivel_tipo">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="BLEARNING">BLEARNING</option>
                            <option value="REINGRESO">REINGRESO</option>
                            <option value="PRIMERO">PRIMERO</option>
                            <option value="TLAQUEP">TLAQUEP</option>
                        </select>
                        <input type="text" id="seccion" name="seccion" placeholder="C01" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 15);">
                    </div>
                    <div class="form-row-titles">
                        <span>C. Min</span>
                        <span>Horas totales</span>
                        <span>Status</span>
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
                        <span>Modalidad</span>
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
                        <span>Días</span>
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
                        <span id="title_dia_presencial">Día presencial</span>
                        <span id="title_dia_virtual">Día virtual</span>
                    </div>
                    <div id="presencial-virtual" class="form-row">
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="MIERCOLES" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 10);">
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="LUNES" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 10);">
                    </div>
                    <div id="mixta" class="form-row">
                        <select id="dia_presencial2" name="dia_presencial2">
                            <option value="" disabled>Seleccione el dia presencial...</option>
                            <option id="lun" value="LUNES">LUNES</option>
                            <option id="mar" value="MARTES">MARTES</option>
                            <option id="mie" value="MIERCOLES">MIÉRCOLES</option>
                            <option id="jue" value="JUEVES">JUEVES</option>
                            <option id="vie" value="VIERNES">VIERNES</option>
                            <option id="sab" value="SABADO">SÁBADO</option>
                            <option id="dom" value="DOMINGO">DOMINGO</option>
                        </select>
                        <select id="dia_virtual2" name="dia_virtual2">
                            <option value="" disabled>Seleccione el dia virtual...</option>
                            <option id="lun2" value="LUNES">LUNES</option>
                            <option id="mar2" value="MARTES">MARTES</option>
                            <option id="mie2" value="MIERCOLES">MIÉRCOLES</option>
                            <option id="jue2" value="JUEVES">JUEVES</option>
                            <option id="vie2" value="VIERNES">VIERNES</option>
                            <option id="sab2" value="SABADO">SÁBADO</option>
                            <option id="dom2" value="DOMINGO">DOMINGO</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span>Fecha inicial</span>
                        <span>Fecha final</span>
                    </div>
                    <div class="form-row">
                        <input type="date" id="fecha_inicial" name="fecha_inicial">
                        <input type="date" id="fecha_final" name="fecha_final">
                    </div>
                    <div class="form-row-titles">
                        <span>Hora inicial</span>
                        <span>Hora final</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="1600" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="hora_final" name="hora_final" placeholder="1855" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row-titles">
                        <span>Módulo</span>
                        <span>Aula</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="modulo" name="modulo" placeholder="CEDC" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 7);">
                        <input type="text" id="aula" name="aula" placeholder="207" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 6);">
                    </div>
                    <div class="form-row-titles">
                        <span>Cupo</span>
                        <span>Examen extraordinario</span>
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
                        <span>Observaciones</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Ingrese sus observaciones aquí..." class="full-width" oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '').slice(0, 150);">
                    </div>
                </div>
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row-titles">
                        <span>Código</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="2537999" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <span>Nombre completo del profesor</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <span>Tipo contrato</span>
                        <span>Categoría</span>
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
                        <span>Descarga</span>
                        <span>Código descarga</span>
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
                        <span>Nombre descarga</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <span>Nombre definitivo</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <span>Horas totales</span>
                        <span>Titular</span>
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
                        <span>Horas</span>
                        <span>Código dependencia</span>
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

<script>
$('#nivel').select2();
$('#tipo').select2();
$('#nivel_tipo').select2();
$('#estatus').select2();
$('#examen_extraordinario').select2();
$('#tipo_contrato').select2();
$('#categoria').select2();
$('#descarga').select2();
$('#titular').select2();

// Cambiar color de texto titulo a azul.
$('input, select').on('focus', function() {
    $(this).closest('.form-row').prev('.form-row-titles').find('span').css('color', '#007bff');
});

$('.select2-container').on('focusin', function() {
    $(this).closest('.form-row').prev('.form-row-titles').find('span').css('color', '#007bff');
});

$('input, select').on('blur', function() {
    $(this).closest('.form-row').prev('.form-row-titles').find('span').css('color', '');
});

$('.select2-container').on('focusout', function() {
    $(this).closest('.form-row').prev('.form-row-titles').find('span').css('color', '');
});
</script>

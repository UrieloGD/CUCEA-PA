<!-- jQuery (requerido para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Modal para añadir registros -->
<div id="modal-añadir" class="modal-R">
    <div class="modal-content">
        <span class="close-R" onclick="cerrarFormularioAñadir()">&times;</span>
        <h2>Añadir nuevo registro</h2>
        <hr style="border: 1px solid #0071b0; width: 99%;">
        <form id="form-añadir-registro">
            <div class="form-container">
                <div class="form-section">
                    <h3>Materia</h3>
                    <div class="form-row-titles">
                        <span class="title-ciclo">Ciclo</span>
                        <span class="title-crn">CRN</span>
                        <span class="title-cve">CVE Materia</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="ciclo" name="ciclo" placeholder="202520" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                        <input type="text" id="crn" name="crn" placeholder="128633" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);">
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="I5095" oninput="this.value = this.value.replace(/[^A-Z0-9]/g, '').slice(0, 5);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-materia">Materia</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="materia" name="materia" placeholder="TEORIA Y DESARROLLO ORGANIZACIONAL" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 100);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-nivel">Nivel</span>
                    </div>
                    <div class="form-row">
                        <select id="nivel" name="nivel">
                            <option value="" disabled selected></option>
                            <option value="licenciatura">LICENCIATURA</option>
                            <option value="tecnico">TECNICO SUP</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span class="title-tipo">Tipo</span>
                        <span class="title-nivel_tipo">Nivel tipo</span>
                        <span class="title-seccion">Sección</span>
                    </div>
                    <div class="form-row">
                        <select id="tipo" name="tipo">
                            <option value="" disabled selected></option>
                            <option value="p">P</option>
                            <option value="t">T</option>
                        </select>
                        <select id="nivel_tipo" name="nivel_tipo">
                            <option value="" disabled selected></option>
                            <option value="BLEARNING">BLEARNING</option>
                            <option value="REINGRESO">REINGRESO</option>
                            <option value="PRIMERO">PRIMERO</option>
                            <option value="TLAQUEP">TLAQUEP</option>
                        </select>
                        <input type="text" id="seccion" name="seccion" placeholder="C01" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 15);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-c_min">C. Min</span>
                        <span class="title-h_totales">Horas totales</span>
                        <span class="title-estatus">Status</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="c_min" name="c_min" placeholder="15" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                        <input type="text" id="h_totales" name="h_totales" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                        <select id="estatus" name="estatus">
                            <option value="" disabled selected></option>
                            <option value="activar">Activar</option>
                            <option value="inactivar">Inactivar</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span class="title-modalidad">Modalidad</span>
                    </div>
                    <div class="form-row">
                        <select id="modalidad" name="modalidad">
                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                            <option value="PRESENCIAL ENRIQUECIDA">PRESENCIAL ENRIQUECIDA</option>
                            <option value="VIRTUAL">VIRTUAL</option>
                            <option value="MIXTA">MIXTA</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span class="title-dias">Días</span>
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
                        <span class="title-dia_presencial" id="title_dia_presencial">Día presencial</span>
                        <span class="title-dia_virtual" id="title_dia_virtual">Día virtual</span>
                    </div>
                    <div id="presencial-virtual" class="form-row">
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="MIERCOLES" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 10);">
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="LUNES" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 10);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-dia_presencial2" id="title_dia_presencial2">Día presencial</span>
                        <span class="title-dia_virtual2" id="title_dia_virtual2">Día virtual</span>
                    </div>
                    <div id="mixta" class="form-row">
                        <select id="dia_presencial2" name="dia_presencial2">
                            <option value="" disabled>Seleccione el dia presencial...</option>
                            <option id="lun" value="LUNES">LUNES</option> <!-- backend ids doesn-t exist -->
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
                        <span class="title-fecha_inicial">Fecha inicial</span>
                        <span class="title-fecha_final">Fecha final</span>
                    </div>
                    <div class="form-row">
                        <input type="date" id="fecha_inicial" name="fecha_inicial">
                        <input type="date" id="fecha_final" name="fecha_final">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-hora_inicial">Hora inicial</span>
                        <span class="title-hora_final">Hora final</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="1600" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="hora_final" name="hora_final" placeholder="1855" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-modulo">Módulo</span>
                        <span class="title-aula">Aula</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="modulo" name="modulo" placeholder="CEDC" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 7);">
                        <input type="text" id="aula" name="aula" placeholder="207" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 6);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-cupo">Cupo</span>
                        <span class="title-examen">Examen extraordinario</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="cupo" name="cupo" placeholder="38" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);">
                        <select id="examen_extraordinario" name="examen_extraordinario">
                            <option value="" disabled selected></option>
                            <option value="si">SI</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span class="title-observaciones">Observaciones</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Ingrese sus observaciones aquí..." class="full-width" oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '').slice(0, 150);">
                    </div>
                </div>
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row-titles">
                        <span class="title-codigo">Código</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="2537999" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-nombre_profesor">Nombre completo del profesor</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-contrato">Tipo contrato</span>
                        <span class="title-categoria">Categoría</span>
                    </div>
                    <div class="form-row">
                        <select id="tipo_contrato" name="tipo_contrato">
                            <option value="" disabled selected></option>
                            <option value="asignatura">Asignatura</option>
                            <option value="cargo">Cargo a Plaza</option>
                            <option value="horas">Horas Definitivas</option>
                        </select>
                        <select id="categoria" name="categoria">
                            <option value="" disabled selected></option>
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
                        <span class="title-descarga">Descarga</span>
                        <span class="title-codigo_descarga">Código descarga</span>
                    </div>
                    <div class="form-row">
                        <select id="descarga" name="descarga">
                            <option value="" disabled selected></option>
                            <option value="no">NO</option>
                            <option value="ok">OK</option>
                        </select>
                        <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="2967799" class="full-width" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-nombre_descarga">Nombre descarga</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-nombre_definitivo">Nombre definitivo</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width" oninput="this.value = this.value.replace(/[^A-Z\s]/g, '').slice(0, 70);">
                    </div>
                    <div class="form-row-titles">
                        <span class="title-horas_totales">Horas totales</span>
                        <span class="title-titular">Titular</span>
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas_totales" name="horas" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                        <select id="titular" name="titular">
                            <option value="" disabled selected></option>
                            <option value="si">SI</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div class="form-row-titles">
                        <span class="title-horas">Horas</span>
                        <span class="title-codigo_dependencia">Código dependencia</span>
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
    $('#nivel').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#tipo').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#nivel_tipo').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#estatus').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#examen_extraordinario').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#tipo_contrato').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#categoria').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#descarga').select2({ placeholder: 'Seleccione la opción correspondiente...' });
    $('#titular').select2({ placeholder: 'Seleccione la opción correspondiente...' });

    // Cambiar color de texto titulo a azul.
    // Para ciclo ............................................................
    $('#ciclo').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-ciclo').css('color', '#007bff');
    });
    $('#ciclo').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-ciclo').css('color', '');
    });
    // Para crn ............................................................
    $('#crn').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-crn').css('color', '#007bff');
    });
    $('#crn').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-crn').css('color', '');
    });
    // Para cve ............................................................
    $('#cve_materia').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-cve').css('color', '#007bff');
    });
    $('#cve_materia').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-cve').css('color', '');
    });
    // Para materia ............................................................
    $('#materia').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-materia').css('color', '#007bff');
    });
    $('#materia').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-materia').css('color', '');
    });
    // Para nivel ............................................................
    $('#nivel').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nivel').css('color', '#007bff');
    });
    $('#nivel').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nivel').css('color', '');
    });
    // Para tipo ............................................................
    $('#tipo').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-tipo').css('color', '#007bff');
    });
    $('#tipo').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-tipo').css('color', '');
    });
    // Para nivel_tipo ............................................................
    $('#nivel_tipo').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nivel_tipo').css('color', '#007bff');
    });
    $('#nivel_tipo').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nivel_tipo').css('color', '');
    });
    // Para seccion ............................................................
    $('#seccion').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-seccion').css('color', '#007bff');
    });
    $('#seccion').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-seccion').css('color', '');
    });
    // Para C. min ............................................................
    $('#c_min').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-c_min').css('color', '#007bff');
    });
    $('#c_min').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-c_min').css('color', '');
    });
    // Para horas totales ............................................................
    $('#h_totales').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-h_totales').css('color', '#007bff');
    });
    $('#h_totales').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-h_totales').css('color', '');
    });
    // Para nivel_tipo ............................................................
    $('#estatus').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-estatus').css('color', '#007bff');
    });
    $('#estatus').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-estatus').css('color', '');
    });
    // Para modalidad ............................................................
    $('#modalidad').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-modalidad').css('color', '#007bff');
    });
    $('#modalidad').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-modalidad').css('color', '');
    });
    // Para dias ............................................................
    $('input').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-dias').css('color', '#007bff');
    });
    $('input').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-dias').css('color', '');
    });
    // Para dia_presencial y dia virtual ............................................................
    $('#dia_presencial').on('focus', function() {
        $(this).closest('#presencial-virtual').prev('.form-row-titles').find('.title-dia_presencial').css('color', '#007bff');
    });
    $('#dia_presencial').on('blur', function() {
        $(this).closest('#presencial-virtual').prev('.form-row-titles').find('.title-dia_presencial').css('color', '');
    });
    $('#dia_virtual').on('focus', function() {
        $(this).closest('#presencial-virtual').prev('.form-row-titles').find('.title-dia_virtual').css('color', '#007bff');
    });
    $('#dia_virtual').on('blur', function() {
        $(this).closest('#presencial-virtual').prev('.form-row-titles').find('.title-dia_virtual').css('color', '');
    });
    // Para dia_presencial y dia virtual (caso de mixta)............................................................
    $('#dia_presencial2').on('focus', function() {
        $(this).closest('#mixta').prev('.form-row-titles').find('.title-dia_presencial2').css('color', '#007bff');
    });
    $('#dia_presencial2').on('blur', function() {
        $(this).closest('#mixta').prev('.form-row-titles').find('.title-dia_presencial2').css('color', '');
    });
    $('#dia_virtual2').on('focus', function() {
        $(this).closest('#mixta').prev('.form-row-titles').find('.title-dia_virtual2').css('color', '#007bff');
    });
    $('#dia_virtual2').on('blur', function() {
        $(this).closest('#mixta').prev('.form-row-titles').find('.title-dia_virtual2').css('color', '');
    });
    // Para fecha inicial ............................................................
    $('#fecha_inicial').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-fecha_inicial').css('color', '#007bff');
    });
    $('#fecha_inicial').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-fecha_inicial').css('color', '');
    });
    // Para fecha final ............................................................
    $('#fecha_final').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-fecha_final').css('color', '#007bff');
    });
    $('#fecha_final').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-fecha_final').css('color', '');
    });
    // Para hora inicial ............................................................
    $('#hora_inicial').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-hora_inicial').css('color', '#007bff');
    });
    $('#hora_inicial').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-hora_inicial').css('color', '');
    });
    // Para hora final ............................................................
    $('#hora_final').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-hora_final').css('color', '#007bff');
    });
    $('#hora_final').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-hora_final').css('color', '');
    });
    // Para modulo ............................................................
    $('#modulo').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-modulo').css('color', '#007bff');
    });
    $('#modulo').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-modulo').css('color', '');
    });
    // Para aula ............................................................
    $('#aula').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-aula').css('color', '#007bff');
    });
    $('#aula').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-aula').css('color', '');
    });
    // Para cupo ............................................................
    $('#cupo').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-cupo').css('color', '#007bff');
    });
    $('#cupo').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-cupo').css('color', '');
    });
    // Para examen extraordinario ............................................................
    $('#examen_extraordinario').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-examen').css('color', '#007bff');
    });
    $('#examen_extraordinario').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-examen').css('color', '');
    });
    // Para observaciones ............................................................
    $('#observaciones').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-observaciones').css('color', '#007bff');
    });
    $('#observaciones').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-observaciones').css('color', '');
    });
    // Para codigo_profesor ............................................................
    $('#codigo_profesor').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-codigo').css('color', '#007bff');
    });
    $('#codigo_profesor').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-codigo').css('color', '');
    });
    // Para nombre_profesor ............................................................
    $('#nombre_profesor').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nombre_profesor').css('color', '#007bff');
    });
    $('#nombre_profesor').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nombre_profesor').css('color', '');
    });
    // Para tipo_contrato ............................................................
    $('#tipo_contrato').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-contrato').css('color', '#007bff');
    });
    $('#tipo_contrato').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-contrato').css('color', '');
    });
    // Para categoria ............................................................
    $('#categoria').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-categoria').css('color', '#007bff');
    });
    $('#categoria').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-categoria').css('color', '');
    });
    // Para descarga ............................................................
    $('#descarga').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-descarga').css('color', '#007bff');
    });
    $('#descarga').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-descarga').css('color', '');
    });
    // Para codigo_descarga ............................................................
    $('#codigo_descarga').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-codigo_descarga').css('color', '#007bff');
    });
    $('#codigo_descarga').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-codigo_descarga').css('color', '');
    });
    // Para nombre_descarga ............................................................
    $('#nombre_descarga').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nombre_descarga').css('color', '#007bff');
    });
    $('#nombre_descarga').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nombre_descarga').css('color', '');
    });
    // Para nombre_definitivo ............................................................
    $('#nombre_definitivo').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nombre_definitivo').css('color', '#007bff');
    });
    $('#nombre_definitivo').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-nombre_definitivo').css('color', '');
    });
    // Para horas_totales ............................................................
    $('#horas_totales').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-horas_totales').css('color', '#007bff');
    });
    $('#horas_totales').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-horas_totales').css('color', '');
    });
    // Para titular ............................................................
    $('#titular').on('select2:open', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-titular').css('color', '#007bff');
    });
    $('#titular').on('select2:close', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-titular').css('color', '');
    });
    // Para horas ............................................................
    $('#horas').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-horas').css('color', '#007bff');
    });
    $('#horas').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-horas').css('color', '');
    });
    // Para codigo_dependencia ............................................................
    $('#codigo_dependencia').on('focus', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-codigo_dependencia').css('color', '#007bff');
    });
    $('#codigo_dependencia').on('blur', function() {
        $(this).closest('.form-row').prev('.form-row-titles').find('.title-codigo_dependencia').css('color', '');
    });
</script>

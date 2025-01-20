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
                        <input type="text" id="ciclo" name="ciclo" placeholder="Ciclo" disabled>
                        <input type="text" id="crn" name="crn" placeholder="CRN" disabled>
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="CVE Materia" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="ciclo" name="ciclo" placeholder="202520">
                        <input type="text" id="crn" name="crn" placeholder="128633"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="cve_materia" name="cve_materia" placeholder="I5095">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="materia" name="materia" placeholder="Materia" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="materia" name="materia" placeholder="TEORIA Y DESARROLLO ORGANIZACIONAL" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="nivel" name="nivel" placeholder="Nivel" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nivel" name="nivel" placeholder="LICENCIATURA">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="tipo" name="tipo" placeholder="Tipo" disabled>
                        <input type="text" id="nivel_tipo" name="nivel_tipo" placeholder="Nivel tipo" disabled>
                        <input type="text" id="seccion" name="seccion" placeholder="Sección" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo" name="tipo" placeholder="T">
                        <input type="text" id="nivel_tipo" name="nivel_tipo" placeholder="REINGRESO">
                        <input type="text" id="seccion" name="seccion" placeholder="C01">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="c_min" name="c_min" placeholder="C. Min" disabled>
                        <input type="text" id="h_totales" name="h_totales" placeholder="Horas totales" disabled>
                        <input type="text" id="estatus" name="estatus" placeholder="Status" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="c_min" name="c_min" placeholder="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="h_totales" name="h_totales" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="estatus" name="estatus" placeholder="Activar">
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
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="Día presencial" disabled>
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="Día virtual" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="dia_presencial" name="dia_presencial" placeholder="MIERCOLES">
                        <input type="text" id="dia_virtual" name="dia_virtual" placeholder="LUNES">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="modalidad" name="modalidad" placeholder="Modalidad" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="modalidad" name="modalidad" placeholder="VIRTUAL">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="fecha_inicial" name="fecha_inicial" placeholder="Fecha inicial" disabled>
                        <input type="text" id="fecha_final" name="fecha_final" placeholder="Fecha final" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="fecha_inicial" name="fecha_inicial" placeholder="16/01/2025">
                        <input type="text" id="fecha_final" name="fecha_final" placeholder="15/07/2025">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="Hora inicial" disabled>
                        <input type="text" id="hora_final" name="hora_final" placeholder="Hora final" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="hora_inicial" name="hora_inicial" placeholder="1600" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="hora_final" name="hora_final" placeholder="1855" maxlength="4" minlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="modulo" name="modulo" placeholder="Módulo" disabled>
                        <input type="text" id="aula" name="aula" placeholder="Aula" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="modulo" name="modulo" placeholder="CEDC">
                        <input type="text" id="aula" name="aula" placeholder="207">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="cupo" name="cupo" placeholder="Cupo" disabled>
                        <input type="text" id="examen_extraordinario" name="examen_extraordinario" placeholder="Examen extraordinario" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="cupo" name="cupo" placeholder="38" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="examen_extraordinario" name="examen_extraordinario" placeholder="SI">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Observaciones" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="observaciones" name="observaciones" placeholder="Ingrese sus observaciones aquí..." class="full-width">
                    </div>
                </div>
            <!-- <div class="form-movil"> -->
                <div class="form-section">
                    <h3>Profesorado</h3>
                    <div class="form-row-titles">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="Código" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="codigo_profesor" name="codigo_profesor" placeholder="2537999" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="Nombre completo del profesor" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_profesor" name="nombre_profesor" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="tipo_contrato" name="tipo_contrato" placeholder="Tipo contrato" disabled>
                        <input type="text" id="categoria" name="categoria" placeholder="Categoría" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="tipo_contrato" name="tipo_contrato" placeholder="Cargo a Plaza">
                        <input type="text" id="categoria" name="categoria" placeholder="PROFESOR DE ASIGNATURA 'B'">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="descarga" name="descarga" placeholder="Descarga" class="full-width" disabled>
                        <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="Código descarga" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="descarga" name="descarga" placeholder="OK" class="full-width">
                        <input type="text" id="codigo_descarga" name="codigo_descarga" placeholder="2967799" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="Nombre descarga" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_descarga" name="nombre_descarga" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="Nombre definitivo" class="full-width" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="nombre_definitivo" name="nombre_definitivo" placeholder="NOMBRE NOMBRE APELLIDO APELLIDO" class="full-width">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="horas_totales" name="horas" placeholder="Horas totales" disabled>
                        <input type="text" id="titular" name="titular" placeholder="Titular" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas_totales" name="horas" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="titular" name="titular" placeholder="SI">
                    </div>
                    <div class="form-row-titles">
                        <input type="text" id="horas" name="horas" placeholder="Horas" disabled>
                        <input type="text" id="codigo_dependencia" name="codigo_dependencia" placeholder="Código dependencia" disabled>
                    </div>
                    <div class="form-row">
                        <input type="text" id="horas" name="horas" placeholder="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="text" id="codigo_dependencia" name="codigo_dependencia" placeholder="1110">
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
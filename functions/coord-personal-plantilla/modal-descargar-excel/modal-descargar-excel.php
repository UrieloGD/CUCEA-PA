<div id="modal-descargar" class="modal">
    <div class="modal-content-descarga">
    <span class="close" onclick="cerrarDescargarExcel()">&times;</span>
        <h3>Selecciona las columnas a descargar</h3>
        <div class="select-first">
                <input class="input-check" type="checkbox" id="select-all-main">
                <label for="select-all" class="select-all-div-label">Seleccionar/Deseleccionar todas</label>
        </div>
        <div id="opciones-columnas" class="container-all">
            <div class="select-all-div">
                <div class="columns-container-js">
                    <div class="columns-subcontainer"> <!-- materia -->
                        <div class="label-profesorado">
                            <label class="encabezado-js">Información Básica</label>
                        </div>
                        <div class="select-all-basica">
                            <input class="input-check" type="checkbox" id="select-all-basica">
                            <label for="select-all-basica">Seleccionar/Deseleccionar grupo completo</label>
                        </div>
                        <div class="columns-container-basica">
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-codigo-" name="columnas[]" value="CODIGO">
                                <label for="col-codigo-">CÓDIGO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-paterno-" name="columnas[]" value="PATERNO">
                                <label for="col-paterno-">PATERNO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-materno-" name="columnas[]" value="MATERNO">
                                <label for="col-materno-">MATERNO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-nombres-" name="columnas[]" value="NOMBRES">
                                <label for="col-nombres-">NOMBRES</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-nombre-completo-" name="columnas[]" value="NOMBRE COMPLETO">
                                <label for="col-nombre-completo-">NOMBRE COMPLETO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-sexo-" name="columnas[]" value="SEXO">
                                <label for="col-sexo-">SEXO</label>
                            </div>
                        </div>
                    </div>
                    <div class="columns-subcontainer">
                        <div class="label-profesorado">
                            <label class="encabezado-js">Información Académica</label>
                        </div>
                        <div class="select-all-academica">
                            <input class="input-check" type="checkbox" id="select-all-academica">
                            <label for="select-all-academica">Seleccionar/Deseleccionar grupo completo</label>
                        </div>
                        <div class="columns-container-academica">
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-departamento-" name="columnas[]" value="DEPARTAMENTO">
                                <label for="col-departamento-">DEPARTAMENTO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-categoria-actual-" name="columnas[]" value="CATEGORIA ACTUAL">
                                <label for="col-categoria-actual-">CATEGORIA ACTUAL (1)</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-categoria-actual-dos-" name="columnas[]" value="CATEGORIA ACTUAL DOS">
                                <label for="col-categoria-actual-dos-">CATEGORIA ACTUAL (2)</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-horas-frente-a-grupo-" name="columnas[]" value="HORAS FRENTE A GRUPO">
                                <label for="col-horas-frente-a-grupo-">HORAS FRENTE A GRUPO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-division-" name="columnas[]" value="DIVISION">
                                <label for="col-division-">DIVISIÓN</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-tipo-de-plaza-" name="columnas[]" value="TIPO DE PLAZA">
                                <label for="col-tipo-de-plaza-">TIPO DE PLAZA</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-cat.-act.-" name="columnas[]" value="CAT.ACT.">
                                <label for="col-cat.-act.-">CAT. ACT.</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-carga-horaria-" name="columnas[]" value="CARGA HORARIA">
                                <label for="col-carga-horaria-">CARGA HORARIA</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-horas-definitivas-" name="columnas[]" value="HORAS DEFINITIVAS">
                                <label for="col-horas-definitivas-">HORAS DEFINITIVAS</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-horario-" name="columnas[]" value="HORARIO">
                                <label for="col-horario-">HORARIO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-turno-" name="columnas[]" value="TURNO">
                                <label for="col-turno-">TURNO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-2024a-" name="columnas[]" value="2024A">
                                <label for="col-2024a-">2024A</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="select-all-div">
                <div class="columns-container-js-1">
                    <div class="label-profesorado">
                        <label class="encabezado-js">Información Personal</label>
                    </div>
                    <div class="select-all-personal">
                        <input class="input-check" type="checkbox" id="select-all-personal">
                        <label for="select-all-personal">Seleccionar/Deseleccionar grupo completo</label>
                    </div>
                    <div class="columns-container-personal">
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-telefono-particular-" name="columnas[]" value="TELEFONO PARTICULAR">
                            <label for="col-telefono-particular-">TELEFONO PARTICULAR</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-telefono-particular-o-celular-" name="columnas[]" value="TELEFONO OFICINA O CELULAR">
                            <label for="col-telefono-particular-o-celular-">TELEFONO OFICINA O CELULAR</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-domicilio-" name="columnas[]" value="DOMICILIO">
                            <label for="col-domicilio-">DOMICILIO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-colonia-" name="columnas[]" value="COLONIA">
                            <label for="col-colonia-">COLONIA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-c.p.-" name="columnas[]" value="C.P.">
                            <label for="col-c.p.-">C.P.</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-ciudad-" name="columnas[]" value="CIUDAD">
                            <label for="col-ciudad-">CIUDAD</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-estado-" name="columnas[]" value="ESTADO">
                            <label for="col-estado-">ESTADO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-no.-afil.-i.m.s.s-" name="columnas[]" value="NO. AFIL. I.M.S.S.">
                            <label for="col-no.-afil.-i.m.s.-">NO. AFIL. I.M.S.S.</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-c.u.r.p.-" name="columnas[]" value="C.U.R.P.">
                            <label for="col-c.u.r.p.-">C.U.R.P.</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-rfc-" name="columnas[]" value="RFC">
                            <label for="col-rfc-">RFC</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-lugar-de-nacimiento-" name="columnas[]" value="LUGAR DE NACIMIENTO">
                            <label for="col-lugar-de-nacimiento-">LUGAR DE NACIMIENTO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-estado-civil-" name="columnas[]" value="ESTADO CIVIL">
                            <label for="col-estado-civil-">ESTADO CIVIL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-tipo-de-sangre-" name="columnas[]" value="TIPO DE SANGRE">
                            <label for="col-tipo-de-sangre-">TIPO DE SANGRE</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-feche-nac.-" name="columnas[]" value="FECHA NAC.">
                            <label for="col-fecha-nac.-">FECHA NAC.</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nacionalidad-" name="columnas[]" value="NACIONALIDAD">
                            <label for="col-nacionalidad-">NACIONALIDAD</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-correo-electronico-" name="columnas[]" value="CORREO ELECTRONICO">
                            <label for="col-correo-electronico-">CORREO ELECTRONICO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-correos-oficiales-" name="columnas[]" value="CORREOS OFICIALES">
                            <label for="col-correos-oficiales-">CORREOS OFICIALES</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="select-all-div">
                <div class="columns-container-js-1">
                    <div class="label-profesorado">
                        <label class="encabezado-js">Formación Académica</label>
                    </div>
                    <div class="select-all-academica2">
                        <input class="input-check" type="checkbox" id="select-all-academica2">
                        <label for="select-all-academica2">Seleccionar/Deseleccionar grupo completo</label>
                    </div>
                    <div class="columns-container-academica2">
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-ultimo-grado-" name="columnas[]" value="ULTIMO GRADO">
                            <label for="col-ultimo-grado-">ÚLTIMO GRADO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-programa-" name="columnas[]" value="PROGRAMA">
                            <label for="col-programa-">PROGRAMA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nivel-" name="columnas[]" value="NIVEL">
                            <label for="col-nivel-">NIVEL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-institucion-" name="columnas[]" value="institucion">
                            <label for="col-institucion-">INSTITUCIÓN</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-estado/pais-" name="columnas[]" value="ESTADO/PAIS">
                            <label for="col-estado/pais-">ESTADO/PAÍS</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-anio-" name="columnas[]" value="ANIO">
                            <label for="col-anio-">AÑO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-gdo-exp-" name="columnas[]" value="GDO EXP">
                            <label for="col-gdo-exp-">GDO. EXP.</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-otro-grado-" name="columnas[]" value="OTRO GRADO2">
                            <label for="col-otro-grado-">OTRO GRADO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-programa-" name="columnas[]" value="PROGRAMA2">
                            <label for="col-programa-">PROGRAMA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nivel-" name="columnas[]" value="NIVEL2">
                            <label for="col-nivel-">NIVEL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-institucion-" name="columnas[]" value="INSTITUCION2">
                            <label for="col-institucion-">INSTITUCIÓN</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-estado/pais-" name="columnas[]" value="ESTADO/PAIS2">
                            <label for="col-estado/pais-">ESTADO/PAÍS</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-anio-" name="columnas[]" value="ANIO2">
                            <label for="col-anio-">AÑO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-gdo-exp-" name="columnas[]" value="GDO EXP2">
                            <label for="col-gdo-exp-">GDO. EXP.</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-otro-grado-" name="columnas[]" value="OTRO GRADO3">
                            <label for="col-otro-grado-">OTRO GRADO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-programa-" name="columnas[]" value="PROGRAMA3">
                            <label for="col-PROGRAMA-">PROGRAMA</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-nivel-" name="columnas[]" value="NIVEL3">
                            <label for="col-nivel-">NIVEL</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-institucion-" name="columnas[]" value="INSTITUCION3">
                            <label for="col-institucion-">INSTITUCIÓN</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-estado/pais-" name="columnas[]" value="ESTADO/PAIS3">
                            <label for="col-estado/pais-">ESTADO/PAÍS</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-anio-" name="columnas[]" value="ANIO3">
                            <label for="col-anio-">AÑO</label>
                        </div>
                        <div class="Check-js">
                            <input class="input-check" type="checkbox" id="col-gdo-exp-" name="columnas[]" value="GDO EXP3">
                            <label for="col-gdo-exp-">GDO. EXP.</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="select-all-div">
                <div class="columns-container-js-2">
                    <div class="columns-subcontainer">
                        <div class="label-profesorado">
                            <label class="encabezado-js">Profesores 24-25</label>
                        </div>
                        <div class="select-all-profesores">
                            <input class="input-check" type="checkbox" id="select-all-profesoreS">
                            <label for="select-all-profesores">Seleccionar/Deseleccionar grupo completo</label>
                        </div>
                        <div class="columns-container-profesores">
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-proesde-24/25-" name="columnas[]" value="PROESDE 24-25">
                                <label for="col-proesde-24/25-">PROESDE 24-25</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-a-partir-de-" name="columnas[]" value="A PARTIR DE">
                                <label for="col-a-partir-de-">A PARTIR DE</label>
                            </div>
                        </div>
                    </div>
                    <div class="columns-subcontainer">
                        <div class="label-profesorado">
                            <label class="encabezado-js">Antigüedad</label>
                        </div>
                        <div class="select-all-antiguedad">
                            <input class="input-check" type="checkbox" id="select-all-antiguedad">
                            <label for="select-all-antiguedad">Seleccionar/Deseleccionar grupo completo</label>
                        </div>
                        <div class="columns-container-antiguedad">
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-fecha-de-ingreso-" name="columnas[]" value="FECHA DE INGRESO">
                                <label for="col-fecha-de-ingreso-">FECHA DE INGRESO</label>
                            </div>
                            <div class="Check-js">
                                <input class="input-check" type="checkbox" id="col-antiguedad-" name="columnas[]" value="ANTIGUEDAD">
                                <label for="col-antiguedad-">ANTIGÜEDAD</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fila-botones">
            <button onclick="descargarExcelSeleccionado()">Descargar seleccion</button>
        </div>
    </div>
</div>   
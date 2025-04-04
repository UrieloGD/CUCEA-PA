    <!-- Modal para añadir registros -->
    <div id="modal-añadir" class="modal">
        <div class="modal-content">
            <span class="close-modal-anadir" onclick="cerrarFormularioAñadir()">&times;</span>
            <h2>Registrar nuevo profesor</h2>
            <hr style="border: 1px solid #0071b0; width: 99%;">
            <form id="form-añadir-registro">
                <div class="form-container">
                    <div class="form-section-anadir">
                        <div class="form-movil-anadir">
                            <div>
                                <h3>Información Básica</h3>
                            </div>
                            <!-- Código, paterno, materno, nombres, nombre completo -->
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <div class="form-row-titles-anadir">
                                        <span>Código</span>
                                        <span>Paterno</span>
                                        <span>Materno</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="number" id="codigo" name="codigo" placeholder="216899007" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                                        <input type="text" id="paterno" name="paterno" placeholder="CAMPOS">
                                        <input type="text" id="materno" name="materno" placeholder="MUÑOZ">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Nombre</span>
                                        <span>Nombre Completo</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <input type="text" id="nombre" name="nombre" placeholder="ÁNGEL RAFAEL">
                                        <input type="text" id="completo" name="completo" placeholder="ÁNGEL RAFAEL CAMPOS MUÑOZ">
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Información Académica</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <!-- Datos, Categoria actúal, horas frente a grupo, tipo de plaza, carga horaria, udg virtual cit otro centro, turno, departamento,
                                    categoria actúal 2, división, cat. act., horas definitivas, horario -->
                                    <div class="form-row-titles-anadir">
                                        <span>Departamento</span>
                                        <span>Datos</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <select name="departamento" id="departamento">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="ADMINISTRACION">Administración</option>
                                            <option value="AUDITORIA">Auditoría</option>
                                            <option value="CIENCIAS SOCIALES">Ciencias Sociales</option>
                                            <option value="CONTABILIDAD">Contabilidad</option>
                                            <option value="ECONOMIA">Economía</option>
                                            <option value="ESTUDIOS REGIONALES">Estudios Regionales</option>
                                            <option value="FINANZAS">Finanzas</option>
                                            <option value="IMPUESTOS">Impuestos</option>
                                            <option value="MERCADOTECNIA">Mercadotecnia</option>
                                            <option value="METODOS CUANTITATIVOS">Metodos Cuantitativos</option>
                                            <option value="POLITICAS PUBLICAS">Politicas Públicas</option>
                                            <option value="RECURSOS HUMANOS">Recursos Humanos</option>
                                            <option value="SISTEMAS DE INFORMACIÓON">Sistemas de Información</option>
                                            <option value="TURISMO R. Y S.">Turismo R. y S.</option>
                                            <option value="OTRO">Otro</option>
                                        </select>
                                        <input type="text" id="datos" name="datos" placeholder="UDG VIRTUAL">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Categoria Actual (1)</span>
                                        <span>Categoria Actual (2)</span>
                                        <span>Horas Frente a Grupo</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <select name="categoria_actual" id="categoria_actual">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="ASIGNATURA 'A'">Asignatura "A"</option>
                                            <option value="ASIGNATURA 'B'">Asignatura "B"</option>
                                            <option value="ASISTENTE 'B'">Asistente "B"</option>
                                            <option value="ASISTENTE 'C'">Asistente "C"</option>
                                            <option value="ASOCIADO 'A'">Asociado "A"</option>
                                            <option value="ASOCIADO 'B'">Asociado "B"</option>
                                            <option value="ASOCIADO 'C'">Asociado "C"</option>
                                            <option value="TITULAR 'A'">Titular "A"</option>
                                            <option value="TITULAR 'B'">Titular "B"</option>
                                            <option value="TITULAR 'C'">Titular "C"</option>
                                            <option value="HONORIFICO">Honorifico</option>
                                            <option value="OTRO CENTRO">Otro centro</option>
                                        </select>
                                        <input type="text" id="categoria_actual_dos" name="categoria_actual_dos" placeholder="1002H">
                                        <input type="text" id="horas_frente_grupo" name="horas_frente_grupo" min="0" placeholder="18">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>División</span>
                                        <span>Tipo de Plaza</span>
                                        <span>Cat. Act</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <select name="division" id="division">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="CONTADURIA">Contaduría</option>
                                            <option value="GESTION EMPRESARIAL">Gestión Empresarial</option>
                                            <option value="ECONOMIA Y SOCIEDAD">Economía y Sociedad</option>
                                        </select>
                                        <select name="tipo_plaza" id="tipo_plaza">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="ASIGNATURA">Asignatura</option>
                                            <option value="DOCENTE">Docente</option>
                                            <option value="HONORIFICO">Honorifico</option>
                                            <option value="INVESTIGADOR POR CAMBIO DE FUNCION POR SER S.N.I.">Investigador por cambio de función por ser S.N.I.</option>
                                            <option value="INVESTIGADOR POR NOMBRAMIENTO">Investigador por nombramiento</option>
                                            <option value="TECNICO ACADEMICO">Técnico Académico</option>
                                            <option value="OTRO CENTRO">Otro centro</option>
                                        </select>
                                        <input type="text" id="cat_act" name="cat_act" placeholder="T1">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Carga Horaria</span>
                                        <span>Horas Definitivas</span>
                                        <span>UDG Virtual Cit</span>
                                    </div>
                                    <div class="form-row-anadir-departamento">
                                        <input type="text" id="carga_horaria" name="carga_horaria" placeholder="20H">
                                        <input type="text" id="horas_definitivas" name="horas_definitivas" min="0" placeholder="14">
                                        <input type="text" id="udg_virtual_cit" name="udg_virtual_cit" placeholder="CIT CUCEI">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Horario</span>
                                        <span>Turno</span>
                                        <span>Investigador por nombramiento o cambio de función</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="horario" name="horario" placeholder="L-V 9:00 - 17:00">
                                        <select name="turno" id="turno">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="M">Matutino</option>
                                            <option value="V">Vespertino</option>
                                            <option value="MIXTO">Mixto</option>
                                        </select>
                                        <input type="text" id="investigacion" name="investigacion" placeholder="CAMBIO DE FUNCION">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>SNI</span>
                                        <span>SNI Desde</span>
                                        <span>Cambio Dedicación de plaza</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <select name="sni" id="sni">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="SI">SI</option>
                                            <option value="NO">NO</option>
                                        </select>
                                        <input type="date" id="sni_desde" name="sni_desde">
                                        <div class="form-row-anadir-dates">
                                            <input type="date" id="cambio_dediacion_inicio" name="cambio_dediacion_inicio">
                                            <input type="date" id="cambio_dediacion_final" name="cambio_dediacion_final">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Información Personal</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <!-- Telefono Particular, colonia, estado, rfc, tipo de sangre, correo electronico, telefono de oficina o celular, C.P., 
                                    No. Afil. I.M.S.S., lugar de nacimiento, fecha nac. correos oficiales, domicilio, ciudad, C.u.r.p., estado civil, nacionalidad
                                    -->
                                    <div class="form-row-titles-anadir">
                                        <span>Telefono Particular</span>
                                        <span>Telefono de Oficina</span>
                                        <span>Domicilio</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="telefono_particular" name="telefono_particular" placeholder="3315000099" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 30);">
                                        <input type="text" id="telefono_oficina" name="telefono_oficina" placeholder="3315000099" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 30);">
                                        <input type="text" id="domicilio" name="domicilio" placeholder="CANARIO 512">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Colonia</span>
                                        <span>CP</span>
                                        <span>Ciudad</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="colonia" name="colonia" placeholder="GUADALAJARA">
                                        <input type="text" id="cp" name="cp" min="0" placeholder="45001" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);">
                                        <input type="text" id="ciudad" name="ciudad" placeholder="GUADALAJARA">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Estado</span>
                                        <span>No. I.M.S.S.</span>
                                        <span>CURP</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="estado" name="estado" placeholder="JALISCO">
                                        <input type="text" id="no_imss" name="no_imss" placeholder="0101013578" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                                        <input type="text" id="curp" name="curp" placeholder="AHIF010195HUDJKDA1">  
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>RFC</span>
                                        <span>Lugar de Nacimiento</span>
                                        <span>Estado Civil</span>                                        
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="rfc" name="rfc" placeholder="ISJH010195876">
                                        <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" placeholder="GUADALAJARA, JAL.">
                                        <select name="estado_civil" id="estado_civil">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="SOLTERO">Soltero</option>
                                            <option value="CASADO">Casado</option>
                                            <option value="VIUDO">Viudo</option>
                                            <option value="SEPARADO">Separado</option>
                                            <option value="VIDORCIADO">Divorciado</option>
                                        </select>                                       
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Tipo de Sangre</span>
                                        <span>Fecha de Nacimiento</span>
                                        <span>Edad</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <select name="tipo_sangre" id="tipo_sangre">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B*">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                                        <input type="text" id="edad" name="edad" min="0" placeholder="28" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Nacionalidad</span>
                                        <span>Correo Eletronico</span>
                                        <span>Correos Oficiales</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <input type="text" id="nacionalidad" name="nacionalidad" placeholder="MEXICANA">
                                        <input type="email" id="correo" name="correo" placeholder="ejemplo@cucea.udg.mx">
                                        <input type="email" id="correos_oficiales" name="correos_oficiales" placeholder="angel@cucea.udg.mx">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Formación Académica</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <!-- Último grado, Institución, Gdo. exp., nivel, año, programa, estado/país,
                                    Último grado, Institución, Gdo. exp., nivel, año, programa, estado/país,
                                    Último grado, Institución, Gdo. exp., nivel, año, programa, estado/país
                                    -->
                                    <div class="form-row-titles-anadir">
                                        <span>Último grado</span>
                                        <span>Último grado</span>
                                        <span>Último grado</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="ultimo_grado" name="ultimo_grado" placeholder="D">
                                        <input type="text" id="otro_grado" name="otro_grado" placeholder="D">
                                        <input type="text" id="otro_grado_alternativo" name="otro_grado_alternativo" placeholder="D">    
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Institución</span>
                                        <span>Institución</span>
                                        <span>Institución</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="institucion" name="institucion" placeholder="UNIVERSIDAD DE GUADALAJARA">
                                        <input type="text" id="otro_institucion" name="otro_institucion" placeholder="UNIVERSIDAD DE GUADALAJARA">
                                        <input type="text" id="otro_institucion_alternativo" name="otro_institucion_alternativo" placeholder="UNIVERSIDAD DE GUADALAJARA">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Grado de Experiencia</span>
                                        <span>Grado de Experiencia</span>
                                        <span>Grado de Experiencia</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="gdo_exp" name="gdo_exp" placeholder="CEDULA">
                                        <input type="text" id="otro_gdo_exp" name="otro_gdo_exp" placeholder="ACTA DE TITULACION">
                                        <input type="text" id="otro_gdo_exp_alternativo" name="otro_gdo_exp_alternativo" placehorder="CARTA PASANTE">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Nivel</span>
                                        <span>Nivel</span>
                                        <span>Nivel</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="nivel" name="nivel" placeholder="L">
                                        <input type="text" id="otro_nivel" name="otro_nivel" placeholder="N">
                                        <input type="text" id="otro_nivel_alternativo" name="otro_nivel_alternativo" placeholder="L">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Año</span>
                                        <span>Año</span>
                                        <span>Año</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="date" id="año" name="año">
                                        <input type="date" id="otro_año" name="otro_año">
                                        <input type="date" id="otro_año_alternativo" name="otro_año_alternativo">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Programa</span>
                                        <span>Programa</span>
                                        <span>Programa</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="programa" name="programa" placeholder="INGENIERIA EN COMPUTACION">
                                        <input type="text" id="otro_programa" name="otro_programa" placeholder="ADMINISTRACION">
                                        <input type="text" id="otro_programa_alternativo" name="otro_programa_alternativo" placeholder="CIENCIAS DE LA SALUD">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Estado/País</span>
                                        <span>Estado/País</span>
                                        <span>Estado/País</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <input type="text" id="estado_pais" name="estado_pais" placeholder="JALISCO, MEXICO">                 
                                        <input type="text" id="otro_estado_pais" name="otro_estado_pais" placeholder="MADRID, ESPAÑA">
                                        <input type="text" id="otro_estado_pais_alternativo" name="otro_estado_pais_alternativo" placeholder="MICHOACAN, MEXICO">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Profesores 24-25</h3> 
                            <div class="form-group-anadir">
                                <div class="form-subgroup">
                                    <!-- Proesde 24-25, A partir de  -->
                                    <div class="form-row-titles-anadir">
                                        <span>Proesde</span>
                                        <span>A partir de</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <input type="text" id="proesde" name="proesde" placeholder="9" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                                        <input type="date" id="a_partir_de" name="a_partir_de">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Antigüedad</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <!-- Fecha de ingreso, Antigüedad  -->
                                    <div class="form-row-titles-anadir">
                                        <span>Fecha de Ingreso</span>
                                        <span>Antigüedad</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <input type="date" id="fecha_ingreso" name="fecha_ingreso">
                                        <input type="text" id="Antiguedad" name="Antiguedad" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);">
                                    </div>
                                </div>
                            </div>
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

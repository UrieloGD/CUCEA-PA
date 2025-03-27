    <!-- Modal para añadir registros -->
    <div id="modal-añadir" class="modal">
        <div class="modal-content">
            <span class="close-modal-anadir" onclick="cerrarFormularioAñadir()">&times;</span>
            <h2>Registrar nuevo profesor</h2>
            <hr style="border: 1px solid #0071b0; width: 99%;">
            <form id="form-añadir-registro">
                <div class="form-container">
                    <div class="form-section">
                        <div class="form-movil">
                            <h3>Información Básica</h3>
                            <!-- Código, paterno, materno, nombres, nombre completo -->
                            <div class="form-row-titles">
                                <span>Código</span>
                                <span>Paterno</span>
                                <span>Materno</span>
                            </div>
                            <div class="form-row">
                                <input type="number" id="codigo" name="codigo" placeholder="Código">
                                <input type="text" id="paterno" name="paterno" placeholder="Paterno">
                                <input type="text" id="materno" name="materno" placeholder="Materno">
                            </div>
                            <div class="form-row-titles">
                                <span>Nombre</span>
                                <span>Nombre Completo</span>
                                <span>Edad</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="nombre" name="nombre" placeholder="Nombre">
                                <input type="text" id="completo" name="completo" placeholder="Nombre Completo">
                                <input type="text" id="edad" name="edad" min="0" placeholder="Edad">
                            </div>
                        </div>
                        <div>
                            <h3>Información Académica</h3>
                            <!-- Datos, Categoria actúal, horas frente a grupo, tipo de plaza, carga horaria, udg virtual cit otro centro, turno, departamento,
                             categoria actúal 2, división, cat. act., horas definitivas, horario -->
                             <div class="form-row-titles">
                                <span>Datos</span>
                                <span>Categoria Actual (1)</span>
                                <span>Categoria Actual (2)</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="datos" name="datos" placeholder="Datos">
                                <input type="text" id="categoria_actual" name="categoria_actual" placeholder="Categoría Actual">
                                <input type="text" id="categoria_actual_dos" name="categoria_actual_dos" placeholder="Categoría Actual">
                            </div>
                            <div class="form-row-titles">
                                <span>Horas Frente a Grupo</span>
                                <span>División</span>
                                <span>Tipo de Plaza</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="horas_frente_grupo" name="horas_frente_grupo" min="0" placeholder="Horas Frente a Grupo">
                                <input type="text" id="division" name="division" placeholder="División">
                                <input type="text" id="tipo_plaza" name="tipo_plaza" placeholder="Tipo de Plaza">
                            </div>
                            <div class="form-row-titles">
                                <span>Cat. Act</span>
                                <span>Carga Horaria</span>
                                <span>Horas Definitivas</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="cat_act" name="cat_act" placeholder="CAT_ACT">
                                <input type="text" id="carga_horaria" name="carga_horaria" placeholder="Carga Horaria">
                                <input type="text" id="horas_definitivas" name="horas_definitivas" min="0" placeholder="Horas Definitivas ">
                            </div>
                            <div class="form-row-titles">
                                <span>Horario</span>
                                <span>Turno</span>
                                <span>Departamento</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="horario" name="horario" placeholder="Horario">
                                <input type="text" id="turno" name="turno" placeholder="Turno">
                                <input type="text" id="departamento" name="departamento" placeholder="Departamento">
                            </div>
                            <div class="form-row-titles">
                                <span>UDG Virtual Cit</span>
                                <span>Investigación</span>
                                <span>SNI</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="udg_virtual_cit" name="udg_virtual_cit" placeholder="UDG VIRTUAL CIT OTRO CENTRO">
                                <input type="text" id="investigacion" name="investsigacion" placeholder="Investigación / Nombramiento / Cambio de función">
                                <input type="text" id="sni" name="sni" placeholder="S.N.I">
                            </div>
                            <div class="form-row-titles">
                                <span>SNI Desde</span>
                                <span>Cambio de Dedicación</span>
                            </div>
                            <div class="form-row">
                                <input type="text" id="sni_desde" name="sni_desde" placeholder="SNI Desde">
                                <input type="text" id="cambio_dediacion" name="cambio_dediacion" placeholder="Cambio de Dedicación">
                            </div>                            
                        </div>
                        <div>
                            <h3>Información Personal</h3>
                            <!-- Telefono Particular, colonia, estado, rfc, tipo de sangre, correo electronico, telefono de oficina o celular, C.P., 
                            No. Afil. I.M.S.S., lugar de nacimiento, fecha nac. correos oficiales, domicilio, ciudad, C.u.r.p., estado civil, nacionalidad
                             -->
                            <input type="text" id="telefono_particular" name="telefono_particular" placeholder="Telefono Particular">
                            <input type="text" id="colonia" name="colonia" placeholder="Colonia">
                            <input type="text" id="estado" name="estado" placeholder="Estado">
                            <input type="text" id="rfc" name="rfc" placeholder="RFC">
                            <input type="text" id="tipo_sangre" name="tipo_sangre" placeholder="Tipo de Sangre">
                            <input type="email" id="correo" name="correo" placeholder="Correo Electrónico">
                            <input type="text" id="telefono_oficina" name="telefono_oficina" placeholder="Telefono Oficina o Celuar">
                            <input type="text" id="cp" name="cp" min="0" placeholder="C.P">
                            <input type="text" id="no_imss" name="no_imss" placeholder="NO. AFIL. I.M.S.S.">
                            <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" placeholder="Lugar de Nacimiento">
                            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="Fecha de Nacimiento">
                            <input type="email" id="correos_oficiales" name="correos_oficiales" placeholder="Correos Oficiales">
                            <input type="text" id="domicilio" name="domicilio" placeholder="Domicilio">
                            <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad">
                            <input type="text" id="curp" name="curp" placeholder="C.U.R.P">
                            <input type="text" id="estado_civil" name="estado_civil" placeholder="Estado Civil">
                            <input type="text" id="nacionalidad" name="nacionalidad" placeholder="Nacionalidad">

                        </div>
                        <div>
                            <h3>Formación Académica</h3>
                            <!-- Último grado, Institución, Gdo. exp., nivel, año, programa, estado/país,
                             Último grado, Institución, Gdo. exp., nivel, año, programa, estado/país,
                             Último grado, Institución, Gdo. exp., nivel, año, programa, estado/país
                              -->
                            <input type="text" id="ultimo_grado" name="ultimo_grado" placeholder="Último grado">
                            <input type="text" id="otro_grado" name="otro_grado" placeholder="Último grado">
                            <input type="text" id="otro_grado_alternativo" name="otro_grado_alternativo" placeholder="Último grado">

                            <input type="text" id="institucion" name="institucion" placeholder="Institución">
                            <input type="text" id="otro_institucion" name="otro_institucion" placeholder="Institución">
                            <input type="text" id="otro_institucion_alternativo" name="otro_institucion_alternativo" placeholder="Institución">

                            <input type="text" id="gdo_exp" name="gdo_exp" placeholder="Gdo_Exp">
                            <input type="text" id="otro_gdo_exp" name="otro_gdo_exp" placeholder="Gdo_Exp">
                            <input type="text" id="otro_gdo_exp_alternativo" name="otro_gdo_exp_alternativo" placeholder="Gdo_Exp">

                            <input type="text" id="nivel" name="nivel" placeholder="Nivel">
                            <input type="text" id="otro_nivel" name="otro_nivel" placeholder="Nivel">
                            <input type="text" id="otro_nivel_alternativo" name="otro_nivel_alternativo" placeholder="Nivel">

                            <input type="text" id="año" name="año" min="0" placeholder="Año">
                            <input type="text" id="otro_año" name="otro_año" min="0" placeholder="Año">
                            <input type="text" id="otro_año_alternativo" name="otro_año_alternativo" min="0" placeholder="Año">

                            <input type="text" id="programa" name="programa" placeholder="Programa">
                            <input type="text" id="otro_programa" name="otro_programa" placeholder="Programa">
                            <input type="text" id="otro_programa_alternativo" name="otro_programa_alternativo" placeholder="Programa">

                            <input type="text" id="estado_pais" name="estado_pais" placeholder="Estado/País">                 
                            <input type="text" id="otro_estado_pais" name="otro_estado_pais" placeholder="Estado/País">
                            <input type="text" id="otro_estado_pais_alternativo" name="otro_estado_pais_alternativo" placeholder="Estado/País">
                        </div>
                        <div>
                            <h3>Profesores 24-25</h3> 
                            <!-- Proesde 24-25, A partir de  -->
                            <input type="text" id="proesde" name="proesde" placeholder="Proesde 24-25">
                            <input type="text" id="a_partir_de" name="a_partir_de" placeholder="A Partir De">
                        </div>
                        <div>
                            <h3>Antigüedad</h3>
                            <!-- Fecha de ingreso, Antigüedad  -->
                            <input type="text" id="fecha_ingreso" name="fecha_ingreso" placeholder="Fecha de Ingreso">
                            <input type="text" id="Antiguedad" name="Antiguedad" placeholder="Antigüedad">
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

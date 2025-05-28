<?php 
require_once './functions/error500/manejo-error.php';
?>
<!-- Modal para añadir registros -->
    <div id="modal-añadir" class="modal">
        <div class="modal-content-aRegistro">
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
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <div class="form-row-titles-anadir">
                                        <span>Código</span>
                                        <span>Paterno</span>
                                        <span>Materno</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-1"></span>
                                        <input type="number" id="codigo" name="codigo" placeholder="216899007" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                                        <span class="titulo-reemplazo-2"></span>
                                        <input type="text" id="paterno" name="paterno" placeholder="CAMPOS">
                                        <span class="titulo-reemplazo-3"></span>
                                        <input type="text" id="materno" name="materno" placeholder="MUÑOZ">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Nombre</span>
                                        <span>Nombre Completo</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <span class="titulo-reemplazo-4"></span>
                                        <input type="text" id="nombres" name="nombres" placeholder="ÁNGEL RAFAEL">
                                        <span class="titulo-reemplazo-5"></span>
                                        <input type="text" id="nombre_completo" name="nombre_completo" placeholder="ÁNGEL RAFAEL CAMPOS MUÑOZ">
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Información Académica</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <div class="form-row-titles-anadir">
                                        <span>Departamento</span>
                                        <span>Datos</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <span class="titulo-reemplazo-6"></span>
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
                                        <span class="titulo-reemplazo-7"></span>
                                        <input type="text" id="datos" name="datos" placeholder="UDG VIRTUAL">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Categoria Actual (1)</span>
                                        <span>Categoria Actual (2)</span>
                                        <span>Horas Frente a Grupo</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-8"></span>
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
                                        <span class="titulo-reemplazo-9"></span>
                                        <input type="text" id="categoria_actual_dos" name="categoria_actual_dos" placeholder="1002H">
                                        <span class="titulo-reemplazo-10"></span>
                                        <input type="text" id="horas_frente_grupo" name="horas_frente_grupo" min="0" placeholder="18">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>División</span>
                                        <span>Tipo de Plaza</span>
                                        <span>Cat. Act</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-11"></span>
                                        <select name="division" id="division">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="CONTADURIA">Contaduría</option>
                                            <option value="GESTION EMPRESARIAL">Gestión Empresarial</option>
                                            <option value="ECONOMIA Y SOCIEDAD">Economía y Sociedad</option>
                                        </select>
                                        <span class="titulo-reemplazo-12"></span>
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
                                        <span class="titulo-reemplazo-13"></span>
                                        <input type="text" id="cat_act" name="cat_act" placeholder="T1">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Carga Horaria</span>
                                        <span>Horas Definitivas</span>
                                        <span>UDG Virtual Cit</span>
                                    </div>
                                    <div class="form-row-anadir-departamento">
                                        <span class="titulo-reemplazo-14"></span>
                                        <input type="text" id="carga_horaria" name="carga_horaria" placeholder="20H">
                                        <span class="titulo-reemplazo-15"></span>
                                        <input type="text" id="horas_definitivas" name="horas_definitivas" min="0" placeholder="14">
                                        <span class="titulo-reemplazo-16"></span>
                                        <input type="text" id="udg_virtual_cit" name="udg_virtual_cit" placeholder="CIT CUCEI">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span class="title-bottom">Horario</span>
                                        <span class="title-bottom">Turno</span>
                                        <span>Investigador por nombramiento o cambio de función</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-17"></span>
                                        <input type="text" id="horario" name="horario" placeholder="L-V 9:00 - 17:00">
                                        <span class="titulo-reemplazo-18"></span>
                                        <select name="turno" id="turno">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="M">Matutino</option>
                                            <option value="V">Vespertino</option>
                                            <option value="MIXTO">Mixto</option>
                                        </select>
                                        <span class="titulo-reemplazo-19"></span>
                                        <input type="text" id="investigacion" name="investigacion" placeholder="CAMBIO DE FUNCION">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span class="title-bottom">SNI</span>
                                        <span class="title-bottom">SNI Desde</span>
                                        <span>Cambio Dedicación de plaza</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-20"></span>
                                        <select name="sni" id="sni">
                                            <option value="" disabled selected>Seleccione la opción correspondiente...</option>
                                            <option value="SI">SI</option>
                                            <option value="NO">NO</option>
                                        </select>
                                        <span class="titulo-reemplazo-21"></span>
                                        <input type="date" id="sni_desde" name="sni_desde">
                                        <span class="titulo-reemplazo-22"></span>
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
                                    <div class="form-row-titles-anadir">
                                        <span>Telefono Particular</span>
                                        <span>Telefono de Oficina</span>
                                        <span>Domicilio</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-23"></span>
                                        <input type="text" id="telefono_particular" name="telefono_particular" placeholder="3315000099" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                                        <span class="titulo-reemplazo-24"></span>
                                        <input type="text" id="telefono_oficina" name="telefono_oficina" placeholder="3315000099" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                                        <span class="titulo-reemplazo-25"></span>
                                        <input type="text" id="domicilio" name="domicilio" placeholder="CANARIO 512">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Colonia</span>
                                        <span>CP</span>
                                        <span>Ciudad</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-26"></span>
                                        <input type="text" id="colonia" name="colonia" placeholder="GUADALAJARA">
                                        <span class="titulo-reemplazo-27"></span>
                                        <input type="text" id="cp" name="cp" min="0" placeholder="45001" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);">
                                        <span class="titulo-reemplazo-28"></span>
                                        <input type="text" id="ciudad" name="ciudad" placeholder="GUADALAJARA">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Estado</span>
                                        <span>No. I.M.S.S.</span>
                                        <span>CURP</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-29"></span>
                                        <input type="text" id="estado" name="estado" placeholder="JALISCO">
                                        <span class="titulo-reemplazo-30"></span>
                                        <input type="text" id="no_imss" name="no_imss" placeholder="0101013578" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                                        <span class="titulo-reemplazo-31"></span>
                                        <input type="text" id="curp" name="curp" placeholder="AHIF010195HUDJKDA1">  
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>RFC</span>
                                        <span>Lugar de Nacimiento</span>
                                        <span>Estado Civil</span>                                        
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-32"></span>
                                        <input type="text" id="rfc" name="rfc" placeholder="ISJH010195876">
                                        <span class="titulo-reemplazo-33"></span>
                                        <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" placeholder="GUADALAJARA, JAL.">
                                        <span class="titulo-reemplazo-34"></span>
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
                                        <span class="titulo-reemplazo-35"></span>
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
                                        <span class="titulo-reemplazo-36"></span>
                                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                                        <span class="titulo-reemplazo-37"></span>
                                        <input type="text" id="edad" name="edad" min="0" placeholder="28" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Nacionalidad</span>
                                        <span>Correo Eletronico</span>
                                        <span>Correos Oficiales</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-38"></span>
                                        <input type="text" id="nacionalidad" name="nacionalidad" placeholder="MEXICANA">
                                        <span class="titulo-reemplazo-39"></span>
                                        <input type="email" id="correo" name="correo" placeholder="ejemplo@cucea.udg.mx">
                                        <span class="titulo-reemplazo-40"></span>
                                        <input type="email" id="correos_oficiales" name="correos_oficiales" placeholder="angel@cucea.udg.mx">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Formación Académica</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <div class="form-row-titles-anadir">
                                        <span>Último grado</span>
                                        <span>Último grado</span>
                                        <span>Último grado</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-41"></span>
                                        <input type="text" id="ultimo_grado" name="ultimo_grado" placeholder="D">
                                        <span class="titulo-reemplazo-42"></span>
                                        <input type="text" id="otro_grado" name="otro_grado" placeholder="D">
                                        <span class="titulo-reemplazo-43"></span>
                                        <input type="text" id="otro_grado_alternativo" name="otro_grado_alternativo" placeholder="D">    
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Institución</span>
                                        <span>Institución</span>
                                        <span>Institución</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-44"></span>
                                        <input type="text" id="institucion" name="institucion" placeholder="UNIVERSIDAD DE GUADALAJARA">
                                        <span class="titulo-reemplazo-45"></span>
                                        <input type="text" id="otro_institucion" name="otro_institucion" placeholder="UNIVERSIDAD DE GUADALAJARA">
                                        <span class="titulo-reemplazo-46"></span>
                                        <input type="text" id="otro_institucion_alternativo" name="otro_institucion_alternativo" placeholder="UNIVERSIDAD DE GUADALAJARA">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Grado de Experiencia</span>
                                        <span>Grado de Experiencia</span>
                                        <span>Grado de Experiencia</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-47"></span>
                                        <input type="text" id="gdo_exp" name="gdo_exp" placeholder="CEDULA">
                                        <span class="titulo-reemplazo-48"></span>
                                        <input type="text" id="otro_gdo_exp" name="otro_gdo_exp" placeholder="ACTA DE TITULACION">
                                        <span class="titulo-reemplazo-49"></span>
                                        <input type="text" id="otro_gdo_exp_alternativo" name="otro_gdo_exp_alternativo" placehorder="CARTA PASANTE">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Nivel</span>
                                        <span>Nivel</span>
                                        <span>Nivel</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-50"></span>
                                        <input type="text" id="nivel" name="nivel" placeholder="L">
                                        <span class="titulo-reemplazo-51"></span>
                                        <input type="text" id="otro_nivel" name="otro_nivel" placeholder="N">
                                        <span class="titulo-reemplazo-52"></span>
                                        <input type="text" id="otro_nivel_alternativo" name="otro_nivel_alternativo" placeholder="L">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Año</span>
                                        <span>Año</span>
                                        <span>Año</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-53"></span>
                                        <input type="date" id="año" name="año">
                                        <span class="titulo-reemplazo-54"></span>
                                        <input type="date" id="otro_año" name="otro_año">
                                        <span class="titulo-reemplazo-55"></span>
                                        <input type="date" id="otro_año_alternativo" name="otro_año_alternativo">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Programa</span>
                                        <span>Programa</span>
                                        <span>Programa</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-56"></span>
                                        <input type="text" id="programa" name="programa" placeholder="INGENIERIA EN COMPUTACION">
                                        <span class="titulo-reemplazo-57"></span>
                                        <input type="text" id="otro_programa" name="otro_programa" placeholder="ADMINISTRACION">
                                        <span class="titulo-reemplazo-58"></span>
                                        <input type="text" id="otro_programa_alternativo" name="otro_programa_alternativo" placeholder="CIENCIAS DE LA SALUD">
                                    </div>
                                    <div class="form-row-titles-anadir">
                                        <span>Estado/País</span>
                                        <span>Estado/País</span>
                                        <span>Estado/País</span>
                                    </div>
                                    <div class="form-row-anadir">
                                        <span class="titulo-reemplazo-59"></span>
                                        <input type="text" id="estado_pais" name="estado_pais" placeholder="JALISCO, MEXICO">   
                                        <span class="titulo-reemplazo-60"></span>              
                                        <input type="text" id="otro_estado_pais" name="otro_estado_pais" placeholder="MADRID, ESPAÑA">
                                        <span class="titulo-reemplazo-61"></span>   
                                        <input type="text" id="otro_estado_pais_alternativo" name="otro_estado_pais_alternativo" placeholder="MICHOACAN, MEXICO">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Profesores 24-25</h3> 
                            <div class="form-group-anadir">
                                <div class="form-subgroup">
                                    <div class="form-row-titles-anadir">
                                        <span>Proesde</span>
                                        <span>A partir de</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <span class="titulo-reemplazo-62"></span>   
                                        <input type="text" id="proesde" name="proesde" placeholder="9" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                                        <span class="titulo-reemplazo-63"></span>   
                                        <input type="date" id="a_partir_de" name="a_partir_de">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-movil-anadir">
                            <h3>Antigüedad</h3>
                            <div class="form-group-anadir">
                                <div class="form-subgroup-anadir">
                                    <div class="form-row-titles-anadir">
                                        <span>Fecha de Ingreso</span>
                                        <span>Antigüedad</span>
                                    </div>
                                    <div class="form-row-anadir-dos-D">
                                        <span class="titulo-reemplazo-64"></span>   
                                        <input type="date" id="fecha_ingreso" name="fecha_ingreso">
                                        <span class="titulo-reemplazo-65"></span>   
                                        <input type="text" id="Antiguedad" name="Antiguedad" placeholder="40" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);">
                                        <input type="text" id="papelera" name="papelera" style="display: none" value="ACTIVO">
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

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php'; ?>

<title>Revisión de horas asignadas</title>
<link rel="stylesheet" href="./CSS/horas-comparacion.css">

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Revisión de horas asignadas</h3>
        </div>
    </div>

    <!-- Nuevas horas comparacion -->
    <di class="contenedor-resumen-full">
        <div class="cuadro-resumen">
            <!-- Titulo superior de: <todos los departamentos> -->
            <div class="titulo-resumen">
                <img src="./Img/Icons/iconos-horas-comparacion/cuadro-resumen/titulo_icon.png" alt="Icono de resumen: todos los departamentos">
                <p>Todos los departamentos</p>
            </div>
            <div class="titulo-underline"></div>
            <!-- Seccion de grafica y boton de despliegue del total general de horas.  -->
            <div class="total-general-hrs_container">
                <p class="titulo-total-general">Total general de horas</p>
                <div class="stats-general-hrs">
                    <div class="stats-grafica">
                        <div class="circulo-progreso">
                            <div class="circulo">
                                <span class="porcentaje" id="porcentaje-general">50%</span>
                            </div>
                        </div>
                    </div>
                    <p id="horas-comp-general"> 5,117 / <strong> 10,234 </strong></p>
                    <button class="desglose-button" id="desglose-todos">Desglose</button>
                </div>
            </div>
            <div class="titulo-underline"></div>
            <!-- Seccion de ultimas modificaciones.  -->
            <div class="ultimas-mod_container">
                <p class="titulo-ultimas-mod">Últimas modificaciones</p>
                <table class="tabla-ultimas-mod">
                    <thead class="encabezado-ultimas-mod">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                            <td>Dpto.</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                            <td id="dpto-resumen">Administracion</td>
                        </tr>
                    </tbody>
                    <tbody class="cuerpo-ultimas-mod">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                            <td id="dpto-resumen">Administracion</td>
                        </tr>
                    </tbody>
                    <tbody class="cuerpo-ultimas-mod">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                            <td id="dpto-resumen">Administracion</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Aqui comienza el codigo para desplegables de departamentos.  -->
    <!-- Lado izquierdo de departamentos -->
    <div class="contenedor-dptos-listado">
        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-1', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/administracion.png" alt="Departamento de Administracion">
            </div>
            <div class="titulo-dpto">
                <p>Administración</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-1">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #46ACC2;">
                    <div class="barra-stats-hrs" style="background-color: #46ACC2;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #46ACC2;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Administración" style="background-color: #46ACC2;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-2', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/ciencias_s.png" alt="Departamento de Administracion">
            </div>
            <div class="titulo-dpto">
                <p style="position:relative; bottom:10px;">Ciencias Sociales</p>
                <p style="position:relative; bottom:30px;">y Jurídicas</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-2">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #2ACD9C;">
                    <div class="barra-stats-hrs" style="background-color: #2ACD9C;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #2ACD9C;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Ciencias Sociales" style="background-color: #2ACD9C;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-3', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/economia.png" alt="Departamento de Administracion">
            </div>    
            <div class="titulo-dpto">
                <p>Economía</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-3">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #39C92C;">
                    <div class="barra-stats-hrs" style="background-color: #39C92C;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #39C92C;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Economía" style="background-color: #39C92C;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-4', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/finanzas.png" alt="Departamento de Administracion">
            </div>    
            <div class="titulo-dpto">
                <p>Finanzas</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-4">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #F8775E;">
                    <div class="barra-stats-hrs" style="background-color: #F8775E;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #F8775E;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Finanzas" style="background-color: #F8775E;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-5', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/merc_negocios.png" alt="Departamento de Administracion" style="width: 105%;">
            </div>
            <div class="titulo-dpto">
                <p style="position:relative; bottom:10px;">Mercadotecnia y Negocios</p>
                <p style="position:relative; bottom:30px;">Internacionales</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-5">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #5676A9;">
                    <div class="barra-stats-hrs" style="background-color: #5676A9;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #5676A9;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Mercadotecnia" style="background-color: #5676A9;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-6', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/pale.png" alt="Departamento de Administracion" style="width: 85%;">
            </div>    
            <div class="titulo-dpto">
                <p>PALE</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-6">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #FB4D79;">
                    <div class="barra-stats-hrs" style="background-color: #FB4D79;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #FB4D79;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="PALE" style="background-color: #FB4D79;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-7', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/posgrados.png" alt="Departamento de Administracion">
            </div>    
            <div class="titulo-dpto">
                <p>Posgrados</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-7">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #3B596F;">
                    <div class="barra-stats-hrs" style="background-color: #3B596F;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #3B596F;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Posgrados" style="background-color: #3B596F;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-8', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/sistemas.png" alt="Departamento de Administracion" style="width: 90%;">
            </div>    
            <div class="titulo-dpto">
                <p>Sistemas de la Información</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-8">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #4B8FBC;">
                    <div class="barra-stats-hrs" style="background-color: #4B8FBC;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #4B8FBC;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Sistemas de Información" style="background-color: #4B8FBC;">Desglose</button>
            </div>
        </div>
    </div>

    <!-- Lado derecho de departamentos -->
    <div class="contenedor-dptos-listado">
        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-9', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/auditoria.png" alt="Departamento de Administracion">
            </div>    
            <div class="titulo-dpto">
                <p>Auditoría</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-9">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #F2545E;">
                    <div class="barra-stats-hrs" style="background-color: #F2545E;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #F2545E;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Auditoría" style="background-color: #F2545E;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-10', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/contabilidad.png" alt="Departamento de Administracion">
            </div>
            <div class="titulo-dpto">
                <p>Contabilidad</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-10">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #F6BC55;">
                    <div class="barra-stats-hrs" style="background-color: #F6BC55;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #F6BC55;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Contabilidad" style="background-color: #F6BC55;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-11', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/regionales.png" alt="Departamento de Administracion" style="width: 95%;">
            </div>
            <div class="titulo-dpto">
                <p>Estudios Regionales</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-11">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #B794C6;">
                    <div class="barra-stats-hrs" style="background-color: #B794C6;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #B794C6;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Estudios Regionales" style="background-color: #B794C6;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-12', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/impuestos.png" alt="Departamento de Administracion">
            </div>    
            <div class="titulo-dpto">
                <p>Impuestos</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-12">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #F0914E;">
                    <div class="barra-stats-hrs" style="background-color: #F0914E;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #F0914E;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Impuesto" style="background-color: #F0914E;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-13', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/metodos.png" alt="Departamento de Administracion">
            </div>
            <div class="titulo-dpto">
                <p>Métodos Cuantitativos</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-13">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #F7C941;">
                    <div class="barra-stats-hrs" style="background-color: #F7C941;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #F7C941;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Métodos Cuantitativos" style="background-color: #F7C941;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-14', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono"> 
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/politicas.png" alt="Departamento de Administracion">
            </div>
            <div class="titulo-dpto">
                <p>Políticas Públicas</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-14">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #806D90;">
                    <div class="barra-stats-hrs" style="background-color: #806D90;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #806D90;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Políticas Públicas" style="background-color: #806D90;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-15', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono">    
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/rh.png" alt="Departamento de Administracion">
            </div>
            <div class="titulo-dpto">
                <p>Recursos Humanos</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-15">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #F27BB5;">
                    <div class="barra-stats-hrs" style="background-color: #F27BB5;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #F27BB5;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Recursos Humanos" style="background-color: #F27BB5;">Desglose</button>
            </div>
        </div>

        <!-- banner del departamento -->
        <div class="departamento-contenedor-principal" onclick="mostrarInformacion('contenedor-informacion-16', this.querySelector('.icono-despliegue i'))">
            <div class="espacio-icono">
                <img class="icono-dpto" src="./Img/Icons/iconos-horas-comparacion/departamentos/turismo.png" alt="Departamento de Administracion" style="width: 105%;">
            </div>
            <div class="titulo-dpto">
                <p>Turismo</p>
            </div>
            <div class="icono-despliegue">
                <i id="icono" class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
        </div>
        <!-- Contenedor que se desplegara. -->
        <div class="contenedor-informacion" id="contenedor-informacion-16">
            <div class="hrs-totales-dpto_container">
                <p class="titulo-totales-dpto">Horas totales</p>
                <div class="borde-barra-stats-hrs" style="border: 3px solid #43AB8D;">
                    <div class="barra-stats-hrs" style="background-color: #43AB8D;">
                        <p id="porcentaje-dpto">60%</p>    
                    </div>
                </div>
                <p id="horas-comp-dpto"> 5,117 / <strong> 10,234 </strong></p>
                <div class="titulo-underline" style="width:100%;"></div>
            </div>
            <div class="ultima-mod-dpto_container">
                <p class="titulo-totales-dpto">Última modificación</p>
                <table class="tabla-ultimas-mod-dpto">
                    <thead class="encabezado-ultimas-mod-dpto" style="background-color: #43AB8D;">
                        <tr>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Responsable</td>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-ultimas-mod-dpto">
                        <tr>
                            <td id="fecha-resumen">23/10/24</td>
                            <td id="hora-resumen">13:00</td>
                            <td id="resp-resumen">Rafael Castanedo Escobedo</td>
                        </tr>
                    </tbody>
                </table>
                <button class="desglose-button-dpto" data-departamento="Turismo" style="background-color: #43AB8D;">Desglose</button>
            </div>
        </div>
    </div>

<!-- Modal -->
<div id="modalPersonal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Personal del Departamento</h2>

        <!-- Agregar barra de búsqueda -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Buscar personal...">
        </div>

        <!-- Contenedor con scroll para la tabla -->
        <div class="table-container">
            <table class="tabla-personal">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre Completo</th>
                        <th>Departamento</th>
                        <th>Tipo Plaza</th>
                        <th>Horas Frente Grupo</th>
                        <th>Carga Horaria</th>
                        <th>Horas Definitivas</th>
                        <th>Suma Horas</th>
                        <th>Horas Otros Departamentos</th>
                        <th>Comparación</th>
                    </tr>
                </thead>
                <tbody id="tablaBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener elementos del DOM
        const modal = document.getElementById('modalPersonal');
        const span = document.getElementsByClassName('close')[0];
        const modalTitle = document.getElementById('modalTitle');
        const tablaBody = document.getElementById('tablaBody');
        const departamentoCards = document.querySelectorAll('.desglose-button-dpto');

        // Función para abrir el modal
        function openModal(departamento) {
            modal.style.display = 'block';
            modalTitle.textContent =
                departamento === 'todos'
                    ? 'Personal de Todos los Departamentos'
                    : `Personal del Departamento ${departamento}`;

            // Realiza el fetch para obtener los datos del departamento
            fetchPersonalData(departamento);
        }

        // Función para cerrar el modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Función para mostrar mensaje de error
        function showError(message) {
            tablaBody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: red;">
            ${message}</td></tr>`;
        }

        // Funcionalidad de búsqueda
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tablaBody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Función para determinar la clase de las horas
        function getHorasClass(actual, requerido) {
            actual = parseInt(actual) || 0;
            requerido = parseInt(requerido) || 0;
            
            if (actual === 0 && requerido === 0) return 'horas-cero';
            if (actual < requerido) return 'horas-faltantes';
            if (actual === requerido) return 'horas-correctas';
            return 'horas-excedidas';
        }

        // Función para obtener los datos del personal
        function fetchPersonalData(departamento) {
            // Limpiar la búsqueda al cargar nuevos datos
            searchInput.value = '';

            // Mostrar mensaje de carga
            const colSpan = 10; // Ajustado para las nuevas columnas
            tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">Cargando...</td></tr>`;

            fetch('./functions/horas-comparacion/obtener-personal.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `departamento=${encodeURIComponent(departamento)}`
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', text);
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    if (!Array.isArray(data) || data.length === 0) {
                        tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">No se encontraron datos para mostrar</td></tr>`;
                        return;
                    }

                    //Encabezados de la tabla
                    const thead = document.querySelector('.tabla-personal thead tr');
                    thead.innerHTML = `
                    <th>Código</th>
                    <th>Nombre Completo</th>
                    <th>Departamento</th>
                    <th>Categoría Actual</th>
                    <th>Tipo Plaza</th>
                    <th>Carga Horaria</th>
                    <th>Horas Frente Grupo</th>
                    <th>Horas Definitivas</th>
                    `;

                    tablaBody.innerHTML = ''; // Limpiar tabla

                    data.forEach(persona => {
                        const row = document.createElement('tr');
                        
                        // Función para obtener la clase del departamento
                        function getDepartamentoClass(departamento) {
                            // Normalizar el texto del departamento
                            const normalizedDept = departamento.toLowerCase()
                                .normalize("NFD")
                                .replace(/[\u0300-\u036f]/g, "")
                                .replace(/[^a-z\s]/g, "")
                                .trim();

                                const mapping = {
                                    'administracion': 'administracion',
                                    'programa de aprendizaje de lengua extranjera': 'pale',
                                    'pale': 'pale',
                                    'administracion/programa de aprendizaje de lengua extranjera': 'pale',
                                    'auditoria': 'auditoria',
                                    'secretaria administrativa': 'auditoria',
                                    'ciencias sociales': 'ciencias-sociales',
                                    'politicas publicas': 'politicas-publicas',
                                    'contabilidad': 'contabilidad',
                                    'economia': 'economia',
                                    'estudios regionales': 'estudios-regionales',
                                    'finanzas': 'finanzas',
                                    'impuestos': 'impuestos',
                                    'mercadotecnia': 'mercadotecnia',
                                    'metodos cuantitativos': 'metodos-cuantitativos',
                                    'recursos humanos': 'recursos-humanos',
                                    'sistemas de informacion': 'sistemas-informacion',
                                    'turismo': 'turismo'
                                };

                                // Buscar coincidencia exacta primero
                                for (let [key, value] of Object.entries(mapping)) {
                                    if (normalizedDept === key) {
                                        return value;
                                    }
                                }

                                // Si no hay coincidencia exacta, buscar coincidencia parcial
                                for (let [key, value] of Object.entries(mapping)) {
                                    // Para PALE, buscar coincidencias específicas
                                    if (value === 'pale' && 
                                        (normalizedDept.includes('pale') || 
                                        normalizedDept.includes('programa de aprendizaje') || 
                                        normalizedDept.includes('lengua extranjera'))) {
                                        return 'pale';
                                    }
                                    if (normalizedDept.includes(key)) {
                                        return value;
                                    }
                                }
                            
                            console.log('Departamento no encontrado:', departamento); // Para debug
                            return 'default';
                        }

                        // Función para formatear las horas por departamento
                        function formatearHorasDepartamento(horasString, tipoHoras) {
                            if (!horasString || horasString.trim() === '') {
                                return '';
                            }

                            let formattedHoras = '';
                            let horasArray = horasString.split('\n');

                            for (let i = 0; i < horasArray.length; i++) {
                                let linea = horasArray[i].trim();
                                if (linea === '') continue; // Saltar líneas vacías

                                // Dividir por el primer ':' solamente
                                const [dept, horas] = linea.split(/:(.+)/).map(s => s?.trim()).filter(Boolean);
                                if (!dept || !horas) continue; // Saltar si falta departamento u horas

                                const [horasActual, horasRequeridas] = horas.split('/').map(h => parseInt(h.trim()));

                                // Si las horas son 0/0, no mostrar la burbuja
                                if (horasActual === 0 && horasRequeridas === 0) {
                                    continue;
                                }

                                const horasClass = getHorasClass(horasActual, tipoHoras === 'definitivas' ? parseInt(persona.Horas_definitivas) : parseInt(persona.Horas_frente_grupo));

                                formattedHoras += `
                                    <div class="departamento-tag tag-${getDepartamentoClass(dept)} ${horasClass}" style="position: relative; display: inline-block; max-width: 100%;">
                                        ${dept}: ${horas}
                                    </div>
                                `;
                            }

                            return formattedHoras;
                            row.setAttribute('style', 'white-space: pre-line;');
                        }
                        
                        // Procesar horas frente a grupo
                        const horasCargoActual = persona.suma_cargo_plaza || 0;
                        const horasFrenteRequeridas = persona.Horas_frente_grupo || 0;
                        const claseFrenteGrupo = getHorasClass(horasCargoActual, horasFrenteRequeridas);

                        // Procesar horas definitivas
                        const horasDefActual = persona.suma_horas_definitivas || 0;
                        const horasDefRequeridas = persona.Horas_definitivas || 0;
                        const claseDefinitivas = getHorasClass(horasDefActual, horasDefRequeridas);

                        // Formatear las horas con sus respectivas clases
                        const horasFrenteGrupoHTML = `
                        <div class="tooltip">
                            <span class="${claseFrenteGrupo}">${horasCargoActual}/${horasFrenteRequeridas}</span>
                            <div class="tooltiptext">${persona.horas_cargo_por_departamento || ''}</div>
                        </div>
                        `;

                        const horasDefinitivasHTML = `
                        <div class="tooltip">
                            <span class="${claseDefinitivas}">${horasDefActual}/${horasDefRequeridas}</span>
                            <div class="tooltiptext">${persona.horas_definitivas_por_departamento || ''}</div>
                        </div>
                        `;

                        const horasCargoDeptos = formatearHorasDepartamento(persona.horas_cargo_por_departamento, 'cargo');
                        const horasDefinitivasDeptos = formatearHorasDepartamento(persona.horas_definitivas_por_departamento, 'definitivas');

                        const tdContent = `
                        <td>${persona.Codigo || ''}</td>
                        <td>${persona.Nombre_completo || ''}</td>
                        <td>${persona.Departamento || ''}</td>
                        <td>${persona.Categoria_actual || ''}</td>
                        <td>${persona.Tipo_plaza || ''}</td>
                        <td>${persona.Carga_horaria || ''}</td>
                        <td>${horasFrenteGrupoHTML}</td>
                        <td>${horasDefinitivasHTML}</td>
                        `;
                        
                        row.innerHTML = tdContent;
                        tablaBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center; color: red;">
                ${error.message || 'Error al cargar los datos'}</td></tr>`;
                });
        }

        // Event Listeners
        departamentoCards.forEach(card => {
            card.addEventListener('click', function() {
                const departamento = this.getAttribute('data-departamento'); // Obtener el atributo correctamente
                openModal(departamento); // Pasar el valor al modal
            });
        });

        const botonTodos = document.getElementById('desglose-todos');
            botonTodos.addEventListener('click', function () {
                openModal('todos'); // Llama a la función con 'todos' como parámetro
        });


        span.onclick = closeModal;

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    });

// Función para mostrar el despliegue de información en pagina principal (personal-solicitud-cambios.php)
function mostrarInformacion(contenedorId, icono) {
    const nuevoContenedor = document.getElementById(contenedorId);

    if (nuevoContenedor.style.display === '' || nuevoContenedor.style.display === 'none') {
        nuevoContenedor.style.display = 'block'; // Cambiar a bloque
        icono.classList.add('rotar'); // Rotar el icono
    } else {
        nuevoContenedor.style.display = 'none'; // Ocultar el contenedor
        icono.classList.remove('rotar'); // Quitar la rotación del icono
    }
}
</script>

<?php include("./template/footer.php"); ?>
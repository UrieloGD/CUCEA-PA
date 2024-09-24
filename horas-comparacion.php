<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Revisión de horas asignadas</title>
<link rel="stylesheet" href="./CSS/horas-comparacion.css?=v1.0">
<!-- Oscurecer al aparecer modal. -->
<div class="overlay"></div>

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!-- Encabezado azul que dice: Revisión de horas asignadas. -->
    <div div class="encabezado">
        <div class="titulo-bd">
            <h3>Revisión de horas asignadas</h3>
        </div>
            <!-- Iconos azules del lado derecho del encabezado azul. -->
        <div class="encabezado-derecha">
            <div class="iconos-container">
                <div class="icono-buscador" id="icono-buscador">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
                <div class="icono-buscador" id="icono-buscador">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Los apartados subsecuentes al encabezado azul. -->
    <div class="horas-descripcion">
        <p>Departamento</p>
        <div class="cuadro-azul"></div>
        <p>Valores coincidentes</p>
        <div class="cuadro-rojo"></div>
        <p>Valores no coincidentes</p>
    </div>
        <!-- Barras de Estadisticos. -->
    <div class="contenedor-departamentos">
        <a href="#">
        <div class="color-izquierdo"></div> 
        <div class="color-derecho"></div> 
        <p class="dept">Administración</p>
        <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>        
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Ciencias Sociales y Jurídicas</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Economía</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
    
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Métodos Cuantitativos</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Estudios Regionales INESER</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
    
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Políticas Públicas</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>    
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Sistemas de Información</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Finanzas</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>    
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Auditoría</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div>     
            <p class="dept">Recursos Humanos</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Mercadotecnia y Negocios Internacionales</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Contabilidad</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
        
    <div class="contenedor-departamentos">
        <a href="#">
            <div class="color-izquierdo"></div> 
            <div class="color-derecho"></div> 
            <p class="dept">Turismo, Recreación y Servicios</p>
            <p class="modif">Ultima modificacion: ##/##/#### ##:##</p>
        </a>
    </div>
</div> <!-- cuadro-principal -->

<!-- Revision especifica de horas de profesores -->
 <!-- Contenedor principal de ventana emergente; tambien barra de busqueda, iconos... -->
<form>
<div class="principal">
    <p class="titulo-modal">Estudios Regionales</p>
    <div class="barra-busqueda">
        <div class="iconos-container">
            <div class="icono-barra" id="icono-barra">
                <i class="fa fa-search" aria-hidden="true"></i>
            </div>
            <input class="input-barra-hidden" type="text" placeholder="Buscar">
        </div>
    </div>
    <div class="icono-filtros" id="icono-filtros">
        <i class="fa fa-sliders" aria-hidden="true"></i>
    </div>

    <!-- Contenedor que contiene todo, Contenedor padre. -->
    <div class="cuadro-datos">
        <ul>
            <li class="codigo">Código</li>
            <li class="nombre">Nombre</li>
            <li class="plaza">Tipo de plaza</li>
            <li class="estado">Estado</li>
        </ul>

        <!-- Contenedor para scroll y contenedores horizontales que contienen la informacion. -->
        <div class="scroll-y">

        <div class="todo-datos" id="primer-elemento">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Rafael Castanedo Escobedo</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Fabiola Quezada Limón</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">María Fernanda González Pérez</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">45/40</div>
                <div class="icono-cross">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Juan Carlos Rodríguez García</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Alfredo Trejo Cabrera</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">30/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Ana Sofía Martínez López</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">38/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Luis Eduardo Hernández Castro</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">30/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Claudia Alejandra Ramírez Fernández</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">50/40</div>
                <div class="icono-cross">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Perenganito Ochoa Rodriguez</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">40/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">José Radulfo Ortiz Dominguez</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">37/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Andres Manuel Lopez Obrador</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">70/40</div>
                <div class="icono-cross">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Gustavo Lopez Ortega</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">40/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">41/40</div>
                <div class="icono-cross">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">56/40</div>
                <div class="icono-cross">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">35/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        <div class="todo-datos">
            <p class="datos-codigo">123456789</p>
            <p class="datos-nombre">Mario Castañeda</p>
            <p class="datos-plaza">Lorem ipsum dolor :]</p>
                <div class="datos-estado">39/40</div>
                <div class="icono-check">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
        </div>
        </div> <!-- scroll-y -->
    </div> <!-- cuadro-datos -->

    <!-- Boton final del modal -->
    <div class="modificar-datos-button"><a href="#">Modificar Datos</a></div>

</form>
<!-- Modal: horas-profesores. -->
<script src="./JS/horas-comparacion/horas-profesores.js"></script>
<?php include ("./template/footer.php"); ?>
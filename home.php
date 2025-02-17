<!--header -->
<?php 
session_start();
include './config/db.php';
include './template/header.php';
include './template/navbar.php';
require_once './functions/home/eventos-home.php';
?>
<!-- css del home -->
<title>Home PA</title>
<link rel="stylesheet" href="./CSS/home.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
<!-- contenedor que incluye banner, texto de bienvenida y eventos proximos.-->
<div class="container-banner-bienvenida">
  <!--Cuadro de bienvenida-->
  <div class="bienvenida">
    <h2>
      <?php
      if ($genero == 'Masculino') {
        echo "Bienvenido, ";
      } else if ($genero == 'Femenino') {
        echo "Bienvenida, ";
      } else {
        echo "Bienvenid@, ";
      }
    
      echo $nombre, " ", $apellido;
      ?>
    </h2>
    <p>
    <?php
    
      if ($rol_id == 1) {
        echo "<br>", $nombre_rol, " - ", $_SESSION['Departamentos'];
      } 
      else {
        echo "<br>", $nombre_rol;
      }
      ?>
    </p>
      
    
  </div>
</div>
  
  <div class="accesodirecto-moviles">
    <div class="cuadro-acceso">
      <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png">
      <span>Plantilla</span>
    </div>
    <div class="cuadro-acceso">
      <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png">
      <span>DB</span>
    </div>
    <div class="cuadro-acceso">
      <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png">
      <span>Oferta</span>
    </div>
    <div class="cuadro-acceso">
      <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png">
      <span>Espacios</span>
    </div>
    <div class="cuadro-acceso">
      <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png">
      <span>Guia</span>
    </div>
  </div>

  <div class="container-eventos-progreso">
    <!-- carrusel-banner -->
  <div class="banner">
    <div class="carrusel">
      <div class="diapositiva">
        <img src="./Img/img-home/carrusel-1.webp" alt="Imagen 1">
        </div>
        <div class="diapositiva">
          <img src="https://csd.cucea.udg.mx/sites/default/files/2024-10/banner-inicio-csd-proceso-de-titulacion-1920-x-550-px_2.png" alt="Imagen 2">
        </div>
        <div class="diapositiva">
        <img src="https://www.cucea.udg.mx/sites/default/files/styles/slideshow_principal/public/imagenes/banner/rectangle_400.png?itok=hy_C19tR" alt="Imagen 3">
      </div>
    </div>
        
    <button class="boton-carrusel" id="botonAnterior"><<</button>
    <button class="boton-carrusel" id="botonSiguiente">>></button>
        
    <div class="contenedor-puntos">
      <span class="punto activo"></span>
      <span class="punto"></span>
      <span class="punto"></span>
    </div>
  </div>
  
    <!-- Siguientes eventos de PA -->
    <div class="eventos">
      <div class="siguienteseventos">
        <h3>Siguientes Eventos</h3>
      </div>
    <?php
      $eventos = obtenerEventosProximos($conexion, $_SESSION['Codigo']);
      echo renderizarEventosProximos($eventos);
      ?>
    </div>
  </div>

  <!--Cuadros de navegación-->
  <div class="cuadros-nav">
    <div class="cuadro-ind">
      <?php
        // Redirigir según el rol del usuario
        if ($rol_id == 1) {
          // Si el usuario es jefe de departamento, redirigir a subir plantilla
          if (isset($_SESSION['Nombre_Departamento'])) {
            // Obtener el nombre del departamento desde la sesión
            $nombre_departamento = $_SESSION['Nombre_Departamento'];
            echo "<a href='./plantilla.php'>";
          } else {
            // Manejar el caso en que no se encuentre asociado a ningún departamento
            echo "<a href='#'>";
          }
        } elseif ($rol_id == 2) {
          // Si el usuario es secretaria administrativa, redirigir a plantillasPA
          echo "<a href='./admin-plantilla.php'>";
        } else {
          // Otros roles o manejo de errores aquí
          echo "<a href='./plantilla-CoordPers.php'>";
        }
        ?>
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Plantilla</h4>
        </div>
        <img src="./Img/img-home/plantilla.webp" alt="Imagen de un pasillo arbolado de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <?php
        // Redirigir según el rol del usuario
        if ($rol_id == 1) {
    	    // Si el usuario es jefe de departamento, redirigir a la base de datos del departamento correspondiente
          if (isset($_SESSION['Nombre_Departamento'])) {
            // Obtener el nombre del departamento desde la sesión
            $nombre_departamento = $_SESSION['Nombre_Departamento'];
            echo "<a href='./basesdedatos.php'>";
          } else {
            // Manejar el caso en que no se encuentre asociado a ningún departamento
            echo "<a href='#'>";
          }
        } elseif ($rol_id == 2) {
          // Si el usuario es secretaria administrativa, redirigir al archivo data_departamento.php
          echo "<a href='./admin-data-departamentos.php'>";
        } elseif ($rol_id == 3) {
          // Si el usuario es secretaria administrativa, redirigir al archivo data_departamento.php
          echo "<a href='./basededatos-CoordPers.php'>";
        } else {
          // Otros roles o manejo de errores aquí
          echo "Sin acceso a este apartado";
        }
      ?>
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Bases de datos</h4>
        </div>
        <img src="./Img/img-home/basededatos.webp" alt="imagen de edificios de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./dashboard-oferta.php">
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Oferta</h4>
        </div>
        <img src="./Img/img-home/oferta.webp" alt="Imagen de fondo de CERI" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./espacios.php">
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Espacios</h4>
        </div>
        <img src="./Img/img-home/espacios.webp" alt="Imagen de las letras de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./guiaPA.php">
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Guía</h4>
        </div>
        <img src="./Img/img-home/guia.webp" alt="Imagen de CiberJardin" />
      </a>
    </div>
  </div>

<!-- Script para las funciones del carrusel -->
<script src="./JS/home/carrusel.js"></script>

<?php include './template/footer.php' ?>
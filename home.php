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
<!-- CSS del modal con información de los eventos en pantalla -->
<link rel="stylesheet" href="./CSS/home/modal-eventos.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
  <div class="cuadro-scroll">
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

        if ($rol_id == 1 || $rol_id == 4) {
          echo "<br>", $nombre_rol, " - ", $_SESSION['Departamentos'];
        } else {
          echo "<br>", $nombre_rol;
        }
        ?>
      </p>


    </div>
  </div>

  <div class="container-eventos-progreso">
    <!-- carrusel-banner -->
    <div class="eventos-banner-alineados">
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

        <button class="boton-carrusel" id="botonAnterior">
          <<</button>
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
        if ($rol_id == 1 || $rol_id == 4) {
          // Si el usuario es jefe de departamento, redirigir a subir plantilla
          if (isset($_SESSION['Nombre_Departamento'])) {
            // Obtener el nombre del departamento desde la sesión
            $nombre_departamento = $_SESSION['Nombre_Departamento'];
            echo "<a href='./plantilla.php'>";
          } else {
            // Manejar el caso en que no se encuentre asociado a ningún departamento
            echo "<a href='#'>";
          }
        } elseif ($rol_id == 2 || $rol_id == 0) {
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
        if ($rol_id == 1 || $rol_id == 4) {
          // Si el usuario es jefe de departamento, redirigir a la base de datos del departamento correspondiente
          if (isset($_SESSION['Nombre_Departamento'])) {
            // Obtener el nombre del departamento desde la sesión
            $nombre_departamento = $_SESSION['Nombre_Departamento'];
            echo "<a href='./basesdedatos.php'>";
          } else {
            // Manejar el caso en que no se encuentre asociado a ningún departamento
            echo "<a href='#'>";
          }
        } elseif ($rol_id == 2 || $rol_id == 0) {
          // Si el usuario es secretaria administrativa, redirigir al archivo data_departamento.php
          echo "<a href='./data-departamentos.php'>";
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
        <a href="./calendario.php">
          <div class="overlay">
            <h4 style="text-shadow: 1px 4px 3px black;">Calendario</h4>
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
        <?php
        // Redirigir según el rol del usuario
        if ($rol_id == 1 || $rol_id == 4) {
          // Si el usuario es jefe de departamento, redirigir a subir plantilla
          if (isset($_SESSION['Nombre_Departamento'])) {
            // Obtener el nombre del departamento desde la sesión
            $nombre_departamento = $_SESSION['Nombre_Departamento'];
            echo "<a href='./profesores.php'>";
          } else {
            // Manejar el caso en que no se encuentre asociado a ningún departamento
            echo "<a href='#'>";
          }
        } elseif ($rol_id == 2 || $rol_id == 0) {
          // Si el usuario es secretaria administrativa, redirigir a plantillasPA
          echo "<a href='./profesores.php'>";
        } else {
          // Otros roles o manejo de errores aquí
          echo "<a href='./horas-comparacion.php'>";
        }
        ?>
        <div class="overlay">
          <?php if ($rol_id == 3) {
            echo "<h4 style='text-shadow: 1px 4px 3px black;'>Horas comparación</h4>";
          } else {
            echo "<h4 style='text-shadow: 1px 4px 3px black;'>Profesores</h4>";
          }
          ?>
          <!-- <h4 style="text-shadow: 1px 4px 3px black;">Profesores</h4> -->
        </div>
        <img src="./Img/img-home/plantilla.webp" alt="Imagen de un pasillo arbolado de CUCEA" />
        </a>
      </div>
    </div>

    <!-- Solo dispositivos moviles (<768px res) -->
    <div id="toggle-bd">
      <a href="./data-departamentos.php">
        <div id="jefes-bd"><span>BD Jefes de Departamento</span></div>
      </a>
      <a href="./basededatos-CoordPers.php">
        <div id="coord-bd"><span>BD Coordinación de Personal</span></div>
    </div>

    <div class="accesodirecto-moviles">
      <?php if ($rol_id == 1 || $rol_id == 4) echo '<a href="./plantilla.php">';
      if ($rol_id == 2) echo '<a href="./admin-plantilla.php">';
      if ($rol_id == 3) echo '<a href="./plantilla-CoordPers.php">';
      ?>
      <div class="cuadro-acceso" id="cuadro-plantilla">
        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png">
        <span>Plantilla</span>
      </div>
      <?php echo '</a>';
      ?>
      <?php if ($rol_id == 1 || $rol_id == 4) {
        echo '<a href="./basesdedatos.php">
              <div class="cuadro-acceso">
                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png">
                <span>DB</span>
              </div>
              </a>';
      }
      if ($rol_id == 2) {
        echo '<a href="./data-departamentos.php">
              <div class="cuadro-acceso">
                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png">
                <span>DB</span>
              </div>
              </a>';
      }
      if ($rol_id == 3) {
      ?> <div class="cuadro-acceso" id="cuadro-toggle" onclick="triggerBd()"> <?php
                                                                              echo '
                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png">
                <span>DB</span>
              </div>';
        }
      ?>
        <a href="./calendario.php">
          <div class="cuadro-acceso" id="cuadro-oferta">
            <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png">
            <span>Calendario</span>
          </div>
        </a>
        <a href="./espacios.php">
          <div class="cuadro-acceso" id="cuadro-espacios">
            <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png">
            <span>Espacios</span>
          </div>
        </a>
        <a href="./profesores.php">
          <div class="cuadro-acceso" id="cuadro-guia">
            <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png">
            <span>Profesores</span>
          </div>
        </a>
        </div>
    </div>
    
    <!-- Script para las funciones del modal con la información de los eventos-->
    <script src="./JS/home/modal-eventos.js"></script>
    <!-- Script para las funciones del carrusel -->
    <script src="./JS/home/carrusel.js"></script>
    <!-- Script para la funcion del boton de base de datos cuando es responsivo en moviles y es coordinador de personal -->
    
    <script>
      function triggerBd() {
        var toggleBd = document.getElementById("toggle-bd");
        var jefesBd = document.getElementById("jefes-bd");
        var coordBd = document.getElementById("coord-bd");

        if (jefesBd.style.display === "flex" && coordBd.style.display === "flex") {
          toggleBd.style.display = "none";
          jefesBd.style.display = "none";
          coordBd.style.display = "none";
        } else {
          toggleBd.style.display = "flex";
          jefesBd.style.display = "flex";
          coordBd.style.display = "flex";
        }


      }
    </script>

    <?php include './template/footer.php' ?>
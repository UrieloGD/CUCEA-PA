<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
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
  <!-- carrusel-banner -->
  <div class="banner">
    <div class="carrusel">
      <div class="diapositiva">
        <img src="https://scontent-dfw5-2.xx.fbcdn.net/v/t39.30808-6/215358320_4033136953402520_8974225234675619499_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=cc71e4&_nc_ohc=VzHBBXByFQgQ7kNvgHthuTa&_nc_zt=23&_nc_ht=scontent-dfw5-2.xx&_nc_gid=AfFIT3ZPPTOtqc0HUPDPD2H&oh=00_AYDtsRFj_NWpHs6vPclABe1_BA90y1faB0Q9WP2_HJrpAA&oe=67428083" alt="Imagen 1">
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
</div>

  <div class="container-eventos-progreso">

    <!-- Siguientes eventos de PA -->
    <div class="eventos">
      <div class="siguienteseventos">
        <h3>Siguientes Eventos de PA</h3>
      </div>
      <div class="evento-item">
        <div class="evento-icono">
          <div class="cuadro-numero"><input type="text" id="cuadro-numero" value="04"></div>
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-flag.png" alt="Icono"> -->
        </div>
        <div class="evento-detalle">
          <span>Reunión de apertura de Programación Académica</span>
          <p>04/04/2024 18:00 h</p>
        </div>
        <div class="evento-flecha">
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha"> -->
        </div>
      </div>
      <hr>
      <div class="evento-item">
        <div class="evento-icono">
          <div class="cuadro-numero"><input type="text" id="cuadro-numero" value="21"></div>
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-bd.png" alt="Icono"> -->
        </div>
        <div class="evento-detalle">
          <span>Entrega de bases de datos</span>
          <p>21/04/2024 23:59 h</p>
        </div>
        <div class="evento-flecha">
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha"> -->
        </div>
      </div>
      <hr>
      <div class="evento-item">
        <div class="evento-icono">
          <div class="cuadro-numero"><input type="text" id="cuadro-numero" value="02"></div>
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-reunion.png" alt="Icono"> -->
        </div>
        <div class="evento-detalle">
          <span>Reunión de revisión de bases de datos</span>
          <p>02/05/2024 18:00 h</p>
        </div>
        <div class="evento-flecha">
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha"> -->
        </div>
      </div>
      <hr>
      <div class="evento-item">
        <div class="evento-icono">
          <div class="cuadro-numero"><input type="text" id="cuadro-numero" value="19"></div>
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-flag.png" alt="Icono"> -->
        </div>
        <div class="evento-detalle">
          <span>Cierre de Programación Académica</span>
          <p>19/05/2024 18:00 h</p>
        </div>
        <div class="evento-flecha">
          <!-- <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha"> -->
        </div>
      </div>

      <!--Aquí iremos agregando las funciones donde Aldo como admin agregara eventos importantes de PA-->
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
          echo "<a href='./plantilla-coordPers.php'>";
        }
        ?>
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Plantilla</h4>
        </div>
        <img src="./img/img-home/plantilla.png" alt="Imagen de un pasillo arbolado de CUCEA" />
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
        <img src="./Img/img-home/basededatos.png" alt="imagen de edificios de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./dashboard-oferta.php">
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Oferta</h4>
        </div>
        <img src="./Img/img-home/oferta.png" alt="Imagen de fondo de CERI" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./espacios.php">
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Espacios</h4>
        </div>
        <img src="./Img/img-home/espacios.png" alt="Imagen de las letras de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./guiaPA.php">
        <div class="overlay">
          <h4 style="text-shadow: 1px 4px 3px black;">Guía</h4>
        </div>
        <img src="./Img/img-home/guia.png" alt="Imagen de CiberJardin" />
      </a>
    </div>
  </div>

<!-- Script para las funciones del carrusel -->
<script src="./JS/home/carrusel.js"></script>

<?php include './template/footer.php' ?>
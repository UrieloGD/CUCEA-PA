<!--header -->
<?php include './template/headerLuis.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- css del home -->
<title>Home PA</title>
<link rel="stylesheet" href="./CSS/home.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
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

      if ($rol_id == 1) {
        echo "<br>", $nombre_rol, " - ", $_SESSION['Departamentos'];
      } 
      else {
        echo "<br>", $nombre_rol;
      }
      ?>
    </h2>
  </div>
  <!--Cuadros de navegación-->
  <div class="cuadros-nav">
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
          echo "<a href='data_departamentos.php'>";
        } else {
          // Otros roles o manejo de errores aquí
          echo "<a href='#'>";
        }
      ?>
        <div class="overlay">
          <h4>Bases de datos</h4>
        </div>
        <img src="./Img/img-home/basededatos.png" alt="imagen de edificios de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./dashboard_oferta.php"">
        <div class="overlay">
          <h4>Oferta</h4>
        </div>
        <img src="./Img/img-home/oferta.png" alt="Imagen de fondo de CERI" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="#">
        <div class="overlay">
          <h4>Espacios</h4>
        </div>
        <img src="./Img/img-home/espacios.png" alt="Imagen de las letras de CUCEA" />
      </a>
    </div>
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
          echo "<a href='./plantillaspa.php'>";
        } else {
          // Otros roles o manejo de errores aquí
          echo "<a href='#'>";
        }
        ?>
        <div class="overlay">
          <h4>Plantilla</h4>
        </div>
        <img src="./img/img-home/plantilla.png" alt="Imagen de un pasillo arbolado de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="./guia.php">
        <div class="overlay">
          <h4>Guía</h4>
        </div>
        <img src="./Img/img-home/guia.png" alt="Imagen de CiberJardin" />
      </a>
    </div>
  </div>

  <!-- Bloque inferior -->
  <div class="container-eventos-progreso">

    <!-- Siguientes eventos de PA -->
    <div class="eventos">
      <div class="siguienteseventos">
        <h3>Siguientes Eventos de PA</h3>
      </div>
      <div class="evento-item">
        <div class="evento-icono">
          <img src="./Img/Icons/iconos-eventosPA/icono-flag.png" alt="Icono">
        </div>
        <div class="evento-detalle">
          <span>Reunión de apertura de Programación Académica</span>
          <p>04/04/2024 18:00 h</p>
        </div>
        <div class="evento-flecha">
          <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha">
        </div>
      </div>
      <hr>
      <div class="evento-item">
        <div class="evento-icono">
          <img src="./Img/Icons/iconos-eventosPA/icono-bd.png" alt="Icono">
        </div>
        <div class="evento-detalle">
          <span>Entrega de bases de datos</span>
          <p>21/04/2024 23:59 h</p>
        </div>
        <div class="evento-flecha">
          <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha">
        </div>
      </div>
      <hr>
      <div class="evento-item">
        <div class="evento-icono">
          <img src="./Img/Icons/iconos-eventosPA/icono-reunion.png" alt="Icono">
        </div>
        <div class="evento-detalle">
          <span>Reunión de revisión de bases de datos</span>
          <p>02/05/2024 18:00 h</p>
        </div>
        <div class="evento-flecha">
          <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha">
        </div>
      </div>
      <hr>
      <div class="evento-item">
        <div class="evento-icono">
          <img src="./Img/Icons/iconos-eventosPA/icono-flag.png" alt="Icono">
        </div>
        <div class="evento-detalle">
          <span>Cierre de Programación Académica</span>
          <p>19/05/2024 18:00 h</p>
        </div>
        <div class="evento-flecha">
          <img src="./Img/Icons/iconos-eventosPA/icono-flechaDer.png" alt="Flecha">
        </div>
      </div>

      <!--Aquí iremos agregando las funciones donde Aldo como admin agregara eventos importantes de PA-->
    </div>


    <!-- Progreso de Pa -->
    <div class="progreso">
      <div class="progresoPA">
        <h3>Progreso de PA</h3>
      </div>

      <div class="progresoContenido">
        <span>Jefes de Departamento</span>
        <div class="progress-container">
          <div class="progress-bar" data-progress="80"></div>
          <!-- <progress value="80" max="100"></progress> 
          <span>80%</span> -->
        </div><br>

        <span>Control Escolar</span>
        <div class="progress-container">
          <div class="progress-bar" data-progress="50"></div>
          <!-- <progress value="50" max="100"></progress>
          <span>50%</span> -->
        </div><br>

        <span>Coordinadores</span>
        <div class="progress-container">
          <div class="progress-bar" data-progress="30"></div>
          <!-- <progress value="30" max="100"></progress>
          <span>30%</span> -->
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-bar');

    progressBars.forEach(function(bar) {
      const progress = bar.getAttribute('data-progress');
      bar.style.width = `${progress}%`;
    });
  });
</script>
<?php include './template/footer.php' ?>
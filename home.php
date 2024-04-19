<!--header -->

<?php include './template/header.php' ?>

<!-- navbar -->

<?php include './template/navbar.php' ?>


<!-- css del home -->
<link rel="stylesheet" href="./CSS/home.css" />

<!--Cuadro principal del home-->
<div class="home">
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
  </div>
  <!--Cuadros de navegación-->
  <div class="cuadros-nav">
    <div class="cuadro-ind">
      <a href="#">
        <div class="overlay">
          <h4>Registro</h4>
        </div>
        <img src="./Img/registro.png" alt="imagen de edificios de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="#">
        <div class="overlay">
          <h4>Oferta</h4>
        </div>
        <img src="./Img/oferta.png" alt="Imagen de fondo de CERI" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="#">
        <div class="overlay">
          <h4>Espacios</h4>
        </div>
        <img src="./Img/espacios.png" alt="Imagen de las letras de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="#">
        <div class="overlay">
          <h4>Plantilla</h4>
        </div>
        <img src="./img/plantilla.png" alt="Imagen de un pasillo arbolado de CUCEA" />
      </a>
    </div>
    <div class="cuadro-ind">
      <a href="#">
        <div class="overlay">
          <h4>Guía</h4>
        </div>
        <img src="./Img/guia.png" alt="Imagen de CiberJardin" />
      </a>
    </div>
  </div>
  <!-- Bloque inferior -->
  <div class="container-eventos-progreso">
    <!-- Siguientes eventos de PA -->
    <div class="eventos">
      <h3>Siguientes Eventos de PA</h3>
      <hr>
      <div class="info-evento">
        <img src="./Icons/personas.png" alt="Icono">
        <span>Reunion de apertura de Programación Académica</span><br>
        <p>DD/MM/AAAA</p>
      </div>
      <hr>
      <div class="info-evento">
        <img src="./Icons/personas.png" alt="Icono">
        <span>Reunion de proyectos de Arte y Cultura</span><br>
        <p>DD/MM/AAAA</p>
      </div>
      <hr>
      <div class="info-evento">
        <img src="./Icons/personas.png" alt="Icono">
        <span>Reunion de proyecciones para 2025A</span><br>
        <p>DD/MM/AAAA</p>
      </div>
      <hr>
      <div class="info-evento">
        <img src="./Icons/personas.png" alt="Icono">
        <span>Reunion de cierre de PA</span><br>
        <p>DD/MM/AAAA</p>
      </div>
      <!--Aquí iremos agregando las funciones donde Aldo como admin agregara eventos importantes de PA-->
    </div>
    <!-- Progreso de Pa -->
    <div class="progreso">
      <h3>Progreso de PA</h3>
      <hr>
      <span>Jefes de Departamento</span>
      <div class="progress-container">
        <progress value="80" max="100"></progress>
        <span>80%</span>
      </div><br>
      <span>Control Escolar</span>
      <div class="progress-container">
        <progress value="50" max="100"></progress>
        <span>50%</span>
      </div><br>
      <span>Coordinadores</span>
      <div class="progress-container">
        <progress value="30" max="100"></progress>
        <span>30%</span>
      </div>
    </div>
  </div>
</div>
<?php include './template/footer.php' ?>
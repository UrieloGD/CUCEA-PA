<?php // Incluir el archivo de conexión a la base de datos
require_once './db.php';

// Consulta SQL para obtener el correo
$sql = "SELECT correo FROM usuarios LIMIT 1";
$result = $conexion->query($sql);

// Verificar si se obtuvo un resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $correo = $row['correo'];
} else {
  $correo = 'Correo no disponible';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home PA</title>
  <link rel="stylesheet" href="./CSS/home.css" />
  <link rel="stylesheet" href="./CSS/navbar.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-">
  </link>
</head>
</head>

<body>
  <!-- Ejemplo de navbar -->

  <nav id="navbar">
    <ul class="navbar-items flexbox-col">
      <li class="navbar-logo flexbox-left">
        <a class="navbar-item-inner flexbox" href="#">
          <img src="./Img/UDG+.png" width="60" height="80" alt="Logo-UDG">
        </a>
      </li>
      <hr>
      <li class="navbar-item flexbox-left">
        <a class="navbar-item-inner flexbox-left" href="#">
          <div class="navbar-item-inner-icon-wrapper flexbox ">
            <img src="./Icons/iconos-azules/icono-home.png" width="50%" height="50%" alt="icono-home" class="original-icon">
            <img src="./Icons/iconos-blancos/icono-home-b.png" width="50%" height="50%" alt="icono-home-hover" class="hover-icon">
          </div>
          <span class="link-text">Inicio</span>
        </a>
      </li>
      <li class="navbar-item flexbox-left">
        <a class="navbar-item-inner flexbox-left" href="#">
          <div class="navbar-item-inner-icon-wrapper flexbox">
            <img src="./Icons/iconos-azules/icono-registro.png" width="50%" height="50%" alt="icono-registro" class="original-icon">
            <img src="./Icons/iconos-blancos/icono-registro-b.png" width="50%" height="50%" alt="icono-home-hover" class="hover-icon">
          </div>
          <span class="link-text">Registro</span>
        </a>
      </li>
      <li class="navbar-item flexbox-left">
        <a class="navbar-item-inner flexbox-left" href="#">
          <div class="navbar-item-inner-icon-wrapper flexbox">
            <img src="./Icons/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta" class="original-icon">
            <img src="./Icons/iconos-blancos/icono-oferta-b.png" width="50%" height="50%" alt="icono-home-hover" class="hover-icon">
          </div>
          <span class="link-text">Oferta</span>
        </a>
      </li>
      <li class="navbar-item flexbox-left">
        <a class="navbar-item-inner flexbox-left" href="#">
          <div class="navbar-item-inner-icon-wrapper flexbox">
            <img src="./Icons/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-espacios" class="original-icon">
            <img src="./Icons/iconos-blancos/icono-espacios-b.png" width="50%" height="50%" alt="icono-home-hover" class="hover-icon">
          </div>
          <span class="link-text">Espacios</span>
        </a>
      </li>
      <li class="navbar-item flexbox-left">
        <a class="navbar-item-inner flexbox-left" href="#">
          <div class="navbar-item-inner-icon-wrapper flexbox">
            <img src="./Icons/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
            <img src="./Icons/iconos-blancos/icono-plantilla-b.png" width="50%" height="50%" alt="icono-home-hover" class="hover-icon">
          </div>
          <span class="link-text">Plantilla</span>
        </a>
      </li>
      <li class="navbar-item flexbox-left">
        <a class="navbar-item-inner flexbox-left" href="#">
          <div class="navbar-item-inner-icon-wrapper flexbox">
            <img src="./Icons/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia" class="original-icon">
            <img src="./Icons/iconos-blancos/icono-guia-b.png" width="50%" height="50%" alt="icono-home-hover" class="hover-icon">
          </div>
          <span class="link-text">Guía</span>
        </a>
      </li>

      <li class="navbar-item flexbox-left">
        <a href="#">
          <div class="navbar-profile-icon flexbox profile-icon-transition">
            <img src="./Icons/iconos-azules/icono=perfil.png" width="50%" height="50%" alt="Imagen de Perfil" class="original-icon">
          </div>
        </a>
      </li>
    </ul>
  </nav>

  <!--Inicio del header-->
  <div class="container">
    <div class="header">
      <div class="titulo">
        <h3>Programación Académica</h3>
      </div>
      <div class="rol">
        <h3>Jefe de Departamento</h3>
      </div>
      <li class="icono-notificaciones">
        <a href="#">
          <i class="fas fa-bell" style="font-size: 28px; color: black   ;"></i>
        </a>
      </li>
    </div>
    <!-- <hr style="margin-left: 10vw; margin-right: 2vw" /> -->
    <!-- Fin del  header-->
    <!--Cuadro principal del home-->
    <div class="home">
      <!--Cuadro de bienvenida-->
      <div class="bienvenida">
        <h2>Bienvenid@ <?php echo $correo; ?></h2>
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
  </div>
</body>

</html>
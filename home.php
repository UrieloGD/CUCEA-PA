<?php
// Incluir el archivo de conexión a la base de datos
require_once './config/db.php';

// Obtener el correo electrónico del usuario loggeado desde la sesión
session_start();

$correo_usuario = $_SESSION['email'];

// Consulta SQL para obtener el nombre del usuario loggeado
$sql = "SELECT Nombre, rol FROM usuarios WHERE Correo = '$correo_usuario'";
$result = $conexion->query($sql);

// Verificar si se obtuvo un resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $nombre = $row['Nombre'];
  $rol = $row['rol'];
} else {
  $nombre = 'Nombre no disponible';
  $rol = 'Rol no disponible';
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
  <!-- navbar -->

  <?php include './template/navbar.html'?>

  <!--header -->

  <?php include './template/header.html'?>

    <!--Cuadro principal del home-->
    <div class="home">
      <!--Cuadro de bienvenida-->
      <div class="bienvenida">
        <h2>Bienvenid@ <?php echo $nombre; ?></h2>
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
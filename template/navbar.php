<?php

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    // El usuario no está autenticado, redirigir a la página de inicio de sesión
    header('Location: login.php');
    exit();
}

// Obtener el rol del usuario de la sesión
$rol_id = $_SESSION['Rol_ID'];
$userId = $_SESSION['Codigo'];

function getColorPalette()
{
    return [
        '#4C4CC2',
        '#DF2E79',
        '#064789',
        '#B00F0F',
        '#03CD54',
        '#FF6F32',
        '#F46BBD',
        '#B75CFF'
    ];
}

function generateColorForUser($userId)
{
    global $conexion;

    $colors = getColorPalette();
    $colorIndex = $userId % count($colors);
    $color = $colors[$colorIndex];

    // Actualizar el color en la base de datos
    $stmt = $conexion->prepare("UPDATE Usuarios SET IconoColor = ? WHERE Codigo = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conexion->error);
    }

    $stmt->bind_param("si", $color, $userId);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();

    return $color;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./CSS/navbar.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-">
    </link>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <nav id="navbar">
        <ul class="navbar-items flexbox-col">
            <li class="navbar-logo flexbox-left">
                <a class="navbar-item-inner flexbox" href="#">
                    <img src="./Img/logos/LogoPA-Vertical.png" width="37" height="75" alt="LogoPA-Vertical">
                </a>
            </li>
            <hr>
            
            <li class="navbar-item flexbox-left">
            <?php 
                if (basename($_SERVER['PHP_SELF']) == 'home.php') { echo "<a class='navbar-item-inner flexbox-left' href='./home.php'><div class='indicador'></div>"; }
                else { echo "<a class='navbar-item-inner flexbox-left' href='./home.php'>"; } 
            ?>
                    <div class="navbar-item-inner-icon-wrapper flexbox ">
                        <img src='./Img/Icons/iconos-navbar/iconos-azules/icono-home.png' width='50%' height='50%' alt='icono-home' class='hover-icon'>
                        <img src='./Img/Icons/iconos-navbar/iconos-blancos/icono-home-b.png' width='50%' height='50%' alt='icono-home-hover' class='original-icon'>
                    </div>
                    <span class='link-text'>Inicio</span>
                </a>
            </li>

            <li class="navbar-item flexbox-left">
                <?php
                // Redirigir según el rol del usuario
                if ($rol_id == 1) {
                    // Si el usuario es jefe de departamento, redirigir a subir plantilla
                    if (isset($_SESSION['Nombre_Departamento'])) {
                        // Obtener el nombre del departamento desde la sesión
                        $nombre_departamento = $_SESSION['Nombre_Departamento'];
                            if (basename($_SERVER['PHP_SELF']) == 'plantilla.php') { echo "<a class='navbar-item-inner flexbox-left' href='./plantilla.php'><div class='indicador'></div>"; }
                            else { echo "<a class='navbar-item-inner flexbox-left' href='./plantilla.php'>"; } 
                    } else {
                        // Manejar el caso en que no se encuentre asociado a ningún departamento
                        echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                    }
                } elseif ($rol_id == 2) {
                    // Si el usuario es secretaria administrativa, redirigir a plantillasPA
                    if (basename($_SERVER['PHP_SELF']) == 'admin-plantilla.php') { echo "<a class='navbar-item-inner flexbox-left' href='./admin-plantilla.php'><div class='indicador'></div>"; }
                    else { echo "<a class='navbar-item-inner flexbox-left' href='./admin-plantilla.php'>"; }
                } else {
                    // Otros roles o manejo de errores aquí
                    if (basename($_SERVER['PHP_SELF']) == 'plantilla-CoordPers.php') { echo "<a class='navbar-item-inner flexbox-left' href='./plantilla-CoordPers.php'><div class='indicador'></div>"; }
                    else { echo "<a class='navbar-item-inner flexbox-left' href='./plantilla-CoordPers.php'>"; }
                }
                ?>
                <div class="navbar-item-inner-icon-wrapper flexbox">
                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                </div>
                <span class="link-text">Plantilla</span>
                </a>
            </li>

            <li class="navbar-item flexbox-left">
                <?php
                if ($rol_id == 3) { // Para Coordinación de Personal     
                    echo '<div class="dropdown-container">';
                    if (basename($_SERVER['PHP_SELF']) == 'admin-data-departamentos.php' || basename($_SERVER['PHP_SELF']) == 'basededatos-CoordPers.php') { 
                        echo "<a class='navbar-item-inner flexbox-left dropdown-trigger'><div class='indicador'></div>"; }
                    else { echo "<a class='navbar-item-inner flexbox-left dropdown-trigger'>"; } 
                    echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                            </div>
                            <span class="link-text">Bases de datos</span>
                        </a>';
                    echo '<div class="dropdown-menu">
                            <div class="dropdown-item">
                                <span class="tree-line">└</span>
                                <a href="./admin-data-departamentos.php" class="dropdown-item-inner">
                                    BD Jefes de Departamento
                                </a>
                            </div>
                            <div class="dropdown-item">
                                <span class="tree-line">└</span>
                                <a href="./basededatos-CoordPers.php" class="dropdown-item-inner">
                                    BD Coordinación de Personal
                                </a>
                            </div>
                        </div>';
                    echo '</div>';
                } elseif ($rol_id == 1) { // Para Jefe de Departamento
                    if (isset($_SESSION['Nombre_Departamento'])) {
                        if (basename($_SERVER['PHP_SELF']) == 'basesdedatos.php') { echo "<a class='navbar-item-inner flexbox-left' href='./basesdedatos.php'><div class='indicador'></div>"; }
                        else { echo "<a class='navbar-item-inner flexbox-left' href='./basesdedatos.php'>"; } 
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                    }
                    echo '
                        <div class="navbar-item-inner-icon-wrapper flexbox">
                            <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                            <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                        </div>
                        <span class="link-text">Bases de datos</span>
                    </a>';
                } elseif ($rol_id == 2) { // Para Secretaria Administrativa
                    if (basename($_SERVER['PHP_SELF']) == 'admin-data-departamentos.php') { echo "<a class='navbar-item-inner flexbox-left' href='./admin-data-departamentos.php'><div class='indicador'></div>"; }
                    else { echo "<a class='navbar-item-inner flexbox-left' href='./admin-data-departamentos.php'>"; } 
                    echo '
                        <div class="navbar-item-inner-icon-wrapper flexbox">
                            <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                            <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                        </div>
                        <span class="link-text">Bases de datos</span>
                    </a>';
                }
                ?>
            </li>

            <li class="navbar-item flexbox-left">
            <?php 
                if (basename($_SERVER['PHP_SELF']) == 'calendario.php') { echo "<a class='navbar-item-inner flexbox-left' href='./calendario.php'><div class='indicador'></div>"; }
                else { echo "<a class='navbar-item-inner flexbox-left' href='./calendario.php'>"; } 
            ?>
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-calendario.png" width="50%" height="50%" alt="icono-espacios" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-calendario-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Calendario</span>
                </a>
            </li>

            <li class="navbar-item flexbox-left">
            <?php 
                if (basename($_SERVER['PHP_SELF']) == 'espacios.php') { echo "<a class='navbar-item-inner flexbox-left' href='./espacios.php'><div class='indicador'></div>"; }
                else { echo "<a class='navbar-item-inner flexbox-left' href='./espacios.php'>"; } 
            ?>
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-espacios" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Espacios</span>
                </a>
            </li>

            <?php
            // Mostrar el ícono de "Horas" solo si el usuario tiene el rol 3 (Coordinación de Personal)
            if ($rol_id == 3) {
                echo "<li class='navbar-item flexbox-left'>";
                if (basename($_SERVER['PHP_SELF']) == 'horas-comparacion.php') { 
                    echo "<a class='navbar-item-inner flexbox-left' href='./horas-comparacion.php'><div class='indicador'></div>";
                } else {
                    echo "<a class='navbar-item-inner flexbox-left' href='./horas-comparacion.php'>";
                }
                echo "
                    <div class='navbar-item-inner-icon-wrapper flexbox'>
                        <img src='./Img/Icons/iconos-navbar/iconos-azules/icono-horas.png' width='50%' height='50%' alt='icono-horas' class='hover-icon'>
                        <img src='./Img/Icons/iconos-navbar/iconos-blancos/icono-horas-b.png' width='50%' height='50%' alt='icono-horas-hover' class='original-icon'>
                    </div>
                    <span class='link-text'>Revisión de horas</span>
                    </a>
                </li>";
            }
            ?>

            <?php
            // Redirigir a esta opcion, unicamente si es jefe de departamento o coordinador de personal
            if ($rol_id == 1 || $rol_id == 3) {
            echo "<li class='navbar-item flexbox-left'>";
                if (basename($_SERVER['PHP_SELF']) == 'personal-solicitud-cambios.php') { echo "<a class='navbar-item-inner flexbox-left' href='./personal-solicitud-cambios.php'><div class='indicador'></div>"; }
                else { echo "<a class='navbar-item-inner flexbox-left' href='./personal-solicitud-cambios.php'>"; } 
                echo
                    "<div class='navbar-item-inner-icon-wrapper flexbox'>
                        <img src='./Img/Icons/iconos-navbar/iconos-azules/icono-solicitudes.png' width='50%' height='50%' alt='icono-guia' class='hover-icon'>
                        <img src='./Img/Icons/iconos-navbar/iconos-blancos/icono-solicitudes-b.png' width='50%' height='50%' alt='icono-home-hover' class='original-icon'>
                    </div>
                    <span class='link-text'>Solicitudes</span>
                </a>
            </li>";
            }
            ?>

            <li class="navbar-item flexbox-left">
            <?php 
                if (basename($_SERVER['PHP_SELF']) == 'dashboard-oferta.php') { echo "<a class='navbar-item-inner flexbox-left' href='./dashboard-oferta.php'><div class='indicador'></div>"; }
                else { echo "<a class='navbar-item-inner flexbox-left' href='./dashboard-oferta.php'>"; } 
            ?>
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Oferta</span>
                </a>
            </li>

            <li class="navbar-item flexbox-left">
            <?php 
                if (basename($_SERVER['PHP_SELF']) == 'guiaPA.php') { echo "<a class='navbar-item-inner flexbox-left' href='./guiaPA.php'><div class='indicador'></div>"; }
                else { echo "<a class='navbar-item-inner flexbox-left' href='./guiaPA.php'>"; } 
            ?>
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Guía</span>
                </a>
            </li>

            <?php
                if ($rol_id == 1) {
                    echo "<li class='navbar-item flexbox-left'>";
             
                    if (basename($_SERVER['PHP_SELF']) == 'modal-profesores.php') { echo "<a class='navbar-item-inner flexbox-left' href='./modal-profesores.php'><div class='indicador'></div>"; }
                    else { echo "<a class='navbar-item-inner flexbox-left' href='./modal-profesores.php'>"; } 
            
                        echo "<div class='navbar-item-inner-icon-wrapper flexbox'>";
                            echo "<img src='./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png' width='50%' height='50%' alt='icono-guia' class='hover-icon'>";
                            echo "<img src='./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png' width='50%' height='50%' alt='icono-home-hover' class='original-icon'>";
                        echo "</div>";
                        echo "<span class='link-text'>Modal Profesores</span>";
                    echo "</a>";
                    echo "</li>";
                }
            ?>

            <!-- Duplicado por alguna razon -->
            <!-- <li class="navbar-item flexbox-left">
                <a href="#">
                    <div class="navbar-profile-icon flexbox profile-icon-transition" id="profile-icon">
                        <?php
                        $nombreInicial = strtoupper(substr($_SESSION['Nombre'], 0, 1));
                        $apellidoInicial = strtoupper(substr($_SESSION['Apellido'], 0, 1));
                        $iniciales = $nombreInicial . $apellidoInicial;
                        $userId = $_SESSION['Codigo'];
                        $backgroundColor = generateColorForUser($userId);
                        echo "<span style='background-color: $backgroundColor;'>$iniciales</span>";
                        ?>
                    </div>
                </a>
            </li> -->
            <?php
            if ($rol_id == 2) { // Mostrar ícono de admin solo si el usuario es secretaria administrativa
            ?>
                <li class="navbar-item flexbox-left">
                <?php 
                    if (basename($_SERVER['PHP_SELF']) == 'admin-home.php') { echo "<a class='navbar-item-inner flexbox-left' href='./admin-home.php'><div class='indicador'></div>"; }
                    else { echo "<a class='navbar-item-inner flexbox-left' href='./admin-home.php'>"; } 
                ?>
                        <div class="navbar-item-inner-icon-wrapper flexbox">
                            <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-admin.png" width="50%" height="50%" alt="icono-admin" class="hover-icon">
                            <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-admin-b.png" width="50%" height="50%" alt="icono-admin-hover" class="original-icon">
                        </div>
                        <span class="link-text">Admin</span>
                    </a>
                </li>
            <?php
            }
            ?>
            <li class="navbar-item flexbox-left">
                <a href="#">
                    <div class="navbar-profile-icon flexbox profile-icon-transition" id="profile-icon">
                        <?php
                        $nombreInicial = strtoupper(substr($_SESSION['Nombre'], 0, 1));
                        $apellidoInicial = strtoupper(substr($_SESSION['Apellido'], 0, 1));
                        $iniciales = $nombreInicial . $apellidoInicial;
                        $userId = $_SESSION['Codigo'];
                        $backgroundColor = generateColorForUser($userId);
                        echo "<span style='background-color: $backgroundColor;'>$iniciales</span>";
                        ?>
                    </div>
                </a>
            </li>
            <li class="logout-container">
                <a href="./config/cerrarsesion.php">
                    <button class="logout-button">Cerrar Sesión</button>
                </a>
            </li>
        </ul>
    </nav>

<script>
/* JavaScript para manejar el toggle */
const navbar = document.getElementById('navbar');
let isExpanded = false;

function toggleNavbar() {
  if (window.innerWidth <= 768) {
    isExpanded = !isExpanded;
    if (isExpanded) {
      navbar.classList.add('nav-expanded');
    } else {
      navbar.classList.remove('nav-expanded');
    }
  }
}

// Evento click para el navbar
navbar.addEventListener('click', (event) => {
  // Solo toggle si el click fue directamente en el navbar o en el logo
  if (event.target.closest('.navbar-logo') || event.target === navbar) {
    toggleNavbar();
  }
});

// Cerrar el navbar cuando se hace click fuera de él
document.addEventListener('click', (event) => {
  if (window.innerWidth <= 768 && !navbar.contains(event.target)) {
    navbar.classList.remove('nav-expanded');
    isExpanded = false;
  }
});

// Opcional: Cerrar el navbar cuando se hace scroll
document.addEventListener('scroll', () => {
  if (window.innerWidth <= 768 && isExpanded) {
    navbar.classList.remove('nav-expanded');
    isExpanded = false;
  }
});

// Funcion para desplegable en icono BD
document.addEventListener('DOMContentLoaded', function() {
    const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
    const navbar = document.getElementById('navbar');
    
    dropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const menu = this.nextElementSibling;
            const isExpanded = menu.classList.contains('show');
            
            // Primero removemos la clase show de todos los menús
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.remove('show');
                }
            });
            
            // Toggle de la clase show en el menú actual
            if (!isExpanded) {
                menu.classList.add('show');
            } else {
                menu.classList.remove('show');
            }
        });
    });
    
    // Cerrar el menú si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    // Cerrar el menú cuando el navbar se minimiza
    navbar.addEventListener('mouseleave', function() {
        if (!navbar.matches(':hover')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});
</script>
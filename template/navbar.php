<?php

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    // El usuario no está autenticado, redirigir a la página de inicio de sesión
    header('Location: login.php');
    exit();
}

// Incluir el archivo de sesión iniciada
require_once './config/sesioniniciada.php';

// Obtener el rol del usuario de la sesión
$rol_id = $_SESSION['Rol_ID'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./CSS/navbar.css" />
    <link rel="stylesheet" href="./CSS/header.css">
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
                <a class="navbar-item-inner flexbox" href="./home.php">
                    <img src="./Img/logos/LogoPA-Vertical.png" width="37" height="75" alt="LogoPA-Vertical">
                </a>
            </li>
            <hr>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="./home.php">
                    <div class="navbar-item-inner-icon-wrapper flexbox ">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-home.png" width="50%" height="50%" alt="icono-home" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-home-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Inicio</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <?php
                // Redirigir según el rol del usuario
                if ($rol_id == 1) {
                    // Si el usuario es jefe de departamento, redirigir a la base de datos del departamento correspondiente
                    if (isset($_SESSION['Nombre_Departamento'])) {
                        // Obtener el nombre del departamento desde la sesión
                        $nombre_departamento = $_SESSION['Nombre_Departamento'];
                        echo "<a class='navbar-item-inner flexbox-left' href='./basesdedatos.php'>";
                    } else {
                        // Manejar el caso en que no se encuentre asociado a ningún departamento
                        echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                    }
                } elseif ($rol_id == 2) {
                    // Si el usuario es secretaria administrativa, redirigir al archivo data_departamento.php
                    echo "<a class='navbar-item-inner flexbox-left' href='data_departamentos.php'>";
                } else {
                    // Otros roles o manejo de errores aquí
                    echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                }
                ?>
                <div class="navbar-item-inner-icon-wrapper flexbox">
                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-registro.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-registro-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                </div>
                <span class="link-text">Bases de datos</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Oferta</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-espacios" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Espacios</span>
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
                        echo "<a class='navbar-item-inner flexbox-left' href='./plantilla.php'>";
                    } else {
                        // Manejar el caso en que no se encuentre asociado a ningún departamento
                        echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                    }
                } elseif ($rol_id == 2) {
                    // Si el usuario es secretaria administrativa, redirigir a plantillasPA
                    echo "<a class='navbar-item-inner flexbox-left' href='./plantillaspa.php'>";
                } else {
                    // Otros roles o manejo de errores aquí
                    echo "<a class='navbar-item-inner flexbox-left' href='#'>";
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
                <a class="navbar-item-inner flexbox-left" href="./guia.php">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia" class="hover-icon">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Guía</span>
                </a>
            </li>

            <li class="navbar-item flexbox-left">
                <a href="#">
                    <div class="navbar-profile-icon flexbox profile-icon-transition">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-usuario-b.png" width="50%" height="50%" alt="Imagen de Perfil" class="original-icon">
                    </div>
                </a>
            </li>
            <?php
            if ($rol_id == 2) { // Mostrar ícono de admin solo si el usuario es secretaria administrativa
                ?>
                <li class="navbar-item flexbox-left">
                    <a class="navbar-item-inner flexbox-left" href="./admin-home.php">
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
                    <div class="navbar-profile-icon flexbox profile-icon-transition">
                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-usuario-b.png" width="50%" height="50%" alt="Imagen de Perfil" class="original-icon">
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
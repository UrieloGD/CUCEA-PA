<?php
include './config/sesiones.php';
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
    <link rel="stylesheet" href="/CSS/navbar.css">
</head>

<body>

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
                        <img src="./Icons/iconos-azules/icono-home.png" width="50%" height="50%" alt="icono-home" class="hover-icon">
                        <img src="./Icons/iconos-blancos/icono-home-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Inicio</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Icons/iconos-azules/icono-registro.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                        <img src="./Icons/iconos-blancos/icono-registro-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Registro</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Icons/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta" class="hover-icon">
                        <img src="./Icons/iconos-blancos/icono-oferta-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Oferta</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Icons/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-espacios" class="hover-icon">
                        <img src="./Icons/iconos-blancos/icono-espacios-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Espacios</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Icons/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                        <img src="./Icons/iconos-blancos/icono-plantilla-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Plantilla</span>
                </a>
            </li>
            <li class="navbar-item flexbox-left">
                <a class="navbar-item-inner flexbox-left" href="#">
                    <div class="navbar-item-inner-icon-wrapper flexbox">
                        <img src="./Icons/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia" class="hover-icon">
                        <img src="./Icons/iconos-blancos/icono-guia-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                    </div>
                    <span class="link-text">Guía</span>
                </a>
            </li>

        <li class="navbar-item flexbox-left">
            <a href="#">
                <div class="navbar-profile-icon flexbox profile-icon-transition">
                    <img src="./Icons/iconos-blancos/icono-usuario-b.png" width="50%" height="50%" alt="Imagen de Perfil" class="original-icon">
                </div>
            </a>
        </li>
        <li class="logout-container">
            <a href="./config/cerrarsesion.php"><button class="logout-button">Cerrar Sesión</button></a>
        </li>
    </ul>
</nav>
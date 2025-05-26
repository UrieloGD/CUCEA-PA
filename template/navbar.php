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
    $stmt = $conexion->prepare("UPDATE usuarios SET IconoColor = ? WHERE Codigo = ?");
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
    <link rel="stylesheet" href="./CSS/navbar.css?v=<?php echo filemtime('./CSS/navbar.css'); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-">
    </link>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Boton Hamburguesa -->
    <button class="hamburguesa" id="toggle-menu">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>

    <nav id="navbar">
        <ul class="navbar-items flexbox-col">
            <li class="navbar-logo flexbox-left">
                <a class="navbar-item-inner flexbox" href="./home.php">
                    <div class="ajuste-logo">
                        <img src="./Img/logos/LogoPA-Vertical.png" width="37" height="75" alt="LogoPA-Vertical">
                    </div>
                </a>
            </li>
            <hr>

            <div class="container-iconos-navbar">
                <li class="navbar-item flexbox-left">
                    <?php
                    if (basename($_SERVER['PHP_SELF']) == 'home.php') {
                        echo "<div class='indicador'>
                            <a class='navbar-item-inner flexbox-left' href='./home.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                            <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-home.png" width="50%" height="50%" alt="icono-home" class="hover-icon">
                            <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-home.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Inicio</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./home.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-home.png" width="50%" height="50%" alt="icono-home" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-home-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                </div>
                                <span class="link-text">Inicio</span>
                            </a>';
                    }
                    ?>
                </li>

                <li class="navbar-item flexbox-left">
                    <?php
                    // Redirigir según el rol del usuario
                    if ($rol_id == 1 || $rol_id == 4) {
                        // Si el usuario es jefe de departamento, redirigir a subir plantilla
                        if (isset($_SESSION['Nombre_Departamento'])) {
                            // Obtener el nombre del departamento desde la sesión
                            $nombre_departamento = $_SESSION['Nombre_Departamento'];
                            if (basename($_SERVER['PHP_SELF']) == 'plantilla.php') {
                                echo "<div class='indicador'>
                                    <a class='navbar-item-inner flexbox-left' href='./plantilla.php'>";
                                echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
                                    </div>
                                    <span class="link-text-select">Plantilla</span>
                                </div>';
                            } else {
                                echo "<a class='navbar-item-inner flexbox-left' href='./plantilla.php'>";
                                echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
                                        </div>
                                        <span class="link-text">Plantilla</span>
                                    </a>';
                            }
                        } else {
                            // Manejar el caso en que no se encuentre asociado a ningún departamento
                            echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                        }
                    } elseif ($rol_id == 2 || $rol_id == 0) {
                        // Si el usuario es secretaria administrativa, redirigir a plantillasPA
                        if (basename($_SERVER['PHP_SELF']) == 'admin-plantilla.php') {
                            echo "<div class='indicador'>
                                    <a class='navbar-item-inner flexbox-left' href='./admin-plantilla.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
                                    </div>
                                    <span class="link-text-select">Plantilla</span>
                                </div>';
                        } else {
                            echo "<a class='navbar-item-inner flexbox-left' href='./admin-plantilla.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
                                    </div>
                                    <span class="link-text">Plantilla</span>
                                </a>';
                        }
                    } else {
                        // Otros roles o manejo de errores aquí
                        if (basename($_SERVER['PHP_SELF']) == 'plantilla-CoordPers.php') {
                            echo "<div class='indicador'>
                                    <a class='navbar-item-inner flexbox-left' href='./plantilla-CoordPers.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
                                    </div>
                                    <span class="link-text-select">Plantilla</span>
                                </div>';
                        } else {
                            echo "<a class='navbar-item-inner flexbox-left' href='./plantilla-CoordPers.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-plantilla.png" width="50%" height="50%" alt="icono-plantilla" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-plantilla-b.png" width="50%" height="50%" alt="icono-plantilla" class="original-icon">
                                    </div>
                                    <span class="link-text">Plantilla</span>
                                </a>';
                        }
                    }
                    ?>
                    </a>
                </li>

                <li class="navbar-item flexbox-left">
                    <?php
                    if ($rol_id == 3 || $rol_id == 0) { // Para Coordinación de Personal     
                        echo '<div class="dropdown-container">';
                        if (basename($_SERVER['PHP_SELF']) == 'data-departamentos.php' || basename($_SERVER['PHP_SELF']) == 'basededatos-CoordPers.php') {
                            echo "<div class='indicador'>
                                <a class='navbar-item-inner flexbox-left dropdown-trigger'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                </div>
                                <span class="link-text-select">Bases de datos</span>
                                </a>
                            </div>';
                        } else {
                            echo "<a class='navbar-item-inner flexbox-left dropdown-trigger'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                    </div>
                                    <span class="link-text">Bases de datos</span>
                                </a>';
                        }
                        // Seccion del desplegable
                        echo '<div class="dropdown-menu">
                                <div class="dropdown-item">
                                    <span class="tree-line">└</span>
                                    <a href="./data-departamentos.php" class="dropdown-item-inner">
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
                    } elseif ($rol_id == 1 || $rol_id == 4) { // Para Jefe de Departamento
                        if (isset($_SESSION['Nombre_Departamento'])) {
                            if (basename($_SERVER['PHP_SELF']) == 'basesdedatos.php') {
                                echo "<div class='indicador'>";
                                echo "<a class='navbar-item-inner flexbox-left' href='./basesdedatos.php'>";
                                echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                    </div>
                                    <span class="link-text-select">Bases de datos</span>
                                    </a>
                                </div>';
                            } else {
                                echo "<a class='navbar-item-inner flexbox-left' href='./basesdedatos.php'>";
                                echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                        <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                        <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                        </div>
                                        <span class="link-text">Bases de datos</span>
                                    </a>';
                            }
                        } else {
                            echo "<a class='navbar-item-inner flexbox-left' href='#'>";
                        }
                    } elseif ($rol_id == 2) { // Para Secretaria Administrativa
                        if (basename($_SERVER['PHP_SELF']) == 'data-departamentos.php') {
                            echo "<div class='indicador'>";
                            echo "<a class='navbar-item-inner flexbox-left' href='./data-departamentos.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                </div>
                                <span class="link-text-select">Bases de datos</span>
                                </a>
                            </div>';
                        } else {
                            echo "<a class='navbar-item-inner flexbox-left' href='./data-departamentos.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-basededatos.png" width="50%" height="50%" alt="icono-registro" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-basededatos-b.png" width="50%" height="50%" alt="icono-home-hover" class="original-icon">
                                    </div>
                                    <span class="link-text">Bases de datos</span>
                                </a>';
                        }
                    }
                    ?>
                </li>

                <?php
                if ($rol_id == 1 || $rol_id == 0 || $rol_id == 4) {
                    echo "<li class='navbar-item flexbox-left'>";

                    if (basename($_SERVER['PHP_SELF']) == 'profesores.php') {
                        echo "<div class='indicador'>";
                        echo "<a class='navbar-item-inner flexbox-left' href='./profesores.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-profesores.png" width="50%" height="50%" alt="icono-profesores" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-profesores.png" width="50%" height="50%" alt="icono-profesores-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Profesores</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./profesores.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-profesores.png" width="50%" height="50%" alt="icono-profesores" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-profesores-b.png" width="50%" height="50%" alt="icono-profesores-hove" class="original-icon">
                                </div>
                                <span class="link-text">Profesores</span>
                            </a>';
                    }

                    echo "</li>";
                }
                ?>

                <li class="navbar-item flexbox-left">
                    <?php
                    if (basename($_SERVER['PHP_SELF']) == 'calendario.php') {
                        echo "<div class='indicador'>";
                        echo "<a class='navbar-item-inner flexbox-left' href='./calendario.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-calendario.png" width="50%" height="50%" alt="icono-calendario" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-calendario.png" width="50%" height="50%" alt="icono-calendario-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Calendario</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./calendario.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-calendario.png" width="50%" height="50%" alt="icono-calendario" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-calendario-b.png" width="50%" height="50%" alt="icono-calendario-hover" class="original-icon">
                                </div>
                                <span class="link-text">Calendario</span>
                            </a>';
                    }
                    ?>
                </li>

                <li class="navbar-item flexbox-left">
                    <?php
                    if (basename($_SERVER['PHP_SELF']) == 'espacios.php') {
                        echo "<div class='indicador'>";
                        echo "<a class='navbar-item-inner flexbox-left' href='./espacios.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-espacios" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-espacios-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Espacios</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./espacios.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-espacios.png" width="50%" height="50%" alt="icono-calendario" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-espacios-b.png" width="50%" height="50%" alt="icono-calendario-hover" class="original-icon">
                                </div>
                                <span class="link-text">Espacios</span>
                            </a>';
                    }
                    ?>
                </li>

                <?php
                // Mostrar el ícono de "Horas" solo si el usuario tiene el rol 3 (Coordinación de Personal) y 4
                if ($rol_id == 3 || $rol_id == 0) {
                    echo "<li class='navbar-item flexbox-left'>";
                    if (basename($_SERVER['PHP_SELF']) == 'horas-comparacion.php') {
                        echo "<div class='indicador'>";
                        echo "<a class='navbar-item-inner flexbox-left' href='./horas-comparacion.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-horas.png" width="50%" height="50%" alt="icono-horas" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-horas.png" width="50%" height="50%" alt="icono-horas-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Revisión de horas</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./horas-comparacion.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-horas.png" width="50%" height="50%" alt="icono-horas" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-horas-b.png" width="50%" height="50%" alt="icono-horas-hover" class="original-icon">
                                </div>
                                <span class="link-text">Revisión de horas</span>
                            </a>';
                    }
                    echo "</li>";
                }
                ?>

                <?php
                // Redirigir a esta opcion, unicamente si es jefe de departamento o coordinador de personal
                if ($rol_id == 1 || $rol_id == 3 || $rol_id == 0 || $rol_id == 4) {
                    echo "<li class='navbar-item flexbox-left'>";
                    if (basename($_SERVER['PHP_SELF']) == 'personal-solicitud-cambios.php') {
                        echo "<div class='indicador'>";
                        echo "<a class='navbar-item-inner flexbox-left' href='./personal-solicitud-cambios.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-solicitudes.png" width="50%" height="50%" alt="icono-solicitudes" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-solicitudes.png" width="50%" height="50%" alt="icono-solicitudes-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Solicitudes</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./personal-solicitud-cambios.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-solicitudes.png" width="50%" height="50%" alt="icono-solicitudes" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-solicitudes-b.png" width="50%" height="50%" alt="icono-solicitudes-hover" class="original-icon">
                                </div>
                                <span class="link-text">Solicitudes</span>
                            </a>';
                    }
                    echo "</li>";
                }
                ?>
                <?php
                if ($rol_id == 0) {
                ?>
                <li class="navbar-item flexbox-left">
                    <?php
                    if (basename($_SERVER['PHP_SELF']) == 'dashboard-oferta.php') {
                        echo "<div class='indicador'>";
                        echo "<a class='navbar-item-inner flexbox-left' href='./dashboard-oferta.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta-hover" class="original-icon">
                            </div>
                            <span class="link-text-select">Oferta</span>
                            </a>
                        </div>';
                    } else {
                        echo "<a class='navbar-item-inner flexbox-left' href='./dashboard-oferta.php'>";
                        echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-oferta.png" width="50%" height="50%" alt="icono-oferta" class="hover-icon">
                                <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-oferta-b.png" width="50%" height="50%" alt="icono-oferta-hover" class="original-icon">
                                </div>
                                <span class="link-text">Oferta</span>
                            </a>';
                    }
                    ?>
                </li>
                <?php } ?>

                <!-- <li class="navbar-item flexbox-left">
                    < ?php
                    // if (basename($_SERVER['PHP_SELF']) == 'guiaPA.php') {
                    //     echo "<div class='indicador'>";
                    //     echo "<a class='navbar-item-inner flexbox-left' href='./guiaPA.php'>";
                    //     echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                    //             <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia" class="hover-icon">
                    //             <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia-hover" class="original-icon">
                    //         </div>
                    //         <span class="link-text-select">Guía</span>
                    //         </a>
                    //     </div>';
                    // } else {
                    //     echo "<a class='navbar-item-inner flexbox-left' href='./guiaPA.php'>";
                    //     echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                    //             <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-guia.png" width="50%" height="50%" alt="icono-guia" class="hover-icon">
                    //             <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-guia-b.png" width="50%" height="50%" alt="icono-guia-hover" class="original-icon">
                    //             </div>
                    //             <span class="link-text">Guía</span>
                    //         </a>';
                    // }
                    ?>
                </li> -->

                <?php
                if ($rol_id == 2 || $rol_id == 0) { // Mostrar ícono de admin solo si el usuario es secretaria administrativa
                ?>
                    <li class="navbar-item flexbox-left">
                        <?php
                        if (basename($_SERVER['PHP_SELF']) == 'admin-home.php') {
                            echo "<div class='indicador'>";
                            echo "<a class='navbar-item-inner flexbox-left' href='./admin-home.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-admin.png" width="50%" height="50%" alt="icono-admin" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-admin.png" width="50%" height="50%" alt="icono-admin-hover" class="original-icon">
                                </div>
                                <span class="link-text-select">Admin</span>
                                </a>
                            </div>';
                        } else {
                            echo "<a class='navbar-item-inner flexbox-left' href='./admin-home.php'>";
                            echo '<div class="navbar-item-inner-icon-wrapper flexbox">
                                    <img src="./Img/Icons/iconos-navbar/iconos-azules/icono-admin.png" width="50%" height="50%" alt="icono-admin" class="hover-icon">
                                    <img src="./Img/Icons/iconos-navbar/iconos-blancos/icono-admin-b.png" width="50%" height="50%" alt="icono-admin-hover" class="original-icon">
                                    </div>
                                    <span class="link-text">Admin</span>
                                </a>';
                        }
                        ?>
                    </li>
                <?php
                }
                ?>
            </div>

            <div class="container-profile-logout">
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
            </div>

            <li class="logout-container">
                <a href="./config/cerrarsesion.php">
                    <button class="logout-button">Cerrar Sesión</button>
                </a>
            </li>
        </ul>
    </nav>

    <script>
        // Funcion para desplegable en icono BD
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
            const navbar = document.getElementById('navbar');

            dropdownTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Buscamos el contenedor padre
                    const container = this.closest('.dropdown-container');
                    // Buscamos el menú dentro del contenedor
                    const menu = container.querySelector('.dropdown-menu');

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


            // Función para ajustar la visibilidad según el tamaño de la ventana
            const toggleButton = document.getElementById('toggle-menu');

            function adjustNavbarVisibility() {
                if (window.innerWidth <= 768) {
                    // En móvil: ocultar navbar inicialmente, a menos que ya esté activo
                    if (!navbar.classList.contains('active')) {
                        navbar.style.display = 'none';
                    }

                    // Configurar eventos para móvil si aún no están configurados
                    if (toggleButton && navbar && !toggleButton.hasEventListener) {
                        // Marcar que ya agregamos los eventos
                        toggleButton.hasEventListener = true;

                        // Función para mostrar el navbar
                        function showNavbar() {
                            navbar.classList.add('active');
                            navbar.style.display = 'flex';
                            navbar.style.width = '16em';

                            navbar.offsetHeight;

                            setTimeout(() => {
                                navbar.classList.add('active');
                            }, 300);
                        }

                        // Función para ocultar el navbar
                        function hideNavbar() {
                            navbar.classList.remove('active');
                            // Esperamos a que termine la transición antes de ocultarlo completamente
                            navbar.addEventListener('transitionend', function hideAfterTransition() {
                                navbar.style.display = 'none';
                                navbar.removeEventListener('transitionend', hideAfterTransition);
                            }, {
                                once: true
                            });
                        }

                        // Evento para el botón hamburguesa
                        toggleButton.addEventListener('click', function(event) {
                            event.stopPropagation();

                            if (navbar.classList.contains('active')) {
                                hideNavbar();
                            } else {
                                showNavbar();
                            }
                        });

                        // Evitar que clics dentro del navbar lo cierren
                        navbar.addEventListener('click', function(event) {
                            event.stopPropagation();
                        });

                        // Cerrar navbar al hacer clic fuera
                        document.addEventListener('click', function() {
                            if (window.innerWidth <= 768 && navbar.classList.contains('active')) {
                                hideNavbar();
                            }
                        });
                    }
                } else {
                    // En desktop: siempre mostrar navbar
                    navbar.style.display = 'flex'; // o 'block', según tu diseño
                    navbar.classList.remove('active'); // Quitar la clase active si existe
                    navbar.style.width = '';
                }
            }

            // Aplicar inicialmente
            adjustNavbarVisibility();

            // Reajustar cuando la ventana cambie de tamaño
            window.addEventListener('resize', adjustNavbarVisibility);
        });
    </script>
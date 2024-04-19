<?php

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    // El usuario no está autenticado, redirigir a la página de inicio de sesión
    header('Location: login.php');
    exit();
}

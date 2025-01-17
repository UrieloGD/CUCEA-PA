<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <!-- <link rel="stylesheet" href="./../../CSS/navbar.css"> -->
    <link rel="stylesheet" href="./../../../CSS/login.css">
    <link rel="icon" href="./../../../Img/Icons/iconos-header/pestaña.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    // Obtener el código del usuario desde la URL o la sesión
    if (isset($_GET['id'])) {
        $codigo = $_GET['id'];
    } else {
        echo "Código de usuario no encontrado.";
        exit();
    }
    ?>
    <form action="./procesarCambioContrasena.php" method="post">
        <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
        <div class="flex items-center min-h-screen px-4">
            <div class="w-full max-w-sm mx-auto">
                <div class="login-container">
                    <div class="space-y-4">
                        <div>
                            <img src="./../../../Img/logos/LogoCUCEA-COLORES-HD.png" width="100%" height="100%" alt="CUCEA">
                        </div>
                        <br>
                        <div class="text-center">
                            <img src="./../../../Img/logos/LogoPA-Color.png" width="100%" height="100%"></img>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium" for="pass">Nueva Contraseña</label>
                                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="pass" placeholder="•••••••••" required type="password" name="pass">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium" for="confpass">Confirmar Contraseña</label>
                                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="confpass" placeholder="•••••••••" required type="password" name="confpass">
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="text-center">
                            <input type="submit" class="button-3" value="Cambiar contraseña">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();

        fetch('./procesarCambioContrasena.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                    }).then(() => {
                        window.location.href = './../../../login.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Verifica que las contraseñas coincidan.',
                });
            });
    });
</script>
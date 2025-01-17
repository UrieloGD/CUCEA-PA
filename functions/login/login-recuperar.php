<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <!-- <link rel="stylesheet" href="./../../CSS/navbar.css" /> -->
    <link rel="stylesheet" href="./../../CSS/login.css" />
    <link rel="icon" href="./../../Img/Icons/iconos-header/pestaña.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <form action="./recuperar-contrasena/correo-recuperacion.php" method="post">

        <div class="flex items-center min-h-screen px-4">
            <div class="w-full max-w-sm mx-auto">
                <div class="login-container">
                    <div class="space-y-4">
                        <div>
                            <img src="./../../Img/logos/LogoCUCEA-COLORES-HD.png" width="100%" height="100%" alt="CUCEA" />
                        </div>
                        <br />
                        <div>
                            <img src="./../../Img/logos/LogoPA-Color.png" width="100%" height="100%" alt="LogoPA" />
                        </div>
                        <br />
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium" for="email">Correo</label>
                                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="codigo" placeholder="ejemplo@cucea.udg.mx" required type="email" name="email">
                            </div>
                        </div>
                        <br />
                        <br />
                        <div class="text-center">
                            <input type="submit" class="button-3" value="Recuperar contraseña">
                        </div>
                        <br>
                        <div class="text-center">
                            <a class="text-sm-nip text-blue-500 hover:underline" href="./../../login.php">Regresar al inicio.</a>
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

        fetch('./recuperar-contrasena/correo-recuperacion.php', {
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
                    text: 'Hubo un problema al procesar la solicitud.',
                });
            });
    });
</script>
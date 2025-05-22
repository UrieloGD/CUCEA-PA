<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="./CSS/navbar.css?v=<?php echo filemtime('./CSS/navbar.css'); ?>" />
  <link rel="stylesheet" href="./CSS/login.css?v=<?php echo filemtime('./CSS/login.css'); ?>" />
  <link rel="icon" href="./Img/Icons/iconos-header/pestaña.png" type="image/png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <form action="./functions/login/validar.php" method="post">

    <div class="flex items-center min-h-screen px-4">
      <div class="w-full max-w-sm mx-auto">
        <div class="login-container">
          <div class="space-y-4">
            <div>
              <img src="./Img/logos/LogoCUCEA-COLORES-HD.png" width="100%" height="100%" alt="CUCEA" />
            </div>
            <br />
            <div>
              <img src="./Img/logos/LogoPA-Color.png" width="100%" height="100%" alt="LogoPA" />
            </div>
            <br />
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="text-sm font-medium" for="email">Correo</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="codigo" placeholder="ejemplo@cucea.udg.mx" required type="email" name="email">
              </div>

              <br>

              <div class="space-y-2">
                <label class="text-sm font-medium" for="pass">Contraseña</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="pass" placeholder="•••••••••" required type="password" name="pass">
              </div>
            </div>
            <br />
            <div class="text-center">
              <a class="text-sm-nip text-blue-500 hover:underline" href="./functions/login/login-recuperar.php">Recuperar contraseña.</a>
            </div>
            <br />
            <div class="text-center">
              <input type="submit" class="button-3" value="Iniciar Sesión">
            </div>
          </div>
        </div>
      </div>
    </div>

  </form>
</body>

</html>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error === '1') {
      Swal.fire({
        icon: 'error',
        title: 'Error de autenticación',
        text: 'Usuario o contraseña incorrectos',
        confirmButtonText: 'Aceptar',
        customClass: {
          confirmButton: 'boton-aceptar',
        }
      });
    }
  });
</script>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="./CSS/navbar.css" />
  <link rel="stylesheet" href="./CSS/login.css" />
</head>

<body>
  <form action="./config/validar.php" method="post">

    <div class="flex items-center min-h-screen px-4">
      <div class="w-full max-w-sm mx-auto">
        <div class="login-container">
          <div class="space-y-4">
            <div>
              <img src="./Img/LogoQCA2.png" width="100%" height="100%" alt="CUCEA" />
            </div>
            <br />
            <div class="text-center">
              <h1 class="text-3xl font-bold">Mi plataforma CUCEA</h1>
            </div>
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="text-sm font-medium" for="email">Correo</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="codigo" placeholder="ejemplo@ejemplo.com" required type="email" name="email">
              </div>

              <br>

              <div class="space-y-2">
                <label class="text-sm font-medium" for="pass">Contraseña</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-200" id="pass" placeholder="•••••••••" required type="password" name="pass">
              </div>
            </div>
            <br />
            <div class="text-center">
              <a class="text-sm-nip text-blue-500 hover:underline" href="#">Recuperar contraseña.</a>
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
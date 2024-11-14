<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexion a base de datos -->
<?php include './config/db.php' ?>

<title>Añadir Usuarios</title>
<link rel="stylesheet" href="./CSS/admin-usuarios.css" />


<!--Cuadro principal del home-->
<div class="cuadro-principal">

  <!--Pestaña azul-->
  <div class="encabezado">
    <div class="titulo-bd">
      <h3>Gestión de usuarios</h3>
    </div>
  </div>
  <br><br>

  <!-- Barra de Busqueda-->
  <div class="busqueda">
    <br>
    <input type="text" placeholder="Buscar..." id="search-input">
    <div class="button-container">
      <button id="add-button" class="btn">Añadir usuario</button>
      <!-- <a href="#" class="btn"><img src="./Img/Icons/iconos-adminAU/ajustes.png"></a> -->
    </div>
  </div>

  <!--Tabla Añadir Usuarios-->
  <div class="tabla">
    <table>

      <tr>
        <th style="text-align: center;">Código</th>
        <th style="text-align: center;">Nombre</th>
        <th style="text-align: center;">Apellido</th>
        <th style="text-align: center;">Correo</th>
        <th style="text-align: center;">Rol</th>
        <th style="text-align: center;">Departamento</th>
        <th style="text-align: center;">Acción</th>
      </tr>

      <?php

      // Consulta para obtener usuarios y departamentos
      $sql = "SELECT u.Codigo, u.Nombre, u.Apellido, u.Correo, r.Nombre_Rol, COALESCE(d.Departamentos, r.Nombre_Rol) AS Departamento
        FROM Usuarios u
        LEFT JOIN Roles r ON u.Rol_ID = r.Rol_ID
        LEFT JOIN Usuarios_Departamentos ud ON u.Codigo = ud.Usuario_ID
        LEFT JOIN Departamentos d ON ud.Departamento_ID = d.Departamento_ID";

      $result = $conexion->query($sql);


      // Consulta para obtener los roles
      $roles_sql = "SELECT Rol_ID, Nombre_Rol FROM Roles";
      $roles_result = $conexion->query($roles_sql);

      $roles = [];
      if ($roles_result->num_rows > 0) {
        while ($row = $roles_result->fetch_assoc()) {
          $roles[] = $row;
        }
      }

      // Consulta para obtener los departamentos
      $departamentos_sql = "SELECT Departamento_ID, Departamentos FROM Departamentos";
      $departamentos_result = $conexion->query($departamentos_sql);

      $departamentos = [];
      if ($departamentos_result->num_rows > 0) {
        while ($row = $departamentos_result->fetch_assoc()) {
          $departamentos[] = $row;
        }
      }

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr data-id='" . $row["Codigo"] . "'>";
          echo "<td style='text-align: center;'>" . $row["Codigo"] . "</td>";
          echo "<td class='editable' data-field='Nombre' style='text-align: center;'>" . $row["Nombre"] . "</td>";
          echo "<td class='editable' data-field='Apellido' style='text-align: center;'>" . $row["Apellido"] . "</td>";
          echo "<td class='editable' data-field='Correo' style='text-align: center;'>" . $row["Correo"] . "</td>";
          echo "<td class='editable' data-field='Rol' style='text-align: center;'>" . $row["Nombre_Rol"] . "</td>";
          echo "<td class='editable' data-field='Departamento' style='text-align: center;'>" . $row["Departamento"] . "</td>";
          echo "<td style='text-align: center;'>
                          <a href='#' class='btn edit'><img src='./Img/Icons/iconos-adminAU/editar2.png'></a>
                          <a href='#' class='btn save' style='display:none;'><img src='./Img/Icons/iconos-adminAU/guardar.png'></a>
                          <a href='#' class='btn cancel' style='display:none;'><img src='./Img/Icons/iconos-adminAU/cancelar.png'></a>
                          <a href='#' class='btn delete'><img src='./Img/Icons/iconos-adminAU/borrar2.png'></a>
                      </td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No hay usuarios registrados</td></tr>";
      }

      $conexion->close();
      ?>
    </table>
  </div>

  <!-- Formulario para agregar nuevo usuario -->
  <div id="nuevoUsuarioModal" class="modal">
    <div class="modal-contenido">
      <span class="cerrar">&times;</span>
      <h2>Agregar nuevo usuario</h2>
      <form id="nuevoUsuarioForm">
        <!-- Campos del formulario -->
        <label for="codigo">Código:</label>
        <input type="text" id="codigo" name="codigo" required>

        <div class="nombre-apellido">
          <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
          </div>
          <div class="campo">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>
          </div>
        </div>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required>

        <div class="rol-departamento">
          <div class="campo">
            <label for="rol">Rol:</label>
            <select id="rol" name="rol"></select>
          </div>
          <div class="campo">
            <label for="departamento">Departamento:</label>
            <select id="departamento" name="departamento"></select>
          </div>
        </div>

        <label for="genero">Género:</label>
        <select id="genero" name="genero">
          <option value="Masculino">Masculino</option>
          <option value="Femenino">Femenino</option>
        </select>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Guardar</button>
      </form>
    </div>
  </div>

  <script src="./JS/admin-usuarios/admin-usuarios.js"></script>
  <script>
    const roles = <?php echo json_encode($roles); ?>;
    const departamentos = <?php echo json_encode($departamentos); ?>;
  </script>

  <?php include './template/footer.php' ?>
<!--header -->
<?php include './template/header.php'; ?>
<!-- navbar -->
<?php include './template/navbar.php'; ?>
<!-- Conexión a la base de datos -->
<?php
// Conexión a la base de datos
include './config/db.php';

// Obtener usuarios de la base de datos
$sql = "SELECT Codigo, Nombre, Apellido, Correo, Rol_ID FROM Usuarios";
$resultado = $conexion->query($sql);

// Se despliegan los usuarios (Nombre y correo en checkboxes)
$filas_usuarios = "";
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $codigo = $fila["Codigo"];
        $nombre = $fila["Nombre"] . " " . $fila["Apellido"];
        $correo = $fila["Correo"];
        $filas_usuarios .= "<tr>
            <td><input type='checkbox' name='participantes[]' value='$codigo'></td>
            <td>$nombre</td>
            <td>$correo</td>
        </tr>";
    }
} else {
    $filas_usuarios = "<tr><td colspan='3'>No hay usuarios registrados</td></tr>";
}

// Cerrar la conexión
$conexion->close();
?>

<title>Crear Evento</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Crear evento</h3>
        </div>
    </div>
    <div class="contenedor-formulario">

        <form action="config/eventos_upload.php" method="post" id="eventoForm">
            <div class="form-group">
                <label for="nombre">
                    <i class="fas fa-pen"></i> Nombre
                </label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del evento">
            </div>
            
            <div class="form-group">
                <label>
                    <i class="fas fa-calendar"></i> Fecha y hora
                </label>
                <div class="fecha-hora-group">
                    <input type="date" id="FechIn" name="FechIn">
                    <input type="date" id="FechFi" name="FechFi">
                    <span>a las</span>
                    <input type="time" id="HorIn" name="HorIn">
                    <span> --> </span>
                    <input type="time" id="HorFin" name="HorFi">
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Notificaciones
                </label>
                <div class="notificaciones-group">
                    <select id="notificacion" name="notificacion">
                        <option value="1 hora antes">1 hora antes</option>
                        <option value="2 horas antes">2 horas antes</option>
                        <option value="1 día antes">1 día antes</option>
                        <option value="1 semana antes">1 semana antes</option>
                        <option value="Sin notificación">Sin notificación</option>
                    </select>
                    <span>a las</span>
                    <input type="time" id="HorNotif" name="HorNotif">
                    <label class="checkbox-label">
                        <input type="checkbox" name="correo_electronico">Correo electrónico
                    </label>
                </div>
            </div>

            <div class="botones_agregar">
                <button type="button" class="boton-agregar">+ Agregar notificación</button>
            </div>
            
            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <table>
                        <thead>
                            <tr>
                                <th>Seleccionar</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $filas_usuarios; ?>
                        </tbody>
                    </table>
                    <label class="checkbox-label">
                        <input type="checkbox" id="select_all_rol_3" name="select_all_rol_3"> Seleccionar todos los usuarios con rol 3
                    </label>
                </div>
                <div class="split-item">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="etiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="1">Programación Académica</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="descripcion">
                    <i class="fas fa-align-left"></i> Descripción
                </label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar">Cancelar</button>
            </div>
        </form>        
    </div>
    
    <script>
        // Obtener el elemento select y el contenedor de participantes seleccionados
        const selectParticipantes = document.getElementById('participantes');
        const contenedorParticipantes = document.getElementById('participantes-seleccionados');
        const selectAllRol3Checkbox = document.getElementById('select_all_rol_3');

        // Manejar la selección de todos los usuarios con rol 3
        selectAllRol3Checkbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="participantes[]"]');
            if (this.checked) {
                fetch('./config/get_users_by_role.php?role=3')
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(user => {
                            // Marcar visualmente los checkboxes correspondientes
                            checkboxes.forEach(checkbox => {
                                if (checkbox.value == user.Codigo) {
                                    checkbox.checked = true;
                                }
                            });
                        });
                    })
                    .catch(error => console.error('Error fetching users:', error));
            } else {
                // Desmarcar a todos
                checkboxes.forEach(checkbox => checkbox.checked = false);
            }
        });
    </script>
</div>

<!--Footer-->
<?php include './template/footer.php'; ?>

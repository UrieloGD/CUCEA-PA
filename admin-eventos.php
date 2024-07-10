<?php include './template/header.php'; ?>
<?php include './template/navbar.php'; ?>
<?php
include './config/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener usuarios de la base de datos
$sql = "SELECT u.Codigo, u.Nombre, u.Apellido, u.Correo, r.Nombre_Rol, GROUP_CONCAT(d.Departamentos SEPARATOR ', ') AS Departamentos
        FROM Usuarios u
        LEFT JOIN Roles r ON u.Rol_ID = r.Rol_ID
        LEFT JOIN Usuarios_Departamentos ud ON u.Codigo = ud.Usuario_ID
        LEFT JOIN Departamentos d ON ud.Departamento_ID = d.Departamento_ID
        GROUP BY u.Codigo";

$resultado = $conexion->query($sql);

$filas_usuarios = "";
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $codigo = $fila["Codigo"];
        $nombre = $fila["Nombre"] . " " . $fila["Apellido"];
        $correo = $fila["Correo"];
        $rol = $fila["Nombre_Rol"];
        
        if(empty($fila["Departamentos"])){
            $rol_departamento = $rol;
        } else{
            $rol= $fila["Nombre_Rol"] . " - " . $fila["Departamentos"];
        }
    
        $filas_usuarios .= "<tr>
            <td><input type='checkbox' name='participantes[]' value='$codigo' class='checkbox-usuario'></td>
            <td>$nombre</td>
            <td>$correo</td>
            <td>$rol</td>
        </tr>";
    }
} else {
    $filas_usuarios = "<tr><td colspan='4'>No hay usuarios registrados</td></tr>";
}

$conexion->close();
?>

<title>Crear Evento</title>
<link rel="stylesheet" href="./CSS/admin-eventos.css" />

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Crear evento</h3>
        </div>
    </div>
    <div class="contenedor-formulario">
        <form id="eventoForm" method="post">
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

            <div class="form-group split-group">
                <div class="split-item">
                    <label for="participantes">
                        <i class="fas fa-users"></i> Participantes
                    </label>
                    <button class="boton-agregar-participantes" type="button" id="abrirModal">Añadir participantes</button>
                    <div id="participantes-seleccionados"></div>
                    <div id="input-participantes"></div>
                </div>
                <div class="split-item">
                    <label for="etiqueta">
                        <i class="fas fa-tag"></i> Etiqueta
                    </label>
                    <select id="etiqueta" name="etiqueta">
                        <option value="">Elige una etiqueta</option>
                        <option value="Programación Académica">Programación Académica</option>
                        <option value="Oferta Académica">Oferta Académica</option>
                        <option value="Administrativo">Administrativo</option>
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
                <a href="./admin-visual-eventos.php"><button type="button" class="btn-cancelar">Cancelar</button></a>
            </div>
        </form>
    </div>
    
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Seleccionar Participantes</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $filas_usuarios; ?>
                </tbody>
            </table>
            <button class="btn-guardar" type="button" id="confirmarParticipantes">Confirmar selección</button>
        </div>
    </div>
</div>

<script src="./JS/admin-eventos.js"></script>

<?php include './template/footer.php'; ?>
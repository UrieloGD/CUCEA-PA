<?php
require_once './config/db.php';
require_once './config/sesioniniciada.php';

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar sesión
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Verificar el rol del usuario y obtener departamento
$rol = $_SESSION['Rol_ID'];
$departamento_id = null;

try {
    // Lógica para seleccionar el departamento
    if ($rol == 1) {
        $departamento_id = $_SESSION['Departamento_ID'];
    } elseif ($rol == 2 || $rol == 3) {
        if (isset($_GET['departamento_id'])) {
            $departamento_id = filter_var($_GET['departamento_id'], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $sql_primer_departamento = "SELECT Departamento_ID FROM departamentos ORDER BY Departamento_ID LIMIT 1";
            $result_primer_departamento = mysqli_query($conexion, $sql_primer_departamento);
            
            if ($result_primer_departamento && mysqli_num_rows($result_primer_departamento) > 0) {
                $row_primer_departamento = mysqli_fetch_assoc($result_primer_departamento);
                $departamento_id = $row_primer_departamento['Departamento_ID'];
            } else {
                throw new Exception("No se encontraron departamentos disponibles.");
            }
        }
    } else {
        throw new Exception("Rol de usuario no autorizado.");
    }

    // Obtener información del departamento
    $sql_departamento = "SELECT d.Nombre_Departamento, d.Departamentos,
                        GROUP_CONCAT(DISTINCT cpp.Departamento) as departamentos_coord
                        FROM departamentos d
                        LEFT JOIN coord_per_prof cpp ON cpp.Departamento LIKE CONCAT('%', d.Nombre_Departamento, '%')
                        WHERE d.Departamento_ID = ?
                        GROUP BY d.Departamento_ID";
    
    $stmt = $conexion->prepare($sql_departamento);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result_departamento = $stmt->get_result();

    if (!$result_departamento || $result_departamento->num_rows === 0) {
        throw new Exception("No se encontró el departamento especificado.");
    }

    $row_departamento = $result_departamento->fetch_assoc();
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    $departamento_nombre = $row_departamento['Departamentos'];
    $departamentos_coord = explode(',', $row_departamento['departamentos_coord']);

    // Consulta principal de profesores
    $placeholders = array_fill(0, count($departamentos_coord), '?');
    $sql_todos_profesores = "SELECT DISTINCT 
                            Codigo, 
                            Nombre_completo, 
                            Categoria_actual,
                            Departamento
                            FROM coord_per_prof 
                            WHERE Departamento IN (" . implode(',', $placeholders) . ")
                            ORDER BY Nombre_completo";
    
    $stmt_profesores = $conexion->prepare($sql_todos_profesores);
    if (!$stmt_profesores) {
        throw new Exception("Error en la preparación de la consulta de profesores: " . $conexion->error);
    }

    // Bind de los parámetros dinámicamente
    $tipos = str_repeat('s', count($departamentos_coord));
    $stmt_profesores->bind_param($tipos, ...$departamentos_coord);
    $stmt_profesores->execute();
    $result_todos_profesores = $stmt_profesores->get_result();

    // Aquí comienza el HTML
    include './template/header.php';
    include './template/navbar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Profesores - <?php echo htmlspecialchars($departamento_nombre); ?></title>
    <link rel="stylesheet" href="./CSS/modal-profesores.css">
</head>
<body>

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="encabezado-izquierda">
            <div class="search-bar">
                <div class="search-input-container">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="text" placeholder="Buscar profesor..." id="buscar-todos-profesores" onkeyup="filtrarTodosProfesores()">
                </div>
            </div>
        </div>
        <div class="encabezado-centro">
            <h3>Profesores - <?php echo htmlspecialchars($departamento_nombre); ?></h3>
        </div>
        <div class="encabezado-derecha"></div>
    </div>

    <div class="contenedor-tabla">
        <div class="contenido-tabla">
            <div class="profesores-container">
                <table class="profesores-table">
                    <thead>
                        <tr>
                            <th class="detalle-column">Código</th>
                            <th class="detalle-column">Nombre Completo</th>
                            <th class="detalle-column">Categoria Actual</th>
                            <th class="detalle-column">Departamento</th>
                            <th class="detalle-column">Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_todos_profesores) {
                            while($row = $result_todos_profesores->fetch_assoc()) {
                                echo "<tr class='tr-info'>";
                                echo "<td class='detalle-column detalle-column1'>" . htmlspecialchars($row['Codigo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Nombre_completo'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Categoria_actual'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column'>" . htmlspecialchars($row['Departamento'] ?? 'Sin datos') . "</td>";
                                echo "<td class='detalle-column detalle-column2'>
                                        <button onclick='verDetalleProfesor(" . $row['Codigo'] . ", " . $departamento_id . ")' 
                                                class='btn-detalle'>Ver detalle</button>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No se encontraron profesores</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles -->
<div id="modal-detalle-profesor" class="modal-detalle">
    <div class="modal-content-detalle">
        <div id="detalle-profesor-contenido">
            <span class="close" onclick="cerrarModalDetalle()">&times;</span>
        </div> 
    </div>
</div>

<script src="./JS/profesores/detalle-profesor.js"></script>
<script src="./JS/profesores/filtro-profesores.js"></script>

<!-- DataTables Scripts -->
 
<?php
    } catch (Exception $e) {
        echo "<div class='error-message'>";
        echo "Error: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }

    include("./template/footer.php");
?>

<script>
$(document).ready(function() {
    $('.profesores-table').DataTable({
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: ['colvis']
    });
});
</script>
</body>
</html>
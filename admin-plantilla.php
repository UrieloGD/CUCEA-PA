<?php
session_start();

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 2) {
    header("Location: home.php");
    exit();
}
?>

<?php
include './template/header.php';
include './template/navbar.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// foreach ($required_files as $file) {
//   if (!file_exists($file)) {
//       die("Error: No se encuentra el archivo $file");
//   }
// }
?>

<title>Progreso Plantillas</title>
<link rel="stylesheet" href="./CSS/admin-plantilla.css?=v1.0" />

<div class="cuadro-principal">
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Plantillas Programación Académica</h3>
        </div>
    </div>
    <br><br>
    <div class="tabla">
        <table>
            <tr>
                <th style="text-align: center;">Departamento</th>
                <th style="text-align: center;">Archivo</th>
                <th style="text-align: center;">Último cambio</th>
                <th style="text-align: center;">Acciones</th>
            </tr>

            <?php
            $departamentos = [
                1 => "Estudios Regionales",
                2 => "Finanzas",
                3 => "Ciencias Sociales",
                4 => "PALE",
                5 => "Posgrados",
                6 => "Economía",
                7 => "Recursos Humanos",
                8 => "Métodos Cuantitativos",
                9 => "Políticas Públicas",
                10 => "Administración",
                11 => "Auditoría",
                12 => "Mercadotecnia",
                13 => "Impuestos",
                14 => "Sistemas de Información",
                15 => "Turisimo",
                16 => "Contabilidad"
            ];

            asort($departamentos);

            $sql = "SELECT Departamento_ID, Nombre_Archivo_Dep, Fecha_Subida_Dep FROM plantilla_sa";
            $result = mysqli_query($conexion, $sql);

            $departamentosConArchivos = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $departamento_id = $row["Departamento_ID"];
                    $nombre_archivo = $row["Nombre_Archivo_Dep"] ? $row["Nombre_Archivo_Dep"] : "No hay archivo asignado";
                    $fecha_subida = $row["Fecha_Subida_Dep"] ? $row["Fecha_Subida_Dep"] : "---";

                    $departamentosConArchivos[$departamento_id] = [
                        "nombre_archivo" => $nombre_archivo,
                        "fecha_subida" => $fecha_subida
                    ];
                }
            }

            foreach ($departamentos as $id => $nombre) {
                $nombre_archivo = "No hay archivo asignado";
                $fecha_subida = "---";

                if (array_key_exists($id, $departamentosConArchivos)) {
                    $nombre_archivo = $departamentosConArchivos[$id]["nombre_archivo"];
                    $fecha_subida = $departamentosConArchivos[$id]["fecha_subida"];
                }
            ?>
                <tr>
                    <td><?php echo $nombre; ?></td>
                    <td id="nombre-archivo-<?php echo $id; ?>" style="text-align: center;"><?php echo $nombre_archivo; ?></td>
                    <td id="fecha-subida-<?php echo $id; ?>" style="text-align: center;"><?php echo $fecha_subida; ?></td>
                    <td style="text-align: center; ">
                        <div class="btn-container">
                            <button class="btn" onclick="subirArchivo(<?php echo $id; ?>)">
                                <img src="./Img/Icons/iconos-plantillasAdmin/icono-subir-plantilla.png" alt="Subir Archivo">
                            </button>
                            <input type="file" id="input-file-<?php echo $id; ?>" class="hidden-input" name="file" onchange="handleFileChange(event, <?php echo $id; ?>)">
                            <input type="hidden" name="departamento_id" value="<?php echo $id; ?>">
                            <input type="hidden" id="nombre_archivo_dep-<?php echo $id; ?>" name="nombre_archivo_dep">
                            <input type="hidden" id="fecha_subida_dep-<?php echo $id; ?>" name="fecha_subida_dep">
                            <button type="submit" class="hidden-button"></button>
                            <a href="./functions/admin-plantilla/descargar-plantilla.php?departamento_id=<?php echo $id; ?>" class="btn"><img src="./Img/Icons/iconos-plantillasAdmin/icono-descargar-plantilla.png"></a>
                            <a href="#" class="btn" onclick="eliminarPlantilla(<?php echo $id; ?>)"><img src="./Img/Icons/iconos-plantillasAdmin/icono-eliminar-plantilla.png"></a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php include './template/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./JS/admin-plantilla/uploadPlantilla.js"></script>
<script src="./JS/admin-plantilla/eliminarPlantilla.js"></script>
<script src="./JS/admin-plantilla/descargarPlantilla.js"></script>
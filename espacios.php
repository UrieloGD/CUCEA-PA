<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<?php
// Obtener el edificio seleccionado (por defecto CEDA)
$edificio_seleccionado = isset($_GET['edificio']) ? $_GET['edificio'] : 'CEDA';

// Consulta para obtener los espacios del edificio seleccionado
$query = "SELECT * FROM Espacios WHERE Edificio = '$edificio_seleccionado' ORDER BY Espacio";
$result = mysqli_query($conn, $query);

// Organizar los espacios por piso
$espacios = [
    '03' => [],
    '02' => [],
    '01' => []
];

while ($row = mysqli_fetch_assoc($result)) {
    $piso = substr($row['Espacio'], 0, 2);
    if (isset($espacios[$piso])) {
        $espacios[$piso][] = $row;
    }
}
?>

<title>Espacios</title>
<link rel="stylesheet" href="./CSS/espacios.css" />

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Espacios</h3>
        </div>
    </div>

<!-- Aquí empieza el código de Espacios-->

<!-- Cuadros de texto y desplegables -->
<div class="filtros">
    <!-- <div class="filtro">
        <label for="ciclo">Ciclo</label>
        <select id="ciclo" name="ciclo">
            <option value="">Seleccione un ciclo</option>
        </select>
    </div> -->
    <div class="filtro">
        <label for="edificio">Edificio</label>
        <select id="edificio" name="edificio">
            <option value="">Seleccione un edificio</option>
            <?php
            $query = "SELECT DISTINCT Edificio FROM Espacios ORDER BY Edificio";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['Edificio'] . "'>" . $row['Edificio'] . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="filtro">
        <label for="dia">Día</label>
        <select id="dia" name="dia">
            <option value="">Seleccione un día</option>
            <option value="L">Lunes</option>
            <option value="M">Martes</option>
            <option value="I">Miércoles</option>
            <option value="J">Jueves</option>
            <option value="V">Viernes</option>
            <option value="S">Sábado</option>
            <option value="D">Domingo</option>
        </select>
    </div>
    <div class="filtro">
        <label for="horario_inicio">Hora Inicio</label>
        <select id="horario_inicio" name="horario_inicio">
            <option value="">Hora inicio</option>
            <?php
            for ($i = 7; $i <= 20; $i++) {
                $hour = str_pad($i, 2, "0", STR_PAD_LEFT) . ":00";
                echo "<option value='$hour'>$hour</option>";
            }
            ?>
        </select>
    </div>
    <div class="filtro">
        <label for="horario_fin">Hora Fin</label>
        <select id="horario_fin" name="horario_fin">
            <option value="">Hora fin</option>
            <?php
            for ($i = 8; $i <= 21; $i++) {
                $hour = str_pad($i, 2, "0", STR_PAD_LEFT) . ":55";
                echo "<option value='$hour'>$hour</option>";
            }
            ?>
        </select>
    </div>
    <button id="filtrar">Filtrar</button>
</div>

<!-- Aquí empieza el código del Edificio -->
<div class="contenedor-principal">
    <div class="techo"></div>
    <div class="contenido-edificio">
        <div class="columna-lateral izquierda">
            <div class="letra-piso">
                <span><?php echo substr($edificio_seleccionado, -1); ?></span>
            </div>
            <div class="escaleras-container">
                <div class="escalera-superior"></div>
                <div class="escalera-inferior"></div>
            </div>
        </div>

        <div class="cuadro-grande">
        <?php foreach ($espacios as $piso => $espacios_piso): ?>
            <div class="piso">
                <div class="numero-piso"></div>
                <div class="salas">
                <?php $espacios_piso = array_reverse($espacios_piso); // Invertir el orden de los espacios
                     foreach ($espacios_piso as $espacio): ?>
                        <div class="sala-container">
                            <span class="sala-texto"><?php echo $espacio['Espacio']; ?></span>
                            <div class="sala <?php echo strtolower(str_replace(' ', '-', $espacio['Etiqueta'])); ?>">
                                <img src="./Img/icons/iconos-espacios/icono-<?php echo strtolower(str_replace(' ', '-', $espacio['Etiqueta'])); ?>.png" alt="<?php echo $espacio['Etiqueta']; ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($piso == '02'): ?>
                    <div class="barandal"></div>
                <?php elseif ($piso == '01'): ?>
                    <div class="barandal"></div>
                <?php endif; ?>
                <div class="piso-gris"></div>
            </div>
        <?php endforeach; ?>
        </div>

        <div class="columna-lateral derecha">
            <div class="letra-piso">
                <span><?php echo substr($edificio_seleccionado, -1); ?></span>
            </div>
            <div class="escaleras-container">
                <div class="escalera-superior"></div>
                <div class="escalera-inferior"></div>
            </div>
        </div>
    </div>
</div>

<div class="leyenda">
    <div class="leyenda-item">
        <div class="cuadrito aula"></div>
        <span>Aula</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito aula-ocupada"></div>
        <span>Aula ocupada</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito laboratorio"></div>
        <span>Laboratorio</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito laboratorio-ocupado"></div>
        <span>Laboratorio ocupado</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito bodega"></div>
        <span>Bodega</span>
    </div>
    <div class="leyenda-item">
        <div class="cuadrito administrativo"></div>
        <span>Administrativo</span>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#edificio').change(function() {
        var edificio = $(this).val();
        window.location.href = 'espacios.php?edificio=' + edificio;
    });
});
</script>

<?php include './template/footer.php'; ?>

<?php include './template/footer.php' ?>
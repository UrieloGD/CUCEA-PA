<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php' ?>

<title>Calendario</title>
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
        <div class="filtro">
            <label for="ciclo">Ciclo</label>
            <select id="ciclo" name="ciclo">
                <option value="">Seleccione un ciclo</option>
                <?php
                $query = "SELECT DISTINCT CICLO FROM Data_Estudios_Regionales ORDER BY CICLO DESC";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['CICLO'] . "'>" . $row['CICLO'] . "</option>";
                }
                ?>
            </select>
        </div>
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
            <label for="horario">Hora Inicio</label>
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
        <label for="horario">Hora Fin</label>
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
            <span>A</span>
        </div>
        <div class="escaleras-container">
            <div class="escalera-superior"></div>
            <div class="escalera-inferior"></div>
        </div>
    </div>

    <div class="cuadro-grande">
       
        <div class="piso">
            <div class="numero-piso"></div>
            <div class="salas">
                <div class="sala-container">
                    <span class="sala-texto">A-309</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-308</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-307</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-306</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png" alt="AULA">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-305</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-304</span>
                    <div class="sala bodega">
                        <img src="./Img/icons/iconos-espacios/icono-bodega.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-303</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-302</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-301</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
            </div>
            <div class="barandal" style="bottom: 20px"></div>
            <div class="piso-gris"></div>
        </div>
        <div class="piso">
            <div class="numero-piso"></div>
            <div class="salas">
                <div class="sala-container">
                    <span class="sala-texto">A-206</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-202</span>
                    <div class="sala bodega">
                        <img src="./Img/icons/iconos-espacios/icono-bodega.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-203</span>
                    <div class="sala aula">
                        <img src="./Img/icons/iconos-espacios/icono-aula.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-204</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-205</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-201</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
            </div>
            <div class="barandal"></div>
            <div class="piso-gris"></div>
        </div>
        <div class="piso">
            <div class="numero-piso"></div>
            <div class="salas">
            <div class="sala-container">
                    <span class="sala-texto">A-104</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-103</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-102</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
                <div class="sala-container">
                    <span class="sala-texto">A-101</span>
                    <div class="sala administrativo">
                        <img src="./Img/icons/iconos-espacios/icono-administrativo.png">
                    </div>
                </div>
            </div>
            <div class="piso-gris"></div>
        </div>
    </div>

    <div class="columna-lateral derecha">
        <div class="letra-piso">
            <span>A</span>
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
    $('#filtrar').click(function() {
        var edificio = $('#edificio').val();
        var dia = $('#dia').val();
        var horario_inicio = $('#horario_inicio').val();
        var horario_fin = $('#horario_fin').val();
        var ciclo = $('#ciclo').val();

        $.ajax({
            url: './functions/espacios/obtener-espacios.php',
            method: 'POST',
            data: {
                edificio: edificio,
                dia: dia,
                horario_inicio: horario_inicio,
                horario_fin: horario_fin,
                ciclo: ciclo
            },
            success: function(response) {
                $('.contenedor-principal').html(response);
            }
        });
    });
});
</script>

<?php include './template/footer.php'; ?>

<?php include './template/footer.php' ?>
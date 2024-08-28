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
            <option value="2019A">2019A</option>
        </select>
    </div>
    <div class="filtro">
        <label for="edificio">Edificio</label>
        <select id="edificio" name="edificio">
            <option value="CEDA">CEDA</option>
        </select>
    </div>
    <div class="filtro">
        <label for="dia">Día</label>
        <select id="dia" name="dia">
            <option value="Lunes">Lunes</option>
        </select>
    </div>
    <div class="filtro">
        <label for="horario">Horario</label>
        <select id="horario" name="horario">
            <option value="16:00-18:00">16:00 - 18:00</option>
        </select>
    </div>
    <div class="filtro">
        <label for="tiempo-real">Tiempo real</label>
        <input type="text" id="tiempo-real" name="tiempo-real" readonly>
    </div>
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
            <div class="barandal"></div>
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

<?php include './template/footer.php' ?>
    <?php
    session_start();

    if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 0) {
        header("Location: home.php");
        exit();
    }
    ?>

    <?php include './template/header.php' ?>
    <?php include './template/navbar.php' ?>
    <?php require_once './config/db.php'; ?>

    <?php
    // Incluir componentes modularizados
    include './functions/horas-comparacion/departamentos.php';
    include './functions/horas-comparacion/generar-departamento.php';
    ?>

    <title>Revisión de horas asignadas</title>
    <link rel="stylesheet" href="./CSS/horas-comparacion.css?v=<?php echo filemtime('./CSS/horas-comparacion.css'); ?>">

    <div class="cuadro-principal">
        <div class="encabezado">
            <div class="titulo-bd">
                <h3>Revisión de horas asignadas</h3>
            </div>
        </div>

        <div class="contenedor-resumen-full">
            <div class="cuadro-resumen">
                <div class="titulo-resumen">
                    <img src="./Img/Icons/iconos-horas-comparacion/cuadro-resumen/titulo_icon.png" alt="Icono resumen">
                    <p>Todos los departamentos</p>
                </div>
                <div class="titulo-underline"></div>

                <div class="total-general-hrs_container">
                    <p class="titulo-total-general">Total general de horas</p>
                    <div class="tipo-hora-selector">
                        <button class="tipo-hora-btn active" data-tipo="frente-grupo">Frente Grupo</button>
                        <button class="tipo-hora-btn" data-tipo="definitivas">Definitivas</button>
                        <button class="tipo-hora-btn" data-tipo="temporales">Temporales</button>
                    </div>
                    <div class="stats-general-hrs">
                        <div class="stats-grafica">
                            <div class="circulo-progreso">
                                <div class="circulo">
                                    <span class="porcentaje" id="porcentaje-general">Cargando datos...</span>
                                </div>
                            </div>
                        </div>
                        <p id="horas-comp-general"><strong></strong></p>
                        <button class="desglose-button" id="desglose-todos">Desglose</button>
                    </div>
                </div>
                <div class="titulo-underline"></div>

                <div class="ultimas-mod_container">
                    <p class="titulo-ultimas-mod">Últimas modificaciones</p>
                    <table class="tabla-ultimas-mod">
                        <thead class="encabezado-ultimas-mod">
                            <tr>
                                <td>Fecha</td>
                                <td>Hora</td>
                                <td>Responsable</td>
                                <td>Dpto.</td>
                            </tr>
                        </thead>
                        <tbody class="cuerpo-ultimas-mod">
                            <tr>
                                <td>23/10/24</td>
                                <td>13:00</td>
                                <td>Rafael Castanedo Escobedo</td>
                                <td>Administracion</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="contenedor-dptos-listado">
                <?php foreach (array_slice($departamentos, 0, 8) as $depto): ?>
                    <?= generarDepartamento($depto) ?>
                <?php endforeach; ?>
            </div>

            <div class="contenedor-dptos-listado">
                <?php foreach (array_slice($departamentos, 8, 8) as $depto): ?>
                    <?= generarDepartamento($depto) ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal -->
        <div id="modalPersonal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalTitle">Personal del Departamento</h2>

                <!-- Agregar barra de búsqueda -->
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Buscar personal...">
                </div>

                <!-- Contenedor con scroll para la tabla -->
                <div class="table-container">
                    <table class="tabla-personal">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre Completo</th>
                                <th>Departamento</th>
                                <th>Tipo Plaza</th>
                                <th>Horas Frente Grupo</th>
                                <th>Carga Horaria</th>
                                <th>Horas Definitivas</th>
                                <th>Suma Horas</th>
                                <th>Horas Otros Departamentos</th>
                                <th>Comparación</th>
                            </tr>
                        </thead>
                        <tbody id="tablaBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="./JS/horas-comparacion/modal.js?v=<?php echo filemtime('./JS/horas-comparacion/modal.js'); ?>"></script>
        <script src="./JS/horas-comparacion/main.js?v=<?php echo filemtime('./JS/horas-comparacion/main.js'); ?>"></script>
        <script src="./JS/horas-comparacion/desplegable.js?v=<?php echo filemtime('./JS/horas-comparacion/desplegable.js'); ?>"></script>

        <?php include("./template/footer.php"); ?>
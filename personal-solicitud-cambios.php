<?php
//personal-solicitud-cambios.php
session_start();
date_default_timezone_set('America/Mexico_City');

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || ($_SESSION['Rol_ID'] != 3 && $_SESSION['Rol_ID'] != 1 && $_SESSION['Rol_ID'] != 0 && $_SESSION['Rol_ID'] != 4)) {
    header("Location: home.php");
    exit();
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Solicitudes de modificaciones</title>
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/personal-solicitud-cambios.css?v=<?php echo filemtime('./CSS/personal-solicitud-cambios/personal-solicitud-cambios.css'); ?>">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-baja.css?v=<?php echo filemtime('./CSS/personal-solicitud-cambios/modal-baja.css'); ?>">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-propuesta.css?v=<?php echo filemtime('./CSS/personal-solicitud-cambios/modal-propuesta.css'); ?>">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-baja-propuesta.css?v=<?php echo filemtime('./CSS/personal-solicitud-cambios/modal-baja-propuesta.css'); ?>">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/filtros.css?v=<?php echo filemtime('./CSS/personal-solicitud-cambios/filtros.css'); ?>">

<div class="cuadro-principal">

    <!-- Sección de filtros -->
    <div class="filtros-container">
        <div class="filtros-grid">
            <!-- Filtro por tipo -->
            <div class="filtro-grupo">
                <label for="filtro-tipo">Tipo de solicitud</label>
                <select id="filtro-tipo" class="filtro-select">
                    <option value="">Todos los tipos</option>
                    <option value="Solicitud de baja">Solicitud de baja</option>
                    <option value="Solicitud de propuesta">Solicitud de propuesta</option>
                    <option value="Solicitud de baja-propuesta">Solicitud de baja-propuesta</option>
                </select>
            </div>

            <!-- Filtro por fecha -->
            <div class="filtro-grupo">
                <label for="filtro-fecha">Período</label>
                <select id="filtro-fecha" class="filtro-select">
                    <option value="">Todas las fechas</option>
                    <option value="hoy">Hoy</option>
                    <option value="7dias">Últimos 7 días</option>
                    <option value="1mes">Último mes</option>
                    <option value="3meses">Últimos 3 meses</option>
                </select>
            </div>

            <!-- Filtro por estado -->
            <div class="filtro-grupo">
                <label for="filtro-estado">Estado</label>
                <select id="filtro-estado" class="filtro-select">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="En revision">En revisión</option>
                    <option value="Aprobado">Aprobado</option>
                    <option value="Rechazado">Rechazado</option>
                </select>
            </div>

            <!-- Filtro por departamento (solo visible para rol 3 y 0) -->
            <?php if ($_SESSION['Rol_ID'] == 3 || $_SESSION['Rol_ID'] == 0) { ?>
            <div class="filtro-grupo">
                <label for="filtro-departamento">Departamento</label>
                <select id="filtro-departamento" class="filtro-select">
                    <option value="">Todos los departamentos</option>
                    <?php
                    // Obtener departamentos para el select
                    $sql_depts = "SELECT Departamento_ID, Departamentos FROM departamentos ORDER BY Departamentos";
                    $result_depts = mysqli_query($conexion, $sql_depts);
                    while($dept = mysqli_fetch_assoc($result_depts)) {
                        echo "<option value='{$dept['Departamento_ID']}'>{$dept['Departamentos']}</option>";
                    }
                    ?>
                </select>
            </div>
            <?php } ?>
        </div>

        <!-- Contador de resultados -->
        <div class="contador-resultados">
            <span id="contador-solicitudes">Mostrando todas las solicitudes</span>
        </div>
    </div>

    <div class="cuadro-scroll">
    <!-- <div class="encabezado">
        <div class="titulo-bd">
            <h3>Solicitudes de modificaciones</h3>
        </div>
    </div> -->

    <div class="solicitudes-scroll-container">
        <div class="solicitud-contenedor-principal">
            <?php
            // Obtener todas las solicitudes
            include './functions/personal-solicitud-cambios/obtener_solicitudes.php';
            $solicitudes = obtenerSolicitudes($conexion);

            if (empty($solicitudes)) {
            ?>
                <div class="info-sup no-solicitudes">
                    <div class="color-sin-solicitudes"></div>
                    <div class="mensaje-sin-solicitudes">
                        <i class="fa fa-inbox" aria-hidden="true"></i>
                        <h3>No hay solicitudes para mostrar</h3>
                        <p>Cuando crees una solicitud aparecerá aquí</p>
                    </div>
                </div>
            <?php
            } else {
                foreach ($solicitudes as $index => $solicitud) {
                    include './functions/personal-solicitud-cambios/mostrar_solicitud.php';
                }
            }
            ?>
        </div>
    </div>

    <!-- Botón de nueva solicitud - visible solo para roles que no sean 3 (Coordinación de personal) -->
    <?php if ($_SESSION['Rol_ID'] != 3) { ?>
        <div class="container-boton-nueva-solicitud">
            <button class="boton-nueva-solicitud" id="nueva-solicitud-btn">Nueva solicitud</button>
            <ul class="lista-opciones" id="lista-opciones">
                <li>Solicitud de baja</li>
                <li>Solicitud de propuesta</li>
                <li>Solicitud de baja-propuesta</li>
            </ul>
        </div>
    <?php } ?>
    </div>
</div>

<!-- No funciona este script DOM si lo colocamos en el personal-solicitud-cambios.js -->
<script>
    // Definir rol_usuario para que esté disponible en todos los scripts
    const rol_usuario = <?php echo $_SESSION['Rol_ID']; ?>;

    // Definir la función mostrarInformacion globalmente
    function mostrarInformacion(contenedorId, icono) {
        const contenedor = document.getElementById(contenedorId);

        if (contenedor) {
            // Alternar la clase 'active' en el contenedor
            contenedor.classList.toggle('active');

            // Si el contenedor está activo, mostrarlo
            if (contenedor.classList.contains('active')) {
                contenedor.style.display = 'block';
                // Pequeño retraso para asegurar que la transición funcione
                setTimeout(() => {
                    contenedor.style.maxHeight = contenedor.scrollHeight + "px";
                }, 10);
                icono.classList.add('rotated');
            } else {
                // Si el contenedor no está activo, ocultarlo
                contenedor.style.maxHeight = '0';
                icono.classList.remove('rotated');
                // Agregar un retraso antes de ocultar completamente
                setTimeout(() => {
                    if (!contenedor.classList.contains('active')) {
                        contenedor.style.display = 'none';
                    }
                }, 200);
            }
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('nueva-solicitud-btn');
        const lista = document.getElementById('lista-opciones');
        const modales = {
            'Solicitud de baja': document.getElementById('solicitud-modal-baja-academica'),
            'Solicitud de propuesta': document.getElementById('solicitud-modal-propuesta-academica'),
            'Solicitud de baja-propuesta': document.getElementById('solicitud-modal-baja-propuesta')
        };

        // Asegurarse de que los modales estén ocultos al inicio
        Object.values(modales).forEach(modal => {
            if (modal) modal.style.display = 'none';
        });

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            lista.classList.toggle('show');
        });

        lista.addEventListener('click', function(e) {
            const opcionSeleccionada = e.target.innerText;
            if (modales[opcionSeleccionada]) {
                lista.classList.remove('show');
                openModal(modales[opcionSeleccionada]);
            }
        });

        document.addEventListener('click', function(e) {
            if (!btn.contains(e.target) && !lista.contains(e.target)) {
                lista.classList.remove('show');
            }
        });

        // Función para abrir el modal
        function openModal(modal) {
            if (!modal) return; // Verificar que el modal existe

            modal.style.display = 'block';

            const closeButton = modal.querySelector('.close-button');
            const modalContent = modal.querySelector('.modal-content-propuesta') || modal.querySelector('.modal-content-baja');

            if (closeButton) { // Verificar que existe el botón antes de agregar el evento
                closeButton.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }

            if (modalContent) { // Verificar que existe el contenido antes de agregar el evento
                modalContent.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Agregar el evento de clic fuera una sola vez
            const clickOutside = function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    window.removeEventListener('click', clickOutside); // Remover el evento después de usarlo
                }
            };
            window.addEventListener('click', clickOutside);
        }
    });
</script>
<!-- JS Principal -->
<script src="./JS/personal-solicitud-cambios/personal-solicitud-cambios.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/personal-solicitud-cambios.js'); ?>"></script>
<!-- modales -->
<?php include './functions/personal-solicitud-cambios/modales/modal-baja.php' ?>
<?php include './functions/personal-solicitud-cambios/modales/modal-propuesta.php' ?>
<?php include './functions/personal-solicitud-cambios/modales/modal-baja-propuesta.php' ?>
<script src="./JS/personal-solicitud-cambios/modal-baja.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/modal-baja.js'); ?>"></script>
<script src="./JS/personal-solicitud-cambios/modal-propuesta.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/modal-propuesta.js'); ?>"></script>
<script src="./JS/personal-solicitud-cambios/modal-baja-propuesta.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/modal-baja-propuesta.js'); ?>"></script>
<script src="./JS/personal-solicitud-cambios/nueva-solicitud.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/nueva-solicitud.js'); ?>"></script>
<!-- generar pdfs -->
<script src="./JS/personal-solicitud-cambios/pdfs/generar-pdf-baja.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/pdfs/generar-pdf-baja.js'); ?>"></script>
<script src="./JS/personal-solicitud-cambios/pdfs/generar-pdf-propuesta.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/pdfs/generar-pdf-propuesta.js'); ?>"></script>
<script src="./JS/personal-solicitud-cambios/pdfs/generar-pdf-baja-propuesta.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/pdfs/generar-pdf-propuesta.js'); ?>"></script>
<!-- Script para manejo de detalles de solicitudes -->
<script src="./JS/personal-solicitud-cambios/ver-detalles.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/ver-detalles.js'); ?>"></script>
<!-- Filtros -->
<script src="./JS/personal-solicitud-cambios/filtros.js?v=<?php echo filemtime('./JS/personal-solicitud-cambios/filtros.js'); ?>"></script>
<!-- JQuerys -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php include("./template/footer.php"); ?>
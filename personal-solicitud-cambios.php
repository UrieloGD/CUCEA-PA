<?php
//personal-solicitud-cambios.php
session_start();
date_default_timezone_set('America/Mexico_City');

// Verificar si el usuario está autenticado y tiene el Rol_ID correcto
if (!isset($_SESSION['Codigo']) || $_SESSION['Rol_ID'] != 3 and $_SESSION['Rol_ID'] != 1) {
    header("Location: home.php");
    exit();
}
?>

<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<title>Solicitudes de modificaciones</title>
<link rel="stylesheet" href="./CSS//personal-solicitud-cambios/personal-solicitud-cambios.css">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-baja.css">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-propuesta.css">
<link rel="stylesheet" href="./CSS/personal-solicitud-cambios/modal-baja-propuesta.css">

    <div class="cuadro-principal">
        <div class="encabezado">
            <div class="titulo-bd">
                <h3>Solicitudes de modificaciones</h3>
            </div>
        </div>

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
                    foreach($solicitudes as $index => $solicitud) {
                        include './functions/personal-solicitud-cambios/mostrar_solicitud.php';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Botón de nueva solicitud -->
        <div class="container-boton-nueva-solicitud">
            <button class="boton-nueva-solicitud" id="nueva-solicitud-btn">Nueva solicitud</button>
            <ul class="lista-opciones" id="lista-opciones">
                <li>Solicitud de baja</li>
                <li>Solicitud de propuesta</li>
                <li>Solicitud de baja-propuesta</li>
            </ul>
        </div>
    </div>

    <!-- Modal Solicitudes Baja -->
    <?php include './functions/personal-solicitud-cambios/modales/modal-baja.php' ?>
    <?php include './functions/personal-solicitud-cambios/modales/modal-propuesta.php' ?>
    <?php include './functions/personal-solicitud-cambios/modales/modal-baja-propuesta.php' ?>

    <!-- No funciona este script DOM si lo colocamos en el personal-solicitud-cambios.js -->
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
    <script src="./JS/personal-solicitud-cambios/personal-solicitud-cambios.js"></script>
    <script src="./JS/personal-solicitud-cambios/modal-baja.js"></script>
    <script src="./JS/personal-solicitud-cambios/modal-propuesta.js"></script>
    <script src="./JS/personal-solicitud-cambios/modal-baja-propuesta.js"></script>
    <script src="./JS/personal-solicitud-cambios/nueva-solicitud.js"></script>

<?php include("./template/footer.php"); ?>
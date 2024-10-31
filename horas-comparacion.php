<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php'; ?>

<title>Revisión de horas asignadas</title>
<link rel="stylesheet" href="./CSS/horas-comparacion.css">

<!--Cuadro principal del home-->
<div class="cuadro-principal">
    <!--Pestaña azul-->
    <div class="encabezado">
        <div class="titulo-bd">
            <h3>Revisión de horas asignadas</h3>
        </div>
    </div>

    <div class="grid-departamentos">

        <!-- Card para "Todos los departamentos" -->
        <div class="departamento-card todos" data-departamento="todos">
            <div class="departamento-overlay">
                <span class="departamento-nombre">Todos los Departamentos</span>
            </div>
        </div>

        <?php
        // Consulta para obtener los departamentos
        $query = "SELECT * FROM Departamentos ORDER BY Departamentos";
        $result = mysqli_query($conn, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="departamento-card" data-departamento="' . htmlspecialchars($row['Departamentos']) . '">';
                echo '<div class="departamento-overlay">';
                echo '<span class="departamento-nombre">' . htmlspecialchars($row['Departamentos']) . '</span>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener elementos del DOM
        const modal = document.getElementById('modalPersonal');
        const span = document.getElementsByClassName('close')[0];
        const modalTitle = document.getElementById('modalTitle');
        const tablaBody = document.getElementById('tablaBody');
        const departamentoCards = document.querySelectorAll('.departamento-card');

        // Función para abrir el modal
        function openModal(departamento) {
            modal.style.display = 'block';
            modalTitle.textContent = departamento === 'todos' ?
                'Personal de Todos los Departamentos' :
                `Personal del Departamento ${departamento}`;

            // Actualizar estructura de la tabla según el departamento
            const thead = document.querySelector('.tabla-personal thead tr');
            thead.innerHTML = departamento === 'todos' ?
                `<th>Código</th>
                <th>Nombre Completo</th>
                <th>Departamento</th>
                <th>Categoría Actual</th>
                <th>Tipo Plaza</th>
                <th>Carga Horaria</th>
                <th>Horas Frente Grupo</th>
                <th>Horas Definitivas</th>
                <th>Suma Horas Totales</th>
                <th>Horas p/Departamento</th>` :
                `<th>Código</th>
                <th>Nombre Completo</th>
                <th>Categoría Actual</th>
                <th>Tipo Plaza</th>
                <th>Carga Horaria</th>
                <th>Horas Frente Grupo</th>
                <th>Horas Definitivas</th>
                <th>Suma Horas Totales</th>
                <th>Horas por Departamento</th>`;

            // Realizar la petición AJAX
            fetchPersonalData(departamento);
        }

        // Función para cerrar el modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Función para mostrar mensaje de error
        function showError(message) {
            tablaBody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: red;">
            ${message}</td></tr>`;
        }

        // Funcionalidad de búsqueda
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tablaBody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Función para determinar la clase de las horas
        function getHorasClass(actual, requerido) {
            actual = parseInt(actual) || 0;
            requerido = parseInt(requerido) || 0;
            
            if (actual === 0 && requerido === 0) return 'horas-cero';
            if (actual < requerido) return 'horas-faltantes';
            if (actual === requerido) return 'horas-correctas';
            return 'horas-excedidas';
        }

        // Función para obtener los datos del personal
        function fetchPersonalData(departamento) {
            // Limpiar la búsqueda al cargar nuevos datos
            searchInput.value = '';

            // Mostrar mensaje de carga
            const colSpan = 9; // Ajustado para las nuevas columnas
            tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">Cargando...</td></tr>`;

            fetch('./functions/horas-comparacion/obtener-personal.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `departamento=${encodeURIComponent(departamento)}`
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', text);
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    if (!Array.isArray(data) || data.length === 0) {
                        tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center;">No se encontraron datos para mostrar</td></tr>`;
                        return;
                    }

                    //Encabezados de la tabla
                    const thead = document.querySelector('.tabla-personal thead tr');
                    thead.innerHTML = `
                        <th>Código</th>
                        <th>Nombre Completo</th>
                        <th>Departamento</th>
                        <th>Categoría Actual</th>
                        <th>Tipo Plaza</th>
                        <th>Carga Horaria</th>
                        <th>Horas Frente Grupo</th>
                        <th>Horas Definitivas</th>
                        <th>Horas Frente Grupo por Departamento</th>
                        <th>Horas Definitivas por Departamento</th>
                    `;

                    tablaBody.innerHTML = ''; // Limpiar tabla

                    data.forEach(persona => {
                        const row = document.createElement('tr');
                        
                        // Procesar horas frente a grupo
                        const horasCargoActual = persona.suma_cargo_plaza || 0;
                        const horasFrenteRequeridas = persona.Horas_frente_grupo || 0;
                        const claseFrenteGrupo = getHorasClass(horasCargoActual, horasFrenteRequeridas);
                        
                        // Procesar horas definitivas
                        const horasDefActual = persona.suma_horas_definitivas || 0;
                        const horasDefRequeridas = persona.Horas_definitivas || 0;
                        const claseDefinitivas = getHorasClass(horasDefActual, horasDefRequeridas);
                        
                        // Formatear las horas con sus respectivas clases
                        const horasFrenteGrupo = `<span class="${claseFrenteGrupo}">${horasCargoActual}/${horasFrenteRequeridas}</span>`;
                        const horasDefinitivas = `<span class="${claseDefinitivas}">${horasDefActual}/${horasDefRequeridas}</span>`;
                        
                        
                        // Manejar casos vacíos para horas por departamento
                        const horasCargoDeptos = persona.horas_cargo_por_departamento && 
                                                persona.horas_cargo_por_departamento.trim() !== '' ? 
                                                persona.horas_cargo_por_departamento : 'N/A';
                                                
                        const horasDefinitivasDeptos = persona.horas_definitivas_por_departamento && 
                                                    persona.horas_definitivas_por_departamento.trim() !== '' ? 
                                                    persona.horas_definitivas_por_departamento : 'N/A';
                        
                        const tdContent = `
                            <td>${persona.Codigo || ''}</td>
                            <td>${persona.Nombre_completo || ''}</td>
                            <td>${persona.Departamento || ''}</td>
                            <td>${persona.Categoria_actual || ''}</td>
                            <td>${persona.Tipo_plaza || ''}</td>
                            <td>${persona.Carga_horaria || ''}</td>
                            <td>${horasFrenteGrupo}</td>
                            <td>${horasDefinitivas}</td>
                            <td style="white-space: pre-line;"><small>${horasCargoDeptos}</small></td>
                            <td style="white-space: pre-line;"><small>${horasDefinitivasDeptos}</small></td>
                        `;
                        
                        row.innerHTML = tdContent;
                        tablaBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    tablaBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center; color: red;">
                ${error.message || 'Error al cargar los datos'}</td></tr>`;
                });
        }

        // Event Listeners
        departamentoCards.forEach(card => {
            card.addEventListener('click', function() {
                const departamento = this.dataset.departamento;
                openModal(departamento);
            });
        });

        span.onclick = closeModal;

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    });
</script>

<?php include("./template/footer.php"); ?>
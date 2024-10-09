<!--header -->
<?php include './template/header.php' ?>
<!-- navbar -->
<?php include './template/navbar.php' ?>
<!-- Conexión a la base de datos -->
<?php include './config/db.php';?>

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
            <div id="modalBody">
                <table class="tabla-personal">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre Completo</th>
                            <th>Tipo Plaza</th>
                            <th>Horas Frente Grupo</th>
                            <th>Carga Horaria</th>
                            <th>Horas Definitivas</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                    </tbody>
                </table>
            </div>
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
        modalTitle.textContent = departamento === 'todos' 
            ? 'Personal de Todos los Departamentos' 
            : `Personal del Departamento ${departamento}`;
        
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

    // Función para obtener los datos del personal
    function fetchPersonalData(departamento) {
        // Mostrar mensaje de carga
        tablaBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Cargando...</td></tr>';

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
                showError('No se encontraron datos para mostrar');
                return;
            }

            tablaBody.innerHTML = ''; // Limpiar tabla
            
            data.forEach(persona => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${persona.Codigo || ''}</td>
                    <td>${persona.Nombre_completo || ''}</td>
                    <td>${persona.Tipo_plaza || ''}</td>
                    <td>${persona.Horas_frente_grupo || '0'}</td>
                    <td>${persona.Carga_horaria || ''}</td>
                    <td>${persona.Horas_definitivas || '0'}</td>
                `;
                tablaBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            showError(error.message || 'Error al cargar los datos');
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

<?php include ("./template/footer.php"); ?>
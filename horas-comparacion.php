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
        $query = "SELECT * FROM Departamentos ORDER BY Nombre_Departamento";
        $result = mysqli_query($conn, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="departamento-card" data-departamento="' . $row['Nombre_Departamento'] . '">';
                echo '<div class="departamento-overlay">';
                echo '<span class="departamento-nombre">' . $row['Departamentos'] . '</span>';
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

<!-- JavaScript para manejar el modal -->
<script>
const modal = document.getElementById('modalPersonal');
const span = document.getElementsByClassName('close')[0];
const modalTitle = document.getElementById('modalTitle');
const tablaBody = document.getElementById('tablaBody');

// Mapping de departamentos
const departamentoMapping = <?php echo json_encode($departamento_mapping); ?>;

document.querySelectorAll('.departamento-card').forEach(card => {
    card.addEventListener('click', function() {
        const departamento = this.dataset.departamento;
        cargarDatosPersonal(departamento);
        modal.style.display = 'block';
    });
});

span.onclick = function() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function cargarDatosPersonal(departamento) {
    const departamentoEncoded = encodeURIComponent(departamento);
    
    // Ajusta esta ruta a donde realmente está tu archivo PHP
    fetch(`obtener-personal.php?departamento=${departamentoEncoded}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
                tablaBody.innerHTML = '<tr><td colspan="6">No se encontraron registros para este departamento</td></tr>';
                return;
            }

            tablaBody.innerHTML = ''; // Limpiar tabla
            
            // Actualizar título del modal
            modalTitle.textContent = departamento === 'todos' ? 
                'Personal de Todos los Departamentos' : 
                `Personal del Departamento de ${departamento}`;
            
            // Llenar la tabla con los datos
            if (data.length === 0) {
                tablaBody.innerHTML = '<tr><td colspan="6">No hay registros disponibles</td></tr>';
            } else {
                data.forEach(persona => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${persona.codigo || ''}</td>
                        <td>${persona.nombre_completo || ''}</td>
                        <td>${persona.tipo_plaza || ''}</td>
                        <td>${persona.horas_frente_grupo || '0'}</td>
                        <td>${persona.carga_horaria || ''}</td>
                        <td>${persona.horas_definitivas || '0'}</td>
                    `;
                    tablaBody.appendChild(row);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tablaBody.innerHTML = '<tr><td colspan="6">Error al cargar los datos</td></tr>';
        });
}
</script>

<?php include ("./template/footer.php"); ?>
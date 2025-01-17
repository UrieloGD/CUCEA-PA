<!-- Modal para listar todos los profesores del departamento -->
<div id="modal-todos-profesores" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalTodosProfesores()">&times;</span>
        
        <!-- Barra de búsqueda -->
        <div class="search-bar">
            <div class="search-input-container">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input type="text" placeholder="Buscar profesor..." id="buscar-todos-profesores" onkeyup="filtrarTodosProfesores()">
            </div>
        </div>

        <!-- Tabla de profesores -->
        <div class="profesores-container">
            <h2>Todos los Profesores - <?php echo $departamento_nombre; ?></h2>
            <table class="profesores-table">
                <thead>
                    <tr>
                        <th class="detalle-column">Código</th>
                        <th>Nombre Completo</th>
                        <th class="detalle-column">Detalles del Profesor</th>
                    </tr>
                </thead>
                <tbody id="lista-todos-profesores">
                <?php
                include './config/db.php';
                
                // Array de mapeo de departamentos
                $departamentos_mapping = [

                    'Administración' => [
                        'Administracion',
                        'ADMINISTRACION',
                        'Administración'
                    ],
                    'PALE' => [
                        'ADMINISTRACION/PROGRAMA DE APRENDIZAJE DE LENGUA EXTRANJERA',
                        'PALE',
                        'Programa de Aprendizaje de Lengua Extranjera'
                    ],
                    'Auditoría' => [
                        'Auditoria',
                        'AUDITORIA',
                        'Auditoría',
                        'SECRETARIA ADMINISTRATIVA/AUDITORIA'
                    ],
                    'Ciencias_Sociales' => [
                        'CERI/CIENCIAS SOCIALES',
                        'CIENCIAS SOCIALES',
                        'Ciencias Sociales'
                    ],
                    'Políticas_Públicas' => [
                        'POLITICAS PUBLICAS',
                        'Políticas Públicas',
                        'Politicas Publicas'
                    ],
                    'Contabilidad' => [
                        'CONTABILIDAD',
                        'Contabilidad'
                    ],
                    'Economía' => [
                        'ECONOMIA',
                        'Economía',
                        'Economia'
                    ],
                    'Estudios_Regionales' => [
                        'ESTUDIOS REGIONALES',
                        'Estudios Regionales'
                    ],
                    'Finanzas' => [
                        'FINANZAS',
                        'Finanzas'
                    ],
                    'Impuestos' => [
                        'IMPUESTOS',
                        'Impuestos'
                    ],
                    'Mercadotecnia' => [
                        'MERCADOTECNIA',
                        'Mercadotecnia',
                        'MERCADOTECNIA Y NEGOCIOS INTERNACIONALES'
                    ],
                    'Métodos_Cuantitativos' => [
                        'METODOS CUANTITATIVOS',
                        'Métodos Cuantitativos',
                        'Metodos Cuantitativos'
                    ],
                    'Recursos_Humanos' => [
                        'RECURSOS HUMANOS',
                        'Recursos Humanos',
                        'RECURSOS_HUMANOS'
                    ],
                    'Sistemas_de_Información' => [
                        'SISTEMAS DE INFORMACION',
                        'Sistemas de Información',
                        'Sistemas de Informacion'
                    ],
                    'Turismo' => [
                        'TURISMO',
                        'Turismo',
                        'Turismo R. y S.'
                    ],
                    'Posgrados' => [
                        'POSGRADOS',
                        'Posgrados'
                    ]
                ];

                // Encontrar todas las variantes del departamento actual
                $departamento_variantes = [];
                foreach ($departamentos_mapping as $key => $variants) {
                    if ($key === $nombre_departamento) {
                        $departamento_variantes = $variants;
                        break;
                    }
                }

                // Crear la condición WHERE para la consulta SQL
                $where_conditions = [];
                foreach ($departamento_variantes as $variante) {
                    $where_conditions[] = "Departamento = '" . mysqli_real_escape_string($conexion, $variante) . "'";
                }
                $where_clause = count($where_conditions) > 0 ? implode(' OR ', $where_conditions) : "1=0";

                // Consulta SQL con las variantes del departamento
                $sql_todos_profesores = "SELECT DISTINCT Codigo, Nombre_Completo 
                                       FROM coord_per_prof 
                                       WHERE $where_clause
                                       ORDER BY Nombre_Completo";
                
                $result_todos_profesores = mysqli_query($conexion, $sql_todos_profesores);
                
                if ($result_todos_profesores) {
                    while($row = mysqli_fetch_assoc($result_todos_profesores)) {
                        echo "<tr>";
                        echo "<td class='detalle-column'>" . htmlspecialchars($row['Codigo'] ?? 'Sin datos') . "</td>";
                        echo "<td class=''>" . htmlspecialchars($row['Nombre_Completo'] ?? 'Sin datos') . "</td>";
                        echo "<td class='detalle-column'><button onclick='verDetalleProfesor(" . $row['Codigo'] . ")' class='btn-detalle'>Ver detalle</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Error en la consulta: " . mysqli_error($conexion) . "</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para visualizar información detallada del profesor -->
<div id="modal-detalle-profesor" class="modal">
    <div class="modal-content">
        <!--<h2>Detalle del Profesor</h2>-->
        <div id="detalle-profesor-contenido">
            <span class="close" onclick="cerrarModalDetalle()">&times;</span>
            <!--El contenido se cargará dinámicamente -->
        </div> 
    </div>
</div>
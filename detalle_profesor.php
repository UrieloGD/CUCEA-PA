<link rel="stylesheet" href="./CSS/detalle-profesor.css?=v1.0">

<script>
// Función para actualizar los días activos
function actualizarDiasActivos(dias, crnId, modulo, esvirtual) {
    requestAnimationFrame(() => {
        const contenedor = document.getElementById(`weekdays-${crnId}-${modulo}`);
        if (!contenedor) return;

        const diasSemana = contenedor.querySelectorAll('.day');
        diasSemana.forEach(dia => {
            const letraDia = dia.textContent;
            if (dias.includes(letraDia)) {
                dia.classList.add('active');
                if (esvirtual === 'true') {
                    dia.classList.add('virtual');
                } else {
                    dia.classList.remove('virtual');
                }
            } else {
                dia.classList.remove('active');
                dia.classList.remove('virtual');
            }
        });
    });
}

// Función para actualizar los días en materias híbridas
function actualizarDiasHibridos(crnId, modulo, diasPresenciales, diasVirtuales) {
    requestAnimationFrame(() => {
        const contenedor = document.getElementById(`weekdays-${crnId}-${modulo}`);
        if (!contenedor) return;

        const diasSemana = contenedor.querySelectorAll('.day');
        diasSemana.forEach(dia => {
            const letraDia = dia.textContent;
            dia.classList.remove('active', 'virtual');
            
            if (diasPresenciales.includes(letraDia)) {
                dia.classList.add('active');
            } else if (diasVirtuales.includes(letraDia)) {
                dia.classList.add('active');
                dia.classList.add('virtual');

                dia.addEventListener('mouseenter', () => {
                    dia.classList.add('show-message');
                });
                dia.addEventListener('mouseleave', () => {
                    dia.classList.remove('show-message');
                });
            }
        });
    });
}
</script>

<?php
include './config/db.php';

if(isset($_POST['codigo_profesor'])) {
    $codigo_profesor = (int)$_POST['codigo_profesor'];
    $departamento_id = (int)$_POST['departamento_id'];

    $sql_departamento = "SELECT Nombre_Departamento, Departamentos FROM Departamentos WHERE Departamento_ID = $departamento_id";
    $result_departamento = mysqli_query($conexion, $sql_departamento);
    $row_departamento = mysqli_fetch_assoc($result_departamento);
    $nombre_departamento = $row_departamento['Nombre_Departamento'];
    $departamento_nombre = $row_departamento['Departamentos'];

   // Obtener información personal del profesor
   $sql_profesor = "SELECT DISTINCT 
                        Codigo, 
                        Nombre_completo, 
                        Correo, 
                        Categoria_actual, 
                        Departamento
                    FROM Coord_Per_Prof 
                    WHERE Codigo = ?";
                                       
    $stmt_profesor = mysqli_prepare($conexion, $sql_profesor);
    mysqli_stmt_bind_param($stmt_profesor, "i", $codigo_profesor);
    mysqli_stmt_execute($stmt_profesor);
    $result_profesor = mysqli_stmt_get_result($stmt_profesor);
    $datos_profesor = mysqli_fetch_assoc($result_profesor);

    // Función para obtener todas las tablas de departamentos
    function obtenerTablasDepartamentos($conexion) {
        $sql = "SELECT Nombre_Departamento FROM Departamentos";
        $result = mysqli_query($conexion, $sql);
        $tablas = [];
        while($row = mysqli_fetch_assoc($result)) {
            $tablas[] = "Data_" . $row['Nombre_Departamento'];
        }
        return $tablas;
    }

    function limpiarNombreDepartamento($departamento) {
        // Eliminar el prefijo "Data_"
        $nombre = preg_replace('/^Data_/', '', $departamento);
        // Reemplazar guiones bajos con espacios
        $nombre = str_replace('_', ' ', $nombre);
        return $nombre;
    }

    function obtenerTodasLasMaterias($codigo_profesor, $tablas) {
        global $conexion;
        $todas_las_materias = [];
        $materias_unicas = [];
        $materias_temp = [];
        
        foreach($tablas as $tabla) {
            // Verificar si la tabla existe
            $check_table = mysqli_query($conexion, "SHOW TABLES LIKE '$tabla'");
            if(mysqli_num_rows($check_table) > 0) {
                $sql = "SELECT DISTINCT *, '$tabla' as departamento_origen 
                        FROM $tabla 
                        WHERE CODIGO_PROFESOR = ?";
                
                $stmt = mysqli_prepare($conexion, $sql);
                mysqli_stmt_bind_param($stmt, "i", $codigo_profesor);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                while($row = mysqli_fetch_assoc($result)) {
                    $materias_temp[] = $row;
                    /*
                    $unique_key = $row['CRN'] . '-' . $row['MATERIA'] . '-' . $row['AULA'] . '-' . $row['MODULO'];
                    if(!isset($materias_unicas[$unique_key])) {
                        $materias_unicas[$unique_key] = $row;
                        $todas_las_materias[] = $row;
                    }
                    */
                }
            }
        }
        // Procesar y combinar materias
        foreach($materias_temp as $materia) {
            $crn = $materia['CRN'];
            $nombre_materia = $materia['MATERIA'];
            
            // Crear una clave única basada en CRN y nombre de materia
            $unique_key = $crn . '-' . $nombre_materia;
            
            if (!isset($materias_unicas[$unique_key])) {
                // Primera vez que vemos esta materia
                $materias_unicas[$unique_key] = $materia;
            } else {
                // Ya existe una entrada para esta materia
                $existing = $materias_unicas[$unique_key];
                
                // Si una es virtual y la otra tiene aula física, combinarlas
                if (($existing['MODULO'] === 'CVIRTU' && $materia['MODULO'] !== 'CVIRTU') ||
                    ($existing['MODULO'] !== 'CVIRTU' && $materia['MODULO'] === 'CVIRTU')) {
                    
                    // Mantener la información del aula física
                    $aula_fisica = ($existing['MODULO'] !== 'CVIRTU') ? $existing : $materia;
                    $virtual = ($existing['MODULO'] === 'CVIRTU') ? $existing : $materia;
                    
                    // Combinar la información
                    $materias_unicas[$unique_key] = array_merge($aula_fisica, [
                        'es_hibrida' => true,
                        'dias_virtuales' => [
                            'L' => $virtual['L'],
                            'M' => $virtual['M'],
                            'I' => $virtual['I'],
                            'J' => $virtual['J'],
                            'V' => $virtual['V'],
                            'S' => $virtual['S'],
                            'D' => $virtual['D']
                        ]
                    ]);
                }
            }
        }
        
        return array_values($materias_unicas);
    }

    
    // Obtain department and professor information
    $tablas_departamentos = obtenerTablasDepartamentos($conexion);
    $materias = obtenerTodasLasMaterias($codigo_profesor, $tablas_departamentos);
    
    if(!empty($materias) && $datos_profesor) {
        $profesor = $materias[0];
        // Group courses by name
        $materias_por_nombre = [];
        foreach ($materias as $materia) {
            $nombre_materia = $materia['MATERIA'];
            
            // Si no existe la materia, crear un nuevo arreglo
            if (!isset($materias_por_nombre[$nombre_materia])) {
                $materias_por_nombre[$nombre_materia] = [];
            }
            
            // Flag para verificar si ya existe un registro similar
            $existe_registro_similar = false;
            
            // Revisar registros existentes para combinar
            foreach ($materias_por_nombre[$nombre_materia] as &$curso_existente) {
                // Condiciones para combinar registros
                $es_mismo_crn = $curso_existente['CRN'] === $materia['CRN'];
                $es_modalidad_diferente = $curso_existente['MODULO'] !== $materia['MODULO'];
                
                if ($es_mismo_crn || $es_modalidad_diferente) {
                    // Si es un registro híbrido
                    if (!isset($curso_existente['es_hibrida'])) {
                        $curso_existente['es_hibrida'] = true;
                        $curso_existente['dias_virtuales'] = [
                            'L' => $materia['L'],
                            'M' => $materia['M'],
                            'I' => $materia['I'],
                            'J' => $materia['J'],
                            'V' => $materia['V'],
                            'S' => $materia['S'],
                            'D' => $materia['D']
                        ];
                    }
                    
                    $existe_registro_similar = true;
                    break;
                }
            }
            
            // Si no existe un registro similar, agregar el nuevo
            if (!$existe_registro_similar) {
                $materias_por_nombre[$nombre_materia][] = $materia;
            }
        }
        ?>

        <div class="container-profesor">
            <div class="header-profesor">
                <div class="profesor-avatar-modal">
                    <span class="avatar-initials">
                        <?php 
                            $nombres = explode(' ', $datos_profesor['Nombre_completo']);
                            echo substr($nombres[0], 0, 1) . (isset($nombres[1]) ? substr($nombres[1], 0, 1) : '');
                        ?>
                    </span>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($datos_profesor['Nombre_completo']); ?></h2>
                    <p><?php echo htmlspecialchars($datos_profesor['Correo']); ?></p>
                    <div class="profile-details">
                        <table class="table-profile">
                            <tbody>
                                <tr>
                                    <td>
                                        <div><span class="profile-span">Código:</span><?php echo htmlspecialchars($datos_profesor['Codigo']); ?></div>
                                    </td>
                                    <td>
                                        <div><span class="profile-span">Categoría:</span><?php echo htmlspecialchars($datos_profesor['Categoria_actual']); ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="profile-span">Departamento:</span>
                                            <span class="data-value3"><?php echo htmlspecialchars($datos_profesor['Departamento']); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><span class="profile-span">Horas frente a grupo:</span> <span class="data-value2">36/40</span></div>
                                    </td>
                                    <td>
                                        <div><span class="profile-span">Horas definitivas:</span> <span class="data-value2">36/40</span></div>
                                    </td>
                                    <td>
                                        <div><span class="profile-span">Horas temporales:</span> <span class="data-value2">36/40</span></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span class="close" onclick="cerrarModalDetalle()">&times;</span>
            </div>

            <div class="search-section">
                <div class="search-box">
                    <input type="text" placeholder="Buscar..." id="search-input">

                </div>
                <!--  
                <select class="department-select">
                    <option>Departamento</option>
                </select> 
                -->
            </div>

            <div class="navigation">
                <button class="nav-arrow prev-arrow" disabled><</button>
                <div class="nav-items-container">
                    <a href="#todas" class="nav-item active" data-section="todas">Todas las materias</a>
                    <?php 
                       foreach ($materias_por_nombre as $nombre_materia => $cursos) {
                        $section_id = preg_replace('/[^a-z0-9]+/', '-', strtolower($nombre_materia));
                        $shortened_name = strlen($nombre_materia) > 15 
                            ? substr($nombre_materia, 0, 15) . '...' 
                            : $nombre_materia;
                        echo "<a href='#$section_id' class='nav-item' data-section='$section_id'>" 
                            . htmlspecialchars($shortened_name) 
                            . "<span class='tooltip'>" . htmlspecialchars($nombre_materia) . "</span>"
                            . "</a>";
                        }
                    ?>
                </div>
                <button class="nav-arrow next-arrow">></button>
            </div>
                
            <div class="navigation-space"></div>

            <!-- Contenedor para todas las secciones -->
            <div class="sections-container"> 
                <div class="curso-seccion active" id="todas">
                    <table class="table-profesor">
                        <thead>
                            <tr>
                                <th class="col-nrc th-L">NRC</th>
                                <th class="col-materia">Materia</th>
                                <th class="col-departamento">Departamento</th>
                                <th class="col-hora">Hora</th>
                                <th class="col-dias">Día(s)</th>
                                <th class="col-aula">Aula</th>
                                <th class="col-modalidad th-R">Edificio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materias as $materia): ?>
                            <tr>
                                <td class="col-nrc"><?php echo htmlspecialchars($materia['CRN']); ?></td>
                                <td class="col-materia">
                                    <?php echo htmlspecialchars($materia['MATERIA']); ?>
                                </td>
                                <td class="col-departamento"><?php echo htmlspecialchars(limpiarNombreDepartamento($materia['departamento_origen'])); ?></td>
                                <td class="col-hora">
                                    <?php 
                                        $hora_inicio = $materia['HORA_INICIAL'] ?? '0000';
                                        $hora_fin = $materia['HORA_FINAL'] ?? '0000';
                                        $horarioFormateado = sprintf('%s - %s', 
                                            substr($hora_inicio, 0, 2) . ':' . substr($hora_inicio, 2, 2), 
                                            substr($hora_fin, 0, 2) . ':' . substr($hora_fin, 2, 2)
                                            );
                                        echo htmlspecialchars($horarioFormateado); 
                                    ?>
                                </td>
                                <td class="col-dias">
                                    <?php 
                                        $dias = '';
                                        if($materia['L'] == 'L') $dias .= 'L';
                                        if($materia['M'] == 'M') $dias .= 'M';
                                        if($materia['I'] == 'I') $dias .= 'I';
                                        if($materia['J'] == 'J') $dias .= 'J';
                                        if($materia['V'] == 'V') $dias .= 'V';
                                        if($materia['S'] == 'S') $dias .= 'S';
                                        if($materia['D'] == 'D') $dias .= 'D';
                                        if($dias == '') $dias .= 'Sin Datos';
                                    ?>
                                    <div class="weekdays" id="weekdays-<?php echo $materia['CRN']; ?>-<?php echo $materia['MODULO']; ?>">
                                        <div class="day">L</div>
                                        <div class="day">M</div>
                                        <div class="day">I</div>
                                        <div class="day">J</div>
                                        <div class="day">V</div>
                                        <div class="day">S</div>
                                    </div>   
                                    <?php if(isset($materia['es_hibrida']) && $materia['es_hibrida']): ?>
                                        <?php
                                        $diasVirtuales = '';
                                        if($materia['dias_virtuales']['L'] == 'L') $diasVirtuales .= 'L';
                                        if($materia['dias_virtuales']['M'] == 'M') $diasVirtuales .= 'M';
                                        if($materia['dias_virtuales']['I'] == 'I') $diasVirtuales .= 'I';
                                        if($materia['dias_virtuales']['J'] == 'J') $diasVirtuales .= 'J';
                                        if($materia['dias_virtuales']['V'] == 'V') $diasVirtuales .= 'V';
                                        if($materia['dias_virtuales']['S'] == 'S') $diasVirtuales .= 'S';
                                        if($materia['dias_virtuales']['D'] == 'D') $diasVirtuales .= 'D';
                                        ?>
                                        <script>
                                            actualizarDiasHibridos(
                                                '<?php echo $materia['CRN']; ?>', 
                                                '<?php echo $materia['MODULO']; ?>', 
                                                '<?php echo $dias; ?>', 
                                                '<?php echo $diasVirtuales; ?>'
                                            );
                                        </script>
                                    <?php else: ?>
                                        <script>
                                            actualizarDiasActivos(
                                                '<?php echo $dias; ?>', 
                                                '<?php echo $materia['CRN']; ?>', 
                                                '<?php echo $materia['MODULO']; ?>', 
                                                '<?php echo $materia['MODULO'] === 'CVIRTU' ? 'true' : 'false'; ?>'
                                            );
                                        </script>
                                    <?php endif; ?>
                                </td>
                                <td class="col-aula"><?php echo htmlspecialchars($materia['AULA'] ?? 'No hay datos'); ?></td>
                                <td class="col-modalidad"><?php 
                                    // Determine modality
                                    $modalidad = $materia['MODULO'] === 'CVIRTU' ? 'Virtual' : 
                                                ($materia['MODULO']);
                                    echo htmlspecialchars($modalidad ?? 'No hay datos'); 
                                ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Individual course sections can be added here dynamically -->
                <?php 
                    foreach ($materias_por_nombre as $nombre_materia => $cursos): 
                        $section_id = preg_replace('/[^a-z0-9]+/', '-', strtolower($nombre_materia));
                ?>

                <div class="curso-seccion" id="<?php echo htmlspecialchars($section_id); ?>">
                    <table class="table-profesor">
                        <thead>
                            <tr>
                                <th class="col-nrc th-L">NRC</th>
                                <th class="col-departamento">Departamento</th>
                                <th class="col-hora">Hora</th>
                                <th class="col-dias">Día(s)</th>
                                <th class="col-aula">Aula</th>
                                <th class="col-modalidad th-R">Edificio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                            <tr>
                                <td class="col-nrc"><?php echo htmlspecialchars($curso['CRN']); ?></td>
                                <td class="col-departamento"><?php echo htmlspecialchars(limpiarNombreDepartamento($curso['departamento_origen'])); ?></td>
                                <td class="col-hora"><?php 
                                    $hora_inicio = $curso['HORA_INICIAL'] ?? '0000';
                                    $hora_fin = $curso['HORA_FINAL'] ?? '0000';
                                    $horarioFormateado = sprintf('%s - %s', 
                                        substr($hora_inicio, 0, 2) . ':' . substr($hora_inicio, 2, 2),
                                        substr($hora_fin, 0, 2) . ':' . substr($hora_fin, 2, 2)
                                    );
                                    echo htmlspecialchars($horarioFormateado); 
                                ?></td>
                                <td class="col-dias">
                                    <div class="weekdays" id="weekdays-<?php echo $curso['CRN']; ?>-<?php echo $curso['MODULO']; ?>">
                                        <?php 
                                        if(isset($curso['es_hibrida']) && $curso['es_hibrida']) {
                                            $dias = ['L', 'M', 'I', 'J', 'V', 'S'];
                                            foreach ($dias as $dia) {
                                                $is_active = $curso[$dia] ? 'active' : '';
                                                $is_virtual = isset($curso['dias_virtuales'][$dia]) ? 'virtual' : '';
                                                echo "<div class='day $is_active $is_virtual'>$dia</div>";
                                            }
                                        } else {
                                            $dias = ['L', 'M', 'I', 'J', 'V', 'S'];
                                            foreach ($dias as $dia) {
                                                echo $curso[$dia] ? "<div class='day active'>$dia</div>" : "<div class='day'>$dia</div>";
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php if(isset($curso['es_hibrida']) && $curso['es_hibrida']): ?>
                                        <script>
                                        actualizarDiasHibridos(
                                            '<?php echo $curso['CRN']; ?>', 
                                            '<?php echo $curso['MODULO']; ?>', 
                                            '<?php 
                                                $dias_presenciales = '';
                                                $dias = ['L', 'M', 'I', 'J', 'V', 'S'];
                                                foreach ($dias as $dia) {
                                                    if($curso[$dia]) $dias_presenciales .= $dia;
                                                }
                                                echo $dias_presenciales;
                                            ?>', 
                                            '<?php 
                                                $dias_virtuales = '';
                                                $dias = ['L', 'M', 'I', 'J', 'V', 'S'];
                                                foreach ($dias as $dia) {
                                                    if(isset($curso['dias_virtuales'][$dia])) $dias_virtuales .= $dia;
                                                }
                                                echo $dias_virtuales;
                                            ?>'
                                        );
                                        </script>
                                    <?php endif; ?>
                                </td>
                                <td class="col-aula"><?php echo htmlspecialchars($curso['AULA'] ?? 'N/A'); ?></td>
                                <td class="col-modalidad"><?php 
                                    $modalidad = isset($curso['es_hibrida']) && $curso['es_hibrida'] ? 'Híbrido' : 
                                                ($curso['MODULO'] === 'CVIRTU' ? 'Virtual' : $curso['MODULO']);
                                    echo htmlspecialchars($modalidad ?? 'N/A'); 
                                ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div>
            <span class="close-materias" onclick="cerrarModalDetalle()">&times;</span>
            <p>No se encontró información para este profesor.</p>
        </div>
        <?php
    }
}
?>

<script>
$(document).ready(function() {
    // Ocultar todas las secciones excepto la activa al inicio
    $('.curso-seccion').hide();
    $('#todas').show();
    
    // Manejar clics en los items de navegación
    $('.nav-item').click(function(e) {
        e.preventDefault();
        
        // Remover clase activa de todos los items y agregarla al clickeado
        $('.nav-item').removeClass('active');
        $(this).addClass('active');
        
        // Ocultar todas las secciones y mostrar la seleccionada
        const targetId = $(this).data('section');
        $('.curso-seccion').hide();
        $(`#${targetId}`).show().css('opacity', 0).animate({opacity: 1}, 200);

        // Nueva funcionalidad de desplazamiento
        const $navContainer = $('.nav-items-container');
        const $clickedNavItem = $(this);
        
        // Calcular las dimensiones del contenedor y del artículo
        const containerWidth = $navContainer.width();
        const itemOffset = $clickedNavItem.position().left;
        const itemWidth = $clickedNavItem.outerWidth();
        
        // Calcula la posición de desplazamiento para centrar el elemento
        const scrollPosition = itemOffset - (containerWidth / 2) + (itemWidth / 2);
        
        // Desplazamiento animado
        $navContainer.animate({
            scrollLeft: scrollPosition
        }, 300);
        
        // Actualizar estado de las flechas
        updateArrows();
    });
    
    // Función para actualizar el estado de las flechas
    function updateArrows() {
        const activeIndex = $('.nav-item.active').index();
        const totalItems = $('.nav-item').length;
        
        $('.prev-arrow').prop('disabled', activeIndex === 0);
        $('.next-arrow').prop('disabled', activeIndex === totalItems - 1);
    }
    
    // Manejar clics en las flechas
    $('.prev-arrow').click(function() {
        if (!$(this).prop('disabled')) {
            const activeItem = $('.nav-item.active');
            activeItem.prev('.nav-item').click();
        }
    });
    
    $('.next-arrow').click(function() {
        if (!$(this).prop('disabled')) {
            const activeItem = $('.nav-item.active');
            activeItem.next('.nav-item').click();
        }
    });
    
    // Inicializar estado de las flechas
    updateArrows();
});
</script>

<script>
$(document).ready(function() {
    // Crear un contenedor para el tooltip global
    $('body').append('<div id="global-tooltip" style="display:none; position:absolute; background-color:#333; color:#fff; padding:5px 10px; border-radius:6px; z-index:1000; white-space:nowrap;"></div>');
    
    $('.nav-item:not([data-section="todas"])').hover(
        function(e) {
            // Mostrar tooltip
            const fullName = $(this).find('.tooltip').text();
            
            // Solo mostrar tooltip si el nombre está truncado
            if ($(this).text().trim() !== fullName) {
                const $tooltip = $('#global-tooltip');
                $tooltip.text(fullName)
                    .css({
                        top: $(this).offset().top - 35,
                        left: $(this).offset().left + ($(this).outerWidth() / 2) - ($tooltip.outerWidth() / 2)
                    })
                    .show();
            }
        },
        function() {
            // Ocultar tooltip
            $('#global-tooltip').hide();
        }
    );
});
</script>

<script>
    $(document).ready(function() {
    // Search functionality
    $('#search-input').on('keyup', function() {
        const searchText = $(this).val().toLowerCase().trim();
        
        // If search is empty, show all courses in the active section
        if (searchText === '') {
            $('.curso-seccion.active tbody tr').show();
            return;
        }

        // Get the currently active section
        const $activeSection = $('.curso-seccion.active');
        
        // Filter rows in the active section
        $activeSection.find('tbody tr').each(function() {
            const $row = $(this);
            const rowText = $row.text().toLowerCase();
            
            // Hide/show row based on search match
            if (rowText.includes(searchText)) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    });

    // Ensure search works when switching between sections
    $('.nav-item').click(function() {
        $('#search-input').val('').trigger('keyup');
    });
});
</script>

<script src="./JS/basesdedatos/profesores-materias.js"></script>
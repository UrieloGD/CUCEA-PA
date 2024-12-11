<link rel="stylesheet" href="./CSS/detalle-profesor.css?=v1.0">


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

    function obtenerCursosUnicos($conexion, $codigo_profesor) {
        // Obtener todas las tablas de departamentos
        $sql_departamentos = "SELECT Nombre_Departamento FROM Departamentos";
        $result_departamentos = mysqli_query($conexion, $sql_departamentos);
        
        // Array para almacenar cursos únicos
        $cursos_unicos = [];
        
        // Iteración a través de cada tabla de departamentos
        while ($row_departamento = mysqli_fetch_assoc($result_departamentos)) {
            $tabla_departamento = "Data_" . $row_departamento['Nombre_Departamento'];
            
            // Consulta SQL para obtener cursos únicos con manejo especial para duplicados virtuales/presenciales
            $sql_cursos = "
                SELECT 
                    CRN, 
                    MATERIA, 
                    MODALIDAD, 
                    DIA_PRESENCIAL, 
                    DIA_VIRTUAL, 
                    HORA_INICIAL,
                    HORA_FINAL, 
                    AULA,
                    MODULO,
                    '$tabla_departamento' AS departamento_origen
                FROM $tabla_departamento
                WHERE CODIGO_PROFESOR = ?
                AND (
                    CRN NOT IN (
                        SELECT CRN 
                        FROM $tabla_departamento 
                        WHERE CODIGO_PROFESOR = ? 
                        GROUP BY CRN 
                        HAVING COUNT(DISTINCT MODALIDAD) > 1
                    )
                    OR (
                        MODALIDAD = 'VIRTUAL' 
                        AND CRN IN (
                            SELECT CRN 
                            FROM $tabla_departamento 
                            WHERE CODIGO_PROFESOR = ? 
                            AND MODALIDAD = 'PRESENCIAL ENRIQUECIDA'
                        )
                    )
                )
                GROUP BY CRN, MATERIA, MODALIDAD, DIA_PRESENCIAL, DIA_VIRTUAL, HORA_INICIAL, HORA_FINAL, AULA, MODULO
            ";
            
            // Preparar y ejecutar la instrucción
            $stmt = mysqli_prepare($conexion, $sql_cursos);
            mysqli_stmt_bind_param($stmt, "iii", $codigo_profesor, $codigo_profesor, $codigo_profesor);
            mysqli_stmt_execute($stmt);
            $result_cursos = mysqli_stmt_get_result($stmt);
            
            // Colecciona cursos únicos
            while ($curso = mysqli_fetch_assoc($result_cursos)) {
                // Utilice CRN como clave única para evitar duplicados
                $cursos_unicos[$curso['CRN']] = $curso;
            }
            }
        // Devolver la matriz de cursos únicos
        
        return array_values($cursos_unicos);
    }

    function convertirDiasAbreviatura($dias_completos, $tipo = null) {
        // Verificar si el input es nulo o está vacío
        if ($dias_completos === null || trim($dias_completos) === '') {
            return [];
        }

        $dias_map = [
            'lunes' => 'L',
            'martes' => 'M',
            'miercoles' => 'I',
            'jueves' => 'J',
            'viernes' => 'V',
            'sabado' => 'S'
        ];
    
        // Convertir a minúsculas y dividir por coma
        $dias_array = array_map('trim', explode(',', mb_strtolower($dias_completos, 'UTF-8')));
        
        // Convertir cada día a su abreviatura
        $dias_abreviados = array_map(function($dia) use ($dias_map, $tipo) {
            return [
                'abreviatura' => isset($dias_map[$dia]) ? $dias_map[$dia] : $dia,
                'tipo' => $tipo
            ];
        }, $dias_array);
    
        return $dias_abreviados;
    }
    
    // Obtener información del departamento y del profesor
    $tablas_departamentos = obtenerTablasDepartamentos($conexion);
    $cursos_profesor = obtenerCursosUnicos($conexion, $codigo_profesor);

    if(!empty($cursos_profesor) && $datos_profesor) {
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

                <?php
                    // Cursos agrupados por nombre de la materia
                    $cursos_agrupados = [];
                    foreach ($cursos_profesor as $curso) {
                        $materia_key = $curso['MATERIA'];
                        
                        // Si esta materia no existe en la matriz agrupada, cree una nueva entrada
                        if (!isset($cursos_agrupados[$materia_key])) {
                            $cursos_agrupados[$materia_key] = [];
                        }
                        
                        // Agregar el curso actual al grupo
                        $cursos_agrupados[$materia_key][] = $curso;
                    }
                ?>

                <div class="navigation">
                    <button class="nav-arrow prev-arrow" disabled><</button>
                    <div class="nav-items-container">
                        <a href="#todas" class="nav-item active" data-section="todas">Todas las materias</a>
                        <?php
                            foreach ($cursos_agrupados as $materia => $cursos_grupo) {
                                // Utilica el primer curso del grupo para generar el elemento de navegación
                                $curso_representativo = $cursos_grupo[0];
                                
                                // Genera un identificador único para el grupo
                                $grupo_id = 'grupo_' . md5($materia);
                                
                                echo '<a href="#' . htmlspecialchars($grupo_id) . '" class="nav-item" data-section="' . htmlspecialchars($grupo_id) . '">' . 
                                    htmlspecialchars($materia) . 
                                    '</a>';
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
                                    <th class="col-nrc th-L">CRN</th>
                                    <th class="col-materia">Materia</th>
                                    <th class="col-departamento">Departamento</th>
                                    <th class="col-hora">Hora</th>
                                    <th class="col-dias">Día(s)</th>
                                    <th class="col-aula">Aula</th>
                                    <th class="col-modalidad th-R">Edificio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($cursos_profesor as $curso) {
                                    // Convertir días presenciales y virtuales, manejando posibles nulos
                                    $dias_presenciales = convertirDiasAbreviatura($curso['DIA_PRESENCIAL'], 'presencial');
                                    $dias_virtuales = convertirDiasAbreviatura($curso['DIA_VIRTUAL'], 'virtual');

                                    // Combinar y eliminar duplicados
                                    $dias_curso = [];
                                    $dias_combinados = array_merge($dias_presenciales, $dias_virtuales);

                                    // Crear un mapa de días con su tipo
                                    foreach ($dias_combinados as $dia) {
                                        $dias_curso[$dia['abreviatura']] = $dia['tipo'] ?? null;
                                    }

                                    $hora_inicio = $curso['HORA_INICIAL'] ?? '0000';
                                    $hora_fin = $curso['HORA_FINAL'] ?? '0000';
                                    $horarioFormateado = sprintf('%s - %s', 
                                        substr($hora_inicio, 0, 2) . ':' . substr($hora_inicio, 2, 2), 
                                        substr($hora_fin, 0, 2) . ':' . substr($hora_fin, 2, 2)
                                    );

                                    $fila_todas = '
                                    <tr>
                                        <td class="col-nrc">' . htmlspecialchars($curso['CRN']) . '</td>
                                        <td class="col-materia">' . htmlspecialchars($curso['MATERIA']) . '</td>
                                        <td class="col-departamento">' . htmlspecialchars(limpiarNombreDepartamento($curso['departamento_origen'])) . '</td>
                                        <td class="col-hora">' .  htmlspecialchars($horarioFormateado) . '</td>
                                        <td class="col-dias">
                                            <div class="weekdays">';
                                    
                                            $dias_semana = ['L', 'M', 'I', 'J', 'V', 'S'];
        
                                            foreach ($dias_semana as $dia) {
                                                $tipo_dia = $dias_curso[$dia] ?? null;
                                                $class = '';
                                                
                                                if ($tipo_dia === 'presencial') {
                                                    $class = 'active presencial';
                                                } elseif ($tipo_dia === 'virtual') {
                                                    $class = 'active virtual';
                                                }
                                                
                                                $fila_todas .= '<div class="day ' . $class . '">' . $dia . '</div>';
                                            }
                                    
                                    $fila_todas .= '
                                            </div>
                                        </td>
                                        <td class="col-aula">' . htmlspecialchars($curso['AULA'] ?? 'No hay datos') . '</td>
                                        <td class="col-modalidad">' . htmlspecialchars($curso['MODULO'] . ' (' . $curso['MODALIDAD'] . ')' ?? 'No hay datos') . '</td>
                                    </tr>';
                                    
                                    echo $fila_todas;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Aquí se añaden secciones individuales del curso de forma dinámica -->
                    <?php
                        // Genera secciones de curso agrupadas
                        foreach ($cursos_agrupados as $materia => $cursos_grupo) {
                            $grupo_id = 'grupo_' . md5($materia);
                            ?>
                            <div class="curso-seccion" id="<?php echo htmlspecialchars($grupo_id); ?>">
                                <table class="table-profesor">
                                    <thead>
                                        <tr>
                                            <th class="col-nrc th-L">CRN</th>
                                            <th class="col-materia">Materia</th>
                                            <th class="col-departamento">Departamento</th>
                                            <th class="col-hora">Hora</th>
                                            <th class="col-dias">Día(s)</th>
                                            <th class="col-aula">Aula</th>
                                            <th class="col-modalidad th-R">Edificio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Renderiza cada curso en el grupo
                                        foreach ($cursos_grupo as $curso) {
                                            // Convertir días presenciales y virtuales
                                            $dias_presenciales = convertirDiasAbreviatura($curso['DIA_PRESENCIAL'], 'presencial');
                                            $dias_virtuales = convertirDiasAbreviatura($curso['DIA_VIRTUAL'], 'virtual');

                                            // Combinar y eliminar duplicados
                                            $dias_curso = [];
                                            $dias_combinados = array_merge($dias_presenciales, $dias_virtuales);

                                            // Crear un mapa de días con su tipo
                                            foreach ($dias_combinados as $dia) {
                                                $dias_curso[$dia['abreviatura']] = $dia['tipo'] ?? null;
                                            }

                                            $hora_inicio = $curso['HORA_INICIAL'] ?? '0000';
                                            $hora_fin = $curso['HORA_FINAL'] ?? '0000';
                                            $horarioFormateado = sprintf('%s - %s', 
                                                substr($hora_inicio, 0, 2) . ':' . substr($hora_inicio, 2, 2), 
                                                substr($hora_fin, 0, 2) . ':' . substr($hora_fin, 2, 2)
                                            );
                                            
                                            $fila_curso = '
                                            <tr>
                                                <td class="col-nrc">' . htmlspecialchars($curso['CRN']) . '</td>
                                                <td class="col-materia">' . htmlspecialchars($curso['MATERIA']) . '</td>
                                                <td class="col-departamento">' . htmlspecialchars(limpiarNombreDepartamento($curso['departamento_origen'])) . '</td>
                                                <td class="col-hora">' .  htmlspecialchars($horarioFormateado) . '</td>
                                                <td class="col-dias">
                                                    <div class="weekdays">';
                                            
                                            $dias_semana = ['L', 'M', 'I', 'J', 'V', 'S'];

                                            foreach ($dias_semana as $dia) {
                                                $tipo_dia = $dias_curso[$dia] ?? null;
                                                $class = '';
                                                
                                                if ($tipo_dia === 'presencial') {
                                                    $class = 'active presencial';
                                                } elseif ($tipo_dia === 'virtual') {
                                                    $class = 'active virtual';
                                                }
                                                
                                                $fila_curso .= '<div class="day ' . $class . '">' . $dia . '</div>';
                                            }
                                    
                                            $fila_curso .= '
                                                    </div>
                                                </td>
                                                <td class="col-aula">' . htmlspecialchars($curso['AULA'] ?? 'No hay datos') . '</td>
                                                <td class="col-modalidad">' . htmlspecialchars($curso['MODULO'] . ' (' . $curso['MODALIDAD'] . ')' ?? 'No hay datos') . '</td>
                                            </tr>';
                                            
                                            echo $fila_curso;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                    ?>
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
    // Funcionalidad de búsqueda
    $('#search-input').on('keyup', function() {
        const searchText = $(this).val().toLowerCase().trim();
        
        // Si la búsqueda está vacía, muestra todos los cursos en la sección activa
        if (searchText === '') {
            $('.curso-seccion.active tbody tr').show();
            return;
        }

        // Obtiene la sección activa
        const $activeSection = $('.curso-seccion.active');
        
        // Filtra filas en la sección activa
        $activeSection.find('tbody tr').each(function() {
            const $row = $(this);
            const rowText = $row.text().toLowerCase();
            
            // Ocultar/mostrar fila en función de la coincidencia de búsqueda
            if (rowText.includes(searchText)) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    });

    $('.nav-item').click(function() {
        $('#search-input').val('').trigger('keyup');
    });
});
</script>

<script src="./JS/basesdedatos/profesores-materias.js"></script>